/**
 * Console Manager Module
 * 
 * Manages VNC and SSH console connections.
 * Handles tab switching between VNC and SSH.
 * Manages connection lifecycle.
 * 
 * Requirements: 1.1, 1.5
 */

import VncClient from './vnc-client.js';
import SshClient from './ssh-client.js';

export default class ConsoleManager {
    /**
     * Create a console manager instance
     * @param {Object} options - Configuration options
     * @param {HTMLElement} options.vncContainer - Container for VNC display
     * @param {HTMLElement} options.sshContainer - Container for SSH terminal
     * @param {HTMLElement} options.tabContainer - Container for tab buttons
     * @param {string} options.vpsId - VPS identifier
     * @param {string} options.csrfToken - CSRF token for API requests
     * @param {string} options.baseUrl - Base URL for API endpoints
     * @param {Function} options.onStatusChange - Callback for status changes
     * @param {Function} options.onError - Callback for errors
     */
    constructor(options = {}) {
        this.options = {
            baseUrl: '',
            ...options
        };

        this.vncContainer = options.vncContainer;
        this.sshContainer = options.sshContainer;
        this.tabContainer = options.tabContainer;
        this.vpsId = options.vpsId;
        this.csrfToken = options.csrfToken;

        this.vncClient = null;
        this.sshClient = null;
        this.activeTab = null;
        this.vncDetails = null;
        this.sshDetails = null;

        // Bind methods
        this._onVncConnect = this._onVncConnect.bind(this);
        this._onVncDisconnect = this._onVncDisconnect.bind(this);
        this._onSshConnect = this._onSshConnect.bind(this);
        this._onSshDisconnect = this._onSshDisconnect.bind(this);
    }


    /**
     * Initialize the console manager
     * Sets up tab event listeners and prepares clients
     */
    async init() {
        // Set up tab click handlers
        if (this.tabContainer) {
            const tabs = this.tabContainer.querySelectorAll('[data-console-tab]');
            tabs.forEach(tab => {
                tab.addEventListener('click', (e) => {
                    e.preventDefault();
                    const tabType = tab.dataset.consoleTab;
                    this.switchTab(tabType);
                });
            });
        }

        // Initialize VNC client if container exists
        if (this.vncContainer) {
            this.vncClient = new VncClient(this.vncContainer, {
                onConnect: this._onVncConnect,
                onDisconnect: this._onVncDisconnect,
                onCredentialsRequired: (types) => {
                    this._updateStatus('vnc', 'credentials_required', 'Credentials required');
                },
                onSecurityFailure: (details) => {
                    this._updateStatus('vnc', 'error', `Security failure: ${details.reason || 'Unknown'}`);
                }
            });
        }

        // Initialize SSH client if container exists
        if (this.sshContainer) {
            this.sshClient = new SshClient(this.sshContainer, {
                onConnect: this._onSshConnect,
                onDisconnect: this._onSshDisconnect,
                onError: (error) => {
                    this._updateStatus('ssh', 'error', error.message || 'Connection error');
                }
            });
        }
    }

    /**
     * Switch between VNC and SSH tabs
     * @param {string} tabType - 'vnc' or 'ssh'
     */
    async switchTab(tabType) {
        if (this.activeTab === tabType) return;

        // Update tab UI
        this._updateTabUI(tabType);

        // Show/hide containers
        if (this.vncContainer) {
            this.vncContainer.style.display = tabType === 'vnc' ? 'block' : 'none';
        }
        if (this.sshContainer) {
            this.sshContainer.style.display = tabType === 'ssh' ? 'block' : 'none';
        }

        this.activeTab = tabType;

        // Connect to the selected console type if not already connected
        if (tabType === 'vnc' && this.vncClient && !this.vncClient.isConnected()) {
            await this.connectVnc();
        } else if (tabType === 'ssh' && this.sshClient && !this.sshClient.isConnected()) {
            await this.connectSsh();
        }

        // Focus the active client
        if (tabType === 'vnc' && this.vncClient) {
            this.vncClient.focus();
        } else if (tabType === 'ssh' && this.sshClient) {
            this.sshClient.focus();
            this.sshClient.fit();
        }
    }

    /**
     * Connect to VNC console
     * @returns {Promise<void>}
     */
    async connectVnc() {
        if (!this.vncClient) {
            throw new Error('VNC client not initialized');
        }

        this._updateStatus('vnc', 'connecting', 'Connecting to VNC...');

        try {
            // Fetch VNC details from server
            const details = await this._fetchVncDetails();
            this.vncDetails = details;

            if (!details.success) {
                throw new Error(details.error || 'Failed to get VNC details');
            }

            // Connect to VNC server
            await this.vncClient.connect(
                details.data.websocket_url,
                details.data.password
            );

        } catch (error) {
            this._updateStatus('vnc', 'error', error.message);
            this._handleError('vnc', error);
            throw error;
        }
    }


