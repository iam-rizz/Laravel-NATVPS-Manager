/**
 * Theme Manager
 * Handles dark/light mode switching with localStorage persistence
 */

const ThemeManager = {
    STORAGE_KEY: 'theme',
    DARK_CLASS: 'dark',

    /**
     * Initialize theme manager
     */
    init() {
        // Apply theme immediately to prevent flash
        this.applyTheme(this.getTheme());
        
        // Bind toggle buttons after DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.bindToggle());
        } else {
            this.bindToggle();
        }

        // Listen for system theme changes
        this.watchSystemTheme();
    },

    /**
     * Get current theme from localStorage or system preference
     * @returns {string} 'dark' or 'light'
     */
    getTheme() {
        const stored = localStorage.getItem(this.STORAGE_KEY);
        if (stored) {
            return stored;
        }
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    },

    /**
     * Set and persist theme
     * @param {string} theme - 'dark' or 'light'
     */
    setTheme(theme) {
        localStorage.setItem(this.STORAGE_KEY, theme);
        this.applyTheme(theme);
    },

    /**
     * Apply theme to document
     * @param {string} theme - 'dark' or 'light'
     */
    applyTheme(theme) {
        const isDark = theme === 'dark';
        document.documentElement.classList.toggle(this.DARK_CLASS, isDark);
        this.updateToggleIcons(theme);
        
        // Update meta theme-color for mobile browsers
        const metaThemeColor = document.querySelector('meta[name="theme-color"]');
        if (metaThemeColor) {
            metaThemeColor.setAttribute('content', isDark ? '#0f172a' : '#ffffff');
        }
    },

    /**
     * Toggle between dark and light mode
     */
    toggle() {
        const current = this.getTheme();
        this.setTheme(current === 'dark' ? 'light' : 'dark');
    },

    /**
     * Bind click handlers to theme toggle buttons
     */
    bindToggle() {
        document.querySelectorAll('[data-theme-toggle]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggle();
            });
        });
    },

    /**
     * Update visibility of sun/moon icons based on current theme
     * @param {string} theme - 'dark' or 'light'
     */
    updateToggleIcons(theme) {
        const isDark = theme === 'dark';
        
        document.querySelectorAll('[data-theme-icon="sun"]').forEach(icon => {
            icon.classList.toggle('hidden', !isDark);
        });
        
        document.querySelectorAll('[data-theme-icon="moon"]').forEach(icon => {
            icon.classList.toggle('hidden', isDark);
        });
    },

    /**
     * Watch for system theme preference changes
     */
    watchSystemTheme() {
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        
        mediaQuery.addEventListener('change', (e) => {
            // Only update if user hasn't set a preference
            if (!localStorage.getItem(this.STORAGE_KEY)) {
                this.applyTheme(e.matches ? 'dark' : 'light');
            }
        });
    },

    /**
     * Clear stored preference and use system default
     */
    useSystemTheme() {
        localStorage.removeItem(this.STORAGE_KEY);
        const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        this.applyTheme(systemTheme);
    }
};

export default ThemeManager;
