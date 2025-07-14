import { css, html, LitElement } from 'lit'
import { customElement, property } from 'lit/decorators.js'
import '@spectrum-web-components/dialog/sp-dialog.js'
import '@spectrum-web-components/button/sp-button.js'
import '@spectrum-web-components/textfield/sp-textfield.js'

@customElement('dt-home-invite-modal')
class InviteModal extends LitElement {
    @property({ type: String }) shareUrl = ''
    @property({ type: Boolean }) isOpen = false
    @property({ type: Object }) translations = {}

    static styles = css`
        :host {
            display: block;
        }
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.35);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.18);
            min-width: 320px;
            max-width: 95vw;
            padding: 0;
        }
        sp-dialog {
            --spectrum-dialog-width: 400px;
            --spectrum-dialog-max-width: 95vw;
        }
        .invite-content {
            padding: 20px;
        }
        .explanation-text {
            margin-bottom: 20px;
            color: #666;
            line-height: 1.5;
        }
        .share-link-container {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-bottom: 20px;
        }
        sp-textfield {
            flex: 1;
        }
        .copy-button {
            flex-shrink: 0;
        }
        .success-message {
            color: #4caf50;
            font-size: 14px;
            margin-top: 10px;
            display: none;
        }
        .success-message.show {
            display: block;
        }
        .dialog-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }
    `

    constructor() {
        super()
        // Get the share URL from the current page URL
        this.shareUrl = window.location.href + '/share'
    }

    open() {
        this.isOpen = true
    }

    close() {
        this.isOpen = false
    }

    async copyToClipboard() {
        try {
            await navigator.clipboard.writeText(this.shareUrl)
            this.showCopySuccess()
        } catch (err) {
            // Fallback for older browsers
            this.fallbackCopy()
        }
    }

    fallbackCopy() {
        const textArea = document.createElement('textarea')
        textArea.value = this.shareUrl
        document.body.appendChild(textArea)
        textArea.select()
        document.execCommand('copy')
        document.body.removeChild(textArea)
        this.showCopySuccess()
    }

    showCopySuccess() {
        const successMessage = this.shadowRoot.querySelector('.success-message')
        successMessage.classList.add('show')
        setTimeout(() => {
            successMessage.classList.remove('show')
        }, 2000)
    }

    t(key, fallback) {
        return (this.translations && this.translations[key]) || fallback
    }

    render() {
        if (!this.isOpen) return html``
        return html`
            <div class="modal-overlay" @click=${this.close}>
                <div class="modal-content" @click=${e => e.stopPropagation()}>
                    <sp-dialog 
                        open
                        mode="modal"
                        size="m"
                        dismissable
                    >
                        <h2 slot="heading">${this.t('inviteTitle', 'Invite')}</h2>
                        <div class="invite-content">
                            <p class="explanation-text">
                                ${this.t('inviteExplanation', 'Copy this link and share it with people you are coaching. They will create their own account and have their own Home Screen.')}
                            </p>
                            <div class="share-link-container">
                                <sp-textfield
                                    readonly
                                    value="${this.shareUrl}"
                                    size="m"
                                ></sp-textfield>
                                <sp-button
                                    class="copy-button"
                                    variant="cta"
                                    size="m"
                                    @click=${this.copyToClipboard}
                                >
                                    ${this.t('inviteCopyButton', 'Copy')}
                                </sp-button>
                            </div>
                            <div class="success-message">
                                ${this.t('inviteCopiedMessage', 'Link copied!')}
                            </div>
                        </div>
                        <div slot="button" class="dialog-actions">
                            <sp-button
                                variant="secondary"
                                size="m"
                                @click=${this.close}
                            >
                                ${this.t('inviteCloseButton', 'Close')}
                            </sp-button>
                        </div>
                    </sp-dialog>
                </div>
            </div>
        `
    }
} 