    /**
     * Connect to SSH terminal
     * @returns {Promise<void>}
     */
    async connectSsh() {
        if (!this.sshClient) {
            throw new Error('SSH client not initialized');
        }

        this._updateStatus('ssh', 'connecting', 'Connecting to SSH...');

        try {
            // Fetch SSH details from server (includes password from database)
            const details = await this._fetchSshDetails();
            this.sshDetails = details;

            if (!details.success) {
                throw new Error(details.error || 'Failed to get SSH details');
            }

            // Connect to SSH server via WebSocket proxy
            // Credentials from database: host (server IP), port (custom), username, password
            await this.sshClient.connect(
                details.data.websocket_url,
                {
                    host: details.data.host,         // From server.ip_address
                    port: details.data.port,         // From nat_vps.ssh_port (custom port)
                    username: details.data.username, // From nat_vps.ssh_username
                    password: details.data.password  // From nat_vps.ssh_password
                }
            );

        } catch (error) {
            this._updateStatus('ssh', 'error', error.message);
            this._handleError('ssh', error);
            throw error;
        }
    }

    /**
     * Disconnect VNC console
     */
    disconnectVnc() {
        if (this.vncClient) {
            this.vncClient.disconnect();
            this._updateStatus('vnc', 'disconnected', 'Disconnected');
        }
    }

    /**
     * Disconnect SSH terminal
     */
    disconnectSsh() {
        if (this.sshClient) {
            this.sshClient.disconnect();
            this._updateStatus('ssh', 'disconnected', 'Disconnected');
        }
    }

    /**
     * Disconnect all consoles
     */
    disconnectAll() {
        this.disconnectVnc();
        this.disconnectSsh();
    }

    /**
     * Send Ctrl+Alt+Del to VNC
     */
    sendCtrlAltDel() {
        if (this.vncClient && this.vncClient.isConnected()) {
            this.vncClient.sendCtrlAltDel();
        }
    }

    /**
     * Send a specific key to VNC
     * @param {number} keysym - X11 keysym
     * @param {string} code - Key code
     * @param {boolean} down - Key down state (optional)
     */
    sendKey(keysym, code, down) {
        if (this.vncClient && this.vncClient.isConnected()) {
            this.vncClient.sendKey(keysym, code, down);
        }
    }

    /**
     * Get the RFB instance for direct access
     * @returns {Object|null} RFB instance
     */
    getRfb() {
        if (this.vncClient) {
            return this.vncClient.getRfb();
        }
        return null;
    }

    /**
     * Set VNC quality level
     * @param {number} level - Quality level 0-9
     */
    setVncQuality(level) {
        if (this.vncClient) {
            this.vncClient.setQualityLevel(level);
        }
    }

    /**
     * Set VNC compression level
     * @param {number} level - Compression level 0-9
     */
    setVncCompression(level) {
        if (this.vncClient) {
            this.vncClient.setCompressionLevel(level);
        }
    }

    /**
     * Send clipboard text to VNC (syncs to VNC clipboard)
     * @param {string} text - Text to send
     */
    sendClipboard(text) {
        if (this.vncClient && this.vncClient.isConnected()) {
            this.vncClient.clipboardPaste(text);
        }
    }

    /**
     * Type text directly to VNC by simulating keyboard
     * @param {string} text - Text to type
     */
    typeText(text) {
        if (this.vncClient && this.vncClient.isConnected()) {
            this.vncClient.typeText(text);
        }
    }

    /**
     * Set VNC scaling mode
     * @param {string} mode - 'fit' or 'actual'
     */
    setVncScaling(mode) {
        if (this.vncClient) {
            this.vncClient.setScaling(mode);
        }
    }

    /**
     * Toggle fullscreen for active console
     */
    async toggleFullscreen() {
        if (this.activeTab === 'vnc' && this.vncClient) {
            await this.vncClient.toggleFullscreen();
        } else if (this.activeTab === 'ssh' && this.sshContainer) {
            try {
                if (!document.fullscreenElement) {
                    await this.sshContainer.requestFullscreen();
                } else {
                    await document.exitFullscreen();
                }
                // Refit terminal after fullscreen change
                setTimeout(() => {
                    if (this.sshClient) {
                        this.sshClient.fit();
                    }
                }, 100);
            } catch (error) {
                console.error('Fullscreen error:', error);
            }
        }
    }

