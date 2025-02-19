import {css, html, LitElement} from 'lit';
import {customElement, property} from 'lit/decorators.js';

@customElement('dt-analytics-reports-filter-date-range')
class AnalyticsReportsFilterDateRange extends LitElement {

  static styles = css`
    .filter-date-range-button {
      background-color: #5959d5;
      color: #fff;
      border: none;
      border-radius: 5px;
      padding: 5px 20px 5px 20px;
      min-width: 75px;
    }

    .filter-date-range-button:hover {
      cursor: pointer;
      background-color: #9999cd;
      transition: 0.7s;
    }

    .filter-date-range-input {
      border: none;
      border-radius: 5px;
      padding: 5px 20px 5px 20px;
      min-width: 75px;
      margin-left: 5px;
      margin-right: 10px;
    }

    .filter-date-range-input:hover {
      cursor: pointer;
    }
  `;

  @property({type: String}) metric = '';

  connectedCallback() {
    super.connectedCallback();
    this.init();
  }

  disconnectedCallback() {
    super.disconnectedCallback();
  }

  init() {
    if (this.hasAttribute('metric')) {
      this.metric = this.getAttribute('metric');
    }
  }

  handleFilter(event) {
    const start_date = this.shadowRoot.getElementById('date_start').value;
    const end_date = this.shadowRoot.getElementById('date_end').value;

    this.dispatchCustomEvent('reports_date_range_filter', {
      detail: {
        metric: this.metric,
        start_date,
        start_ts: Date.parse(start_date),
        end_date,
        end_ts: Date.parse(end_date)
      }
    });
  }

  handleReset(event) {
    this.shadowRoot.getElementById('date_start').value = null;
    this.shadowRoot.getElementById('date_end').value = null;

    this.dispatchCustomEvent('reports_date_range_reset', {
      detail: {
        metric: this.metric
      }
    });
  }

  dispatchCustomEvent(name, params) {
    document.dispatchEvent(new CustomEvent(name, params));
  }

  render() {
    return html`
      <table class="widefat striped" style="margin-top: 20px; margin-bottom: 20px;">
        <tbody>
        <tr>
          <td>
            <label for="date_start">Start Date:</label>
            <input type="date" id="date_start" name="date_start" required
                   class="filter-date-range-input"
                   value=""/>
          </td>
          <td>
            <label for="date_end">End Date:</label>
            <input type="date" id="date_end" name="date_end" required
                   class="filter-date-range-input"
                   value=""/>
          </td>
          <td style="padding-left: 30px;">
            <button id="reports_filter_date_range_filter_but" class="button float-right filter-date-range-button" @click="${(event) => this.handleFilter(event)}">Filter</button>
            <button id="reports_filter_date_range_reset_but" class="button float-right filter-date-range-button" @click="${(event) => this.handleReset(event)}">Reset</button>
          </td>
        </tr>
        </tbody>
      </table>
    `;
  }
}
