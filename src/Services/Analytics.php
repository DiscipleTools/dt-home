<?php

namespace DT\Home\Services;

use OpenTelemetry\API\Globals;
use OpenTelemetry\API\Trace\Propagation\TraceContextPropagator;
use OpenTelemetry\Contrib\Otlp\LogsExporter;
use OpenTelemetry\Contrib\Otlp\MetricExporter;
use OpenTelemetry\Contrib\Otlp\OtlpHttpTransportFactory;
use OpenTelemetry\Contrib\Otlp\SpanExporter;
use OpenTelemetry\SDK\Common\Attribute\Attributes;
use OpenTelemetry\SDK\Logs\LoggerProvider;
use OpenTelemetry\SDK\Logs\Processor\SimpleLogRecordProcessor;
use OpenTelemetry\SDK\Metrics\MeterProvider;
use OpenTelemetry\SDK\Metrics\MetricReader\ExportingReader;
use OpenTelemetry\SDK\Resource\ResourceInfo;
use OpenTelemetry\SDK\Resource\ResourceInfoFactory;
use OpenTelemetry\SDK\Sdk;
use OpenTelemetry\SDK\Trace\Sampler\AlwaysOnSampler;
use OpenTelemetry\SDK\Trace\Sampler\ParentBased;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProvider;
use OpenTelemetry\SemConv\ResourceAttributes;
use function DT\Home\config;
use function DT\Home\container;
use function DT\Home\get_plugin_option;
use function DT\Home\set_plugin_option;

/**
 * Manage Home Screen Telemetry Events & Analytics
 */
class Analytics {

    public const DT_HOME_PLUGIN_NAME = 'dt-home';
    public const DT_HOME_PLUGIN_VERSION = '1.0.3';
    public const OPTION_KEY_ANALYTICS_PERMISSION = 'dt_home_analytics_permission';
    public const OPTION_KEY_ANALYTICS_EXPORT_ENDPOINTS = 'dt_home_analytics_export_endpoints';
    public const OPTION_KEY_ANALYTICS_RESOURCE_ATTRIBUTES = 'dt_home_analytics_resource_attributes';

    private array $events = [];

    public function __construct() {
    }

    /**
     * Determine if analytics permission is enabled.
     * @return bool
     */
    public function is_enabled(): bool {
        return get_plugin_option( self::OPTION_KEY_ANALYTICS_PERMISSION, false );
    }

    /**
     * Update analytics permission enabled state.
     * @param bool $enable
     * @return bool
     */
    public function enabled( bool $enable ): bool {
        return set_plugin_option( self::OPTION_KEY_ANALYTICS_PERMISSION, $enable );
    }

    /**
     * Fetch OpenTelemetry settings.
     * @return array
     */
    private function ot_settings(): array {
        return [
            'export_endpoints' => get_plugin_option( self::OPTION_KEY_ANALYTICS_EXPORT_ENDPOINTS, config( 'analytics.export_endpoints.honeycomb' ) ),
            'resource_attributes' => [
                ResourceAttributes::SERVICE_NAMESPACE => 'DT\Home',
                ResourceAttributes::SERVICE_NAME => self::DT_HOME_PLUGIN_NAME,
                ResourceAttributes::SERVICE_VERSION => self::DT_HOME_PLUGIN_VERSION,
                ResourceAttributes::DEPLOYMENT_ENVIRONMENT => 'production'
            ]
        ];
    }

