import {css, html, LitElement} from 'lit'
import {customElement} from 'lit-element'
import {property} from 'lit/decorators.js'

@customElement('dt-home-menu')
class Menu extends LitElement {
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
    
    // Get the menu icon elements
    const menuIcons = this.shadowRoot.querySelectorAll('.menu-icon')
    const tripleGripper = this.shadowRoot.querySelector('sp-icon-triple-gripper')
    const closeIcon = this.shadowRoot.querySelector('sp-icon-close')
    const menuButton = this.shadowRoot.querySelector('sp-button')
    const popover = this.shadowRoot.querySelector('sp-popover')
    const menuItems = this.shadowRoot.querySelectorAll('sp-menu-item')
    
    // Apply icon colors
    menuIcons.forEach(icon => {
      if (isDark) {
        icon.style.color = '#ffffff'
      } else {
        icon.style.color = ''
      }
    })
    
    if (tripleGripper) {
      if (isDark) {
        tripleGripper.style.color = '#ffffff'
      } else {
        tripleGripper.style.color = ''
      }
    }
    
    if (closeIcon) {
      if (isDark) {
        closeIcon.style.color = '#ffffff'
      } else {
        closeIcon.style.color = ''
      }
    }
    
    if (menuButton) {
      if (isDark) {
        menuButton.style.color = '#ffffff'
        menuButton.style.setProperty('--spectrum-button-text-color', '#ffffff')
        menuButton.style.setProperty('--spectrum-button-text-color-hover', '#ffffff')
        menuButton.style.setProperty('--spectrum-button-text-color-down', '#ffffff')
        menuButton.style.setProperty('--spectrum-button-text-color-key-focus', '#ffffff')
      } else {
        menuButton.style.color = ''
        menuButton.style.setProperty('--spectrum-button-text-color', '')
        menuButton.style.setProperty('--spectrum-button-text-color-hover', '')
        menuButton.style.setProperty('--spectrum-button-text-color-down', '')
        menuButton.style.setProperty('--spectrum-button-text-color-key-focus', '')
      }
    }
    
    // Apply popover dark mode styles
    if (popover) {
      if (isDark) {
        popover.style.backgroundColor = 'var(--menu-popover-bg-dark)'
        popover.style.borderColor = 'var(--menu-popover-border-dark)'
        popover.style.color = 'var(--menu-text-dark)'
      } else {
        popover.style.backgroundColor = 'var(--menu-popover-bg-light)'
        popover.style.borderColor = 'var(--menu-popover-border-light)'
        popover.style.color = ''
      }
    }
    
