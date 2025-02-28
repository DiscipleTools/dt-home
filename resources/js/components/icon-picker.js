import { css, html, LitElement } from "lit";
import { property } from "lit/decorators.js";
import { customElement } from "lit-element";
import "@spectrum-web-components/search/sp-search.js";
import "@spectrum-web-components/icons-workflow/icons/sp-icon-close.js";

@customElement("icon-picker")
class IconPicker extends LitElement {
  @property({ type: Boolean }) showIconPicker = false;
  @property({ type: String }) iconSearchQuery = "";
  @property({ type: Array }) materialIcons = [];
  @property({ type: String }) name = "";
  @property({ type: String }) slug = "";
  @property({ type: String }) label = "";
  @property({ type: String }) placeholder = "";
  @property({ type: Boolean }) required = false;
  @property({ type: String, reflect: true }) value = "";

  static styles = css`
    :host {
      --dt-fields-background-color: #f2f2f2;
      --system-spectrum-actionbutton-background-color-default: var(
        --dt-fields-background-color
      );
      --system-spectrum-actionbutton-background-color-hover: var(
        --dt-fields-background-color
      );
      --system-spectrum-actionbutton-background-color-down: var(
        --dt-fields-background-color
      );
      --highcontrast-actionbutton-background-color-down: var(
        --dt-fields-background-color
      );
      --highcontrast-actionbutton-content-color-default: #ffffff;
      //--spectrum-neutral-content-color-hover: #ffffff;
      --spectrum-neutral-content-color-down: #ffffff;
      --mod-actionbutton-height: 41px;
      --spectrum-action-button-edge-to-hold-icon-medium: 10px;
    }

    @media (prefers-color-scheme: dark) {
      :host {
        --dt-fields-background-color: #333;
        --upload-icon-color: #ffffff;
      }

      .selected-icon {
        color: #ffffff;
      }

      .icon-picker-icon {
        color: #fff;
      }

      .close-icon {
        color: #fff;
      }

      .icon-search-div {
        background-color: #424242;
      }

      .selected-icon .svg-icon {
        filter: invert(1) hue-rotate(180deg);
      }

      .upload-icon {
        background-color: #292929 !important;
        border: none;
      }
    }

    .upload-icon {
      top: 20px;
      left: 6px;

      &:hover {
        svg {
          animation: running;
          transform: scale(1.1); /* Add zoom effect */
          transition: transform 0.3s ease-in-out; /* Set duration time with ease-in-out */
        }
      }
    }

    @media (prefers-color-scheme: light) {
      :host {
        --upload-icon-color: #0a0a0a;
      }

      .selected-icon {
        color: #0a0a0a;
      }

      .selected-icon .svg-icon {
        filter: none;
      }
    }

    .icon-loader {
      position: absolute;
      z-index: 1000;
      background: var(--dt-fields-background-color);
      box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.2);
      border-radius: 4px;
      margin-right: 20px;
      margin-top: -15px;
      width: 90%;
    }

    .icon-picker {
      padding-left: 10px;
      margin-top: 0;
      overflow-y: auto;
      max-height: 150px;
    }

    .icon-table {
      width: 100%;
    }

    .icon-table td {
      padding: 10px;
      //text-align: center;
    }

    .icon-table i {
      font-size: 20px; /* Icon Size */
      cursor: pointer;
    }

    .icon-picker i:hover {
      opacity: 0.25;
      cursor: pointer;
    }

    .icon-search {
      margin: 10px;
    }

    .dt__icon {
      display: flex;
    }

    .selected-icon {
      margin-top: 25px;
      font-size: 20px;
      margin-left: 5px;
      width: 20px;
    }

    .upload-icon {
      background-color: #f2f2f2;
      border: 1px solid var(--dt-text-border-color, #fefefe);
    }

    .upload-icon .sp-upload-icon {
      color: var(--upload-icon-color);
      fill: var(--upload-icon-color);
      font-size: 20px;
    }

    .close-icon {
      float: right;
      margin: 10px;
      cursor: pointer;
      pointer-events: all !important;
    }

    .icon-input {
      width: 80% !important;
      float: left;
    }
  `;

  constructor() {
    super();
    this.loadIcons();
  }

  connectedCallback() {
    super.connectedCallback();
    this.loadIcons();
  }

  disconnectedCallback() {
    super.disconnectedCallback();
  }

  buildIconClassNameList() {
    const iconClassNames = [];
    Array.from(document.styleSheets).forEach((styleSheet) => {
      if (
        styleSheet.href &&
        styleSheet.href.includes(
          "/wp-content/plugins/dt-home/resources/css/materialdesignicons.min.css",
        )
      ) {
        Array.from(styleSheet.cssRules).forEach((rule) => {
          if (rule.constructor.name === "CSSStyleRule") {
            iconClassNames.push({
              class: rule.selectorText.substring(
                1,
                rule.selectorText.indexOf(":"),
              ),
            });
          }
        });
      }
    });
    return iconClassNames;
  }

  async loadIcons() {
    try {
      const iconClassNames = this.buildIconClassNameList();
      this.materialIcons = iconClassNames
        .map((icon) => icon.class)
        .filter((className) => className !== "mdi");
    } catch (error) {
      console.error("Error loading icons:", error);
    }
  }