    /**
     * Initialise telemetry framework and instrumentation.
     * @return void
     */
    public function init(): void {
        /*
         * Ensure analytics permission has been granted, before proceeding.
         */

        if ( !$this->is_enabled() ) {
            return;
        }

        /*
         * Initialise open telemetry resource attributes.
         */

        $settings = $this->ot_settings();
        $resources = ResourceInfoFactory::emptyResource()->merge( ResourceInfo::create( Attributes::create(
            $settings['resource_attributes']
        ) ) );

        /*
         * Initialise domain exporters and transport mechanisms.
         */

        $trace_settings = $settings['export_endpoints']['traces'];
        $trace_exporter = new SpanExporter(
            ( new OtlpHttpTransportFactory() )->create( $trace_settings['endpoint'], $trace_settings['content_type'], $trace_settings['headers'] )
        );

        $metric_settings = $settings['export_endpoints']['metrics'];
        $metric_exporter = new ExportingReader(
            new MetricExporter(
                ( new OtlpHttpTransportFactory() )->create( $metric_settings['endpoint'], $metric_settings['content_type'], $metric_settings['headers'] )
            )
        );

        $log_settings = $settings['export_endpoints']['logs'];
        $log_exporter = new LogsExporter(
            ( new OtlpHttpTransportFactory() )->create( $log_settings['endpoint'], $log_settings['content_type'], $log_settings['headers'] )
        );

        /*
         * Initialise domain providers.
         */

        $trace_provider = TracerProvider::builder()
            ->addSpanProcessor(
                new SimpleSpanProcessor( $trace_exporter )
            )
            ->setResource( $resources )
            ->setSampler( new ParentBased( new AlwaysOnSampler() ) )
            ->build();

        $metric_provider = MeterProvider::builder()
            ->setResource( $resources )
            ->addReader( $metric_exporter )
            ->build();

        $log_provider = LoggerProvider::builder()
            ->setResource( $resources )
            ->addLogRecordProcessor(
                new SimpleLogRecordProcessor( $log_exporter )
            )
            ->build();

        /*
         * Final build and global registration of domain providers.
         */

        Sdk::builder()
            ->setTracerProvider( $trace_provider )
            ->setMeterProvider( $metric_provider )
            ->setLoggerProvider( $log_provider )
            ->setPropagator( TraceContextPropagator::getInstance() )
            ->setAutoShutdown( true )
            ->buildAndRegisterGlobal();
    }

    /**
     * Handle analytical event requests.
     * @return bool
     */
    public function event( $name, $properties = [] ): bool {
        /*
         * Ensure analytics permission has been granted, before proceeding.
         */

        if ( !$this->is_enabled() ) {
            return false;
        }

        /*
         * Adjust incoming properties to be processed.
         */

        $properties = array_merge( [
            'action' => null,
            'lib_name' => null,
            'lib_version' => self::DT_HOME_PLUGIN_VERSION,
            'evt_name' => $name,
            'schema' => 'https://opentelemetry.io/schemas/1.24.0',
            'attributes' => []
        ], $properties );

        if ( !isset( $properties['action'] ) ) {
            return false;
        }

        /*
         * Ensure to include a hashed source id; to aid backend report grouping,
         * whilst adhering to anonymity policy.
         */

        if ( !isset( $properties['attributes'] ) ) {
            $properties['attributes'] = [];
        }

        $properties['attributes']['src.id'] = wp_hash( get_site_url() );

        /*
         * Handle event request accordingly based on specified action.
         */

        $result = false;
        switch ( $properties['action'] ) {
            case 'start':

                // Ensure required start action properties are present.
                if ( isset( $properties['lib_name'], $properties['lib_version'], $properties['schema'], $properties['evt_name'] ) ) {

                    $lib_name = $properties['lib_name'];
                    $lib_version = $properties['lib_version'];
                    $schema = $properties['schema'];
                    $evt_name = $properties['evt_name'];

                    // Instantiate and store a new event span for given name.
                    $this->events[$evt_name] = Globals::tracerProvider()->getTracer(
                        $lib_name,
                        $lib_version,
                        $schema,
                        $properties['attributes'] ?? []
                    )->spanBuilder( $evt_name )->startSpan();

                    // Capture returning result.
                    $result = !empty( $this->events[$evt_name] );
                }
                break;
            case 'stop':

                // End corresponding event span, to trigger telemetry backend export.
                if ( isset( $properties['evt_name'] ) ) {
                    $evt_name = $properties['evt_name'];

                    if ( !empty( $this->events[$evt_name] ) ) {

                        // End and trigger data export.
                        $this->events[$evt_name]->end();

                        // Destroy telemetry span object element reference and update result variable.
                        unset( $this->events[$evt_name] );
                        $result = true;

                        // Capture completed events for local reporting.
                        container()->get( AnalyticsReporting::class )->log_event( $evt_name );
                    }
                }
                break;
            case 'snapshot':

                // Ensure required snapshot action properties are present.
                if ( isset( $properties['lib_name'], $properties['lib_version'], $properties['schema'], $properties['evt_name'] ) ){

                    $lib_name = $properties['lib_name'];
                    $lib_version = $properties['lib_version'];
                    $schema = $properties['schema'];
                    $evt_name = $properties['evt_name'];

                    // Create a quick event snapshot; which is started and immediately stopped.
                    Globals::tracerProvider()->getTracer(
                        $lib_name,
                        $lib_version,
                        $schema,
                        $properties['attributes'] ?? []
                    )->spanBuilder( $evt_name )->startSpan()->end();
                    $result = true;

                    // Capture completed events for local reporting.
                    container()->get( AnalyticsReporting::class )->log_event( $evt_name );
                }
                break;
        }

        return $result;
    }

