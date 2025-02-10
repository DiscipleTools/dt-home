<?php

/**
 * @var string $tab
 * @var string $link
 * @var string $page_title
 * @var array $reports
 */
$this->layout( 'layouts/settings', compact( 'tab', 'link', 'page_title' ) )
?>


<?php
foreach ( $reports ?? [] as $report_key => $report ) {
    ?>
    <h1 style="margin-top: 20px; margin-bottom: 20px;"><?php echo esc_attr( $report['label'] ) ?></h1>
    <h2 class="nav-tab-wrapper nav-tab-analytics-reports-wrapper">
        <input id="default_metric" type="hidden" value="<?php echo esc_attr( $report['default_metric'] ) ?>" />
        <?php
        foreach ( $report['metrics'] ?? [] as $metric ) {
            ?>
            <a href="#" class="nav-tab analytics-reports-tab" data-tab="<?php echo esc_attr( $report['tab'] ) ?>" data-metric="<?php echo esc_attr( $metric['metric'] ) ?>" data-chart_type="<?php echo esc_attr( $metric['chart_type'] ) ?>" data-calculation_type="<?php echo esc_attr( $metric['calculation_type'] ) ?>" data-events="<?php echo esc_attr( implode( ',', $metric['events'] ?? [] ) ) ?>" data-filters="<?php echo esc_attr( implode( ',', $metric['filters'] ?? [] ) ) ?>"><?php echo esc_attr( $metric['label'] ) ?></a>
            <?php
        }
        ?>
    </h2>
    <div id="<?php echo esc_attr( $report['tab'] ) ?>">
        <div id="analytics_reports_tab_filters"></div>
        <div id="analytics_reports_tab_charts"></div>
    </div>
    <?php
}
?>
