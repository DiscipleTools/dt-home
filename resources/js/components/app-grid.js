import { css, html, LitElement } from 'lit'
import { customElement } from 'lit-element'
import { property, queryAll } from 'lit/decorators.js'
import { isAndroid, isiOS, magic_url, translate } from '../helpers.js'
import './app-form-modal.js'
import Sortable from 'sortablejs'

@customElement('dt-home-app-grid')
class AppGrid extends LitElement {
    /**
     * Styles for the AppGrid element.
     * @static
     * @type {CSSResult}
     */
    static styles = css`
        :host {
            --dt-button-background-color: #3f729b;
            --mod-actionbutton-border-radius: 50%; /* Set the desired border-radius value here */
            --mod-actionbutton-background-color-default: var(
                --dt-button-background-color
            );
            --mod-actionbutton-background-color-hover: var(
                --dt-button-background-color
            );
            --mod-actionbutton-background-color-active: var(
                --dt-button-background-color
            );

            --spectrum-neutral-background-color-selected-hover: var(
                --dt-button-background-color
            );
            --mod-actionbutton-background-color-default-selected: var(
                --dt-button-background-color
            );

            --highcontrast-actionbutton-background-color-down: var(
                --dt-button-background-color
            );
            --highcontrast-actionbutton-content-color-down: #ffffff;

            --mod-actionbutton-content-color-hover: #ffffff;
            --mod-actionbutton-content-color-default: #ffffff; /* Set the desired color value here */
        }

        :host(.modal-open) {
            --mod-actionbutton-border-radius: initial;
            --mod-actionbutton-background-color-default: initial;
            --mod-actionbutton-background-color-hover: initial;
            --mod-actionbutton-background-color-active: initial;
            --mod-actionbutton-content-color-hover: initial;
            --mod-actionbutton-content-color-default: initial;
            color: initial;
            border-radius: initial;
            background: initial;
        }

        .edit-menu span {
            display: flex;
        }

        .remove-menu span {
            display: flex;
            color: #f16d71;
        }

        .remove-menu:hover {
            background: #f16d71;

            & span {
                color: white;
            }
        }

        .app-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            padding: 20px 0;
            justify-items: center;
        }

        .app-grid__item {
            position: relative;
            width: 100%;
            cursor: pointer;
            min-width: 0;
            min-height: 0;
        }

        .app-grid__item:hover {
            transform: scale(1.05);
        }

        .app-grid__item--over {
            opacity: 0.2;
        }

        .app-grid__item--dragging {
            background-color: transparent;
        }

        .app-grid__remove-icon {
            position: absolute;
            top: -7px;
            right: -10px;
            color: #fcfbfb;
            cursor: pointer;
            border-radius: 50%;
            font-size: 14px;
            z-index: 1;
        }

        .app-grid__icon {
            width: 100%;
            pointer-events: none;
        }

        .app-grid__remove-icon.hidden {
            display: none;
        }

        .menu-icon {
            position: absolute;
            top: -7px;
            right: -10px;
            color: #fcfbfb;
            cursor: pointer;
            border-radius: 50%;
            font-size: 14px;
            z-index: 1;
            --mod-actionbutton-background-color-default: #3f729b;
            --mod-actionbutton-border-radius: 50%;
            --dt-button-background-color: #3f729b;
            --mod-actionbutton-background-color-active: #3f729b;
        }
    `
    @property({ type: Array }) appData = []
    @property({ type: Array }) appDataCopy = []
    @property({ type: Number }) selectedIndex = -1
    @property({ type: String }) appUrl = ''
    @property({ type: Boolean }) editing = false
    @property({ type: Boolean }) open = false
    @property({ type: Object }) app = {}
    @queryAll('.app-grid__item') items = []
    @property({ type: Boolean }) isSortableDragging = true

    showRemoveIconId = null
    clickTimer = null
    clickDelay = 300
    longPressTimer = 500
    longPressDuration = 1000
    sortableInstances = []

