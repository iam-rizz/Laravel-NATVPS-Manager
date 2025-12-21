/**
 * VNC Client Module
 * 
 * Provides VNC console access using noVNC library.
 * Implements connect, disconnect, sendCtrlAltDel methods.
 * Handles scaling and fullscreen functionality.
 * 
 * Requirements: 1.2, 4.1, 4.3, 5.1, 5.2
 * 
 * Note: noVNC is loaded at runtime via dynamic import because the npm package
 * uses top-level await which is not compatible with Vite/Rollup bundlers.
 * The noVNC library files are served from /vendor/novnc/
 */

// RFB class will be loaded dynamically
let RFB = null;
let rfbLoadPromise = null;

/**
 * Load noVNC RFB class dynamically using native ES module import
 * @returns {Promise<Function>} RFB constructor
 */
async function loadRFB() {
    if (RFB) {
        return RFB;
    }
    
    if (rfbLoadPromise) {
        return rfbLoadPromise;
    }
    
    rfbLoadPromise = (async () => {
        // Check if already loaded
        if (window.RFB) {
            RFB = window.RFB;
            return RFB;
        }
        
        try {
            // Use dynamic import to load noVNC RFB module
            // This works because modern browsers support dynamic import()
            // Add cache-busting timestamp to avoid stale cache issues
            const module = await import('/vendor/novnc/rfb.js?v=' + Date.now());
            RFB = module.default;
            window.RFB = RFB; // Cache globally
            return RFB;
        } catch (error) {
            console.error('Failed to load noVNC:', error);
            throw new Error('Failed to load VNC client library: ' + error.message);
        }
    })();
    
    return rfbLoadPromise;
}

export default class VncClient {
    /**
     * Create a VNC client instance
     * @param {HTMLElement} container - The container element for the VNC display
     * @param {Object} options - Configuration options
     * @param {string} options.background - Background color (default: '#1a1a2e')
     * @param {boolean} options.scaleViewport - Scale to fit container (default: true)
     * @param {boolean} options.clipViewport - Clip to container (default: false)
     * @param {boolean} options.resizeSession - Resize remote session (default: false)
     * @param {Function} options.onConnect - Callback when connected
     * @param {Function} options.onDisconnect - Callback when disconnected
     * @param {Function} options.onCredentialsRequired - Callback when credentials needed
     * @param {Function} options.onSecurityFailure - Callback on security failure
     * @param {Function} options.onDesktopName - Callback when desktop name changes
     */
    constructor(container, options = {}) {
        this.container = container;
        this.options = {
            background: '#1a1a2e',
            scaleViewport: true,
            clipViewport: false,
            resizeSession: false,
            viewOnly: false,
            qualityLevel: 6,
            compressionLevel: 2,
            ...options
        };
        
        this.rfb = null;
        this.connected = false;
        this.scalingMode = 'fit'; // 'fit' or 'actual'
        this.isFullscreen = false;
        
        // Bind methods
        this._onConnect = this._onConnect.bind(this);
        this._onDisconnect = this._onDisconnect.bind(this);
        this._onCredentialsRequired = this._onCredentialsRequired.bind(this);
        this._onSecurityFailure = this._onSecurityFailure.bind(this);
        this._onDesktopName = this._onDesktopName.bind(this);
        this._onFullscreenChange = this._onFullscreenChange.bind(this);
        
        // Listen for fullscreen changes
        document.addEventListener('fullscreenchange', this._onFullscreenChange);
    }


