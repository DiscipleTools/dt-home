import {css, html, LitElement} from 'lit';
import {customElement, property} from 'lit/decorators.js';
import * as am5 from '@amcharts/amcharts5';
import * as am5percent from '@amcharts/amcharts5/percent';

@customElement('dt-analytics-reports-chart-pie')
class AnalyticsReportsChartPie extends LitElement {

  static styles = css`
        :host {
            display: block;
            width: 100%;
            height: 300px;
        }
        .reports-chart-pie {
            width: 100%;
            height: 300px;
        }
    `;

  @property({type: Array}) reports = [];

  connectedCallback() {
    super.connectedCallback();
  }

  disconnectedCallback() {
    super.disconnectedCallback();
  }

  async firstUpdated() {
    await this.updateComplete;
    this.init();
  }

  init() {
    if (this.hasAttribute('reports')) {
      this.reports = JSON.parse( decodeURIComponent( this.getAttribute('reports') ) );

      const root = am5.Root.new( this.shadowRoot.getElementById( 'reports_chart_pie' ) );

      const chart = root.container.children.push(
        am5percent.PieChart.new(root, {
          layout: root.verticalLayout,
          paddingBottom: 50, // Adjust the padding as needed
        })
      );

      const pie_series = chart.series.push(
        am5percent.PieSeries.new(root, {
          valueField: 'value',
          categoryField: 'event',
        })
      );

      pie_series.data.setAll(this.reports);

      // Add legend
      const legend = chart.children.push(
        am5.Legend.new(root, {
          centerX: am5.p50,
          x: am5.p50,
        })
      );

      legend.data.setAll(pie_series.dataItems);
    }
  }

  render() {
    return html`
      <div id="reports_chart_pie" class="reports-chart-pie"></div>
    `;
  }
}