    connectedCallback() {
        super.connectedCallback()
        this.loadAppData()
        document.addEventListener('click', this.handleDocumentClick)
        document.addEventListener('mousedown', this.handleMouseDown)
        document.addEventListener('mouseup', this.handleMouseUp)
        document.addEventListener('mouseleave', this.handleMouseLeave)
        document.addEventListener(
            'app-unhidden',
            this.handleAppUnhidden.bind(this)
        )
        document.addEventListener(
            'modal-closed',
            this.handleModalClosed.bind(this)
        )
    }

    disconnectedCallback() {
        super.disconnectedCallback()
        document.removeEventListener(
            'app-unhidden',
            this.handleAppUnhidden.bind(this)
        )
        document.removeEventListener('click', this.handleDocumentClick)
        document.removeEventListener('mousedown', this.handleMouseDown)
        document.removeEventListener('mouseup', this.handleMouseUp)
        document.removeEventListener('mouseleave', this.handleMouseLeave)
        document.removeEventListener(
            'modal-closed',
            this.handleModalClosed.bind(this)
        )
    }

    updated(changedProperties) {
        super.updated(changedProperties)

        if (changedProperties?.has('appData')) {
            this.initSortable() // Initialize sortable when appData changes
        }
    }

    initSortable() {
        // Select the container for the grid items
        if (this.isSortableDragging) {
            if (this.editing) {
                const appGrids = this.shadowRoot.querySelectorAll('.app-grid')

                appGrids.forEach((appGrid) => {
                    const sortableInstance = new Sortable(appGrid, {
                        group: 'shared',
                        animation: 500,
                        draggable: '.app-grid__item',
                        fallbackOnBody: true,
                        dragClass: 'sortable-drag',
                        onEnd: (evt) => this.handleReorder(evt),
                    })
                    this.sortableInstances.push(sortableInstance)
                })
            } else {
                this.sortableInstances.forEach((sortableInstance) => {
                    sortableInstance.destroy()
                })
                this.sortableInstances = []
            }
        }
    }

    handleReorder(event) {
        // Reorder the array based on the new order of DOM elements
        const oldIndex = event.oldIndex
        const newIndex = event.newIndex
        const reorderedItems = [...this.appDataCopy]

        const [movedItem] = reorderedItems.splice(oldIndex, 1)
        reorderedItems.splice(newIndex, 0, movedItem)
        this.appDataCopy = reorderedItems

        this.postNewOrderToServer(reorderedItems)
    }

