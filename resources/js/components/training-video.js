import { LitElement, html, css } from 'lit'
import { property } from 'lit/decorators.js'
import { customElement } from 'lit-element'

@customElement('dt-home-video-list')
class VideoList extends LitElement {
    @property({ type: Array })
    trainingData = []
    @property({ type: Boolean }) isDarkMode = false

    static get styles() {
        return css`
            :host {
                display: block;
            }
            
            .video-container {
                margin-bottom: 20px;
            }
            
            .video-title {
                color: var(--dt-text-color, #333);
                font-size: 18px;
                font-weight: 600;
                margin-bottom: 10px;
                line-height: 1.4;
            }
            
            .video-title.dark-mode {
                color: #ffffff !important;
            }
            
            iframe {
                width: 100% !important;
                height: auto !important;
                aspect-ratio: 16 / 9;
                border-radius: 8px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            }
            
            .video-container.dark-mode iframe {
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
            }
        `
    }

    /**
     * Parses the training data attribute and sets it to the trainingData property.
     *
     * @memberof VideoList
     * @returns {void}
     */
    connectedCallback() {
        super.connectedCallback()
        const data = this.getAttribute('training-data')
        if (data) {
            try {
                this.trainingData = JSON.parse(data)
            } catch (e) {
                console.error('Error parsing training data:', e)
                this.trainingData = []
            }
        }
        
        // Listen for theme changes
        document.addEventListener('theme-changed', this.handleThemeChange)
        // Apply theme styles after a short delay to ensure DOM is ready
        setTimeout(() => {
            this.applyThemeStyles()
        }, 100)
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
        this.isDarkMode = isDark
    }

    /**
     * Handles the click event on a video, updating the URL hash.
     *
     * @memberof VideoList
     * @param {Object} training - The training object clicked.
     * @returns {void}
     */
    handleVideoClick(training) {
        // Concatenate 'training' before the anchor value
        history.pushState({}, '', `training#${training.anchor}`)
    }

    render() {
        return html`
            <div>
                ${this.trainingData.map(
                    (training) => html`
                        <div id=${training.anchor} class="video-container ${this.isDarkMode ? 'dark-mode' : ''}">
                            <div class="video-title ${this.isDarkMode ? 'dark-mode' : ''}">
                                ${training.name}
                            </div>
                            ${this.renderIframe(training.embed_video)}
                        </div>
                    `
                )}
            </div>
        `
    }

    /**
     * Renders an iframe element from the embed code.
     *
     * @memberof VideoList
     * @param {string} embedCode - The embed code for the video.
     * @returns {TemplateResult} The rendered iframe element.
     */
    renderIframe(embedCode) {
        // Use JavaScript's String replace method to remove backslashes
        embedCode = embedCode.replace(/\\+/g, '')
        const template = document.createElement('template')
        embedCode = embedCode.trim() // Trim the string to remove any whitespace from the ends
        template.innerHTML = embedCode // Set the innerHTML of the template to the embed code
        return html`${template.content}` // Use the template's content for the HTML
    }
}
