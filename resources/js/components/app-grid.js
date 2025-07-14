import { css, html, LitElement } from 'lit'
import { customElement } from 'lit-element'
import { property, queryAll } from 'lit/decorators.js'
import { magic_url } from '../helpers.js'

/**
 * Custom element representing an application grid.
 */

@customElement('dt-home-app-grid')
class AppGrid extends LitElement {
    /**
     * Styles for the AppGrid element.
     * @static
     * @type {CSSResult}
     */
    static styles = css`
        .app-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            padding: 20px 0;
            justify-items: center;
        }

        .links-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
            padding: 20px 0;
        }

        .link-item {
            display: flex;
            align-items: center;
            padding: 16px;
            border-radius: 12px;
            background-color: #f0f0f0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .link-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
        }

        .link-item:before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: linear-gradient(45deg, #4a90e2, #63b3ed);
            border-radius: 4px 0 0 4px;
        }

        .link-item__icon {
            width: 40px;
            height: 40px;
            margin-right: 16px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .link-item__icon img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
        }

        .link-item__icon i {
            font-size: 24px;
            color: #4a90e2;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
        }

        .link-item__content {
            flex: 1;
            min-width: 0;
            padding: 0 8px;
        }

        .link-item__title {
            font-size: 16px;
            font-weight: 600;
            color: #1a202c;
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .link-item__url {
            color: #718096;
            text-decoration: none;
            font-size: 13px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: block;
            transition: color 0.2s ease;
        }

        .link-item__url:hover {
            color: #4a90e2;
        }

        .link-item__copy {
            background: linear-gradient(45deg, #4a90e2, #63b3ed);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            margin-left: 16px;
            flex-shrink: 0;
            transition: all 0.2s ease;
            box-shadow: 0 2px 6px rgba(74, 144, 226, 0.3);
        }

        .link-item__copy i {
            font-size: 18px;
        }

        .link-item__copy:hover {
            transform: translateY(-1px) rotate(5deg);
            box-shadow: 0 4px 8px rgba(74, 144, 226, 0.4);
        }

        .link-item__remove {
            background: linear-gradient(45deg, #e53e3e, #fc8181);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            margin-left: 12px;
            flex-shrink: 0;
            transition: all 0.2s ease;
            box-shadow: 0 2px 6px rgba(229, 62, 62, 0.3);
        }

        .link-item__remove i {
            font-size: 18px;
        }

        .link-item__remove:hover {
            transform: translateY(-1px) rotate(5deg);
            box-shadow: 0 4px 8px rgba(229, 62, 62, 0.4);
        }

        .app-grid__item {
            transition: transform 0.2s;
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
            background-color: rgb(255, 255, 255);
            color: #fcfbfb;
            padding: 5px 5px 0px 5px;
            cursor: pointer;
            border-radius: 53%;
            font-size: 14px;
            z-index: 1;
            background-color: #f16d71;
            border: 1px solid #7e1919;
        }

        .app-grid__icon {
            width: 100%;
            pointer-events: none;
        }

        .app-grid__remove-icon.hidden {
            display: none;
        }
    `
    @property({ type: Array }) appData = []
    @property({ type: Number }) selectedIndex = -1
    @property({ type: String }) appUrl = ''
    @property({ type: Boolean }) editing = false
    @queryAll('.app-grid__item') items = []
    showRemoveIconId = null
    clickTimer = null
    clickDelay = 300
    longPressTimer = 500
    longPressDuration = 1000

    /**
     * Lifecycle callback when the element is connected to the DOM.
     */
    connectedCallback() {
        super.connectedCallback()
        this.loadAppData()
        document.addEventListener('click', this.handleDocumentClick)
        document.addEventListener('mousedown', this.handleMouseDown)
        document.addEventListener('mouseup', this.handleMouseUp)
        document.addEventListener('mouseleave', this.handleMouseLeave)
    }

    /**
     * Adds the drag-over class to the app.
     * @param {DragEvent} event - The drag event.
     */
    handleDragOver(event) {
        event.preventDefault()
        event.target.classList.add('app-grid__item--over')
    }

    /**
     * Removes the drag-over class from the app.
     */
    handleDragLeave(event) {
        event.target.classList.remove('app-grid__item--over')
    }

    /**
     * Handles the drag event by setting the data being dragged.
     * @param {DragEvent} event - The drag event.
     * @param {number} index - The index of the dragged item.
     */
    handleDragStart(event, index) {
        this.showRemoveIconId = null
        event.dataTransfer.setData('text/plain', index)
        // Hide all remove icons
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
        // Show all remove icons again
        this.shadowRoot
            .querySelectorAll('.app-grid__remove-icon')
            .forEach((icon) => {
                icon.classList.remove('hidden')
            })
    }

