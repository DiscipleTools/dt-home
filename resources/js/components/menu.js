import { css, html, LitElement } from 'lit'
import { customElement } from 'lit-element'
import { property } from 'lit/decorators.js'

@customElement('dt-home-menu')
class Menu extends LitElement {
    static styles = css`
        .menu-button {
            --spectrum-button-min-width: 0px;
            --spectrum-button-edge-to-text: 1px;
            float: inline-end;
        }

        .menu-item:hover {
            background-color: transparent !important;
        }

        sp-button.toggle-button {
            cursor: pointer;
            --system-spectrum-button-accent-background-color-default: transparent;
            --system-spectrum-button-accent-background-color-hover: transparent;
            --system-spectrum-button-accent-background-color-down: transparent;
            --system-spectrum-button-accent-background-color-focus: transparent;
            --spectrum-focus-indicator-color: transparent;
            /*--spectrum-component-pill-edge-to-text-100: 0px;
--spectrum-button-minimum-width-multiplier: 0px;*/
            /* --spectrum-border-width-200: 15px;
--spectrum-button-edge-to-text: 0px;*/
        }

        .inline-element {
            display: inline-block;
            vertical-align: top;
        }

        .menu-icon {
            color: hsla(198, 45%, 28%, 1);
            --spectrum-icon-size: 25px;
        }

        sp-popover {
            background-color: var(--spectrum-gray-50, #ffff);
            border: 2px solid var(--spectrum-border-color, #7a76767d);
            padding: 10px;
        }

        @media (prefers-color-scheme: dark) {
            sp-popover {
                --spectrum-gray-50: #333;
                --spectrum-border-color: #555;
            }

            .menu-icon {
                color: rgb(39 110 142);
                --spectrum-icon-size: 25px;
            }
        }

        @media (prefers-color-scheme: light) {
            sp-popover {
                --spectrum-gray-50: #ffff;
                --spectrum-border-color: #7a76767d;
            }
        }

        .right-aligned-menu sp-menu-item {
            padding: 5px 0px;
            font-weight: 100;
        }

        .menu-title {
            font-weight: 100;
            margin: 3px 0px;
        }

        @media (min-width: 230px) and (max-width: 950px) {
            sp-popover {
                background-color: var(--spectrum-gray-50);
                min-width: 175%;
                margin-left: -94%;
                border: 2px solid #7a76767d;
                padding: 10px;
            }
        }

        @media (min-width: 750px) and (max-width: 950px) {
            sp-popover {
                background-color: var(--spectrum-gray-50);
                min-width: 175%;
                margin-left: -152%;
                border: 2px solid #7a76767d;
                padding: 10px;
            }
        }

        .right-aligned-menu {
            text-align: left;
        }

        .toggle-button {
            display: flex;
        }

        sp-menu-item:hover {
            color: transparent !important;
        }

        :host(:hover) .menu-icon {
            color: #326a82;
        }

        .right-aligned-menu a {
            text-decoration: none !important;
            color: #222 !important;
        }

        .right-aligned-menu a:hover {
            text-decoration: none !important;
            color: rgb(7, 130, 235) !important;
        }

        .menu-title:hover {
            --spectrum-menu-item-label-content-color-hover: rgb(7, 130, 235);
        }

        .menu-item {
            --spectrum-menu-item-label-content-color-hover: rgb(7, 130, 235);
        }
    `
    @property({ type: Boolean }) isOpen = false
    @property({ type: String }) menuItems = '[]'
    @property({type: String}) translations = '{}'

    render() {
        return html`
            <sp-button
                id="trigger"
                placement="right"
                class="menu-button inline-element menu-icon toggle-button ${this
                    .isOpen
                    ? 'active'
                    : ''}"
            >
                ${this.isOpen
                    ? html` <sp-icon-close
                          class="menu-icon"
                          slot="icon"
                      ></sp-icon-close>`
                    : html` <sp-icon-triple-gripper
                          class="menu-icon"
                          slot="icon"
                      ></sp-icon-triple-gripper>`}
            </sp-button>

            <sp-overlay
                trigger="trigger@click"
                placement="bottom-end"
                style="position: relative"
                @sp-closed="${() => (this.isOpen = false)}"
                @sp-opened="${() => (this.isOpen = true)}"
            >
                <sp-popover placement="right-end">
                    <sp-menu class="right-aligned-menu">
                                                ${JSON.parse(this.menuItems).map(
                            (item) =>
                                html`
                                  <sp-menu-item
                                    class="menu-item"
                                    @click="${() => this.handleMenuClick(item)}"
                                  >
                                    ${item.label}
                                  </sp-menu-item>`
                        )}
                    </sp-menu>
                </sp-popover>
            </sp-overlay>

            <dt-home-invite-modal id="inviteModal" .translations=${JSON.parse(this.translations)}></dt-home-invite-modal>
        `
    }

  handleMenuClick(item) {
    if (item.action === 'invite') {
      // Open invite modal
      const inviteModal = this.shadowRoot.getElementById('inviteModal')
      if (inviteModal) {
        inviteModal.open()
      }
      // Close the menu overlay
      const overlay = this.shadowRoot.querySelector('sp-overlay')
      if (overlay) {
        overlay.dispatchEvent(new CustomEvent('close', { bubbles: true, composed: true }))
      }
      this.isOpen = false
    } else {
      // Navigate to href
      this.navigate(item.href)
    }
  }

  navigate(href) {
      window.location.href = href
  }
}
