import { css, html, LitElement } from 'lit'
import { property } from 'lit/decorators.js'
import '@spectrum-web-components/dialog/sp-dialog.js'
import '@spectrum-web-components/button/sp-button.js'
import '@spectrum-web-components/overlay/overlay-trigger.js'
import '@spectrum-web-components/action-bar/sp-action-bar.js'
import '@spectrum-web-components/icons-workflow/icons/sp-icon-upload-to-cloud.js'
import { customElement } from 'lit-element'
import './app-menu.js'
import './app-menu-item.js'
import { magic_url, translate } from '../helpers.js'
import './app-form-modal.js'

@customElement('dt-home-footer')
class HomeFooter extends LitElement {
    static properties = {
        appUrl: { type: String },

        resetApps: { type: Boolean },
        buttonColor: { type: String },
    }

    @property({ type: Object })
    translations = {
        hiddenAppsLabel: 'Hidden Apps',
        buttonLabel: 'Ok',
    }

    @property({ type: Array })
    appData = []

    static get styles() {
        return css`
            :host {
                --mod-divider-thickness: 0px;
                --spectrum-spacing-300: 0px;
                --spectrum-spacing-200: 0px;
                --spectrum-dialog-confirm-divider-block-spacing-start: var(
                    --spectrum-spacing-300
                );
                --spectrum-dialog-confirm-divider-block-spacing-end: var(
                    --spectrum-spacing-200
                );
                --mod-dialog-confirm-padding-grid: 0px;
                --spectrum-dialog-confirm-padding-grid: 0px;
            }

            @media (prefers-color-scheme: dark) {
                sp-dialog {
                    --dialog-bg-color: #333;
                    --dialog-border-color: #555;
                }

                .app-name {
                    --app-name-color: white;
                    --app-name-hover-color: hsla(216, 100%, 70%, 1);
                }
            }

            .footer-container {
                padding: 5px;
                display: flex;
                justify-content: right;
                bottom: 20px;
            }

            .trigger-button {
                --mod-button-min-width: 41px;
                --mod-button-border-width: 6px 3px 2.5px 3px;
                --spectrum-border-width-200: 12px;
                --spectrum-button-bottom-to-text-medium: 0px;
                --spectrum-button-top-to-text-medium: 0px;
                --spectrum-workflow-icon-size-100: 26px;
                --spectrum-button-edge-to-text: 0px;
                --system-spectrum-button-accent-background-color-hover: var(
                    --button-color
                );
                --system-spectrum-button-accent-background-color-down: var(
                    --button-color
                );
                --system-spectrum-button-accent-background-color-default: var(
                    --button-color
                );
                --spectrum-focus-indicator-color: transparent;
                border-radius: 50%;
            }

            sp-icon-add {
                color: white;
            }

            sp-dialog {
                background-color: var(--dialog-bg-color, white);
                border: 1px solid var(--dialog-border-color, #a1a1a1);
                border-radius: 5px;
                box-shadow: -2px -2px 40px 20px rgb(0 0 0 / 10%);
                height: 200px; /* Let the content dictate the height */
                padding: 0; /* Remove default padding */
                overflow: hidden; /* Hide overflow */
                margin-right: 165px;
                margin-bottom: -41px;
            }

            .app-menu-item {
                border-radius: 5px;
                margin-left: 2px;
                margin-right: 2px;
            }

            .app-menu-item:hover {
                background-color: #ededed;
                border-left: 2px solid hsl(0, 0%, 60%);
            }

            .app-row {
                display: flex;
                align-items: center;
                border-bottom: 1px solid lightgray; /* Add bottom border */
            }

            .app-row:last-child {
                border-bottom: none; /* Remove bottom border for the last item */
            }

            .app-icon {
                width: 30px;
                height: 30px;
                margin-right: 8px;
            }

            .material-icons.app-icon {
                font-size: 30px;
                line-height: 30px;
            }

            .app-name {
                flex: 1; /* Ensure the name takes the remaining space */
                color: var(
                    --app-name-color,
                    black
                ); /* Use CSS variable for text color */
            }

            .app-name:hover {
                color: var(
                    --app-name-hover-color,
                    hsla(216, 100%, 50%, 1)
                ); /* Use CSS variable for hover color */
            }

            .reset-apps {
                color: #ffffff;
                background-color: #e94f54;
                --system-spectrum-actionbutton-background-color-default: var(
                    --background-color
                );
                --spectrum-component-height-100: 19px;
                --spectrum-font-size-100: 10px;
                margin-left: -439px;
                top: 238px;
            }

            .custom-app {
                margin-right: -102px;
                margin-top: -35px;
                --system-spectrum-actionbutton-background-color-default: #3fab3f;
                --system-spectrum-actionbutton-background-color-hover: #3fab3f;
                --system-spectrum-actionbutton-background-color-down: #3fab3f;
                --highcontrast-actionbutton-content-color-default: #ffffff;
                --spectrum-neutral-content-color-hover: #ffffff;
                --spectrum-neutral-content-color-down: #ffffff;
            }

            .custom-apps-menu {
                margin-top: -55px;
                margin-right: -288px;
                width: 288px;
            }

            /* Mobile */
            @media (max-width: 600px) {
                .footer-container {
                    position: fixed;
                    width: 100%;
                    display: flex;
                    right: 10px;
                }

                .custom-dialog-overlay {
                    left: calc(100vw - 72vw) !important;
                    right: 0;
                    bottom: 25px;
                    top: auto;
                    width: 60vw;
                    max-height: 400px; /* Set max height */
                    overflow: hidden; /* Hide overflow */
                }

                sp-dialog {
                    background-color: var(--dialog-bg-color, white);
                    height: 200px;
                }

                .custom-apps-menu {
                    left: calc(100vw - 72vw) !important;
                    margin-right: -60vw;
                    width: 60vw;
                }

                #custom_app_menu_bar {
                    width: 132%;
                    max-width: 290px;
                }
            }

            /* Tablet */
            @media (min-width: 601px) and (max-width: 962px) {
                .footer-container {
                    position: fixed;
                    width: 100%;
                    display: flex;
                    right: 10px;
                }

                .custom-dialog-overlay {
                    left: calc(100vw - 350px) !important;
                    top: auto;
                    width: 100px;
                }

                .custom-apps-menu {
                    left: calc(100vw - 350px) !important;
                    margin-right: -288px;
                    width: 288px;
                }

                #custom_app_menu_bar {
                    width: 100%;
                    max-width: 400px;
                }
            }

            /* Desktop */
            @media (min-width: 963px) and (max-width: 1920px) {
                .footer-container {
                    position: fixed;
                    width: 100%;
                    display: flex;
                    right: 10px;
                }

                .custom-dialog-overlay {
                    left: calc(100vw - 350px) !important;
                    top: auto;
                    width: 100px;
                }

                .custom-apps-menu {
                    left: calc(100vw - 350px) !important;
                    margin-right: -288px;
                    width: 288px;
                }

                #custom_app_menu_bar {
                    width: 100%;
                    max-width: 400px;
                }
            }

            sp-menu-item {
                background-color: transparent !important;
                border-left: none !important;
                transition: none !important;
                padding-left: 0 !important;
                outline: none !important; /* Ensure no focus outline */
                box-shadow: none !important; /* Ensure no box shadow */
            }

            sp-menu-item::part(heading),
            sp-menu-item::part(indicator),
            sp-menu-item::part(checkmark) {
                display: none !important;
            }

            sp-menu-item:hover,
            sp-menu-item:focus,
            sp-menu-item:active,
            sp-menu-item:focus-visible {
                border-left: none !important; /* Remove left border on all states */
                background-color: lightgray !important; /* Change hover background color as needed */
                outline: none !important; /* Remove focus outline */
                box-shadow: none !important; /* Ensure no box shadow */
            }

            sp-menu-item[focused] {
                outline: none !important;
                border-left: none !important; /* Remove blue left border */
                box-shadow: none !important; /* Ensure no box shadow */
            }

            .no-data {
                color: gray;
                padding: 10px; /* Add padding for space */
            }
        `
    }

