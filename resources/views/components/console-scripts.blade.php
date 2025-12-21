{{-- Console Scripts Component --}}
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('consoleEmbed', (vpsId, csrfToken) => ({
        activeTab: 'vnc',
        connectionStatus: 'idle',
        statusMessage: '',
        scalingMode: 'fit',
        zoomLevel: 1,
        qualityLevel: 6,
        compressionLevel: 2,
        consoleManager: null,

        async init() {
            await this.$nextTick();

            if (window.ConsoleManager) {
                this.consoleManager = new window.ConsoleManager({
                    vncContainer: this.$refs.vncContainer,
                    sshContainer: this.$refs.sshContainer,
                    tabContainer: this.$refs.tabContainer,
                    vpsId: vpsId,
                    csrfToken: csrfToken,
                    baseUrl: '',
                    onStatusChange: (status) => {
                        this.connectionStatus = status.status;
                        this.statusMessage = status.message;
                    },
                    onError: (error) => {
                        this.connectionStatus = 'error';
                        this.statusMessage = error.error?.message || 'Connection error';
                    }
                });

                await this.consoleManager.init();
                this.switchTab('vnc');
            }
        },

        async switchTab(tab) {
            this.activeTab = tab;
            if (this.consoleManager) {
                await this.consoleManager.switchTab(tab);
            }
        },

        sendCtrlAltDel() {
            if (this.consoleManager) {
                this.consoleManager.sendCtrlAltDel();
            }
        },

        sendKey(key) {
            if (!this.consoleManager) return;
            
            const rfb = this.consoleManager.getRfb();
            if (!rfb) return;
            
            const KeyTable = {
                Tab: 0xFF09,
                Escape: 0xFF1B,
                Control_L: 0xFFE3,
                c: 0x0063,
                v: 0x0076
            };
            
            switch(key) {
                case 'Tab':
                    rfb.sendKey(KeyTable.Tab, 'Tab');
                    break;
                case 'Escape':
                    rfb.sendKey(KeyTable.Escape, 'Escape');
                    break;
                case 'CtrlC':
                    rfb.sendKey(KeyTable.Control_L, 'ControlLeft', true);
                    rfb.sendKey(KeyTable.c, 'KeyC', true);
                    rfb.sendKey(KeyTable.c, 'KeyC', false);
                    rfb.sendKey(KeyTable.Control_L, 'ControlLeft', false);
                    break;
                case 'CtrlV':
                    rfb.sendKey(KeyTable.Control_L, 'ControlLeft', true);
                    rfb.sendKey(KeyTable.v, 'KeyV', true);
                    rfb.sendKey(KeyTable.v, 'KeyV', false);
                    rfb.sendKey(KeyTable.Control_L, 'ControlLeft', false);
                    break;
            }
        },

        setScaling(mode) {
            this.scalingMode = mode;
            if (mode === 'fit') {
                this.zoomLevel = 1;
                this.applyZoom();
            }
            if (this.consoleManager) {
                this.consoleManager.setVncScaling(mode);
            }
        },

        applyZoom() {
            if (this.$refs.vncContainer) {
                const canvas = this.$refs.vncContainer.querySelector('canvas');
                if (canvas) {
                    canvas.style.transform = `scale(${this.zoomLevel})`;
                    canvas.style.transformOrigin = 'top left';
                }
            }
        },

        typeText(text) {
            if (this.consoleManager && text) {
                this.consoleManager.typeText(text);
            }
        },

        takeScreenshot() {
            if (this.consoleManager) {
                const dataUrl = this.consoleManager.getVncScreenshot();
                if (dataUrl) {
                    const link = document.createElement('a');
                    link.download = `console-${vpsId}-${Date.now()}.png`;
                    link.href = dataUrl;
                    link.click();
                }
            }
        },

        async reconnect() {
            this.connectionStatus = 'connecting';
            this.statusMessage = 'Reconnecting...';
            
            if (this.consoleManager) {
                if (this.activeTab === 'vnc') {
                    await this.consoleManager.connectVnc();
                } else {
                    await this.consoleManager.connectSsh();
                }
            }
        },

        destroy() {
            if (this.consoleManager) {
                this.consoleManager.destroy();
            }
        }
    }));
});
</script>