    // Apply menu item dark mode styles
    menuItems.forEach(item => {
      if (isDark) {
        item.style.color = 'var(--menu-text-dark)'
        item.style.setProperty('--spectrum-menu-item-label-content-color-default', 'var(--menu-text-dark)')
        item.style.setProperty('--spectrum-menu-item-label-content-color-hover', 'var(--menu-hover-dark)')
      } else {
        item.style.color = ''
        item.style.setProperty('--spectrum-menu-item-label-content-color-default', '')
        item.style.setProperty('--spectrum-menu-item-label-content-color-hover', 'var(--menu-hover-light)')
      }
    })
  }

  static styles = css`
    :host {
      /* CSS Custom Properties for theming */
      --menu-popover-bg-light: #ffffff;
      --menu-popover-bg-dark: #2a2a2a;
      --menu-popover-border-light: #7a76767d;
      --menu-popover-border-dark: #4a4a4a;
      --menu-text-light: #222;
      --menu-text-dark: #ffffff;
      --menu-hover-light: rgb(7, 130, 235);
      --menu-hover-dark: #4a9eff;
    }

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
      padding-top: 4px;
      color: hsla(198, 45%, 28%, 1);
      --spectrum-icon-size: 25px;
    }

    sp-popover {
      background-color: var(--menu-popover-bg-light);
      border: 2px solid var(--menu-popover-border-light);
      padding: 10px;
      transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
    }

    /* Dark mode styles for popover */
    :host-context(sp-theme[color="dark"]) sp-popover {
      background-color: var(--menu-popover-bg-dark) !important;
      border-color: var(--menu-popover-border-dark) !important;
      color: var(--menu-text-dark) !important;
    }

    .right-aligned-menu sp-menu-item {
      padding: 5px 0px;
      font-weight: 100;
      transition: color 0.3s ease;
    }

    /* Dark mode styles for menu items */
    :host-context(sp-theme[color="dark"]) .right-aligned-menu sp-menu-item {
      color: var(--menu-text-dark) !important;
      --spectrum-menu-item-label-content-color-default: var(--menu-text-dark) !important;
      --spectrum-menu-item-label-content-color-hover: var(--menu-hover-dark) !important;
    }

    .menu-title {
      font-weight: 100;
      margin: 3px 0px;
    }

    @media (min-width: 230px) and (max-width: 950px) {
      sp-popover {
        background-color: var(--menu-popover-bg-light);
        min-width: 175%;
        margin-left: -94%;
        border: 2px solid var(--menu-popover-border-light);
        padding: 10px;
      }
    }

    /* Dark mode media queries */
    @media (min-width: 230px) and (max-width: 950px) {
      :host-context(sp-theme[color="dark"]) sp-popover {
        background-color: var(--menu-popover-bg-dark) !important;
        border-color: var(--menu-popover-border-dark) !important;
        color: var(--menu-text-dark) !important;
      }
    }

    @media (min-width: 750px) and (max-width: 950px) {
      sp-popover {
        background-color: var(--menu-popover-bg-light);
        min-width: 175%;
        margin-left: -152%;
        border: 2px solid var(--menu-popover-border-light);
        padding: 10px;
      }
    }

    @media (min-width: 750px) and (max-width: 950px) {
      :host-context(sp-theme[color="dark"]) sp-popover {
        background-color: var(--menu-popover-bg-dark) !important;
        border-color: var(--menu-popover-border-dark) !important;
        color: var(--menu-text-dark) !important;
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
      color: var(--menu-text-light) !important;
      transition: color 0.3s ease;
    }

    .right-aligned-menu a:hover {
      text-decoration: none !important;
      color: var(--menu-hover-light) !important;
    }

    /* Dark mode styles for menu links */
    :host-context(sp-theme[color="dark"]) .right-aligned-menu a {
      color: var(--menu-text-dark) !important;
    }

    :host-context(sp-theme[color="dark"]) .right-aligned-menu a:hover {
      color: var(--menu-hover-dark) !important;
    }

    .menu-title:hover {
      --spectrum-menu-item-label-content-color-hover: var(--menu-hover-light);
    }

    .menu-item {
      --spectrum-menu-item-label-content-color-hover: var(--menu-hover-light);
    }

    /* Dark mode hover states */
    :host-context(sp-theme[color="dark"]) .menu-title:hover {
      --spectrum-menu-item-label-content-color-hover: var(--menu-hover-dark);
    }

    :host-context(sp-theme[color="dark"]) .menu-item {
      --spectrum-menu-item-label-content-color-hover: var(--menu-hover-dark);
    }

    /* Dark mode styles for menu icon */
    :host-context(sp-theme[color="dark"]) .menu-icon {
      color: #ffffff !important;
    }

    :host-context(sp-theme[color="dark"]) sp-icon-triple-gripper {
      color: #ffffff !important;
    }

    /* Dark mode styles for menu button */
    :host-context(sp-theme[color="dark"]) sp-button {
      color: #ffffff !important;
      --spectrum-button-text-color: #ffffff !important;
      --spectrum-button-text-color-hover: #ffffff !important;
      --spectrum-button-text-color-down: #ffffff !important;
      --spectrum-button-text-color-key-focus: #ffffff !important;
    }

    :host-context(sp-theme[color="dark"]) sp-button:hover {
      color: #ffffff !important;
      --spectrum-button-text-color-hover: #ffffff !important;
    }

    :host-context(sp-theme[color="dark"]) sp-button:focus {
      color: #ffffff !important;
      --spectrum-button-text-color-key-focus: #ffffff !important;
    }

    :host-context(sp-theme[color="dark"]) sp-button:active {
      color: #ffffff !important;
      --spectrum-button-text-color-down: #ffffff !important;
    }

    :host-context(sp-theme[color="dark"]) sp-button[focused] {
      color: #ffffff !important;
      --spectrum-button-text-color-key-focus: #ffffff !important;
    }

    :host-context(sp-theme[color="dark"]) sp-icon-close {
      color: #ffffff !important;
    }

    :host-context(sp-theme[color="dark"]) sp-icon-close:hover {
      color: #ffffff !important;
    }

    :host-context(sp-theme[color="dark"]) sp-icon-close:focus {
      color: #ffffff !important;
    }

    :host-context(sp-theme[color="dark"]) sp-icon-close:active {
      color: #ffffff !important;
    }

  `
  @property({type: Boolean}) isOpen = false
  @property({type: Array}) menuItems = []
  @property({type: Object}) translations = {}

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
          ? html`
            <sp-icon-close
              class="menu-icon"
              slot="icon"
            ></sp-icon-close>`
          : html`
            <sp-icon-triple-gripper
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
            ${this.menuItems.map(
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
      
      <dt-home-invite-modal id="inviteModal" .translations=${this.translations}></dt-home-invite-modal>
    `
  }

  handleMenuClick(item) {
    if (item.action === 'invite') {
      // Open invite modal
      const inviteModal = this.shadowRoot.getElementById('inviteModal')
      if (inviteModal) {
        inviteModal.open()
      }
      // Try dispatching a 'close' event on the overlay
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
