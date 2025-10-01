import { css, html, LitElement } from 'lit'
import { customElement, property } from 'lit/decorators.js'
import '@spectrum-web-components/button/sp-button.js'
import ViewportManager from '../utils/viewport.js'

@customElement('dt-home-theme-toggle')
class ThemeToggle extends LitElement {
    @property({ type: String }) currentTheme = 'light'
    @property({ type: Boolean }) isTransitioning = false

    static styles = css`
        :host {
            display: inline-block;
        }

        .theme-toggle-button {
            --spectrum-button-min-width: 0px;
            --spectrum-button-edge-to-text: 1px;
            cursor: pointer;
            --system-spectrum-button-accent-background-color-default: transparent;
            --system-spectrum-button-accent-background-color-hover: transparent;
            --system-spectrum-button-accent-background-color-down: transparent;
            --system-spectrum-button-accent-background-color-focus: transparent;
            --spectrum-focus-indicator-color: transparent;
            transition: all 0.3s ease;
        }

        .theme-icon {
            color: var(--text-color, #0a0a0a);
            font-size: 30px;
            transition: all 0.3s ease;
        }

        :host(:hover) .theme-icon {
            color: var(--primary-color, #326a82);
        }

        .theme-toggle-button:hover .theme-icon {
            color: var(--primary-color, #326a82);
        }

        .transitioning {
            opacity: 0.7;
            transform: scale(0.95);
        }

        /* Dark mode specific styles */
        :host([theme="dark"]) .theme-icon {
            color: var(--text-color, #e0e0e0);
        }

        :host([theme="dark"]:hover) .theme-icon {
            color: var(--primary-color, #ffffff);
        }
    `

    constructor() {
        super()
        this.initializeTheme()
    }

    connectedCallback() {
        super.connectedCallback()
        this.setupSystemPreferenceListener()
    }

    disconnectedCallback() {
        super.disconnectedCallback()
        if (this.systemPreferenceMediaQuery) {
            this.systemPreferenceMediaQuery.removeEventListener('change', this.handleSystemPreferenceChange)
        }
        
        // Clean up Safari event listeners
        if (this.safariCleanup) {
            this.safariCleanup()
        }
    }

    initializeTheme() {
        // Check for saved preference first
        const savedTheme = localStorage.getItem('dt-home-theme')
        if (savedTheme) {
            this.currentTheme = savedTheme
        } else {
            // Check system preference
            this.currentTheme = this.getSystemPreference()
        }
        this.applyTheme()
    }

