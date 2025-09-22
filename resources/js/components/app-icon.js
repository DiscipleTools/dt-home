import {css, html, LitElement} from 'lit'
import {customElement} from 'lit-element'
import {property} from 'lit/decorators.js'

/**
 * Represents an application icon component.
 *
 * @extends LitElement
 */
@customElement('dt-home-app-icon')
class AppIcon extends LitElement {
    @property({ type: String }) name = ''
    @property({ type: String }) icon = ''
    @property({ type: Boolean }) isVisible = true

    connectedCallback() {
        super.connectedCallback()
        // Listen for theme changes
        document.addEventListener('theme-changed', this.handleThemeChange)
        
        // Apply theme styles after a short delay to ensure DOM is ready
        setTimeout(() => {
            this.applyThemeStyles()
        }, 100)
    }

    /**
     * Called after the component has been updated for the first time
     */
    firstUpdated() {
        // Apply theme styles after first render
        this.applyThemeStyles()
    }

    disconnectedCallback() {
        super.disconnectedCallback()
        document.removeEventListener('theme-changed', this.handleThemeChange)
    }

    /**
     * Handles theme change events
     */
    handleThemeChange = (event) => {
        this.applyThemeStyles()
    }

    /**
     * Applies theme-specific styles
     */
    applyThemeStyles() {
        const themeElement = document.querySelector('sp-theme')
        const isDark = themeElement && themeElement.color === 'dark'
        
        // Get the app icon element
        const appIcon = this.shadowRoot.querySelector('#app-icon')
        const appName = this.shadowRoot.querySelector('.app-icon__name')
        
        if (appIcon) {
            if (isDark) {
                appIcon.style.color = '#ffffff'
                appIcon.style.filter = 'brightness(0) invert(1)'
            } else {
                appIcon.style.color = ''
                appIcon.style.filter = ''
            }
        }
        
        if (appName) {
            if (isDark) {
                appName.style.color = '#ffffff'
            } else {
                appName.style.color = ''
            }
        }
    }

    /**
     * CSS styles for the app icon.
     * @typedef {String} appIconContainerStyle
     */
    static styles = [
        css`
            .app-icon__container {
                display: flex;
                flex-direction: column;
                align-items: center;
                width: 100%;
            }

            .app-icon__icon {
                display: flex;
                align-items: center;
                justify-content: center;
                aspect-ratio: auto 60 / 60;
                background-color: var(--dt-tile-background-color, #f0f0f0);
                border-radius: 25%;
                width: 100%;
                box-shadow: var(--shadow-0, 0 2px 5px rgba(0, 0, 0, 0.2));
                margin-bottom: 8px;
                cursor: pointer;
                transition: all 0.3s ease;
                pointer-events: none;
            }

            .app-icon__icon:hover {
                transform: scale(1.05);
                box-shadow: var(--shadow-1, 0 4px 8px rgba(0, 0, 0, 0.3));
            }

            .app-icon__icon img {
                width: 40%;
            }

            .app-icon__name {
                font-size: 10px;
                color: var(--text-color, #333);
                text-align: center;
                white-space: nowrap;
                width: 100%;
                overflow: hidden;
                text-overflow: ellipsis;
                transition: color 0.3s ease;
            }

            #app-icon {
                font-size: 40px;
            }

            /* Dark mode styles for app icons */
            :host-context(sp-theme[color="dark"]) #app-icon {
                color: #ffffff !important;
                filter: brightness(0) invert(1) !important;
            }

            :host-context(sp-theme[color="dark"]) .app-icon__name {
                color: #ffffff !important;
            }
        `,
    ]

    /**
     * Checks if the icon is a URL.
     * @returns {boolean} - True if the icon is a URL, otherwise false.
     */
    isIconURL() {
        const pattern = new RegExp('^(https?:\\/\\/|\\/)', 'i')
        return pattern.test(this.icon)
    }

    /**
     * Renders the app icon.
     * @returns {html} - The rendered HTML for the app icon.
     */
    render() {
        return this.isVisible
            ? html`
                  <style>
                      @import url('https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css');
                  </style>
                  <div class="app-icon__container">
                      <div class="app-icon__icon">
                          ${this.isIconURL()
                              ? html`<img src="${this.icon}" />`
                              : html`<i
                                    class="${this.icon}"
                                    id="app-icon"
                                ></i>`}
                      </div>
                      <span class="app-icon__name">${this.name}</span>
                  </div>
              `
            : html``
    }
}
