/**
 * Import Spectrum Web Components
 */
import '@spectrum-web-components/theme/sp-theme.js'
import '@spectrum-web-components/theme/src/themes.js'
import '@disciple.tools/web-components'

import '@spectrum-web-components/menu/sp-menu.js'
import '@spectrum-web-components/menu/sp-menu-group.js'
import '@spectrum-web-components/menu/sp-menu-item.js'
import '@spectrum-web-components/menu/sp-menu-divider.js'
import '@spectrum-web-components/overlay/sp-overlay.js'
import '@spectrum-web-components/icon/sp-icon.js'
import '@spectrum-web-components/icons-ui/icons/sp-icon-triple-gripper.js'
import '@spectrum-web-components/icons-workflow/icons/sp-icon-more-small-list-vert.js'
import '@spectrum-web-components/action-group/sp-action-group.js'
import '@spectrum-web-components/button-group/sp-button-group.js'
import '@spectrum-web-components/button/sp-button.js'
import '@spectrum-web-components/button/sp-clear-button.js'
import '@spectrum-web-components/button/sp-close-button.js'
import '@spectrum-web-components/progress-circle/sp-progress-circle.js'
import '@spectrum-web-components/action-menu/sp-action-menu.js'
import '@spectrum-web-components/tooltip/sp-tooltip.js'
import '@spectrum-web-components/icons-workflow/icons/sp-icon-help.js'
import '@spectrum-web-components/overlay/sync/overlay-trigger.js'
import '@spectrum-web-components/icons-workflow/icons/sp-icon-view-grid.js'
import '@spectrum-web-components/icons-workflow/icons/sp-icon-close.js'
import '@spectrum-web-components/field-group/sp-field-group.js'
import '@spectrum-web-components/textfield/sp-textfield.js'
import '@spectrum-web-components/dialog/sp-dialog-base.js'
import '@spectrum-web-components/icons-workflow/icons/sp-icon-add.js'
import '@spectrum-web-components/icons-workflow/icons/sp-icon-edit.js'
import '@spectrum-web-components/icons-workflow/icons/sp-icon-add-circle.js'
import '@spectrum-web-components/icons-workflow/icons/sp-icon-delete.js'
import '@spectrum-web-components/dialog/sp-dialog-wrapper.js'
/**
 * Web Components
 */
import './components/app-grid.js'
import './components/home-footer.js'
import './components/menu.js'
import './components/training-video.js'
import './components/app-icon.js'
import './components/tool-tip.js'
import './components/icon-picker.js'
import './components/app-json.js'

/**
 * CSS
 */
import '../css/plugin.css'

/**
 * Imports
 */
import { loaded } from './helpers.js'
import handleDomLoaded from './dom-hooks/handle-dom-loaded.js'
import decloak from './dom-hooks/decloak.js'
import submitFormOnEnter from './dom-hooks/submit-form-on-enter.js'

/**
 * Bootstrap the application
 */
loaded((document) => {
    document.querySelectorAll('form').forEach(submitFormOnEnter)
    document.querySelectorAll('.cloak').forEach(decloak)
    document.querySelectorAll('body').forEach(handleDomLoaded)
})
