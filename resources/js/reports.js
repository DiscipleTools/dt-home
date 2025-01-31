import { css, html, LitElement } from 'lit'
import { customElement } from 'lit/decorators.js'
import * as am5 from '@amcharts/amcharts5'
import * as am5xy from '@amcharts/amcharts5/xy'
import * as am5percent from '@amcharts/amcharts5/percent'
import am5themes_Animated from '@amcharts/amcharts5/themes/Animated'
import { route_url } from './helpers.js'

@customElement('dt-combined-chart')
class CombinedChart extends LitElement {
    static styles = css`
        :host {
            display: block;
            width: 100%;
            height: 1200px; /* Adjusted height to accommodate three charts */
        }

        .trainingchartdiv {
            height: 150px;
        }

        .piechartdiv {
            width: 100%;
            height: 33%;
        }

        .chartdiv {
            width: 80%;
            height: 33%; /* Each chart takes one-third of the height */
        }
    `

    async firstUpdated() {
        await this.updateComplete
        this.fetchData()
    }

    async fetchData() {
        const url = route_url(
            '/analytics-reports?metrics=login,total-active-apps-count,total-active-custom-apps-count,total-active-coded-apps-count,total-deleted-coded-apps-count,total-active-training-videos-count,login-error,logout,admin-app-creation'
        )

        try {
            const response = await fetch(url)
            const data = await response.json()
            this.updateCharts(data.reports)
        } catch (error) {
            console.error('Error fetching data:', error)
        }
    }

    updateCharts(data) {
        this.createAppsChart(data)
        this.createLoginChart(data)
        this.createTrainingVideosChart(data)
    }

    createAppsChart(data) {
        let root = am5.Root.new(this.shadowRoot.getElementById('piechartdiv'))

        let chart = root.container.children.push(
            am5percent.PieChart.new(root, {
                layout: root.verticalLayout,
                paddingBottom: 50, // Adjust the padding as needed
            })
        )

        let pieData = [
            {
                apps: `Total Active Apps (${data['total-active-apps-count']})`,
                appcounts: data['total-active-apps-count'],
            },
            {
                apps: `Total Active Custom Apps (${data['total-active-custom-apps-count']})`,
                appcounts: data['total-active-custom-apps-count'],
            },
            {
                apps: `Total Active Coded Apps (${data['total-active-coded-apps-count']})`,
                appcounts: data['total-active-coded-apps-count'],
            },
            {
                apps: `Total Deleted Coded Apps (${data['total-deleted-coded-apps-count']})`,
                appcounts: data['total-deleted-coded-apps-count'],
            },
            {
                apps: `Total Admin Apps (${data['admin-app-creation']})`,
                appcounts: data['admin-app-creation'],
            },
        ]

        let pieSeries = chart.series.push(
            am5percent.PieSeries.new(root, {
                valueField: 'appcounts',
                categoryField: 'apps',
            })
        )

        pieSeries.slices.template.adapters.add('fill', function (fill, target) {
            if (
                target.dataItem.dataContext.apps.includes(
                    'Total Deleted Coded Apps'
                )
            ) {
                return am5.color('#dd5a5a') // Set color to red for "Total Deleted Coded Apps"
            }
            return fill
        })

        pieSeries.data.setAll(pieData)

        // Add legend
        let legend = chart.children.push(
            am5.Legend.new(root, {
                centerX: am5.p50,
                x: am5.p50,
            })
        )

        legend.data.setAll(pieSeries.dataItems)
    }

