import {css, html, LitElement} from 'lit';
import {customElement, property} from 'lit/decorators.js';
import * as am5 from '@amcharts/amcharts5';
import * as am5xy from "@amcharts/amcharts5/xy.js";
import am5themes_Animated from '@amcharts/amcharts5/themes/Animated';

@customElement('dt-analytics-reports-chart-h-bar')
class AnalyticsReportsChartHBar extends LitElement {

  static styles = css`
        :host {
            display: block;
            width: 100%;
            height: 300px;
        }
        .reports-chart-h-bar {
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

      const root = am5.Root.new(this.shadowRoot.getElementById('reports_chart_h_bar'));

      root.setThemes([am5themes_Animated.new(root)]);

      const chart = root.container.children.push(
        am5xy.XYChart.new(root, {
          panX: false,
          panY: false,
          wheelX: 'panX',
          wheelY: 'zoomX',
          paddingLeft: 0,
          layout: root.verticalLayout,
        })
      );

      const yRenderer = am5xy.AxisRendererY.new(root, {
        inversed: true,
        cellStartLocation: 0.1,
        cellEndLocation: 0.9,
        minorGridEnabled: true
      });

      yRenderer.labels.template.setAll({
        rotation: 0,
        maxWidth: 100,
        oversizedBehavior: "truncate",
        textAlign: "center"
      });

      const yAxis = chart.yAxes.push(
        am5xy.CategoryAxis.new(root, {
          categoryField: 'event',
          renderer: yRenderer
        })
      );

      yAxis.data.setAll(this.reports);

      const xAxis = chart.xAxes.push(
        am5xy.ValueAxis.new(root, {
          renderer: am5xy.AxisRendererX.new(root, {
            strokeOpacity: 0.1,
            minGridDistance: 50,
          }),
          min: 0,
        })
      );

      function create_series(name, field, data) {
        const series = chart.series.push(
          am5xy.ColumnSeries.new(root, {
            name: name,
            xAxis: xAxis,
            yAxis: yAxis,
            valueXField: field,
            categoryYField: 'event',
            sequencedInterpolation: true,
            tooltip: am5.Tooltip.new(root, {
              pointerOrientation: 'horizontal',
              labelText: '[bold]{name}[/]\n{categoryY}: {valueX}',
            }),
          })
        );

        series.columns.template.setAll({
          height: am5.p100,
          strokeOpacity: 0,
        });

        series.bullets.push(function () {
          return am5.Bullet.new(root, {
            locationX: 1,
            locationY: 0.5,
            sprite: am5.Label.new(root, {
              centerY: am5.p50,
              text: '{valueX}',
              populateText: true,
            }),
          })
        });

        series.bullets.push(function () {
          return am5.Bullet.new(root, {
            locationX: 1,
            locationY: 0.5,
            sprite: am5.Label.new(root, {
              centerX: am5.p100,
              centerY: am5.p50,
              text: '{name}',
              fill: am5.color(0xffffff),
              populateText: true,
            }),
          })
        });

        series.data.setAll(data);
        series.appear();

        return series;
      }

      create_series(null, 'value', this.reports);

      const legend = chart.children.push(
        am5.Legend.new(root, {
          centerX: am5.p50,
          x: am5.p50,
        })
      );

      legend.data.setAll(chart.series.values)

      const cursor = chart.set(
        'cursor',
        am5xy.XYCursor.new(root, {
          behavior: 'zoomY',
        })
      );

      cursor.lineY.set('forceHidden', true);
      cursor.lineX.set('forceHidden', true);

      chart.appear(1000, 100);
    }
  }

  render() {
    return html`
      <div id="reports_chart_h_bar" class="reports-chart-h-bar"></div>
    `;
  }
}
