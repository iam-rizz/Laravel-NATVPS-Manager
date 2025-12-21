/**
 * Console WebSocket Proxy Server
 * 
 * A unified WebSocket proxy for VNC and SSH connections.
 * Combines VNC (websockify) and SSH proxy into a single server.
 * 
 * Usage:
 *   node server.js
 * 
 * Configuration:
 *   - If deployed with Laravel: reads from ../../.env (Laravel root)
 *   - If deployed separately: reads from local .env
 * 
 * Endpoints:
 *   VNC: ws://localhost:6080/websockify/HOST/PORT
 *   SSH: ws://localhost:6080/ssh
 *   Health: http://localhost:6080/health
 */

const path = require('path');

// Load .env - try Laravel root first, then local .env for standalone deployment
require('dotenv').config({ path: path.resolve(__dirname, '../../.env') });
require('dotenv').config(); // Local .env can override for standalone deployment

const WebSocket = require('ws');
const { Client: SSHClient } = require('ssh2');
const net = require('net');
const http = require('http');
const url = require('url');

// Configuration
const PORT = process.env.CONSOLE_PROXY_PORT || process.env.WEBSOCKIFY_VNC_PORT || 6080;
const HOST = process.env.CONSOLE_PROXY_HOST || process.env.WEBSOCKIFY_HOST || '0.0.0.0';

// Create HTTP server
const server = http.createServer((req, res) => {
    if (req.url === '/health') {
        res.writeHead(200, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify({ 
            status: 'ok', 
            services: ['vnc', 'ssh'],
            timestamp: new Date().toISOString() 
        }));
        return;
    }
    res.writeHead(404);
    res.end('Not Found');
});

// Create WebSocket server
const wss = new WebSocket.Server({ 
    server,
    perMessageDeflate: false
});

console.log('========================================');
console.log('NAT VPS Console Proxy');
console.log('========================================');
console.log(`Host: ${HOST}:${PORT}`);
console.log(`VNC endpoint: ws://${HOST}:${PORT}/websockify/HOST/PORT`);
console.log(`SSH endpoint: ws://${HOST}:${PORT}/ssh`);
console.log(`Health check: http://${HOST}:${PORT}/health`);
console.log('========================================');

wss.on('connection', (ws, req) => {
    const clientIp = req.socket.remoteAddress;
    const parsedUrl = url.parse(req.url, true);
    const pathname = parsedUrl.pathname;
    
    console.log(`[${timestamp()}] Connection from ${clientIp} to ${pathname}`);
    
    if (pathname.startsWith('/websockify')) {
        handleVNC(ws, req, parsedUrl);
    } else if (pathname.startsWith('/ssh')) {
        handleSSH(ws, req, parsedUrl);
    } else {
        ws.close(1008, 'Invalid endpoint');
    }
});

/**
 * Handle VNC WebSocket connection
 */
function handleVNC(ws, req, parsedUrl) {
    const pathParts = parsedUrl.pathname.split('/').filter(p => p && p !== 'websockify');
    
    let targetHost, targetPort;
    
    if (pathParts.length >= 2) {
        targetHost = pathParts[0];
        targetPort = parseInt(pathParts[1]);
    } else if (parsedUrl.query.host && parsedUrl.query.port) {
        targetHost = parsedUrl.query.host;
        targetPort = parseInt(parsedUrl.query.port);
    } else {
        console.error(`[${timestamp()}] VNC: Missing target host/port`);
        ws.close(1008, 'Missing target host/port');
        return;
    }
    
    if (!targetHost || !targetPort || isNaN(targetPort)) {
        ws.close(1008, 'Invalid target');
        return;
    }
    
    console.log(`[${timestamp()}] VNC: Connecting to ${targetHost}:${targetPort}`);
    
    const tcpSocket = net.createConnection({ host: targetHost, port: targetPort });
    let isConnected = false;
    
    tcpSocket.on('connect', () => {
        console.log(`[${timestamp()}] VNC: Connected to ${targetHost}:${targetPort}`);
        isConnected = true;
    });
    
    tcpSocket.on('data', (data) => {
        if (ws.readyState === WebSocket.OPEN) {
            try { ws.send(data); } catch (e) {}
        }
    });
    
    tcpSocket.on('error', (err) => {
        console.error(`[${timestamp()}] VNC error: ${err.message}`);
        if (ws.readyState === WebSocket.OPEN) {
            ws.close(1011, `VNC error: ${err.message}`);
        }
    });
    
    tcpSocket.on('close', () => {
        console.log(`[${timestamp()}] VNC: TCP closed`);
        if (ws.readyState === WebSocket.OPEN) ws.close(1000);
    });
    
    tcpSocket.setTimeout(30000);
    tcpSocket.on('timeout', () => {
        tcpSocket.destroy();
        if (ws.readyState === WebSocket.OPEN) ws.close(1011, 'Timeout');
    });
    
    ws.on('message', (message) => {
        if (isConnected && !tcpSocket.destroyed) {
            const data = Buffer.isBuffer(message) ? message : Buffer.from(message);
            try { tcpSocket.write(data); } catch (e) {}
        }
    });
    
    ws.on('close', () => {
        console.log(`[${timestamp()}] VNC: WebSocket closed`);
        if (!tcpSocket.destroyed) tcpSocket.destroy();
    });
    
    ws.on('error', () => {
        if (!tcpSocket.destroyed) tcpSocket.destroy();
    });
}