    createLoginChart(data) {
        let root = am5.Root.new(this.shadowRoot.getElementById('barchartdiv'))

        let chart = root.container.children.push(
            am5xy.XYChart.new(root, {
                panX: true,
                panY: true,
                wheelX: 'panX',
                wheelY: 'zoomX',
                pinchZoomX: true,
                paddingLeft: 0,
                paddingRight: 1,
            })
        )

        let cursor = chart.set('cursor', am5xy.XYCursor.new(root, {}))
        cursor.lineY.set('visible', false)

        let xRenderer = am5xy.AxisRendererX.new(root, {
            minGridDistance: 30,
            minorGridEnabled: true,
        })

        xRenderer.labels.template.setAll({
            rotation: -90,
            centerY: am5.p50,
            centerX: am5.p100,
            paddingRight: 15,
        })

        xRenderer.grid.template.setAll({
            location: 1,
        })

        let xAxis = chart.xAxes.push(
            am5xy.CategoryAxis.new(root, {
                maxDeviation: 0.3,
                categoryField: 'metric',
                renderer: xRenderer,
                tooltip: am5.Tooltip.new(root, {}),
            })
        )

        let yRenderer = am5xy.AxisRendererY.new(root, {
            strokeOpacity: 0.1,
        })

        let yAxis = chart.yAxes.push(
            am5xy.ValueAxis.new(root, {
                maxDeviation: 0.3,
                renderer: yRenderer,
            })
        )

        let series = chart.series.push(
            am5xy.ColumnSeries.new(root, {
                name: 'Series 1',
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: 'value',
                sequencedInterpolation: true,
                categoryXField: 'metric',
                tooltip: am5.Tooltip.new(root, {
                    labelText: '{valueY}',
                }),
            })
        )

        series.columns.template.setAll({
            cornerRadiusTL: 5,
            cornerRadiusTR: 5,
            strokeOpacity: 0,
            maxWidth: 50, // Reduce the bar width
        })

        series.columns.template.adapters.add('fill', function (fill, target) {
            if (target.dataItem.dataContext.metric === 'Login Error') {
                return am5.color('#dd5a5a') // Set color to red for "Login Error"
            }
            return chart.get('colors').getIndex(series.columns.indexOf(target))
        })

        series.columns.template.adapters.add(
            'stroke',
            function (stroke, target) {
                if (target.dataItem.dataContext.metric === 'Login Error') {
                    return am5.color('#dd5a5a') // Set color to red for "Login Error"
                }
                return chart
                    .get('colors')
                    .getIndex(series.columns.indexOf(target))
            }
        )

        let barData = [
            { metric: 'Login', value: data['login'] },
            { metric: 'Logout', value: data['logout'] },
            { metric: 'Login Error', value: data['login-error'] },
        ]

        xAxis.data.setAll(barData)
        series.data.setAll(barData)

        series.appear(1000)
        chart.appear(1000, 100)

        // Add legend
        let legend = chart.children.push(
            am5.Legend.new(root, {
                centerX: am5.p50,
                x: am5.p50,
            })
        )

        legend.data.setAll(chart.series.values)
    }

    createTrainingVideosChart(data) {
        let root = am5.Root.new(
            this.shadowRoot.getElementById('trainingvideoschartdiv')
        )

        root.setThemes([am5themes_Animated.new(root)])

        let chart = root.container.children.push(
            am5xy.XYChart.new(root, {
                panX: false,
                panY: false,
                wheelX: 'panX',
                wheelY: 'zoomX',
                paddingLeft: 0,
                layout: root.verticalLayout,
            })
        )

        let trainingVideosData = [
            {
                trainingVideosLabel: 'Training Videos',
                activeTrainingVideos:
                    data['total-active-training-videos-count'],
            },
        ]

        let yAxis = chart.yAxes.push(
            am5xy.CategoryAxis.new(root, {
                categoryField: 'trainingVideosLabel',
                renderer: am5xy.AxisRendererY.new(root, {
                    inversed: true,
                    cellStartLocation: 0.1,
                    cellEndLocation: 0.9,
                    minorGridEnabled: true,
                }),
            })
        )

        yAxis.data.setAll(trainingVideosData)

        let xAxis = chart.xAxes.push(
            am5xy.ValueAxis.new(root, {
                renderer: am5xy.AxisRendererX.new(root, {
                    strokeOpacity: 0.1,
                    minGridDistance: 50,
                }),
                min: 0,
            })
        )

        function createSeries(field, name) {
            let series = chart.series.push(
                am5xy.ColumnSeries.new(root, {
                    name: name,
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueXField: field,
                    categoryYField: 'trainingVideosLabel',
                    sequencedInterpolation: true,
                    tooltip: am5.Tooltip.new(root, {
                        pointerOrientation: 'horizontal',
                        labelText: '[bold]{name}[/]\n{categoryY}: {valueX}',
                    }),
                })
            )

            series.columns.template.setAll({
                height: am5.p100,
                strokeOpacity: 0,
            })

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
            })

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
            })

            series.data.setAll(trainingVideosData)
            series.appear()

            return series
        }

        createSeries('activeTrainingVideos', 'Training Videos')

        let legend = chart.children.push(
            am5.Legend.new(root, {
                centerX: am5.p50,
                x: am5.p50,
            })
        )

        legend.data.setAll(chart.series.values)

        let cursor = chart.set(
            'cursor',
            am5xy.XYCursor.new(root, {
                behavior: 'zoomY',
            })
        )
        cursor.lineY.set('forceHidden', true)
        cursor.lineX.set('forceHidden', true)

        chart.appear(1000, 100)
    }

    render() {
        return html`
            <h2>Login Count</h2>
            <div id="barchartdiv" class="chartdiv"></div>
            <hr />
            <h2>Apps Count</h2>
            <div id="piechartdiv" class="piechartdiv"></div>
            <hr />
            <h2>Training Videos Count</h2>
            <div id="trainingvideoschartdiv" class="trainingchartdiv"></div>
        `
    }
}