    postNewOrderToServer(updatedAppData) {
        const url = magic_url('reorder')
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': $home.nonce,
            },
            body: JSON.stringify(updatedAppData),
        })
            .then((response) => response.json())
            .then((data) => {
                this.requestUpdate()
            })
            .catch((error) => {
                console.error('Error updating order:', error)
            })
    }

    handleAppUnhidden(event) {
        const unhiddenApp = event.detail.app
        const url = magic_url('apps')
        fetch(url)
            .then((response) => response.json())
            .then((data) => {
                if (this.isSortableDragging) {
                    this.appDataCopy = data

                    const appCopyIndex = this.appDataCopy.findIndex(
                        (app) => app.slug === unhiddenApp.slug
                    )

                    if (appCopyIndex > -1) {
                        this.appDataCopy[appCopyIndex].is_hidden = false
                    }

                    const appIndex = this.appData.findIndex(
                        (app) => app.slug === unhiddenApp.slug
                    )

                    if (appIndex === -1) {
                        this.appData.splice(this.appData.length, 0, unhiddenApp)
                    } else {
                        this.appData.splice(appIndex, 1, unhiddenApp)
                        this.initSortable()
                    }
                } else {
                    this.appData = data
                    const appIndex = this.appData.findIndex(
                        (app) => app.slug === unhiddenApp.slug
                    )
                    if (appIndex > -1) {
                        this.appData[appIndex] = unhiddenApp
                    }
                }
                this.requestUpdate()
            })
            .catch((error) => {
                console.error('Error:', error)
            })
    }

    handleClick(event, slug) {
        if (
            this.showRemoveIconId === null &&
            this.clickTimer === null &&
            this.longPressTimer === null
        ) {
            this.clickTimer = setTimeout(() => {
                this.handleSingleClick(event, slug)
                this.showRemoveIconId = null
                this.requestUpdate()
                this.clickTimer = null
            }, this.clickDelay)
        }
    }

    handleSingleClick(event, slug) {
        const selectedApp = this.appData.find((app) => app.slug === slug)
        if (selectedApp) {
            switch (selectedApp.type) {
                case 'Link':
                    this.visitApp(selectedApp.url, selectedApp)
                    break
                case 'Native App Link':
                    this.redirectToApp(selectedApp)
                    break
                default:
                    this.visitApp(
                        this.addOrUpdateQueryParam(
                            magic_url(`/app/${selectedApp.slug}/`),
                            'dt_home',
                            'true'
                        ),
                        selectedApp
                    )
                    break
            }
        }
    }

    visitApp(url, options) {
        if (Boolean(JSON.parse(options.open_in_new_tab ?? false))) {
            window.open(url, '_blank')
        } else {
            window.location.href = url
        }
    }

    redirectToApp(selectedApp) {
        const fallbackLinkAndroid = selectedApp.fallback_url_android
        const fallbackLinkIos = selectedApp.fallback_url_ios
        const fallbackLinkOthers = selectedApp.fallback_url_others
        const customScheme = selectedApp.url

        // isiOS and isAndroid should be implemented by yourself
        if (isiOS() || isAndroid()) {
            window.location.href = customScheme

            setTimeout(() => {
                if (document.hasFocus()) {
                    const storeLink = isAndroid()
                        ? fallbackLinkAndroid
                        : fallbackLinkIos
                    window.location.href = storeLink
                }
            }, 1000)
        } else {
            window.location.href = fallbackLinkOthers
        }
    }

    /**
     * Adds or updates a query parameter in the given URL.
     *
     * @param {string} url - The URL to modify.
     * @param {string} key - The query parameter key.
     * @param {string} value - The query parameter value.
     * @returns {string} - The updated URL with the new or updated query parameter.
     */
    addOrUpdateQueryParam(url, key, value) {
        const urlObj = new URL(url)
        urlObj.searchParams.set(key, value)
        return urlObj.toString()
    }

    /**
     * Handles a double click event on an app.
     * @param event
     * @param {number} index - The index of the double-clicked app.
     * @param {object} app - The app object.
     */
    handleDoubleClick(event, index, { slug }) {
        clearTimeout(this.clickTimer)
        this.clickTimer = null
        // Your double click logic here
        this.showRemoveIconId = slug
        this.requestUpdate()
    }

    /**
     * Handles removal of an app.
     * @param event
     * @param {number} index - The index of the app to be removed.
     * @param {object} app - The app object.
     */
    handleRemove(event, index, { slug }) {
        event.stopPropagation()
        event.preventDefault()
        const confirmationMessage = $home.translations.remove_app_confirmation
        const confirmed = window.confirm(confirmationMessage)
        if (confirmed) {
            this.postAppDataToServer(slug)
        }
        return false
    }

    /**
     * Handles a click outside the context menu.
     * @param {MouseEvent} event - The click event.
     */
    handleDocumentClick = (event) => {
        // Check if a long press has occurred
        if (this.longPressOccured) {
            // Reset the flag
            this.longPressOccured = false
            return
        }
        // Check if the click is outside the context menu
        const removeIcon = this.shadowRoot.querySelector(
            '.app-grid__remove-icon'
        )
        if (removeIcon && !removeIcon.contains(event.target)) {
            this.showRemoveIconId = null
            this.editing = false // Set editing to false when clicking outside
            this.updated() // Reinitialize sortable instances
            this.initSortable()
            this.isDragging = false
            this.requestUpdate()
        }
    }

    loadAppData() {
        const jsonData = this.getAttribute('app-data')
        this.appUrl = this.getAttribute('app-url')

        if (jsonData) {
            this.appData = JSON.parse(jsonData)
            this.appDataCopy = JSON.parse(jsonData)
        }
    }

    postAppDataToServer(slug) {
        const url = magic_url('hide')

        const appCopyToHide = this.isSortableDragging
            ? this.appDataCopy.find((app) => app.slug === slug)
            : this.appData.find((app) => app.slug === slug)

        if (!appCopyToHide) {
            console.error('App not found')
            return
        }

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': $home.nonce,
            },
            body: JSON.stringify(appCopyToHide),
        })
            .then((response) => {
                if (response.ok) {
                    appCopyToHide.is_hidden = true
                    this.requestUpdate()
                    this.dispatchEvent(
                        new CustomEvent('app-hidden', {
                            detail: { app: appCopyToHide },
                            bubbles: true,
                            composed: true,
                        })
                    )
                } else {
                    console.error('Failed to update the server')
                }
            })
            .catch((error) => {
                console.error('Error:', error)
            })
    }

    /**
     * Handles mouse down event on an app.
     * @param {MouseEvent} event - The mouse down event.
     * @param {number} index - The index of the app.
     */
    handleMouseDown = (event, index, slug) => {
        let appGridItem = event.target.closest('.app-grid__item')
        if (!appGridItem) {
            // If the target itself isn't an app grid item, check its parents
            appGridItem = event.target.parentElement.closest('.app-grid__item')
        }
        if (appGridItem) {
            this.longPressTimer = setTimeout(() => {
                this.editing = true // Enable editing mode on long press
                this.showContextMenu(slug)
                // Set a flag to indicate that a long press has occurred
                this.longPressOccured = true
                this.updated() // Reinitialize sortable instances
                this.initSortable()
                this.requestUpdate()
            }, this.longPressDuration)
        }
    }

    /**
     * Handles mouse up event.
     */
    handleMouseUp = () => {
        clearTimeout(this.longPressTimer)
        this.longPressTimer = null
    }

    /**
     * Handles mouse leave event.
     * @param {number} index - The index of the app.
     */
    handleMouseLeave = (index) => {
        clearTimeout(this.longPressTimer)
        // Do something with the index if needed
    }

    /**
     * Shows context menu for the app.
     * @param {number} index - The index of the app.
     */
    showContextMenu(slug) {
        // Your logic to show the context menu
        // For example, you can set a property to indicate which index's context menu to show
        this.showRemoveIconId = slug
        this.requestUpdate()
    }

    handleTouchStart = (event, index, slug) => {
        let appGridItem = event.target.closest('.app-grid__item')
        if (!appGridItem) {
            appGridItem = event.target.parentElement.closest('.app-grid__item')
        }
        if (appGridItem) {
            this.longPressTimer = setTimeout(() => {
                this.editing = true // Enable editing mode on long press
                this.showContextMenu(slug)
                this.longPressOccured = true
                this.updated() // Reinitialize sortable instances
                this.initSortable()
                this.requestUpdate()
            }, this.longPressDuration)
        }
    }

    handleTouchEnd = () => {
        clearTimeout(this.longPressTimer)
        this.longPressTimer = null
    }

    /**
     * Toggles the modal.
     * @param event
     * @param index
     * @param slug
     */

    toggleModal(event, index, { slug }) {
        event.preventDefault()
        this.app = this.appData.find((app) => app.slug === slug)

        this.open = !this.open
        if (this.open) {
            this.classList.add('modal-open')
        } else {
            this.classList.remove('modal-open')
        }
        const modal = this.shadowRoot.getElementById('customModal')
        if (modal) {
            modal.toggleModal()
        }
    }

    handleModalClosed(event) {
        this.open = false
        this.app = {}
        this.classList.remove('modal-open')
    }

    handleDragStart(event, index) {
        this.showRemoveIconId = null
        event.dataTransfer.setData('text/plain', index)
        this.shadowRoot
            .querySelectorAll('.app-grid__remove-icon')
            .forEach((icon) => {
                icon.classList.add('hidden')
            })
    }

    handleDragEnd(event) {
        this.items.forEach((item) => {
            item.classList.remove('app-grid__item--over')
            item.classList.remove('app-grid__item--dragging')
        })
        this.shadowRoot
            .querySelectorAll('.app-grid__remove-icon')
            .forEach((icon) => {
                icon.classList.remove('hidden')
            })
    }

    handleDragOver(event) {
        event.preventDefault()
        event.target.classList.add('app-grid__item--over')
    }

    handleDragLeave(event) {
        event.target.classList.remove('app-grid__item--over')
    }

    handleDrop(event) {
        event.preventDefault()
        const visibleApps = this.appData.filter((app) => !app.is_hidden)
        const fromIndex = parseInt(event.dataTransfer.getData('text/plain'), 10)
        const toElement = event.target.closest('.app-grid__item')
        const toIndex = Array.from(this.items).indexOf(toElement)
        if (fromIndex >= 0 && toIndex >= 0) {
            const originalFromIndex = this.appData.findIndex(
                (app) => app.slug === visibleApps[fromIndex].slug
            )
            const originalToIndex = this.appData.findIndex(
                (app) => app.slug === visibleApps[toIndex].slug
            )
            this.reorderApps(originalFromIndex, originalToIndex)
        }
        this.handleDocumentClick(event)
    }

    reorderApps(fromIndex, toIndex) {
        const appsCopy = [...this.appData]
        const [removedApp] = appsCopy.splice(fromIndex, 1)
        appsCopy.splice(toIndex, 0, removedApp)
        this.appData = appsCopy
        this.postNewOrderToServer(this.appData)
    }

    /**
     * Generate required app icon string, based on current dark mode setting.
     *
     * @param app
     *
     * @returns {string}
     */
    getAppIcon(app) {
        // First, determine the current system color mode, session is in.
        const isDarkMode = window.matchMedia(
            '(prefers-color-scheme: dark)'
        ).matches

        // Return icon accordingly, based on mode and availability.
        return isDarkMode && app['icon_dark'] ? app['icon_dark'] : app['icon']
    }

    /**
     * Generate corresponding app icon color, based on current dark mode setting and
     * color availability.
     *
     * @param app
     *
     * @returns {string}
     */
    getAppIconColor(app) {
        // First, determine the current system color mode, session is in.
        const isDarkMode = window.matchMedia(
            '(prefers-color-scheme: dark)'
        ).matches

        // Return icon color accordingly, based on mode and availability.
        if (isDarkMode && app['icon_dark_color']) {
            return app['icon_dark_color']
        } else if (!isDarkMode && app['icon_color']) {
            return app['icon_color']
        }

        return null
    }

    /**
     * Renders the AppGrid element.
     * @returns {TemplateResult} HTML template result.
     */
    render() {
        return html`
            <div class="app-grid">
                ${this.appData
                    .filter((app) =>
                        !this.isSortableDragging ? !app.is_hidden : true
                    )
                    .map(
                        (app, index) => html`
                            <div
                                class="app-grid__item ${this.editing
                                    ? 'editing'
                                    : ''}"
                                style="display: ${this.isSortableDragging &&
                                this.appDataCopy[
                                    this.appDataCopy.findIndex(
                                        (item) => item.slug === app.slug
                                    )
                                ].is_hidden
                                    ? 'none'
                                    : 'block'}"
                                data-index="${index}"
                                @touchstart="${(event) =>
                                    this.handleTouchStart(event, index, app)}"
                                @touchend="${this.handleTouchEnd}"
                                @touchcancel="${this.handleTouchEnd}"
                                @click="${(event) =>
                                    !this.editing &&
                                    this.handleClick(event, app.slug, app)}"
                                @mousedown="${(event) =>
                                    this.handleMouseDown(event, index, app)}"
                                draggable="${this.editing ? 'true' : 'false'}"
                                @dragstart="${!this.isSortableDragging
                                    ? (event) =>
                                          this.handleDragStart(event, index)
                                    : null}"
                                @dragend="${!this.isSortableDragging
                                    ? (event) =>
                                          this.handleDragEnd(event, index, app)
                                    : null}"
                                @dragover="${!this.isSortableDragging
                                    ? (event) =>
                                          this.handleDragOver(event, index, app)
                                    : null}"
                                @dragleave="${!this.isSortableDragging
                                    ? (event) =>
                                          this.handleDragLeave(
                                              event,
                                              index,
                                              app
                                          )
                                    : null}"
                                @drop="${!this.isSortableDragging
                                    ? (event) =>
                                          this.handleDrop(event, index, app)
                                    : null}"
                            >
                                ${this.editing
                                    ? html`
                                          <sp-action-menu
                                              style="
                                        position: absolute;
                                        top: -7px;
                                        right: -10px;
                                        color: #fcfbfb;
                                        cursor: pointer;
                                        border-radius: 50%;
                                        font-size: 14px;
                                        z-index: 1;
                                        --mod-actionbutton-background-color-default: #3f729b;
                                        --mod-actionbutton-border-radius: 50%;
                                        --dt-button-background-color: #3f729b;
                                        --mod-actionbutton-background-color-active: #3f729b;
                                        --spectrum-actionbutton-border-color-default: #3f729b;
                                        --mod-actionbutton-border-width: 0px !important;
                                        width: 32px;
                                        height: 31px;
                                        --mod-actionbutton-edge-to-text:10px !important;
                                            "
                                              class="app-grid__remove-icon ${this
                                                  .showRemoveIconId
                                                  ? ''
                                                  : 'hidden'}"
                                              @click="${(event) =>
                                                  event.stopPropagation()}"
                                              label="More Actions"
                                          >
                                              <sp-menu-item
                                                  @click="${(event) =>
                                                      this.toggleModal(
                                                          event,
                                                          index,
                                                          app
                                                      )}"
                                                  @touchstart="${(event) =>
                                                      this.toggleModal(
                                                          event,
                                                          index,
                                                          app
                                                      )}"
                                                  class="edit-menu"
                                              >
                                                  <span>
                                                      <sp-icon-edit></sp-icon-edit
                                                      >&nbsp;
                                                      ${translate(
                                                          'edit_menu_label'
                                                      )}</span
                                                  >
                                              </sp-menu-item>
                                              <sp-menu-item
                                                  @click="${(event) =>
                                                      this.handleRemove(
                                                          event,
                                                          index,
                                                          app
                                                      )}"
                                                  @touchstart="${(event) =>
                                                      this.handleRemove(
                                                          event,
                                                          index,
                                                          app
                                                      )}"
                                                  class="remove-menu"
                                              >
                                                  <span>
                                                      <sp-icon-close></sp-icon-close
                                                      >&nbsp;
                                                      ${translate(
                                                          'remove_menu_label'
                                                      )}
                                                  </span>
                                              </sp-menu-item>
                                          </sp-action-menu>
                                      `
                                    : ''}
                                <dt-home-app-icon
                                    class="app-grid__icon"
                                    name="${app.name}"
                                    icon="${app.icon}"
                                    slug="${app.slug}"
                                ></dt-home-app-icon>
                            </div>
                        `
                    )}
            </div>
            <app-form-modal
                id="customModal"
                appData="${JSON.stringify(this.app)}"
                modelName="${translate('edit_custom_app_label')}"
            >
            </app-form-modal>
        `
    }
}