    /**
     * Get VNC screenshot
     * @returns {string|null} Data URL of screenshot
     */
    getVncScreenshot() {
        if (this.vncClient && this.vncClient.isConnected()) {
            return this.vncClient.getScreenshot();
        }
        return null;
    }

    /**
     * Check if VNC is connected
     * @returns {boolean}
     */
    isVncConnected() {
        return this.vncClient ? this.vncClient.isConnected() : false;
    }

    /**
     * Check if SSH is connected
     * @returns {boolean}
     */
    isSshConnected() {
        return this.sshClient ? this.sshClient.isConnected() : false;
    }

    /**
     * Get active tab type
     * @returns {string|null} 'vnc', 'ssh', or null
     */
    getActiveTab() {
        return this.activeTab;
    }

    /**
     * Clean up all resources
     */
    destroy() {
        this.disconnectAll();
        
        if (this.vncClient) {
            this.vncClient.destroy();
            this.vncClient = null;
        }
        
        if (this.sshClient) {
            this.sshClient.destroy();
            this.sshClient = null;
        }
    }


    // Private methods

    /**
     * Fetch VNC details from server
     * @returns {Promise<Object>}
     * @private
     */
    async _fetchVncDetails() {
        const url = `${this.options.baseUrl}/console/${this.vpsId}/vnc`;
        
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': this.csrfToken
            },
            credentials: 'same-origin'
        });

        if (!response.ok) {
            const error = await response.json().catch(() => ({}));
            throw new Error(error.message || `HTTP ${response.status}`);
        }

        return response.json();
    }

    /**
     * Fetch SSH details from server
     * @returns {Promise<Object>}
     * @private
     */
    async _fetchSshDetails() {
        const url = `${this.options.baseUrl}/console/${this.vpsId}/ssh`;
        
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': this.csrfToken
            },
            credentials: 'same-origin'
        });

        if (!response.ok) {
            const error = await response.json().catch(() => ({}));
            throw new Error(error.message || `HTTP ${response.status}`);
        }

        return response.json();
    }

    /**
     * Update tab UI to show active state
     * @param {string} activeTab - 'vnc' or 'ssh'
     * @private
     */
    _updateTabUI(activeTab) {
        if (!this.tabContainer) return;

        const tabs = this.tabContainer.querySelectorAll('[data-console-tab]');
        tabs.forEach(tab => {
            const isActive = tab.dataset.consoleTab === activeTab;
            tab.classList.toggle('active', isActive);
            tab.classList.toggle('bg-primary', isActive);
            tab.classList.toggle('text-white', isActive);
            tab.classList.toggle('bg-gray-200', !isActive);
            tab.classList.toggle('dark:bg-gray-700', !isActive);
        });
    }

    /**
     * Update connection status
     * @param {string} type - 'vnc' or 'ssh'
     * @param {string} status - Status code
     * @param {string} message - Status message
     * @private
     */
    _updateStatus(type, status, message) {
        if (typeof this.options.onStatusChange === 'function') {
            this.options.onStatusChange({
                type,
                status,
                message
            });
        }
    }

    /**
     * Handle connection error
     * @param {string} type - 'vnc' or 'ssh'
     * @param {Error} error - Error object
     * @private
     */
    _handleError(type, error) {
        console.error(`${type.toUpperCase()} error:`, error);
        
        if (typeof this.options.onError === 'function') {
            this.options.onError({
                type,
                error
            });
        }
    }

    /**
     * VNC connect callback
     * @private
     */
    _onVncConnect() {
        this._updateStatus('vnc', 'connected', 'Connected to VNC');
    }

    /**
     * VNC disconnect callback
     * @param {Object} details - Disconnect details
     * @private
     */
    _onVncDisconnect(details) {
        const message = details.clean ? 'Disconnected' : 'Connection lost';
        this._updateStatus('vnc', 'disconnected', message);
    }

    /**
     * SSH connect callback
     * @private
     */
    _onSshConnect() {
        this._updateStatus('ssh', 'connected', 'Connected to SSH');
    }

    /**
     * SSH disconnect callback
     * @param {Object} details - Disconnect details
     * @private
     */
    _onSshDisconnect(details) {
        const message = details.wasConnected ? 'Disconnected' : 'Connection failed';
        this._updateStatus('ssh', 'disconnected', message);
    }
}

// Export for use in Blade templates via window
if (typeof window !== 'undefined') {
    window.ConsoleManager = ConsoleManager;
}
