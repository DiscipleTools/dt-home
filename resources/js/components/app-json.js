import {customElement} from "lit-element";
import {html, LitElement} from "lit";

@customElement('dt-home-app-json')
class AppJson extends LitElement {
  static properties = {
    apps: {type: Array}
  }

  display_json_output() {
    return JSON.stringify(this.apps, null, 4);
  }

  build_options() {
    let options = [];
    Object.entries(this.apps).forEach(([slug, app]) => {
      if (slug && app['name']) {
        options.push({
          'id': slug,
          'label': app['name']
        });
      }
    });

    return JSON.stringify(options);
  }

  handle_change(e) {

    // Obtain handle onto json pre element.
    const app_json_pre_element = this.shadowRoot.getElementById('app_json_output');
    if (app_json_pre_element) {
      let app_json = "";

      // Ensure we have a new value and corresponding slug entry.
      if (e?.detail?.newValue && this.apps[e?.detail?.newValue]) {
        const slug = e?.detail?.newValue;

        // Package corresponding slug app settings.
        let app = {};
        app[ slug ] = this.apps[e?.detail?.newValue];

        // Convert packaged app settings into json string.
        app_json = JSON.stringify(app, null, 4);
      }

      // Update json pre element html with selected app details.
      app_json_pre_element.innerHTML = app_json;
    }
  }

  render() {
    return html`
      <dt-tile>
          <!-- <dt-single-select options='${this.build_options()}' @change="${this.handle_change}
          "></dt-single-select> -->
        <div style="min-width: 100%; overflow: auto;">
          <pre id="app_json_output">${this.display_json_output()}</pre>
        </div>
      </dt-tile>
    `;
  }
}