    get hiddenApps() {
        return this.appData.filter((app) => app.is_hidden)
    }

    connectedCallback() {
        super.connectedCallback()
        this.loadAppData()
        this.style.setProperty('--button-color', this.buttonColor)
        document.addEventListener('app-hidden', this.handleAppHidden.bind(this))
        document.addEventListener(
            'app-return',
            this.handleAppUnhidden.bind(this)
        )
    }

    handleAppUnhidden(event) {
        const unhiddenApp = event.detail.app
        const url = magic_url('apps')
        fetch(url)
            .then((response) => response.json())
            .then((data) => {
                this.appData = data
                const appIndex = this.appData.findIndex(
                    (app) => app.slug === unhiddenApp.slug
                )
                if (appIndex > -1) {
                    this.appData[appIndex] = unhiddenApp
                    this.requestUpdate()
                } else {
                    console.log('App not found in appData:', unhiddenApp.slug)
                }
            })
            .catch((error) => {
                console.error('Error:', error)
            })
    }

    disconnectedCallback() {
        super.disconnectedCallback()
        document.removeEventListener(
            'app-hidden',
            this.handleAppHidden.bind(this)
        )
        document.removeEventListener(
            'app-return',
            this.handleAppUnhidden.bind(this)
        )
    }

    handleAppHidden(event) {
        const hiddenApp = event.detail.app
        const appIndex = this.appData.findIndex(
            (app) => app.slug === hiddenApp.slug
        )

        if (appIndex > -1) {
            this.appData[appIndex] = hiddenApp
            this.requestUpdate()
        }
    }