    /**
     * Connect to VNC server
     * @param {string} url - WebSocket URL (wss://host:port/path)
     * @param {string} password - VNC password
     * @returns {Promise<void>}
     */
    async connect(url, password = null) {
        if (this.rfb) {
            this.disconnect();
        }

        try {
            // Load RFB dynamically to handle top-level await
            const RFBClass = await loadRFB();
            
            const credentials = password ? { password } : {};
            
            return new Promise((resolve, reject) => {
                this.rfb = new RFBClass(this.container, url, {
                    credentials,
                    shared: true,
                    wsProtocols: ['binary']
                });

                // Configure RFB properties
                this.rfb.background = this.options.background;
                this.rfb.scaleViewport = this.options.scaleViewport;
                this.rfb.clipViewport = this.options.clipViewport;
                this.rfb.resizeSession = this.options.resizeSession;
                this.rfb.viewOnly = this.options.viewOnly;
                this.rfb.qualityLevel = this.options.qualityLevel;
                this.rfb.compressionLevel = this.options.compressionLevel;
                this.rfb.focusOnClick = true;
                this.rfb.showDotCursor = true;

                // Set up event listeners
                this.rfb.addEventListener('connect', (e) => {
                    this._onConnect(e);
                    resolve();
                });
                
                this.rfb.addEventListener('disconnect', (e) => {
                    this._onDisconnect(e);
                    if (!this.connected) {
                        reject(new Error('Connection failed'));
                    }
                });
                
                this.rfb.addEventListener('credentialsrequired', this._onCredentialsRequired);
                this.rfb.addEventListener('securityfailure', this._onSecurityFailure);
                this.rfb.addEventListener('desktopname', this._onDesktopName);
            });

        } catch (error) {
            throw error;
        }
    }

    /**
     * Disconnect from VNC server
     */
    disconnect() {
        if (this.rfb) {
            this.rfb.disconnect();
            this.rfb = null;
        }
        this.connected = false;
    }

    /**
     * Send Ctrl+Alt+Del key combination
     */
    sendCtrlAltDel() {
        if (this.rfb && this.connected) {
            this.rfb.sendCtrlAltDel();
        }
    }

    /**
     * Send a specific key combination
     * @param {number} keysym - The keysym to send
     * @param {string} code - The key code
     * @param {boolean} down - Whether key is pressed (true) or released (false)
     */
    sendKey(keysym, code, down = null) {
        if (this.rfb && this.connected) {
            this.rfb.sendKey(keysym, code, down);
        }
    }

    /**
     * Set scaling mode
     * @param {string} mode - 'fit' to scale to container, 'actual' for 1:1 size
     */
    setScaling(mode) {
        this.scalingMode = mode;
        
        if (this.rfb) {
            if (mode === 'fit') {
                this.rfb.scaleViewport = true;
                this.rfb.clipViewport = false;
            } else if (mode === 'actual') {
                this.rfb.scaleViewport = false;
                this.rfb.clipViewport = true;
            }
        }
    }


    /**
     * Toggle fullscreen mode
     * @returns {Promise<void>}
     */
    async toggleFullscreen() {
        if (!this.container) return;

        try {
            if (!document.fullscreenElement) {
                await this.container.requestFullscreen();
                this.isFullscreen = true;
            } else {
                await document.exitFullscreen();
                this.isFullscreen = false;
            }
        } catch (error) {
            console.error('Fullscreen error:', error);
        }
    }

    /**
     * Focus the VNC canvas for keyboard input
     */
    focus() {
        if (this.rfb) {
            this.rfb.focus();
        }
    }

    /**
     * Remove focus from VNC canvas
     */
    blur() {
        if (this.rfb) {
            this.rfb.blur();
        }
    }

    /**
     * Send clipboard text to remote server
     * This uses clipboardPasteFrom which syncs to VNC clipboard
     * @param {string} text - Text to paste
     */
    clipboardPaste(text) {
        console.log('VncClient.clipboardPaste called with:', text);
        if (this.rfb && this.connected) {
            console.log('RFB exists, calling clipboardPasteFrom');
            if (typeof this.rfb.clipboardPasteFrom === 'function') {
                this.rfb.clipboardPasteFrom(text);
            } else {
                console.error('clipboardPasteFrom is not a function on RFB');
            }
        } else {
            console.log('RFB not connected, rfb:', this.rfb, 'connected:', this.connected);
        }
    }

