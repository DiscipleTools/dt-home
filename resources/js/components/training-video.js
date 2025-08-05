import { css, html, LitElement } from 'lit'
import { property } from 'lit/decorators.js'
import { customElement } from 'lit-element'

@customElement('dt-home-video-list')
class VideoList extends LitElement {
    @property({ type: Array })
    trainingData = []

    static get styles() {
        return css`
            iframe {
                width: 100% !important;
                height: auto !important;
                aspect-ratio: 16 / 9;
                //padding-bottom: 6.25%; /* 16:9 aspect ratio */
                /* Add any other styles you want here */
            }

            @media (prefers-color-scheme: dark) {
                .training-videos-text-color {
                    color: #ffffff;
                }
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
            <div class="training-videos-text-color">
                ${this.trainingData.map(
                    (training) => html`
                        <div id=${training.anchor}>
                            ${training.name}&nbsp;
                            ${this.renderIframe(training.embed_video)}
                            <br />
                            <br />
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
