import { css, html, LitElement } from "lit";
import { customElement } from "lit-element";
import { property, queryAll } from "lit/decorators.js";
import { isAndroid, isiOS, magic_url, translate } from "../helpers.js";
import "./app-form-modal.js";
import Sortable from "sortablejs";

@customElement("dt-home-app-grid")
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
  `;
  @property({ type: Array }) appData = [];
  @property({ type: Array }) appDataCopy = [];
  @property({ type: Number }) selectedIndex = -1;
  @property({ type: String }) appUrl = "";
  @property({ type: Boolean }) editing = false;
  @property({ type: Boolean }) open = false;
  @property({ type: Object }) app = {};
  @queryAll(".app-grid__item") items = [];
  @property({ type: Boolean }) isSortableDragging = true;

  showRemoveIconId = null;
  clickTimer = null;
  clickDelay = 300;
  longPressTimer = 500;
  longPressDuration = 1000;
  sortableInstances = [];

  connectedCallback() {
    super.connectedCallback();
    this.loadAppData();
    document.addEventListener("click", this.handleDocumentClick);
    document.addEventListener("mousedown", this.handleMouseDown);
    document.addEventListener("mouseup", this.handleMouseUp);
    document.addEventListener("mouseleave", this.handleMouseLeave);
    document.addEventListener(
      "app-unhidden",
      this.handleAppUnhidden.bind(this),
    );
    document.addEventListener(
      "modal-closed",
      this.handleModalClosed.bind(this),
    );
  }

  disconnectedCallback() {
    super.disconnectedCallback();
    document.removeEventListener(
      "app-unhidden",
      this.handleAppUnhidden.bind(this),
    );
    document.removeEventListener("click", this.handleDocumentClick);
    document.removeEventListener("mousedown", this.handleMouseDown);
    document.removeEventListener("mouseup", this.handleMouseUp);
    document.removeEventListener("mouseleave", this.handleMouseLeave);
    document.removeEventListener(
      "modal-closed",
      this.handleModalClosed.bind(this),
    );
  }

  updated(changedProperties) {
    super.updated(changedProperties);

    if (changedProperties?.has("appData")) {
      this.initSortable(); // Initialize sortable when appData changes
    }
  }

  initSortable() {
    // Select the container for the grid items
    if (this.isSortableDragging) {
      if (this.editing) {
        const appGrids = this.shadowRoot.querySelectorAll(".app-grid");

        appGrids.forEach((appGrid) => {
          const sortableInstance = new Sortable(appGrid, {
            group: "shared",
            animation: 500,
            draggable: ".app-grid__item",
            fallbackOnBody: true,
            dragClass: "sortable-drag",
            onEnd: (evt) => this.handleReorder(evt),
          });
          this.sortableInstances.push(sortableInstance);
        });
      } else {
        this.sortableInstances.forEach((sortableInstance) => {
          sortableInstance.destroy();
        });
        this.sortableInstances = [];
      }
    }
  }

  handleReorder(event) {
    // Reorder the array based on the new order of DOM elements
    const oldIndex = event.oldIndex;
    const newIndex = event.newIndex;
    const reorderedItems = [...this.appDataCopy];

    const [movedItem] = reorderedItems.splice(oldIndex, 1);
    reorderedItems.splice(newIndex, 0, movedItem);
    this.appDataCopy = reorderedItems;

    this.postNewOrderToServer(reorderedItems);
  }

  postNewOrderToServer(updatedAppData) {
    const url = magic_url("reorder");
    fetch(url, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-WP-Nonce": $home.nonce,
      },
      body: JSON.stringify(updatedAppData),
    })
      .then((response) => response.json())
      .then((data) => {
        this.requestUpdate();
      })
      .catch((error) => {
        console.error("Error updating order:", error);
      });
  }

  handleAppUnhidden(event) {
    const unhiddenApp = event.detail.app;
    const url = magic_url("apps");
    fetch(url)
      .then((response) => response.json())
      .then((data) => {
        if (this.isSortableDragging) {
          this.appDataCopy = data;

          const appCopyIndex = this.appDataCopy.findIndex(
            (app) => app.slug === unhiddenApp.slug,
          );

          if (appCopyIndex > -1) {
            this.appDataCopy[appCopyIndex].is_hidden = false;
          }

          const appIndex = this.appData.findIndex(
            (app) => app.slug === unhiddenApp.slug,
          );

          if (appIndex === -1) {
            this.appData.splice(this.appData.length, 0, unhiddenApp);
          } else {
            this.appData.splice(appIndex, 1, unhiddenApp);
            this.initSortable();
          }
        } else {
          this.appData = data;
          const appIndex = this.appData.findIndex(
            (app) => app.slug === unhiddenApp.slug,
          );
          if (appIndex > -1) {
            this.appData[appIndex] = unhiddenApp;
          }
        }
        this.requestUpdate();
      })
      .catch((error) => {
        console.error("Error:", error);
      });
  }

  handleClick(event, slug) {
    if (
      this.showRemoveIconId === null &&
      this.clickTimer === null &&
      this.longPressTimer === null
    ) {
      this.clickTimer = setTimeout(() => {
        this.handleSingleClick(event, slug);
        this.showRemoveIconId = null;
        this.requestUpdate();
        this.clickTimer = null;
      }, this.clickDelay);
    }
  }

  handleSingleClick(event, slug) {
    const selectedApp = this.appData.find((app) => app.slug === slug);
    if (selectedApp) {
      switch (selectedApp.type) {
        case "Link":
          this.visitApp(selectedApp.url, selectedApp);
          break;
        case "Native App Link":
          this.redirectToApp(selectedApp);
          break;
        default:
          this.visitApp(
            this.addOrUpdateQueryParam(
              magic_url(`/app/${selectedApp.slug}/`),
              "dt_home",
              "true",
            ),
            selectedApp,
          );
          break;
      }
    }
  }

  visitApp(url, options) {
    if (JSON.parse(options.open_in_new_tab ?? false)) {
      window.open(url, "_blank");
    } else {
      window.location.href = url;
    }
  }

  /**
   * Redirects the user to the appropriate app based on the selected app's URL scheme.
   * If the user is on iOS or Android, it attempts to open the app using a custom scheme.
   * If the app is not installed, it redirects to the app store.
   * If the user is on another platform, it redirects to a fallback URL.
   *
   * @param {Object} selectedApp - The selected app object containing URLs.
   * @param {string} selectedApp.fallback_url_android - The fallback URL for Android devices.
   * @param {string} selectedApp.fallback_url_ios - The fallback URL for iOS devices.
   * @param {string} selectedApp.fallback_url_others - The fallback URL for other devices.
   * @param {string} selectedApp.url - The custom scheme URL for the app.
   */
  redirectToApp(selectedApp) {
    const fallbackLinkAndroid = selectedApp.fallback_url_android;
    const fallbackLinkIos = selectedApp.fallback_url_ios;
    const fallbackLinkOthers = selectedApp.fallback_url_others;
    const customScheme = selectedApp.url;

    // isiOS and isAndroid should be implemented by yourself
    if (isiOS() || isAndroid()) {
      const iframe = document.createElement("iframe");
      iframe.style.display = "none";
      iframe.src = customScheme;
      document.body.appendChild(iframe);

      setTimeout(() => {
        if (document.hasFocus()) {
          const storeLink = isAndroid() ? fallbackLinkAndroid : fallbackLinkIos;
          window.location.href = storeLink;
        }
        document.body.removeChild(iframe);
      }, 1000);
    } else {
      window.location.href = fallbackLinkOthers;
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
    const urlObj = new URL(url);
    urlObj.searchParams.set(key, value);
    return urlObj.toString();
  }

  /**
   * Handles a double click event on an app.
   * @param event
   * @param {number} index - The index of the double-clicked app.
   * @param {object} app - The app object.
   */
  handleDoubleClick(event, index, { slug }) {
    clearTimeout(this.clickTimer);
    this.clickTimer = null;
    // Your double click logic here
    this.showRemoveIconId = slug;
    this.requestUpdate();
  }

  /**
   * Handles removal of an app.
   * @param event
   * @param {number} index - The index of the app to be removed.
   * @param {object} app - The app object.
   */
  handleRemove(event, index, { slug }) {
    event.stopPropagation();
    event.preventDefault();
    const confirmationMessage = $home.translations.remove_app_confirmation;
    const confirmed = window.confirm(confirmationMessage);
    if (confirmed) {
      this.postAppDataToServer(slug);
    }
    return false;
  }

  /**
   * Handles a click outside the context menu.
   * @param {MouseEvent} event - The click event.
   */
  handleDocumentClick = (event) => {
    // Check if a long press has occurred
    if (this.longPressOccured) {
      // Reset the flag
      this.longPressOccured = false;
      return;
    }
    // Check if the click is outside the context menu
    const removeIcon = this.shadowRoot.querySelector(".app-grid__remove-icon");
    if (removeIcon && !removeIcon.contains(event.target)) {
      this.showRemoveIconId = null;
      this.editing = false; // Set editing to false when clicking outside
      this.updated(); // Reinitialize sortable instances
      this.initSortable();
      this.isDragging = false;
      this.requestUpdate();
    }
  };

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

  postAppDataToServer(slug) {
    const url = magic_url("hide");

    const appCopyToHide = this.isSortableDragging
      ? this.appDataCopy.find((app) => app.slug === slug)
      : this.appData.find((app) => app.slug === slug);

    if (!appCopyToHide) {
      console.error("App not found");
      return;
    }

    fetch(url, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-WP-Nonce": $home.nonce,
      },
      body: JSON.stringify(appCopyToHide),
    })
      .then((response) => {
        if (response.ok) {
          appCopyToHide.is_hidden = true;
          this.requestUpdate();
          this.dispatchEvent(
            new CustomEvent("app-hidden", {
              detail: { app: appCopyToHide },
              bubbles: true,
              composed: true,
            }),
          );
        } else {
          console.error("Failed to update the server");
        }
      })
      .catch((error) => {
        console.error("Error:", error);
      });
  }

  /**
   * Handles mouse down event on an app.
   * @param {MouseEvent} event - The mouse down event.
   * @param {number} index - The index of the app.
   */
  handleMouseDown = (event, index, slug) => {
    let appGridItem = event.target.closest(".app-grid__item");
    if (!appGridItem) {
      // If the target itself isn't an app grid item, check its parents
      appGridItem = event.target.parentElement.closest(".app-grid__item");
    }
    if (appGridItem) {
      this.longPressTimer = setTimeout(() => {
        this.editing = true; // Enable editing mode on long press
        this.showContextMenu(slug);
        // Set a flag to indicate that a long press has occurred
        this.longPressOccured = true;
        this.updated(); // Reinitialize sortable instances
        this.initSortable();
        this.requestUpdate();
      }, this.longPressDuration);
    }
  };

  /**
   * Handles mouse up event.
   */
  handleMouseUp = () => {
    clearTimeout(this.longPressTimer);
    this.longPressTimer = null;
  };

  /**
   * Handles mouse leave event.
   * @param {number} index - The index of the app.
   */
  handleMouseLeave = (index) => {
    clearTimeout(this.longPressTimer);
    // Do something with the index if needed
  };

  /**
   * Shows context menu for the app.
   * @param {number} index - The index of the app.
   */
  showContextMenu(slug) {
    // Your logic to show the context menu
    // For example, you can set a property to indicate which index's context menu to show
    this.showRemoveIconId = slug;
    this.requestUpdate();
  }

  handleTouchStart = (event, index, slug) => {
    let appGridItem = event.target.closest(".app-grid__item");
    if (!appGridItem) {
      appGridItem = event.target.parentElement.closest(".app-grid__item");
    }
    if (appGridItem) {
      this.longPressTimer = setTimeout(() => {
        this.editing = true; // Enable editing mode on long press
        this.showContextMenu(slug);
        this.longPressOccured = true;
        this.updated(); // Reinitialize sortable instances
        this.initSortable();
        this.requestUpdate();
      }, this.longPressDuration);
    }
  };

  handleTouchEnd = () => {
    clearTimeout(this.longPressTimer);
    this.longPressTimer = null;
  };

  /**
   * Toggles the modal.
   * @param event
   * @param index
   * @param slug
   */

  toggleModal(event, index, { slug }) {
    event.preventDefault();
    this.app = this.appData.find((app) => app.slug === slug);

    this.open = !this.open;
    if (this.open) {
      this.classList.add("modal-open");
    } else {
      this.classList.remove("modal-open");
    }
    const modal = this.shadowRoot.getElementById("customModal");
    if (modal) {
      modal.toggleModal();
    }
  }

  handleModalClosed(event) {
    this.open = false;
    this.app = {};
    this.classList.remove("modal-open");
  }

  handleDragStart(event, index) {
    this.showRemoveIconId = null;
    event.dataTransfer.setData("text/plain", index);
    this.shadowRoot
      .querySelectorAll(".app-grid__remove-icon")
      .forEach((icon) => {
        icon.classList.add("hidden");
      });
  }

  handleDragEnd(event) {
    this.items.forEach((item) => {
      item.classList.remove("app-grid__item--over");
      item.classList.remove("app-grid__item--dragging");
    });
    this.shadowRoot
      .querySelectorAll(".app-grid__remove-icon")
      .forEach((icon) => {
        icon.classList.remove("hidden");
      });
  }

  handleDragOver(event) {
    event.preventDefault();
    event.target.classList.add("app-grid__item--over");
  }

  handleDragLeave(event) {
    event.target.classList.remove("app-grid__item--over");
  }

  handleDrop(event) {
    event.preventDefault();
    const visibleApps = this.appData.filter((app) => !app.is_hidden);
    const fromIndex = parseInt(event.dataTransfer.getData("text/plain"), 10);
    const toElement = event.target.closest(".app-grid__item");
    const toIndex = Array.from(this.items).indexOf(toElement);
    if (fromIndex >= 0 && toIndex >= 0) {
      const originalFromIndex = this.appData.findIndex(
        (app) => app.slug === visibleApps[fromIndex].slug,
      );
      const originalToIndex = this.appData.findIndex(
        (app) => app.slug === visibleApps[toIndex].slug,
      );
      this.reorderApps(originalFromIndex, originalToIndex);
    }
    this.handleDocumentClick(event);
  }

  reorderApps(fromIndex, toIndex) {
    const appsCopy = [...this.appData];
    const [removedApp] = appsCopy.splice(fromIndex, 1);
    appsCopy.splice(toIndex, 0, removedApp);
    this.appData = appsCopy;
    this.postNewOrderToServer(this.appData);
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
      "(prefers-color-scheme: dark)",
    ).matches;

    // Return icon accordingly, based on mode and availability.
    return isDarkMode && app["icon_dark"] ? app["icon_dark"] : app["icon"];
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
      "(prefers-color-scheme: dark)",
    ).matches;

    // Return icon color accordingly, based on mode and availability.
    if (isDarkMode && app["icon_dark_color"]) {
      return app["icon_dark_color"];
    } else if (!isDarkMode && app["icon_color"]) {
      return app["icon_color"];
    }

    return null;
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