    getSystemPreference() {
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            return 'dark'
        }
        return 'light'
    }

    setupSystemPreferenceListener() {
        if (window.matchMedia) {
            this.systemPreferenceMediaQuery = window.matchMedia('(prefers-color-scheme: dark)')
            this.handleSystemPreferenceChange = () => {
                // Only update if no saved preference exists
                if (!localStorage.getItem('dt-home-theme')) {
                    const newTheme = this.getSystemPreference()
                    if (newTheme !== this.currentTheme) {
                        this.currentTheme = newTheme
                        this.applyTheme()
                    }
                }
            }
            this.systemPreferenceMediaQuery.addEventListener('change', this.handleSystemPreferenceChange)
        }
    }

    applyTheme() {
        // Update the sp-theme component
        const themeElement = document.querySelector('sp-theme')
        if (themeElement) {
            themeElement.color = this.currentTheme
        }
        
        // Update host attribute for styling
        this.setAttribute('theme', this.currentTheme)
        
        // Force background color change directly
        const body = document.body
        const html = document.documentElement
        
        if (this.currentTheme === 'dark') {
            body.style.backgroundColor = '#1a1a1a'
            html.style.backgroundColor = '#1a1a1a'
            // Ensure blank template body gets dark background
            if (body.id === 'blank-template-body') {
                body.style.backgroundColor = '#1a1a1a'
                // Safari-specific fixes
                this.applySafariFixes(body, '#1a1a1a')
            }
        } else {
            body.style.backgroundColor = '#e2e2e2'
            html.style.backgroundColor = '#e2e2e2'
            // Ensure blank template body gets light background
            if (body.id === 'blank-template-body') {
                body.style.backgroundColor = '#e2e2e2'
                // Safari-specific fixes
                this.applySafariFixes(body, '#e2e2e2')
            }
        }
        
        // Force viewport height update after theme change
        if (window.viewportManager) {
            window.viewportManager.forceUpdate()
        }
        
        // Save preference
        localStorage.setItem('dt-home-theme', this.currentTheme)
        
        // Dispatch custom event for other components
        this.dispatchEvent(new CustomEvent('theme-changed', {
            detail: { theme: this.currentTheme },
            bubbles: true,
            composed: true
        }))
    }

    /**
     * Apply Safari-specific fixes for background rendering
     */
    applySafariFixes(element, color) {
        // Detect Safari
        const isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent)
        
        if (isSafari) {
            // Force Safari to repaint by temporarily changing and restoring styles
            const originalBackground = element.style.background
            const originalBackgroundColor = element.style.backgroundColor
            
            // Set both background and backgroundColor for Safari
            element.style.background = color
            element.style.backgroundColor = color
            
            // Force a reflow
            element.offsetHeight
            
            // Safari-specific: Force hardware acceleration
            element.style.transform = 'translateZ(0)'
            element.style.willChange = 'background-color'
            
            // Safari viewport height fixes
            this.applySafariViewportFixes(element)
            
            // Restore original values after a brief moment
            setTimeout(() => {
                element.style.background = originalBackground
                element.style.backgroundColor = originalBackgroundColor
                element.style.transform = ''
                element.style.willChange = ''
            }, 50)
        }
    }

    /**
     * Apply Safari viewport height fixes
     */
    applySafariViewportFixes(element) {
        const isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent)
        
        if (isSafari) {
            // Set dynamic height based on actual viewport
            const setViewportHeight = () => {
                const vh = window.innerHeight * 0.01
                document.documentElement.style.setProperty('--vh', `${vh}px`)
                
                // Apply to body and html
                element.style.height = `${window.innerHeight}px`
                element.style.minHeight = `${window.innerHeight}px`
                document.documentElement.style.height = `${window.innerHeight}px`
                document.documentElement.style.minHeight = `${window.innerHeight}px`
            }
            
            // Set initial height
            setViewportHeight()
            
            // Update on resize (Safari address bar changes)
            window.addEventListener('resize', setViewportHeight)
            window.addEventListener('orientationchange', setViewportHeight)
            
            // Store cleanup function
            if (!this.safariCleanup) {
                this.safariCleanup = () => {
                    window.removeEventListener('resize', setViewportHeight)
                    window.removeEventListener('orientationchange', setViewportHeight)
                }
            }
        }
    }

    async toggleTheme() {
        if (this.isTransitioning) return
        
        this.isTransitioning = true
        this.classList.add('transitioning')
        
        // Small delay for smooth transition
        await new Promise(resolve => setTimeout(resolve, 150))
        
        this.currentTheme = this.currentTheme === 'light' ? 'dark' : 'light'
        this.applyTheme()
        
        // Reset transition state
        await new Promise(resolve => setTimeout(resolve, 150))
        this.isTransitioning = false
        this.classList.remove('transitioning')
    }

    render() {
        return html`
            <style>
                @import url('https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css');
            </style>
            <sp-button
                class="theme-toggle-button inline-element"
                @click="${this.toggleTheme}"
                aria-label="${this.currentTheme === 'light' ? 'Switch to dark mode' : 'Switch to light mode'}"
                title="${this.currentTheme === 'light' ? 'Switch to dark mode' : 'Switch to light mode'}"
            >
                ${this.currentTheme === 'light' 
                    ? html`<i class="mdi mdi-weather-night theme-icon" slot="icon"></i>`
                    : html`<i class="mdi mdi-white-balance-sunny theme-icon" slot="icon"></i>`
                }
            </sp-button>
        `
    }
}
