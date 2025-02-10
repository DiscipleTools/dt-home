import {css, html, LitElement} from 'lit';
import {customElement, property} from 'lit/decorators.js';
import * as am5 from '@amcharts/amcharts5';
import * as am5xy from "@amcharts/amcharts5/xy.js";;

@customElement('dt-analytics-reports-chart-v-bar')
class AnalyticsReportsChartVBar extends LitElement {

  static styles = css`
        :host {
            display: block;
            width: 100%;
            height: 300px;
        }
        .reports-chart-v-bar {
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

      const root = am5.Root.new(this.shadowRoot.getElementById('reports_chart_v_bar'));

      const chart = root.container.children.push(
        am5xy.XYChart.new(root, {
          panX: true,
          panY: true,
          wheelX: 'panX',
          wheelY: 'zoomX',
          pinchZoomX: true,
          paddingLeft: 0,
          paddingRight: 1,
        })
      );

      const cursor = chart.set('cursor', am5xy.XYCursor.new(root, {}));
      cursor.lineY.set('visible', false);

      const xRenderer = am5xy.AxisRendererX.new(root, {
        minGridDistance: 30,
        minorGridEnabled: true,
      });

      xRenderer.labels.template.setAll({
        rotation: 0,
        //centerY: am5.p50,
        //centerX: am5.p100,
        //paddingRight: 15,
        maxWidth: 200,
        oversizedBehavior: "truncate",
        textAlign: "center"
      });

      xRenderer.grid.template.setAll({
        location: 1,
      });

      const xAxis = chart.xAxes.push(
        am5xy.CategoryAxis.new(root, {
          maxDeviation: 0.3,
          categoryField: 'event',
          renderer: xRenderer,
          tooltip: am5.Tooltip.new(root, {}),
        })
      );

      const yRenderer = am5xy.AxisRendererY.new(root, {
        strokeOpacity: 0.1,
      });

      const yAxis = chart.yAxes.push(
        am5xy.ValueAxis.new(root, {
          maxDeviation: 0.3,
          renderer: yRenderer,
        })
      );

      const series = chart.series.push(
        am5xy.ColumnSeries.new(root, {
          //name: 'Series 1',
          xAxis: xAxis,
          yAxis: yAxis,
          valueYField: 'value',
          sequencedInterpolation: true,
          categoryXField: 'event',
          tooltip: am5.Tooltip.new(root, {
            labelText: '{valueY}',
          }),
        })
      );

      series.columns.template.setAll({
        cornerRadiusTL: 5,
        cornerRadiusTR: 5,
        strokeOpacity: 0,
        maxWidth: 50, // Reduce the bar width
      });

      xAxis.data.setAll(this.reports);
      series.data.setAll(this.reports);

      series.appear(1000);
      chart.appear(1000, 100);

      // Add legend
      const legend = chart.children.push(
        am5.Legend.new(root, {
          centerX: am5.p50,
          x: am5.p50,
        })
      );

      legend.data.setAll(chart.series.values);
    }
  }

  render() {
    return html`
      <div id="reports_chart_v_bar" class="reports-chart-v-bar"></div>
    `;
  }
}