    /**
     * Handles the drop event by reordering the apps.
     * @param {DragEvent} event - The drop event.
     */
    handleDrop(event) {
        event.preventDefault()
        const fromIndex = event.dataTransfer.getData('text/plain')
        const toElement = event.target.closest('.app-grid__item')
        const toIndex = Array.from(this.items).indexOf(toElement)
        if (fromIndex >= 0 && toIndex >= 0) {
            this.reorderApps(fromIndex, toIndex)
        }

        // Call handleDocumentClick to ensure immediate removal of context menu icon
        this.handleDocumentClick(event)
    }

    /**
     * Handles a single click event on an app.
     *
     * @param {Event} event - The click event.
     * @param {number} index - The index of the clicked app.
     *
     * @return {void}
     */
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

    /**
     * Handles a single click event.
     *
     * @param {Event} event - The click event.
     * @param {number} index - The index of the app in the appData array.
     * @return {void}
     */
    handleSingleClick(event, slug) {
        const selectedApp = this.appData.find((app) => app.slug === slug)
        if (selectedApp) {
            switch (selectedApp.type) {
                case 'Link':
                    this.visitApp(selectedApp.url, selectedApp)
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
            this.appData.splice(index, 1)
            this.selectedIndex = -1
            this.showRemoveIconId = null
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
            this.isDragging = false
            this.requestUpdate()
        }
    }

    /**
     * Reorders apps based on drag and drop.
     * @param {number} fromIndex - The index from which the app is dragged.
     * @param {number} toIndex - The index to which the app is dropped.
     */
    reorderApps(fromIndex, toIndex) {
        const appsCopy = [...this.appData]
        const [removedApp] = appsCopy.splice(fromIndex, 1)
        appsCopy.splice(toIndex, 0, removedApp)
        this.appData = appsCopy
        this.postNewOrderToServer()
    }

    /**
     * Posts the new order of apps to the server.
     */
    postNewOrderToServer() {
        const url = magic_url('reorder')
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': $home.nonce,
            },
            body: JSON.stringify(this.appData),
        })
            .then((response) => response.json())
            .then((data) => {
                console.log('Order update successful:', data)
            })
            .catch((error) => {
                console.error('Error updating order:', error)
            })
    }

    /**
     * Loads application data from an external source.
     */
    loadAppData() {
        // Fetch your data from an external source or set it from an attribute
        // For this example, let's assume it's set from a JSON attribute
        const jsonData = this.getAttribute('app-data')
        this.appUrl = this.getAttribute('app-url')

        if (jsonData) {
            this.appData = JSON.parse(jsonData)
        }
    }

    /**
     * Posts data of the app to be hidden to the server.
     * @param {string} appId - The ID of the app to be hidden.
     */
    postAppDataToServer(slug) {
        const url = magic_url('hide')
        const appToHide = this.appData.find((app) => app.slug === slug)

        if (!appToHide) {
            console.error('App not found')
            return
        }

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': $home.nonce,
            },
            body: JSON.stringify(appToHide),
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
            }, this.longPressDuration)
        }
    }

    handleTouchEnd = () => {
        clearTimeout(this.longPressTimer)
        this.longPressTimer = null
    }

    /**
     * Handles copying a link URL to clipboard.
     * @param {Event} event - The click event.
     * @param {Object} link - The link object.
     */
    handleCopy(event, link) {
        event.stopPropagation()
        event.preventDefault()

        // Get the button element that was clicked
        const button = event.target.closest('.link-item__copy')

        if (navigator.clipboard) {
            navigator.clipboard
                .writeText(link.url)
                .then(() => {
                    this.showCopyFeedback(button)
                })
                .catch(() => {
                    this.fallbackCopy(link, button)
                })
        } else {
            this.fallbackCopy(link, button)
        }
    }

    /**
     * Fallback copy method for older browsers.
     * @param {Object} link - The link object.
     * @param {Element} button - The button element.
     */
    fallbackCopy(link, button) {
        const textArea = document.createElement('textarea')
        textArea.value = link.url
        document.body.appendChild(textArea)
        textArea.select()
        document.execCommand('copy')
        document.body.removeChild(textArea)
        this.showCopyFeedback(button)
    }

    /**
     * Shows visual feedback when URL is copied.
     * @param {Element} button - The button element.
     */
    showCopyFeedback(button) {
        // Store the original icon
        const originalIcon = button.innerHTML
        const originalBackground = button.style.background

        // Change to checkmark icon and success color
        button.innerHTML = '<i class="mdi mdi-check"></i>'
        button.style.background = 'linear-gradient(45deg, #38a169, #68d391)'
        button.style.transform = 'scale(1.1)'
        button.style.boxShadow = '0 4px 8px rgba(56, 161, 105, 0.4)'

        // Reset after animation
        setTimeout(() => {
            button.innerHTML = originalIcon
            button.style.background = originalBackground
            button.style.transform = 'scale(1)'
            button.style.boxShadow = '0 2px 6px rgba(74, 144, 226, 0.3)'
        }, 1500)
    }

    /**
     * Renders the AppGrid element.
     * @returns {TemplateResult} HTML template result.
     */
    render() {
        return html`
            <style>
                @import url('https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css');
            </style>
            <div>
                <h2>Apps</h2>

                <div class="app-grid">
                    ${this.appData
                        .filter(
                            (app) =>
                                app.is_hidden !== 1 &&
                                app.magic_link_meta !== undefined
                        ) // Filter out hidden apps
                        .map(
                            (app, index) => html`
                                <div
                                    class="app-grid__item ${this.editing
                                        ? 'editing'
                                        : ''}"
                                    data-index="${index}"
                                    @touchstart="${(event) =>
                                        this.handleTouchStart(
                                            event,
                                            index,
                                            app
                                        )}"
                                    @touchend="${this.handleTouchEnd}"
                                    @touchcancel="${this.handleTouchEnd}"
                                    @click="${(event) =>
                                        !this.editing &&
                                        this.handleClick(event, app.slug, app)}"
                                    @mousedown="${(event) =>
                                        this.handleMouseDown(
                                            event,
                                            index,
                                            app
                                        )}"
                                    @dragstart="${(event) =>
                                        this.handleDragStart(
                                            event,
                                            index,
                                            app
                                        )}"
                                    @dragend="${(event) =>
                                        this.handleDragEnd(event, index, app)}"
                                    @dragover="${(event) =>
                                        this.handleDragOver(event, index, app)}"
                                    @dragleave="${(event) =>
                                        this.handleDragLeave(
                                            event,
                                            index,
                                            app
                                        )}"
                                    @drop="${(event) =>
                                        this.handleDrop(event, index, app)}"
                                    draggable="${this.editing
                                        ? 'true'
                                        : 'false'}"
                                >
                                    ${this.editing
                                        ? html`
                                              <span
                                                  class="app-grid__remove-icon ${this
                                                      .showRemoveIconId
                                                      ? ''
                                                      : 'hidden'}"
                                                  @click="${(event) =>
                                                      this.handleRemove(
                                                          event,
                                                          index,
                                                          app
                                                      )}"
                                              >
                                                  <sp-icon-close></sp-icon-close>
                                              </span>
                                          `
                                        : ''}
                                    <dt-home-app-icon
                                        class="app-grid__icon"
                                        name="${app.name}"
                                        icon="${app.icon}"
                                    ></dt-home-app-icon>
                                </div>
                            `
                        )}
                </div>
            </div>
            <div>
                <h2>Links</h2>
                <div class="links-list">
                    ${this.appData
                        .filter(
                            (app) =>
                                app.is_hidden !== 1 &&
                                app.magic_link_meta === undefined
                        ) // Filter out hidden apps
                        .map(
                            (link, index) => html`
                                <div class="link-item">
                                    <div class="link-item__icon">
                                        ${link.icon.startsWith('http') ||
                                        link.icon.startsWith('/')
                                            ? html`<img
                                                  src="${link.icon}"
                                                  alt="${link.name || 'Link'}"
                                              />`
                                            : html`<i
                                                  class="${link.icon}"
                                                  aria-hidden="true"
                                              ></i>`}
                                    </div>
                                    <div class="link-item__content">
                                        <div class="link-item__title">
                                            ${link.name || 'Link'}
                                        </div>
                                        <a
                                            href="${link.url}"
                                            class="link-item__url"
                                            target="${link.open_in_new_tab
                                                ? '_blank'
                                                : '_self'}"
                                        >
                                            ${link.url}
                                        </a>
                                    </div>
                                    <button
                                        class="link-item__copy"
                                        @click="${(event) =>
                                            this.handleCopy(event, link)}"
                                        title="Copy link"
                                    >
                                        <i class="mdi mdi-content-copy"></i>
                                    </button>
                                    ${this.editing
                                        ? html`
                                              <button
                                                  class="link-item__remove"
                                                  @click="${(event) =>
                                                      this.handleRemove(
                                                          event,
                                                          index,
                                                          link
                                                      )}"
                                                  title="Remove link"
                                              >
                                                  <i class="mdi mdi-delete"></i>
                                              </button>
                                          `
                                        : ''}
                                </div>
                            `
                        )}
                </div>
            </div>
        `
    }
}