    /**
     * Type text directly by simulating keyboard input
     * This sends each character as a key press
     * @param {string} text - Text to type
     */
    typeText(text) {
        if (!this.rfb || !this.connected) {
            console.log('Cannot type: not connected');
            return;
        }

        console.log('Typing text:', text);
        
        // Send each character as a key event
        for (let i = 0; i < text.length; i++) {
            const char = text[i];
            const charCode = char.charCodeAt(0);
            
            // Handle special characters
            let keysym;
            if (char === '\n' || char === '\r') {
                keysym = 0xFF0D; // Return/Enter
            } else if (char === '\t') {
                keysym = 0xFF09; // Tab
            } else if (charCode >= 32 && charCode <= 126) {
                // Printable ASCII - keysym is same as char code for basic ASCII
                keysym = charCode;
            } else {
                // For other characters, try Unicode keysym
                keysym = charCode;
            }
            
            // Send key down and up
            this.rfb.sendKey(keysym, null, true);
            this.rfb.sendKey(keysym, null, false);
        }
    }

    /**
     * Set quality level
     * @param {number} level - Quality level 0-9
     */
    setQualityLevel(level) {
        console.log('VncClient.setQualityLevel called with:', level);
        if (this.rfb) {
            this.rfb.qualityLevel = parseInt(level);
            console.log('Quality level set to:', this.rfb.qualityLevel);
        } else {
            console.log('RFB not available');
        }
    }

    /**
     * Set compression level
     * @param {number} level - Compression level 0-9
     */
    setCompressionLevel(level) {
        console.log('VncClient.setCompressionLevel called with:', level);
        if (this.rfb) {
            this.rfb.compressionLevel = parseInt(level);
            console.log('Compression level set to:', this.rfb.compressionLevel);
        } else {
            console.log('RFB not available');
        }
    }

    /**
     * Get the RFB instance
     * @returns {Object|null} RFB instance or null
     */
    getRfb() {
        return this.rfb;
    }

    /**
     * Get current screen as data URL
     * @param {string} type - MIME type (default: 'image/png')
     * @param {number} quality - Quality 0-1 for JPEG
     * @returns {string} Data URL of current screen
     */
    getScreenshot(type = 'image/png', quality = 0.92) {
        if (this.rfb && this.connected) {
            return this.rfb.toDataURL(type, quality);
        }
        return null;
    }

    /**
     * Check if connected
     * @returns {boolean}
     */
    isConnected() {
        return this.connected;
    }

    /**
     * Get current scaling mode
     * @returns {string} 'fit' or 'actual'
     */
    getScalingMode() {
        return this.scalingMode;
    }

    /**
     * Check if in fullscreen mode
     * @returns {boolean}
     */
    isInFullscreen() {
        return this.isFullscreen;
    }

    /**
     * Clean up resources
     */
    destroy() {
        this.disconnect();
        document.removeEventListener('fullscreenchange', this._onFullscreenChange);
    }

    // Private event handlers

    _onConnect(event) {
        this.connected = true;
        if (typeof this.options.onConnect === 'function') {
            this.options.onConnect(event);
        }
    }

    _onDisconnect(event) {
        const wasConnected = this.connected;
        this.connected = false;
        this.rfb = null;
        
        if (typeof this.options.onDisconnect === 'function') {
            this.options.onDisconnect({
                clean: event.detail.clean,
                wasConnected
            });
        }
    }

    _onCredentialsRequired(event) {
        if (typeof this.options.onCredentialsRequired === 'function') {
            this.options.onCredentialsRequired(event.detail.types);
        }
    }

    _onSecurityFailure(event) {
        if (typeof this.options.onSecurityFailure === 'function') {
            this.options.onSecurityFailure({
                status: event.detail.status,
                reason: event.detail.reason
            });
        }
    }

    _onDesktopName(event) {
        if (typeof this.options.onDesktopName === 'function') {
            this.options.onDesktopName(event.detail.name);
        }
    }

    _onFullscreenChange() {
        this.isFullscreen = !!document.fullscreenElement;
        
        // Trigger resize after fullscreen change
        if (this.rfb) {
            setTimeout(() => {
                // Force a redraw by toggling scale
                const currentScale = this.rfb.scaleViewport;
                this.rfb.scaleViewport = !currentScale;
                this.rfb.scaleViewport = currentScale;
            }, 100);
        }
    }
}