    /**
     * Handle analytical metered metrics; typically used when
     * capturing numerical counts.
     */
    public function metric( $name, $properties = [] ): bool {
        /*
         * Ensure analytics permission has been granted, before proceeding.
         */

        if ( !$this->is_enabled() ) {
            return false;
        }

        /*
         * Adjust incoming properties to be processed.
         */

        $properties = array_merge( [
            'type' => 'counter',
            'lib_name' => null,
            'lib_version' => self::DT_HOME_PLUGIN_VERSION,
            'metric_name' => $name,
            'schema' => 'https://opentelemetry.io/schemas/1.24.0',
            'attributes' => [],
            'value' => null, // Metric float/int value; e.g. 1.5 or 150
            'unit' => null, // Unit of measure; e.g. apps
            'description' => null // Description of the metric; e.g. Total count of active apps
        ], $properties );

        if ( !isset( $properties['type'], $properties['metric_name'] ) ) {
            return false;
        }

        /*
         * Ensure to include a hashed source id; to aid backend report grouping,
         * whilst adhering to anonymity policy.
         */

        if ( !isset( $properties['attributes'] ) ) {
            $properties['attributes'] = [];
        }

        $properties['attributes']['src.id'] = wp_hash( get_site_url() );

        /*
         * Handle meter request accordingly based on specified type.
         */

        $result = false;
        switch ( $properties['type'] ) {
            case 'counter':

                // Ensure required counter properties are present.
                if ( isset( $properties['lib_name'], $properties['lib_version'], $properties['schema'], $properties['value'] ) ) {

                    $lib_name = $properties['lib_name'];
                    $lib_version = $properties['lib_version'];
                    $schema = $properties['schema'];
                    $metric_name = $properties['metric_name'];
                    $metric_value = $properties['value'];
                    $unit  = $properties['unit'] ?? null;
                    $description = $properties['description'] ?? null;

                    // Ensure any available units & descriptions are also included.
                    if ( !empty( $unit ) ) {
                        $properties['attributes']['unit'] = $unit;
                    }
                    if ( !empty( $description ) ) {
                        $properties['attributes']['description'] = $description;
                    }

                    $meter_provider = Globals::meterProvider();
                    $meter_provider->getMeter(
                        $lib_name,
                        $lib_version,
                        $schema
                    )->createCounter( 'value' )->add( $metric_value, array_merge( $properties['attributes'], [
                        'name' => $metric_name,
                        'library.name' => $lib_name,
                        'library.version' => $lib_version
                    ] ) );

                    // Switch to sdk in order to force a flush.
                    if ( $meter_provider instanceof \OpenTelemetry\SDK\Metrics\MeterProviderInterface ) {
                        $meter_provider->forceFlush();
                    }

                    $result = true;

                    // Capture completed events for local reporting.
                    container()->get( AnalyticsReporting::class )->log_event( $metric_name, $metric_value );
                }
                break;
        }

        return $result;
    }
}
