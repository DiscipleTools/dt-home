import { css, html, LitElement } from "lit";
import { customElement, property } from "lit/decorators.js";
import { magic_url, translate } from "../helpers.js";

@customElement("app-form-modal")
class AppFormModal extends LitElement {
  @property({ type: Boolean }) open = false;
  @property({ type: String }) modelName = "";
  @property({ type: Object }) appData = {};
  @property({ type: Boolean }) validationError = false;
  @property({ type: String }) error = "";

  static styles = css`
    /* CSS for the modal */

    :host {
      --dt-fields-background-color: #f2f2f2;
      --dt-text-background-color: var(--dt-fields-background-color);
      --dt-form-background-color: var(--dt-fields-background-color);
    }

    .trigger {
      text-align: center;
      padding: 7px 13px;
      background: #e34b4b;
      color: #fff;
      font-size: 15px;
      outline: none;
      border: none;
      border-radius: 5px;
    }

    .trigger-submit {
      text-align: center;
      padding: 7px 13px;
      background: #4caf50;
      color: #fff;
      font-size: 15px;
      outline: none;
      border: none;
      border-radius: 5px;
    }

    @media (prefers-color-scheme: dark) {
      .text-color {
        color: #fff;
      }

      :host {
        --dt-label-color: #fff;
        --dt-fields-background-color: #292929;
        --dt-text-border-color: #292929;
        --dt-form-text-color: #fff;
        --dt-single-select-text-color: #fff;
        --dt-form-border-color: #292929;
        --dt-text-disabled-background-color: #292929;
        --close-icon-background-color: #292929;
        --close-icon-color: #fff;
        --close-icon-background-color-hover: #2929297d;
        --modal-background-color: rgba(25, 24, 24, 0.9);
      }
    }

    @media (prefers-color-scheme: light) {
      :host {
        --close-icon-background-color: lightgray;
        --close-icon-background-color-hover: darkgray;
        --modal-background-color: rgba(211, 211, 211, 0.85);
      }
    }

    .modal-content {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background-color: var(--spectrum-gray-100);
      padding: 1rem 1.5rem;
      width: 24rem;
      border-radius: 0.5rem;
    }

    .modal {
      position: fixed;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      opacity: 0;
      visibility: hidden;
      transform: scale(1.1);
      transition:
        visibility 0s linear 0.25s,
        opacity 0.25s 0s,
        transform 0.25s;
      z-index: 10000; /* Ensure it appears above other content */
    }

    .modal-content {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background-color: var(--spectrum-gray-100);
      padding: 1rem 1.5rem;
      width: 24rem;
      border-radius: 0.5rem;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }

    .modal-close {
      float: right;
      width: 1.5rem;
      line-height: 1.5rem;
      text-align: center;
      cursor: pointer;
      border-radius: 0.25rem;
      background-color: var(--close-icon-background-color);
      color: var(--close-icon-color);
    }

    .modal-close:hover {
      background-color: var(--close-icon-background-color-hover);
    }

    .show-modal {
      background-color: var(--modal-background-color);
      opacity: 1;
      visibility: visible;
      transform: scale(1);
      transition:
        visibility 0s linear 0s,
        opacity 0.25s 0s,
        transform 0.25s;
    }

    @media (max-width: 600px) {
      .modal-content {
        width: 90%;
        padding: 1rem;
      }
    }

    .error {
      border-color: red; /* or use background-color if preferred */
    }

    #form-alert {
      color: red;
      font-weight: bold;
      margin-bottom: 10px;
    }

    .modal-title > h3 {
      display: contents;
      font-size: 18px;
      font-style: poppins;
    }
  `;

  /**
   * close the modal
   */
  closeModal() {
    this.open = false;
    this.classList.remove("modal-open");
    this.clearForm();
    this.resetValidationError();
    this.requestUpdate();
    this.dispatchEvent(
      new CustomEvent("modal-closed", { bubbles: true, composed: true }),
    );
  }

  /**
   * Toggle the modal
   */

  toggleModal() {
    this.open = !this.open;
    this.requestUpdate();
  }

  /**
   * Clear the form fields
   */
  clearForm() {
    const form = this.shadowRoot.querySelector("form");

    if (form) {
      form.reset();
      // Manually clear custom elements
      const customElements = form.querySelectorAll(
        "dt-text, dt-single-select, icon-picker",
      );
      customElements.forEach((element) => {
        element.value = "";
      });
    }
  }

  /**
   * Update the slug field based on the name field
   */

