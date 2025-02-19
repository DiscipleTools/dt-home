import {css, html, LitElement} from 'lit';
import {customElement, property} from 'lit/decorators.js';

@customElement('dt-analytics-reports-chart-count')
class AnalyticsReportsChartCount extends LitElement {

  @property({type: Array}) reports = [];

  connectedCallback() {
    super.connectedCallback();
    this.init();
  }

  disconnectedCallback() {
    super.disconnectedCallback();
  }

  init() {
    if (this.hasAttribute('reports')) {
      this.reports = JSON.parse( decodeURIComponent( this.getAttribute('reports') ) );
    }
  }

  render() {
    return html`
      <table style="margin-top: 40px;">
        <tbody>
        <tr>
          ${
            this.reports.map((report) => html`
              <td style="vertical-align: middle; text-align: center; padding-left: 20px;">
                <span style="font-size: 60px;">${report['value']}</span>
                <hr style="min-width: 50px; max-width: 50px;">
                <span style="color: #959595; font-size: 15px; font-weight: normal;">${report['event']}</span>
              </td>
            `)
          }
        </tr>
        </tbody>
      </table>
    `;
  }
}
