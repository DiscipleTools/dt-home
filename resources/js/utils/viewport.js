/**
 * Viewport height utilities for cross-browser compatibility
 * Handles dynamic viewport height changes across Safari, Chrome, and Firefox
 */

class ViewportManager {
    constructor() {
        this.isInitialized = false;
        this.resizeHandler = null;
        this.orientationHandler = null;
        this.cleanupFunctions = [];
    }

    /**
     * Initialize viewport height management
     */
    init() {
        if (this.isInitialized) return;
        
        // Wait for DOM to be fully ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                setTimeout(() => this.setViewportHeight(), 100);
            });
        } else {
            setTimeout(() => this.setViewportHeight(), 100);
        }
        
        this.setupEventListeners();
        this.isInitialized = true;
    }

    /**
     * Set the CSS custom property for viewport height
     */
    setViewportHeight() {
        const vh = window.innerHeight * 0.01;
        document.documentElement.style.setProperty('--vh', `${vh}px`);
        
        // Calculate the optimal height for sp-theme element
        const optimalHeight = this.calculateOptimalHeight();
        
        // Apply dynamic height to sp-theme element
        const themeElement = document.querySelector('sp-theme');
        if (themeElement) {
            themeElement.style.height = `${optimalHeight}px`;
            themeElement.style.minHeight = `${optimalHeight}px`;
            
            // Also set max-height to ensure it doesn't get constrained
            themeElement.style.maxHeight = 'none';
            
            // Ensure the background color is applied to the sp-theme element
            this.applyThemeBackground(themeElement);
        }
        
        // Apply to html and body - use min-height to allow content to extend beyond viewport
        document.documentElement.style.minHeight = `${window.innerHeight}px`;
        document.body.style.minHeight = `${window.innerHeight}px`;
        
        // Force a reflow to ensure the changes take effect
        if (themeElement) {
            themeElement.offsetHeight; // Trigger reflow
        }
    }

    /**
     * Setup event listeners for viewport changes
     */
    setupEventListeners() {
        // Throttled resize handler
        this.resizeHandler = this.throttle(() => {
            this.setViewportHeight();
        }, 16); // ~60fps

        // Orientation change handler
        this.orientationHandler = () => {
            // Small delay to ensure orientation change is complete
            setTimeout(() => {
                this.setViewportHeight();
            }, 100);
        };

        // Add event listeners
        window.addEventListener('resize', this.resizeHandler);
        window.addEventListener('orientationchange', this.orientationHandler);
        
        // Safari-specific events
        if (this.isSafari()) {
            document.addEventListener('visibilitychange', this.orientationHandler);
        }

        // Store cleanup functions
        this.cleanupFunctions = [
            () => window.removeEventListener('resize', this.resizeHandler),
            () => window.removeEventListener('orientationchange', this.orientationHandler)
        ];

        if (this.isSafari()) {
            this.cleanupFunctions.push(
                () => document.removeEventListener('visibilitychange', this.orientationHandler)
            );
        }
    }

    /**
     * Detect Safari browser
     */
    isSafari() {
        return /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
    }

    /**
     * Throttle function to limit execution frequency
     */
    throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }

    /**
     * Cleanup event listeners
     */
    destroy() {
        this.cleanupFunctions.forEach(cleanup => cleanup());
        this.cleanupFunctions = [];
        this.isInitialized = false;
    }

    /**
     * Calculate the optimal height for sp-theme element
     * Returns the larger of screen height or content height
     */
    calculateOptimalHeight() {
        const screenHeight = window.innerHeight;
        
        // Try multiple methods to get document height
        let documentHeight = 0;
        
        // Method 1: Use body dimensions
        if (document.body) {
            documentHeight = Math.max(
                document.body.scrollHeight,
                document.body.offsetHeight,
                document.body.clientHeight
            );
        }
        
        // Method 2: Use document element dimensions
        const docElementHeight = Math.max(
            document.documentElement.scrollHeight,
            document.documentElement.offsetHeight,
            document.documentElement.clientHeight
        );
        
        // Method 3: Use the larger of the two methods
        const finalDocumentHeight = Math.max(documentHeight, docElementHeight);
        
        // If document height is still 0 or very small, fall back to screen height
        let contentHeight = finalDocumentHeight > screenHeight ? finalDocumentHeight : screenHeight;
        
        // If content height is still too small, use a generous fallback
        if (contentHeight < screenHeight * 1.5) {
            contentHeight = Math.max(screenHeight * 2, 2000); // At least 2x screen height or 2000px
        }
        
        return contentHeight;
    }

    /**
     * Apply theme background color to sp-theme element
     */
    applyThemeBackground(themeElement) {
        const isDark = themeElement.getAttribute('color') === 'dark';
        const backgroundColor = isDark ? '#1a1a1a' : '#e2e2e2';
        
        // Apply background color with maximum specificity
        themeElement.style.setProperty('background-color', backgroundColor, 'important');
        themeElement.style.setProperty('background', backgroundColor, 'important');
        
        // Also ensure html and body have the same background
        document.documentElement.style.setProperty('background-color', backgroundColor, 'important');
        document.body.style.setProperty('background-color', backgroundColor, 'important');
        
        // Force a repaint
        themeElement.style.display = 'none';
        themeElement.offsetHeight; // Trigger reflow
        themeElement.style.display = '';
    }

    /**
     * Force update viewport height (useful for theme changes)
     */
    forceUpdate() {
        this.setViewportHeight();
    }
}

// Create global instance
window.viewportManager = new ViewportManager();

// Auto-initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.viewportManager.init();
    });
} else {
    window.viewportManager.init();
}

export default ViewportManager;