/**
 * Handle SSH WebSocket connection
 */
function handleSSH(ws, req, parsedUrl) {
    const queryParams = parsedUrl.query;
    let sshClient = null;
    let sshStream = null;
    let isConnected = false;
    
    const send = (msg) => {
        if (ws.readyState === WebSocket.OPEN) {
            ws.send(JSON.stringify(msg));
        }
    };
    
    const cleanup = () => {
        isConnected = false;
        if (sshStream) { try { sshStream.close(); } catch (e) {} sshStream = null; }
        if (sshClient) { try { sshClient.end(); } catch (e) {} sshClient = null; }
    };
    
    ws.on('message', (message) => {
        try {
            const msg = JSON.parse(message.toString());
            
            switch (msg.type) {
                case 'connect':
                    const host = msg.host || queryParams.host;
                    const port = parseInt(msg.port || queryParams.port || 22);
                    const username = msg.username || 'root';
                    const password = msg.password || '';
                    
                    if (!host) {
                        send({ type: 'error', message: 'Missing host' });
                        return;
                    }
                    
                    console.log(`[${timestamp()}] SSH: Connecting to ${username}@${host}:${port}`);
                    
                    sshClient = new SSHClient();
                    
                    sshClient.on('ready', () => {
                        console.log(`[${timestamp()}] SSH: Connected to ${host}:${port}`);
                        
                        sshClient.shell({ term: 'xterm-256color', cols: 80, rows: 24 }, (err, stream) => {
                            if (err) {
                                send({ type: 'error', message: `Shell error: ${err.message}` });
                                return;
                            }
                            
                            sshStream = stream;
                            isConnected = true;
                            send({ type: 'connected' });
                            
                            stream.on('data', (data) => {
                                send({ type: 'data', data: data.toString('utf8') });
                            });
                            
                            stream.stderr.on('data', (data) => {
                                send({ type: 'data', data: data.toString('utf8') });
                            });
                            
                            stream.on('close', () => {
                                console.log(`[${timestamp()}] SSH: Stream closed`);
                                send({ type: 'close' });
                                cleanup();
                            });
                        });
                    });
                    
                    sshClient.on('error', (err) => {
                        console.error(`[${timestamp()}] SSH error: ${err.message}`);
                        send({ type: 'error', message: `SSH error: ${err.message}` });
                        cleanup();
                    });
                    
                    sshClient.on('close', () => {
                        console.log(`[${timestamp()}] SSH: Connection closed`);
                        send({ type: 'close' });
                        cleanup();
                    });
                    
                    const config = {
                        host, port, username,
                        readyTimeout: 30000,
                        keepaliveInterval: 10000
                    };
                    if (password) config.password = password;
                    
                    try { sshClient.connect(config); } 
                    catch (err) { send({ type: 'error', message: err.message }); }
                    break;
                    
                case 'data':
                    if (sshStream && isConnected) sshStream.write(msg.data);
                    break;
                    
                case 'resize':
                    if (sshStream && isConnected) sshStream.setWindow(msg.rows, msg.cols, 0, 0);
                    break;
            }
        } catch (err) {
            send({ type: 'error', message: 'Invalid message' });
        }
    });
    
    ws.on('close', () => {
        console.log(`[${timestamp()}] SSH: WebSocket closed`);
        cleanup();
    });
    
    ws.on('error', cleanup);
}

function timestamp() {
    return new Date().toISOString();
}

// Start server
server.listen(PORT, HOST, () => {
    console.log(`\nConsole Proxy running on ${HOST}:${PORT}\n`);
});

// Graceful shutdown
process.on('SIGINT', shutdown);
process.on('SIGTERM', shutdown);

function shutdown() {
    console.log('\nShutting down...');
    wss.close();
    server.close();
    process.exit(0);
}
