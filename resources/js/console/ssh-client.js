/**
 * SSH Client Module
 * 
 * Provides SSH terminal access using xterm.js library.
 * Implements connect, disconnect, resize methods.
 * Handles terminal input/output.
 * 
 * Requirements: 1.3, 5.1
 */

import { Terminal } from '@xterm/xterm';
import { FitAddon } from '@xterm/addon-fit';
import { WebLinksAddon } from '@xterm/addon-web-links';

export default class SshClient {
    /**
     * Create an SSH client instance
     * @param {HTMLElement} container - The container element for the terminal
     * @param {Object} options - Configuration options
     * @param {string} options.fontFamily - Font family (default: 'Menlo, Monaco, monospace')
     * @param {number} options.fontSize - Font size (default: 14)
     * @param {string} options.theme - Terminal theme object
     * @param {boolean} options.cursorBlink - Enable cursor blinking (default: true)
     * @param {Function} options.onConnect - Callback when connected
     * @param {Function} options.onDisconnect - Callback when disconnected
     * @param {Function} options.onData - Callback when data received
     * @param {Function} options.onError - Callback on error
     */
    constructor(container, options = {}) {
        this.container = container;
        this.options = {
            fontFamily: 'Menlo, Monaco, "Courier New", monospace',
            fontSize: 14,
            cursorBlink: true,
            cursorStyle: 'block',
            scrollback: 1000,
            theme: {
                background: '#1a1a2e',
                foreground: '#e0e0e0',
                cursor: '#e0e0e0',
                cursorAccent: '#1a1a2e',
                selection: 'rgba(255, 255, 255, 0.3)',
                black: '#000000',
                red: '#e74c3c',
                green: '#2ecc71',
                yellow: '#f39c12',
                blue: '#3498db',
                magenta: '#9b59b6',
                cyan: '#1abc9c',
                white: '#ecf0f1',
                brightBlack: '#7f8c8d',
                brightRed: '#e74c3c',
                brightGreen: '#2ecc71',
                brightYellow: '#f39c12',
                brightBlue: '#3498db',
                brightMagenta: '#9b59b6',
                brightCyan: '#1abc9c',
                brightWhite: '#ffffff'
            },
            ...options
        };

        
        this.terminal = null;
        this.fitAddon = null;
        this.webLinksAddon = null;
        this.socket = null;
        this.connected = false;
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = 3;
        
        // Bind methods
        this._onSocketOpen = this._onSocketOpen.bind(this);
        this._onSocketMessage = this._onSocketMessage.bind(this);
        this._onSocketClose = this._onSocketClose.bind(this);
        this._onSocketError = this._onSocketError.bind(this);
        this._onTerminalData = this._onTerminalData.bind(this);
        this._onResize = this._onResize.bind(this);
        
        // Initialize terminal
        this._initTerminal();
        
        // Listen for window resize
        window.addEventListener('resize', this._onResize);
    }

    /**
     * Initialize the xterm.js terminal
     * @private
     */
    _initTerminal() {
        this.terminal = new Terminal({
            fontFamily: this.options.fontFamily,
            fontSize: this.options.fontSize,
            cursorBlink: this.options.cursorBlink,
            cursorStyle: this.options.cursorStyle,
            scrollback: this.options.scrollback,
            theme: this.options.theme,
            allowProposedApi: true
        });

        // Initialize addons
        this.fitAddon = new FitAddon();
        this.webLinksAddon = new WebLinksAddon();

        this.terminal.loadAddon(this.fitAddon);
        this.terminal.loadAddon(this.webLinksAddon);

        // Open terminal in container
        this.terminal.open(this.container);
        
        // Fit to container
        this.fit();

        // Handle terminal input
        this.terminal.onData(this._onTerminalData);
    }

    /**
     * Connect to SSH server via WebSocket
     * @param {string} wsUrl - WebSocket URL for SSH proxy
     * @param {Object} credentials - SSH credentials
     * @param {string} credentials.host - SSH host
     * @param {number} credentials.port - SSH port (default: 22)
     * @param {string} credentials.username - SSH username
     * @param {string} credentials.password - SSH password (optional)
     * @returns {Promise<void>}
     */
    connect(wsUrl, credentials = {}) {
        return new Promise((resolve, reject) => {
            if (this.socket) {
                this.disconnect();
            }

            try {
                this.credentials = credentials;
                this.socket = new WebSocket(wsUrl);
                this.socket.binaryType = 'arraybuffer';

                this.socket.onopen = (e) => {
                    this._onSocketOpen(e);
                    resolve();
                };
                
                this.socket.onmessage = this._onSocketMessage;
                this.socket.onclose = this._onSocketClose;
                this.socket.onerror = (e) => {
                    this._onSocketError(e);
                    if (!this.connected) {
                        reject(new Error('WebSocket connection failed'));
                    }
                };

            } catch (error) {
                reject(error);
            }
        });
    }


    /**
     * Disconnect from SSH server
     */
    disconnect() {
        if (this.socket) {
            this.socket.close();
            this.socket = null;
        }
        this.connected = false;
        this.reconnectAttempts = 0;
    }

    /**
     * Resize terminal to fit container
     */
    fit() {
        if (this.fitAddon && this.terminal) {
            try {
                this.fitAddon.fit();
            } catch (e) {
                // Ignore fit errors when terminal not visible
            }
        }
    }

