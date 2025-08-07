import { css, unsafeCSS, html, LitElement } from 'lit'
import { customElement } from 'lit-element'
import { property } from 'lit/decorators.js'
import CssFilterConverter from "css-filter-converter";

/**
 * Represents an application icon component.
 *
 * @extends LitElement
 */
@customElement('dt-home-app-icon')
class AppIcon extends LitElement {
    @property({ type: String }) name = ''
    @property({ type: String }) slug = ''
    @property({ type: String }) icon = ''
    @property({ type: String }) color = null
    @property({ type: Boolean }) isVisible = true

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
                background-color: #f0f0f0;
                border-radius: 25%;
                width: 100%;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
                margin-bottom: 8px;
                cursor: pointer;
                transition: transform 0.3s ease;
                pointer-events: none;
            }

            .app-icon__icon:hover {
                transform: scale(1.05);
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            }

            .app-icon__icon img {
                width: 40%;
            }

            .app-icon__name {
                font-size: 10px;
                color: var(--app-icon-name-color, #333);
                text-align: center;
                white-space: nowrap;
                width: 100%;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .app-icon__icon .svg-icon {
                filter: none;
            }

            @media (prefers-color-scheme: dark) {
                .app-icon__name {
                    --app-icon-name-color: #fff;
                }

                .app-icon__icon {
                    background-color: #333;
                }

                #app-icon {
                    color: #fff;
                }

                .app-icon__icon .svg-icon {
                    filter: invert(1) hue-rotate(180deg);
                }
            }

            #app-icon {
                font-size: 40px;
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

    // TODO: DO A FUNCTION FOR SVG ALSO...

    /**
     * Generate corresponding filter style for given icon hex color. If no
     * color is specified, then revert to default settings.
     *
     * @returns {string}
     */
    imgIconColorStyle() {
      return (this.color) ? (`filter: ${CssFilterConverter.hexToFilter(this.color).color} !important;`) : '';
    }

    /**
     * Generate corresponding icon font color style, or revert to default setting.
     *
     * @returns {string}
     */
    fontIconColorStyle() {
      return (this.color) ? (`color: ${this.color} !important;`) : '';
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
                              ? html`<img
                                    alt="${this.slug} icon"
                                    src="${this.icon}"
                                    class="${this.slug !== 'disciple-tools'
                                        ? 'svg-icon'
                                        : ''}"
                                />`
                              : html`<i
                                    class="${this.icon}"
                                    id="app-icon"
                                    style="${this.fontIconColorStyle()}"
                                ></i>`}
                      </div>
                      <span class="app-icon__name">${this.name}</span>
                  </div>
              `
            : html``
    }
}
