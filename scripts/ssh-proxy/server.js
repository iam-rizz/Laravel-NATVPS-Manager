/**
 * SSH WebSocket Proxy Server
 * 
 * A lightweight SSH-over-WebSocket proxy for NAT VPS Manager.
 * Allows xterm.js to connect to SSH servers via WebSocket.
 * 
 * Usage:
 *   node server.js
 * 
 * Environment Variables:
 *   SSH_PROXY_PORT - Port to listen on (default: 2222)
 *   SSH_PROXY_HOST - Host to bind to (default: 0.0.0.0)
 *   SSH_PROXY_PATH - WebSocket path (default: /ssh)
 * 
 * WebSocket URL format:
 *   ws://localhost:2222/ssh?host=example.com&port=22
 * 
 * Message Protocol:
 *   Client -> Server:
 *     { type: 'connect', host, port, username, password }
 *     { type: 'data', data: 'input string' }
 *     { type: 'resize', cols, rows }
 *   
 *   Server -> Client:
 *     { type: 'connected' }
 *     { type: 'data', data: 'output string' }
 *     { type: 'error', message: 'error message' }
 *     { type: 'close' }
 */

const path = require('path');

// Load .env from Laravel root directory first, then local .env as fallback
require('dotenv').config({ path: path.resolve(__dirname, '../../.env') });
require('dotenv').config(); // Local .env can override

const WebSocket = require('ws');
const { Client } = require('ssh2');
const http = require('http');
const url = require('url');

// Configuration
const PORT = process.env.SSH_PROXY_PORT || 2222;
const HOST = process.env.SSH_PROXY_HOST || '0.0.0.0';
const PATH = process.env.SSH_PROXY_PATH || '/ssh';

// Create HTTP server
const server = http.createServer((req, res) => {
    // Health check endpoint
    if (req.url === '/health') {
        res.writeHead(200, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify({ status: 'ok', timestamp: new Date().toISOString() }));
        return;
    }
    
    res.writeHead(404);
    res.end('Not Found');
});

// Create WebSocket server
const wss = new WebSocket.Server({ 
    server,
    path: PATH
});

console.log(`SSH WebSocket Proxy starting...`);
console.log(`Listening on ${HOST}:${PORT}${PATH}`);

wss.on('connection', (ws, req) => {
    console.log(`[${new Date().toISOString()}] New WebSocket connection from ${req.socket.remoteAddress}`);
    
    // Parse query parameters for initial connection info
    const parsedUrl = url.parse(req.url, true);
    const queryParams = parsedUrl.query;
    
    let sshClient = null;
    let sshStream = null;
    let isConnected = false;
    
    // Handle incoming messages
    ws.on('message', (message) => {
        try {
            const msg = JSON.parse(message.toString());
            
            switch (msg.type) {
                case 'connect':
                    handleConnect(msg);
                    break;
                    
                case 'data':
                    handleData(msg.data);
                    break;
                    
                case 'resize':
                    handleResize(msg.cols, msg.rows);
                    break;
                    
                default:
                    console.log(`Unknown message type: ${msg.type}`);
            }
        } catch (err) {
            console.error('Error parsing message:', err);
            sendError('Invalid message format');
        }
    });
    
    ws.on('close', () => {
        console.log(`[${new Date().toISOString()}] WebSocket connection closed`);
        cleanup();
    });
    
    ws.on('error', (err) => {
        console.error('WebSocket error:', err);
        cleanup();
    });
    
    /**
     * Handle SSH connection request
     */
    function handleConnect(msg) {
        // Use message params or fall back to query params
        const host = msg.host || queryParams.host;
        const port = parseInt(msg.port || queryParams.port || 22);
        const username = msg.username || 'root';
        const password = msg.password || '';
        
        if (!host) {
            sendError('Missing host parameter');
            return;
        }
        
        console.log(`[${new Date().toISOString()}] Connecting to SSH: ${username}@${host}:${port}`);
        
        // Create SSH client
        sshClient = new Client();
        
        sshClient.on('ready', () => {
            console.log(`[${new Date().toISOString()}] SSH connection established to ${host}:${port}`);
            
            // Request a shell
            sshClient.shell({ term: 'xterm-256color', cols: 80, rows: 24 }, (err, stream) => {
                if (err) {
                    console.error('Shell error:', err);
                    sendError(`Failed to open shell: ${err.message}`);
                    return;
                }
                
                sshStream = stream;
                isConnected = true;
                
                // Send connected message
                send({ type: 'connected' });
                
                // Forward SSH output to WebSocket
                stream.on('data', (data) => {
                    if (ws.readyState === WebSocket.OPEN) {
                        send({ type: 'data', data: data.toString('utf8') });
                    }
                });
                
                stream.stderr.on('data', (data) => {
                    if (ws.readyState === WebSocket.OPEN) {
                        send({ type: 'data', data: data.toString('utf8') });
                    }
                });
                
                stream.on('close', () => {
                    console.log(`[${new Date().toISOString()}] SSH stream closed`);
                    send({ type: 'close' });
                    cleanup();
                });
            });
        });
        
        sshClient.on('error', (err) => {
            console.error(`[${new Date().toISOString()}] SSH error:`, err.message);
            sendError(`SSH connection failed: ${err.message}`);
            cleanup();
        });
        
        sshClient.on('close', () => {
            console.log(`[${new Date().toISOString()}] SSH connection closed`);
            if (ws.readyState === WebSocket.OPEN) {
                send({ type: 'close' });
            }
            cleanup();
        });
        
        // Connect to SSH server
        const connectConfig = {
            host: host,
            port: port,
            username: username,
            readyTimeout: 30000,
            keepaliveInterval: 10000
        };
        
        // Use password authentication
        if (password) {
            connectConfig.password = password;
        }
        
        try {
            sshClient.connect(connectConfig);
        } catch (err) {
            console.error('SSH connect error:', err);
            sendError(`Failed to connect: ${err.message}`);
        }
    }
    
    /**
     * Handle terminal input data
     */
    function handleData(data) {
        if (sshStream && isConnected) {
            sshStream.write(data);
        }
    }
    
    /**
     * Handle terminal resize
     */
    function handleResize(cols, rows) {
        if (sshStream && isConnected) {
            sshStream.setWindow(rows, cols, 0, 0);
        }
    }
    
    /**
     * Send message to WebSocket client
     */
    function send(msg) {
        if (ws.readyState === WebSocket.OPEN) {
            ws.send(JSON.stringify(msg));
        }
    }
    
    /**
     * Send error message
     */
    function sendError(message) {
        send({ type: 'error', message: message });
    }
    
    /**
     * Cleanup resources
     */
    function cleanup() {
        isConnected = false;
        
        if (sshStream) {
            try {
                sshStream.close();
            } catch (e) {}
            sshStream = null;
        }
        
        if (sshClient) {
            try {
                sshClient.end();
            } catch (e) {}
            sshClient = null;
        }
    }
});

// Start server
server.listen(PORT, HOST, () => {
    console.log(`SSH WebSocket Proxy is running on ${HOST}:${PORT}`);
    console.log(`WebSocket endpoint: ws://${HOST}:${PORT}${PATH}`);
    console.log(`Health check: http://${HOST}:${PORT}/health`);
});

// Handle process termination
process.on('SIGINT', () => {
    console.log('\nShutting down SSH proxy...');
    wss.close();
    server.close();
    process.exit(0);
});

process.on('SIGTERM', () => {
    console.log('\nShutting down SSH proxy...');
    wss.close();
    server.close();
    process.exit(0);
});