    /**
     * Resize terminal to specific dimensions
     * @param {number} cols - Number of columns
     * @param {number} rows - Number of rows
     */
    resize(cols, rows) {
        if (this.terminal) {
            this.terminal.resize(cols, rows);
            
            // Send resize message to server
            if (this.socket && this.connected) {
                this._sendMessage({
                    type: 'resize',
                    cols: cols,
                    rows: rows
                });
            }
        }
    }

    /**
     * Send command to terminal
     * @param {string} cmd - Command to send
     */
    sendCommand(cmd) {
        if (this.socket && this.connected) {
            this._sendMessage({
                type: 'data',
                data: cmd
            });
        }
    }

    /**
     * Write text to terminal display (local only)
     * @param {string} text - Text to write
     */
    write(text) {
        if (this.terminal) {
            this.terminal.write(text);
        }
    }

    /**
     * Write line to terminal display (local only)
     * @param {string} text - Text to write
     */
    writeln(text) {
        if (this.terminal) {
            this.terminal.writeln(text);
        }
    }

    /**
     * Clear terminal screen
     */
    clear() {
        if (this.terminal) {
            this.terminal.clear();
        }
    }

    /**
     * Focus the terminal for keyboard input
     */
    focus() {
        if (this.terminal) {
            this.terminal.focus();
        }
    }

    /**
     * Remove focus from terminal
     */
    blur() {
        if (this.terminal) {
            this.terminal.blur();
        }
    }

    /**
     * Check if connected
     * @returns {boolean}
     */
    isConnected() {
        return this.connected;
    }

    /**
     * Get terminal dimensions
     * @returns {Object} { cols, rows }
     */
    getDimensions() {
        if (this.terminal) {
            return {
                cols: this.terminal.cols,
                rows: this.terminal.rows
            };
        }
        return { cols: 80, rows: 24 };
    }

    /**
     * Set font size
     * @param {number} size - Font size in pixels
     */
    setFontSize(size) {
        if (this.terminal) {
            this.terminal.options.fontSize = size;
            this.fit();
        }
    }

    /**
     * Clean up resources
     */
    destroy() {
        this.disconnect();
        window.removeEventListener('resize', this._onResize);
        
        if (this.terminal) {
            this.terminal.dispose();
            this.terminal = null;
        }
    }


    // Private methods

    /**
     * Send message to WebSocket server
     * @param {Object} message - Message object to send
     * @private
     */
    _sendMessage(message) {
        if (this.socket && this.socket.readyState === WebSocket.OPEN) {
            this.socket.send(JSON.stringify(message));
        }
    }

    /**
     * Handle WebSocket open event
     * @private
     */
    _onSocketOpen(event) {
        this.connected = true;
        this.reconnectAttempts = 0;
        
        // Send initial connection info with credentials
        if (this.credentials) {
            this._sendMessage({
                type: 'connect',
                host: this.credentials.host,
                port: this.credentials.port || 22,
                username: this.credentials.username,
                password: this.credentials.password
            });
        }

        // Send terminal size
        const dims = this.getDimensions();
        this._sendMessage({
            type: 'resize',
            cols: dims.cols,
            rows: dims.rows
        });

        if (typeof this.options.onConnect === 'function') {
            this.options.onConnect(event);
        }
    }

    /**
     * Handle WebSocket message event
     * @private
     */
    _onSocketMessage(event) {
        try {
            // Handle binary data
            if (event.data instanceof ArrayBuffer) {
                const text = new TextDecoder().decode(event.data);
                this.terminal.write(text);
                return;
            }

            // Handle text/JSON data
            const data = typeof event.data === 'string' ? event.data : '';
            
            // Try to parse as JSON
            try {
                const message = JSON.parse(data);
                
                if (message.type === 'data') {
                    this.terminal.write(message.data);
                } else if (message.type === 'error') {
                    this.terminal.writeln(`\r\n\x1b[31mError: ${message.message}\x1b[0m`);
                    if (typeof this.options.onError === 'function') {
                        this.options.onError(new Error(message.message));
                    }
                } else if (message.type === 'connected') {
                    this.terminal.writeln('\x1b[32mConnected to SSH server\x1b[0m\r\n');
                }
            } catch (e) {
                // Not JSON, write directly to terminal
                this.terminal.write(data);
            }

            if (typeof this.options.onData === 'function') {
                this.options.onData(event.data);
            }
        } catch (error) {
            console.error('Error processing message:', error);
        }
    }

    /**
     * Handle WebSocket close event
     * @private
     */
    _onSocketClose(event) {
        const wasConnected = this.connected;
        this.connected = false;
        
        if (wasConnected) {
            this.terminal.writeln('\r\n\x1b[33mConnection closed\x1b[0m');
        }

        if (typeof this.options.onDisconnect === 'function') {
            this.options.onDisconnect({
                code: event.code,
                reason: event.reason,
                wasConnected
            });
        }
    }

    /**
     * Handle WebSocket error event
     * @private
     */
    _onSocketError(event) {
        console.error('WebSocket error:', event);
        
        if (typeof this.options.onError === 'function') {
            this.options.onError(event);
        }
    }

    /**
     * Handle terminal data input
     * @private
     */
    _onTerminalData(data) {
        if (this.socket && this.connected) {
            this._sendMessage({
                type: 'data',
                data: data
            });
        }
    }

    /**
     * Handle window resize event
     * @private
     */
    _onResize() {
        this.fit();
        
        // Send new dimensions to server
        if (this.socket && this.connected) {
            const dims = this.getDimensions();
            this._sendMessage({
                type: 'resize',
                cols: dims.cols,
                rows: dims.rows
            });
        }
    }
}
