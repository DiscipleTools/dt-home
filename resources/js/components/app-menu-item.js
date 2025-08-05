import { MenuItem } from '@spectrum-web-components/menu'
import { css } from 'lit'
import { customElement } from 'lit-element'

@customElement('dt-app-menu-item')
export class AppMenuItem extends MenuItem {
    static get styles() {
        return [
            super.styles,
            css`
                :host {
                    border-left: 2px solid transparent; /* Transparent border in non-hover state */
                    transition: border-color 0.3s ease; /* Smooth transition for the border color */
                }

                @media (prefers-color-scheme: light) {
                    :host(:hover) {
                        background-color: hsl(209, 72%, 87%);
                        border-left: 2px solid hsla(221, 94%, 47%, 1);
                    }
                }
                @media (prefers-color-scheme: dark) {
                    :host(:hover) {
                        background-color: rgb(80 82 83);
                        border-left: 2px solid #e7e9ee;
                    }
                }
            `,
        ]
    }
}