    loadAppData() {
        const jsonData = this.getAttribute('hidden-data')
        this.appUrl = this.getAttribute('app-url-unhide')
        this.resetApps = this.getAttribute('reset-apps') === '1'
        this.buttonColor = this.getAttribute('button-color')
        if (jsonData) {
            this.appData = JSON.parse(jsonData)
        }
    }

    postAppDataToServer(appSlug) {
        const url = magic_url('unhide')
        const appToUnHide = this.appData.find((app) => app.slug === appSlug)
        if (!appToUnHide) {
            console.error('App not found')
            return
        }
        // Optimistically update the UI before making the request
        appToUnHide.is_hidden = 0
        this.requestUpdate()

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': $home.nonce,
            },
            body: JSON.stringify(appToUnHide),
        })
            .then((response) => {
                // Handle error
                if (!response.ok) {
                    // If the request fails, revert the UI change
                    appToUnHide.is_hidden = 1
                    this.requestUpdate()
                    console.error('Failed to update the server')
                }
            })
            .catch((error) => {
                appToUnHide.is_hidden = 1
                this.requestUpdate()
                console.error('Error:', error)
            })
    }

    handleAppClick(e, appSlug) {
        e.stopPropagation()
        const appIndex = this.appData.findIndex((app) => app.slug === appSlug)
        if (appIndex === -1) {
            console.error('App not found')
            return
        }
        const appId = this.appData[appIndex].slug
        this.appData[appIndex].is_hidden = 0
        this.postAppDataToServer(appSlug)
        // Dispatch a custom event that the app has been unhidden
        this.dispatchEvent(
            new CustomEvent('app-unhidden', {
                detail: { app: this.appData[appIndex] },
                bubbles: true,
                composed: true,
            })
        )
    }

    isIconURL(icon) {
        return /^(https?:\/\/|data:image|\/|\.\/|\.\.\/)/.test(icon)
    }

    toggleModal() {
        const overlayTrigger = this.shadowRoot.querySelector('overlay-trigger')
        if (overlayTrigger && overlayTrigger.open) {
            overlayTrigger.open = false // Close the overlay (and the dialog)
        }
        const modal = this.shadowRoot.getElementById('customModal')
        modal.toggleModal()
    }

    reset_apps() {
        const confirmationResetMessage =
            $home.translations.reset_app_confirmation
        const confirmDelete = confirm(confirmationResetMessage)

        if (confirmDelete) {
            fetch(magic_url('reset-apps'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': $home.nonce,
                },
            })
                .then((response) => {
                    if (response.ok) {
                        console.log(response)
                        window.location.reload()
                    } else {
                        // Handle error
                    }
                })
                .catch((error) => {
                    console.error('Error:', error)
                })
        } else {
            return false
        }
    }

    renderAppItems() {
        const hiddenApps = this.hiddenApps.sort((a, b) => b.sort - a.sort)
        if (hiddenApps.length === 0) {
            return html` <dt-app-menu-item class="no-data">
                ${$home.translations.no_hidden_apps}.
            </dt-app-menu-item>`
        }

        return hiddenApps.map(
            (app) => html`
                <dt-app-menu-item
                    class="app-menu-item"
                    @click="${(e) => this.handleAppClick(e, app.slug)}"
                >
                    <div class="app-row">
                        ${this.isIconURL(app.icon)
                            ? html`<img
                                  src="${app.icon}"
                                  class="app-icon"
                                  alt="icon"
                              />`
                            : html`<span
                                  id="app-icon"
                                  class="app-icon material-icons ${app.icon}"
                              ></span>`}
                        <span class="app-name">${app.name}</span>
                    </div>
                </dt-app-menu-item>
            `
        )
    }

    init_components() {
        /**
         * Action Bar Setup.
         */

        // Obtain handle onto action bar element.
        const action_bar = this.shadowRoot.getElementById('custom_app_menu_bar')
        if (action_bar) {
            // Attempt to locate nested close button.
            const close_button =
                action_bar.shadowRoot.querySelector('.close-button')

            // If found, ensure close button is permanently hidden.
            if (close_button) {
                close_button.style.display = 'none'
            }

            // Always ensure action bar is shown!
            action_bar.open = true
        }
    }

    init_popover_menu() {
        // Obtain handle onto action bar element.
        const action_bar = this.shadowRoot.getElementById('custom_app_menu_bar')
        if (action_bar) {
            // Obtain handle to child bar menu.
            const bar_menu = action_bar.querySelector(
                '.custom-app-menu-bar-menu'
            )
            if (bar_menu) {
                // Obtain handle to shadow root child overlay.
                const overlay =
                    bar_menu.shadowRoot.querySelector('sp-overlay[open]')
                if (overlay) {
                    // Adjust popover menu alignment.
                    const popover = overlay.querySelector(
                        'sp-popover[id="popover"]'
                    )
                    if (popover) {
                        popover.style.marginBottom = '7px'
                        popover.style.marginRight = '-8px'
                    }
                }
            }
        }
    }

    render() {
        return html`
            <style>
                @import url('https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css');
            </style>
            <div class="footer-container">
                <overlay-trigger type="replace" placement="top">
                    <div slot="click-content" class="custom-apps-menu">
                        <sp-action-bar id="custom_app_menu_bar" open>
                            <sp-action-button
                                slot="buttons"
                                label="Create"
                                @click="${this.toggleModal}"
                            >
                                <sp-icon-add-circle
                                    slot="icon"
                                ></sp-icon-add-circle>
                            </sp-action-button>

                            ${this.resetApps
                                ? html`
                                      <sp-action-button
                                          slot="buttons"
                                          label="Reset"
                                          @click="${this.reset_apps}"
                                      >
                                          <sp-icon-delete
                                              slot="icon"
                                              style="color: red;"
                                          ></sp-icon-delete>
                                      </sp-action-button>
                                  `
                                : null}

                            <sp-action-menu
                                class="custom-app-menu-bar-menu"
                                label="More Actions"
                                placement="top-end"
                                slot="buttons"
                                @click="${this.init_popover_menu}"
                            >
                                <sp-menu-item @click="${this.toggleModal}">
                                    <sp-icon-add-circle
                                        slot="icon"
                                        style="margin-left: 10px;"
                                    ></sp-icon-add-circle>
                                    ${translate('add_custom_app_label')}
                                </sp-menu-item>

                                ${this.resetApps
                                    ? html`
                                          <sp-menu-item
                                              @click="${this.reset_apps}"
                                          >
                                              <sp-icon-delete
                                                  slot="icon"
                                                  style="margin-left: 10px; color: red;"
                                              ></sp-icon-delete>
                                              <span style="color: red;"
                                                  >${translate(
                                                      'reset_apps_label'
                                                  )}</span
                                              >
                                          </sp-menu-item>
                                      `
                                    : null}
                            </sp-action-menu>
                        </sp-action-bar>
                    </div>

                    <sp-button
                        slot="trigger"
                        class="trigger-button"
                        @click="${this.init_components}"
                    >
                        <sp-icon-add></sp-icon-add>
                    </sp-button>
                    <sp-dialog
                        slot="click-content"
                        class="custom-dialog-overlay"
                        size="xs"
                    >
                        <dt-app-menu label="Choose an app">
                            ${this.renderAppItems()}
                        </dt-app-menu>
                    </sp-dialog>

                    <div
                        slot="click-content"
                        class="custom-app custom-dialog-overlay-button"
                    ></div>
                </overlay-trigger>
            </div>
            <!-- Custom Modal Structure -->
            <app-form-modal
                id="customModal"
                modelName="${translate('add_custom_app_label')}"
            >
            </app-form-modal>
        `
    }
}
