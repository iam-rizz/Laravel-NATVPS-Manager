/**
 * VNC WebSocket Proxy Server (websockify-like)
 * 
 * A lightweight WebSocket-to-TCP proxy for VNC connections.
 * Allows noVNC to connect to VNC servers via WebSocket.
 * 
 * Usage:
 *   node server.js
 * 
 * Environment Variables:
 *   VNC_PROXY_PORT - Port to listen on (default: 6080)
 *   VNC_PROXY_HOST - Host to bind to (default: 0.0.0.0)
 *   VNC_PROXY_PATH - WebSocket path (default: /websockify)
 * 
 * WebSocket URL format:
 *   ws://localhost:6080/websockify/TARGET_HOST/TARGET_PORT
 *   Example: ws://localhost:6080/websockify/192.168.1.100/5900
 */

const path = require('path');

// Load .env from Laravel root directory first, then local .env as fallback
require('dotenv').config({ path: path.resolve(__dirname, '../../.env') });
require('dotenv').config(); // Local .env can override

const WebSocket = require('ws');
const net = require('net');
const http = require('http');
const url = require('url');

// Configuration - supports both Laravel .env naming and local .env naming
const PORT = process.env.WEBSOCKIFY_VNC_PORT || process.env.VNC_PROXY_PORT || 6080;
const HOST = process.env.WEBSOCKIFY_HOST || process.env.VNC_PROXY_HOST || '0.0.0.0';
const PATH = process.env.VNC_PROXY_PATH || '/websockify';

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
    perMessageDeflate: false // Disable compression for binary data
});

console.log(`VNC WebSocket Proxy starting...`);
console.log(`Listening on ${HOST}:${PORT}${PATH}`);

wss.on('connection', (ws, req) => {
    const clientIp = req.socket.remoteAddress;
    console.log(`[${new Date().toISOString()}] New WebSocket connection from ${clientIp}`);
    
    // Parse URL to get target host and port
    // Format: /websockify/HOST/PORT or /websockify?host=HOST&port=PORT
    const parsedUrl = url.parse(req.url, true);
    const pathParts = parsedUrl.pathname.split('/').filter(p => p && p !== 'websockify');
    
    let targetHost, targetPort;
    
    if (pathParts.length >= 2) {
        // Path format: /websockify/HOST/PORT
        targetHost = pathParts[0];
        targetPort = parseInt(pathParts[1]);
    } else if (parsedUrl.query.host && parsedUrl.query.port) {
        // Query format: /websockify?host=HOST&port=PORT
        targetHost = parsedUrl.query.host;
        targetPort = parseInt(parsedUrl.query.port);
    } else {
        console.error('Missing target host/port in URL');
        ws.close(1008, 'Missing target host/port');
        return;
    }
    
    if (!targetHost || !targetPort || isNaN(targetPort)) {
        console.error(`Invalid target: ${targetHost}:${targetPort}`);
        ws.close(1008, 'Invalid target host/port');
        return;
    }
    
    console.log(`[${new Date().toISOString()}] Connecting to VNC: ${targetHost}:${targetPort}`);
    
    // Create TCP connection to VNC server
    const tcpSocket = net.createConnection({
        host: targetHost,
        port: targetPort
    });
    
    let isConnected = false;
    
    tcpSocket.on('connect', () => {
        console.log(`[${new Date().toISOString()}] TCP connected to ${targetHost}:${targetPort}`);
        isConnected = true;
    });
    
    tcpSocket.on('data', (data) => {
        // Forward TCP data to WebSocket as binary
        if (ws.readyState === WebSocket.OPEN) {
            try {
                ws.send(data);
            } catch (err) {
                console.error('Error sending to WebSocket:', err);
            }
        }
    });
    
    tcpSocket.on('error', (err) => {
        console.error(`[${new Date().toISOString()}] TCP error:`, err.message);
        if (ws.readyState === WebSocket.OPEN) {
            ws.close(1011, `VNC connection error: ${err.message}`);
        }
    });
    
    tcpSocket.on('close', () => {
        console.log(`[${new Date().toISOString()}] TCP connection closed`);
        if (ws.readyState === WebSocket.OPEN) {
            ws.close(1000, 'VNC connection closed');
        }
    });
    
    tcpSocket.on('timeout', () => {
        console.log(`[${new Date().toISOString()}] TCP connection timeout`);
        tcpSocket.destroy();
        if (ws.readyState === WebSocket.OPEN) {
            ws.close(1011, 'VNC connection timeout');
        }
    });
    
    // Set TCP timeout (30 seconds)
    tcpSocket.setTimeout(30000);
    
    // Handle WebSocket messages (forward to TCP)
    ws.on('message', (message) => {
        if (isConnected && !tcpSocket.destroyed) {
            try {
                // Convert to Buffer if needed
                const data = Buffer.isBuffer(message) ? message : Buffer.from(message);
                tcpSocket.write(data);
            } catch (err) {
                console.error('Error writing to TCP:', err);
            }
        }
    });
    
    ws.on('close', (code, reason) => {
        console.log(`[${new Date().toISOString()}] WebSocket closed: ${code} ${reason}`);
        if (!tcpSocket.destroyed) {
            tcpSocket.destroy();
        }
    });
    
    ws.on('error', (err) => {
        console.error('WebSocket error:', err);
        if (!tcpSocket.destroyed) {
            tcpSocket.destroy();
        }
    });
});

// Start server
server.listen(PORT, HOST, () => {
    console.log(`VNC WebSocket Proxy is running on ${HOST}:${PORT}`);
    console.log(`WebSocket endpoint: ws://${HOST}:${PORT}${PATH}/HOST/PORT`);
    console.log(`Example: ws://${HOST}:${PORT}${PATH}/192.168.1.100/5900`);
    console.log(`Health check: http://${HOST}:${PORT}/health`);
});

// Handle process termination
process.on('SIGINT', () => {
    console.log('\nShutting down VNC proxy...');
    wss.close();
    server.close();
    process.exit(0);
});

process.on('SIGTERM', () => {
    console.log('\nShutting down VNC proxy...');
    wss.close();
    server.close();
    process.exit(0);
});