  updateSlugField() {
    if (this.appData.slug) {
      // If the form is in edit mode, this not change the slug
      return;
    }
    const nameField = this.shadowRoot.querySelector('dt-text[name="name"]');
    const slugField = this.shadowRoot.querySelector('dt-text[name="slug"]');

    if (nameField && slugField) {
      const nameValue = nameField.value.trim();
      const slugValue = nameValue
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, "_") // Replace non-alphanumeric characters with underscores
        .replace(/^_+|_+$/g, ""); // Remove leading and trailing underscores
      slugField.value = slugValue;
    }
  }

  /**
   *  Validate the form fields
   * @returns {boolean}
   */

  validateForm() {
    const form = this.shadowRoot.querySelector("form");
    if (!form) return false;

    const requiredFields = form.querySelectorAll("[require]");
    let isValid = true;

    for (const field of requiredFields) {
      const value = field.value && field.value.trim();

      if (!value) {
        isValid = false;
        field.setAttribute("aria-invalid", "true");
        field.classList.add("error");
        const label = field.getAttribute("label") || field.name;
        this.validationError = true;
        this.error = `The ${label} is required.`;
        break; // Stop after the first invalid field
      } else {
        field.removeAttribute("aria-invalid");
        field.classList.remove("error");
      }
    }

    // Trigger native form validation
    if (!form.reportValidity()) {
      isValid = false;
    }

    return isValid;
  }

  /**
   * Handle form submission event
   * @param e
   */

  handleSubmit(e) {
    e.preventDefault();
    const isFormValid = this.validateForm();

    if (isFormValid) {
      this.handleFormSubmit(e);
    } else {
      console.log("Form is invalid. Please fill in the required fields.");
    }
  }

  /**
   * Handle form submission
   * @param e
   */

  handleFormSubmit(e) {
    const form = e.target;
    const formObject = this.getFormObject(form);

    const isEdit = !!this.appData.slug;
    const url = isEdit
      ? magic_url(`update-app/${this.appData.slug}`)
      : magic_url("store-app");

    fetch(url, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-WP-Nonce": $home.nonce,
      },
      body: JSON.stringify(formObject),
    })
      .then((response) => {
        if (response.ok) {
          return response.json(); // Parse the response JSON
        } else {
          // Handle error
          throw new Error("Network response was not ok.");
        }
      })
      .then((data) => {
        const event = new CustomEvent("app-unhidden", {
          detail: { app: data.app }, // Use the app_data from the response
          bubbles: true, // Allows the event to bubble up through the DOM
          composed: true, // Allows the event to cross the shadow DOM boundary
        });
        document.dispatchEvent(event);

        const eventApp = new CustomEvent("app-return", {
          detail: { app: data.app },
          bubbles: true,
          composed: true,
        });
        document.dispatchEvent(eventApp);
        this.closeModal();
      })
      .catch((error) => {
        console.error("Error:", error);
      });
  }

  /**
   * Reset validation error
   */
  resetValidationError() {
    this.validationError = false;
    this.error = "";
    return this.requestUpdate();
  }

  /**
   * Get form object
   * @param form
   * @returns {{}}
   */

  getFormObject(form) {
    const formObject = {};

    const fields = [
      { selector: 'dt-text[name="name"]', key: "name" },
      { selector: 'dt-single-select[name="type"]', key: "type" },
      {
        selector: 'input[name="open_in_new_tab"]',
        key: "open_in_new_tab",
        isCheckbox: true,
      },
      { selector: 'icon-picker[name="icon"]', key: "icon" },
      { selector: 'dt-text[name="url"]', key: "url" },
      { selector: 'dt-text[name="slug"]', key: "slug" },
    ];

    fields.forEach(({ selector, key, isCheckbox }) => {
      const field = form.querySelector(selector);
      if (field) {
        formObject[key] = isCheckbox
          ? field.checked
            ? "1"
            : "0"
          : field.value;
      }
    });
    formObject["creation_type"] = this.appData.creation_type;

    return formObject;
  }

  /**
   * Render the component
   * @returns {TemplateResult}
   */

  render() {
    return html`
      <div class="modal ${this.open ? "show-modal" : ""}" id="customModal">
        <div class="modal-content">
          <div class="modal-title" style="margin-bottom: 10px;">
            <h3 class="text-color">${translate(this.modelName)}</h3>
            <span class="modal-close" @click="${this.closeModal}">&times;</span>
          </div>
          ${this.validationError
            ? html`
                <dt-alert
                  context="alert"
                  dismissable
                  @click="${this.resetValidationError}"
                  style="margin-bottom: 10px;"
                >
                  ${this.error}
                </dt-alert>
              `
            : ""}
          <form @submit="${this.handleSubmit}" id="custom-form">
            <dt-text
              name="name"
              label="${translate("name_label")}"
              placeholder="${translate("name_label")}"
              require
              tabindex="1"
              .value="${this.appData.name || ""}"
              @change="${this.updateSlugField}"
            ></dt-text>
            <dt-text
              id="slug"
              label="${translate("slug_label")}"
              name="slug"
              placeholder="${translate("slug_label")}"
              require
              tabindex="5"
              .value="${this.appData.slug || ""}"
              ?disabled="${!!this.appData.slug}"
            ></dt-text>
            <dt-single-select
              name="type"
              require
              label="${translate("type_label")}"
              placeholder="${translate("select_type_label")}"
              .options="${[
                { id: "Web View", label: "Web View" },
                { id: "Link", label: "Link" },
              ]}"
              .value="${this.appData.type || ""}"
            ></dt-single-select>
            <label for="open_in_new_tab" class="text-color">
              ${translate("open_new_tab_label")}
            </label>
            <input
              type="checkbox"
              name="open_in_new_tab"
              id="open_in_new_tab"
              .checked="${this.appData.open_in_new_tab === "1"}"
            />
            <br />
            <br />
            <icon-picker
              name="icon"
              label="Icon"
              id="icon"
              placeholder="Select an icon"
              require
              slug="${this.appData.slug || ""}"
              .value="${this.appData.icon || ""}"
            ></icon-picker>
            <dt-text
              name="url"
              label="${translate("url_label")}"
              placeholder="${translate("url_label")}"
              require
              tabindex="4"
              .value="${this.appData.url || ""}"
            ></dt-text>
            <sp-button-group>
              <sp-button tabindex="6" type="submit" class="trigger-submit">
                <span>${translate("submit_label")}</span>
              </sp-button>
              <sp-button
                class="cre-ac trigger"
                variant="secondary"
                @click="${this.closeModal}"
              >
                <span>${translate("close_label")}</span>
              </sp-button>
            </sp-button-group>
          </form>
        </div>
      </div>
    `;
  }
}