  toggleIconPicker() {
    this.showIconPicker = !this.showIconPicker;
    if (!this.showIconPicker) {
      this.iconSearchQuery = "";
    }
  }

  updateIconSearch(e) {
    this.iconSearchQuery = e.target.value.toLowerCase();
  }

  selectIcon(icon) {
    this.value = `mdi ${icon}`;
    this.showIconPicker = false;
    this.iconSearchQuery = "";
  }

  handleInputChange(e) {
    this.value = e.target.value;
  }

  closeIconPicker() {
    this.showIconPicker = false;
    this.iconSearchQuery = "";
  }

  get filteredIcons() {
    return this.materialIcons
      .filter((icon) => icon.includes(this.iconSearchQuery))
      .slice(0, 40);
  }

  get selectedIcon() {
    return this.value;
  }

  /**
   * Checks if the icon is a URL.
   * @returns {boolean} - True if the icon is a URL, otherwise false.
   */
  isIconURL() {
    const pattern = new RegExp("^(https?:\\/\\/|\\/)", "i");
    return pattern.test(this.selectedIcon);
  }

  renderIconPicker() {
    if (!this.showIconPicker) return null;

    return html`
      <div class="icon-search-div">
        <sp-search
          class="icon-search"
          @input="${this.updateIconSearch}"
          size="s"
          quiet
        ></sp-search>
        <sp-icon-close
          @click="${this.closeIconPicker}"
          class="close-icon"
        ></sp-icon-close>
      </div>
      <div class="icon-picker">
        <table class="icon-table">
          <tbody>
            ${this.filteredIcons.length > 0
              ? this.filteredIcons
                  .reduce((rows, icon, index) => {
                    if (index % 8 === 0) rows.push([]); // Start a new row after every 8 icons
                    rows[rows.length - 1].push(icon); // Add the icon to the current row
                    return rows;
                  }, [])
                  .map(
                    (row) => html`
                      <tr>
                        ${row.map(
                          (icon) => html`
                            <td>
                              <i
                                @click="${() => this.selectIcon(icon)}"
                                class="mdi ${icon} icon-picker-icon"
                              ></i>
                            </td>
                          `,
                        )}
                      </tr>
                    `,
                  )
              : html` <tr>
                  <td colspan="8">No Icons Found</td>
                </tr>`}
          </tbody>
        </table>
      </div>
    `;
  }

  render() {
    return html`
      <style>
        @import url("https://cdn.jsdelivr.net/npm/@mdi/font@6.6.96/css/materialdesignicons.min.css");
      </style>
      <div class="dt__icon">
        <dt-text
          class="icon-input"
          label="${this.label}"
          name="${this.name}"
          placeholder="${this.placeholder}"
          ?required="${this.required}"
          @change="${this.handleInputChange}"
          .value="${this.value}"
        ></dt-text>
        <span class="selected-icon">
          ${this.isIconURL()
            ? html`<img
                style="width:100%"
                class="${this.slug !== "disciple-tools" ? "svg-icon" : ""}"
                src="${this.selectedIcon}"
              />`
            : html`<i class="mdi ${this.selectedIcon}"></i>`}
        </span>

        <sp-action-button
          class="upload-icon button change-icon-button"
          @click="${this.toggleIconPicker}"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            id="Layer_1"
            data-name="Layer 1"
            viewBox="0 0 400 346.68"
            class="sp-upload-icon"
            width="19"
            height="19"
          >
            <path
              d="M185.89,52.32c-10.75,13.02-21.5,26.04-32.25,39.06-3.04,3.68-6.01,7.41-9.12,11.03-5.05,5.89-12.93,6.74-18.62,2.13-5.85-4.74-6.7-12.88-1.71-18.95,21.53-26.2,43.11-52.37,64.74-78.49,6.01-7.26,16.49-7.14,22.52,.15,21.45,25.94,42.86,51.9,64.27,77.88,5.2,6.31,4.64,14.42-1.25,19.29-5.91,4.89-13.84,3.89-19.12-2.48-13.08-15.79-26.12-31.61-39.19-47.41-.78-.94-1.63-1.82-3.06-3.42,0,2.29,0,3.65,0,5.01,0,60.62,0,121.24,0,181.86,0,9.01-5.26,14.83-13.24,14.8-6.94-.03-12.54-5.26-13.05-12.23-.11-1.54-.06-3.09-.06-4.64,0-59.72,0-119.43,0-179.15v-4.13c-.28-.1-.55-.2-.83-.3Z"
            />
            <path
              d="M384.55,344.96H15.45c-7.44,0-13.5-6.06-13.5-13.5v-105.15c0-7.14,5.79-12.92,12.92-12.92s12.92,5.79,12.92,12.92v92.8H372.2v-92.8c0-7.14,5.79-12.92,12.92-12.92s12.92,5.79,12.92,12.92v105.15c0,7.44-6.06,13.5-13.5,13.5Z"
            />
          </svg>
        </sp-action-button>
      </div>
      <div class="icon-loader">${this.renderIconPicker()}</div>
    `;
  }
}
