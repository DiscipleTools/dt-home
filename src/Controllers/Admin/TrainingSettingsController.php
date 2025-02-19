<?php

namespace DT\Home\Controllers\Admin;

use DT\Home\GuzzleHttp\Psr7\ServerRequest as Request;
use DT\Home\Psr\Http\Message\ResponseInterface;
use DT\Home\Services\Analytics;
use DT\Home\Services\Trainings;
use DT\Home\Sources\Trainings as TrainingsSource;
use function DT\Home\container;
use function DT\Home\extract_request_input;
use function DT\Home\redirect;
use function DT\Home\sanitize_youtube_iframe;
use function DT\Home\view;

class TrainingSettingsController
{
    private Trainings $trainings;
    private TrainingsSource $trainings_source;

    public function __construct( Trainings $trainings, TrainingsSource $source )
    {
        $this->trainings = $trainings;
        $this->trainings_source = $source;
    }

    /**
     * Show the training settings page
     *
     * @return ResponseInterface
     */
    public function show()
    {

        $tab = "training";
        $link = 'admin.php?page=dt_home&tab=';
        $page_title = "Home Settings";

        $data = $this->trainings_source->all();

        return view( "settings/training", compact( 'tab', 'link', 'page_title', 'data' ) );
    }


    /**
     * Creates a new training and returns a view with the necessary data.
     *
     * @return ResponseInterface The view with the necessary data.
     */
    public function create()
    {
        $tab = "training";
        $link = 'admin.php?page=dt_home&tab=';
        $page_title = "Home Settings";

        return view( "settings/training/create", compact( 'tab', 'link', 'page_title' ) );
    }

    /**
     * Edit method for the TrainingController.
     *
     * Retrieves the existing training data based on the provided ID and displays the edit page.
     * If the ID is null or no existing data is found, it redirects back to the training tab in the admin panel.
     *
     * @param Request $request The request object.
     * @param array $params The parameters (including the ID) passed to the route.
     *
     * @return ResponseInterface The response object.
     */
    public function edit( Request $request, $params )
    {
        $id = $params['id'] ?? null;
        $edit_id = isset( $id ) ? intval( $id ) : 0;

        if ( !$edit_id ) {
            return redirect( 'admin.php?page=dt_home&tab=training' );
        }

        // Retrieve the existing data based on $edit_id
        $existing_data = $this->trainings_source->find( $edit_id );



        if ( !$existing_data ) {
            return redirect( 'admin.php?page=dt_home&tab=training' );
        }

        $tab = "training";
        $link = 'admin.php?page=dt_home&tab=';
        $page_title = "Home Settings";

        return view( "settings/training/edit", compact( 'existing_data', 'link', 'tab', 'page_title' ) );
    }


    /**
     * Updates a training entry based on the input data.
     *
     * @param Request $request The HTTP request object.
     * @return ResponseInterface The HTTP response object.
     */
    public function update( Request $request, $params )
    {
        $input = extract_request_input( $request );
        $name = sanitize_text_field( $input['name'] ?? '' );
        $embed_video = sanitize_youtube_iframe( $input['embed_video'] ?? '' );
        $anchor = sanitize_text_field( $input['anchor'] ?? '' );
        $sort = intval( $input['sort'] ?? 0 );
        $edit_id = intval( $params['id'] ?? 0 );
        $training = $this->trainings_source->find( $edit_id );

        $this->trainings_source->update( $edit_id, array_merge( $training, [
            'name' => $name,
            'embed_video' => $embed_video,
            'anchor' => $anchor,
            'sort' => $sort,
        ] ) );

        // Save and return updated bool result.
        return redirect( 'admin.php?page=dt_home&tab=training&updated=' . ( $this->trainings_source->save( $this->trainings_source->fetch_for_save() ) ? 'true' : 'false' ) );
    }


    /**
     * Stores a new training record in the database.
     *
     * @param Request $request The HTTP request.
     *
     * @return ResponseInterface
     */
    public function store( Request $request )
    {
        // Retrieve form data
        $input = extract_request_input( $request );
        $name = sanitize_text_field( $input['name'] ?? '' );
        $embed_video = sanitize_youtube_iframe( $input['embed_video'] ?? '' );
        $anchor = sanitize_text_field( $input['anchor'] ?? '' );
        $sort = intval( $input['sort'] ?? 0 );

        // Prepare the data to be stored
        $training_data = [
            'name' => $name,
            'embed_video' => $embed_video,
            'anchor' => $anchor,
            'sort' => $sort,
        ];

        // Get the existing apps array
        $trainings_array = $this->trainings_source->fetch_for_save();

        // Generate a unique ID for the new app
        $next_id = 1;
        foreach ( $trainings_array as $training ) {
            if ( isset( $training['id'] ) && $training['id'] >= $next_id ) {
                $next_id = $training['id'] + 1;
            }
        }

        $training_data['id'] = $next_id; // Add the ID to the new app data

        // Append new app data to the array
        $trainings_array[] = $training_data;

        $result = $this->trainings_source->save( $trainings_array );

        // Capture core analytics metric counts.
        $this->trainings_source->capture_analytics_metric_counts( __CLASS__ );

        // Save and return updated bool result.
        return redirect( 'admin.php?page=dt_home&tab=training&updated=' . ( $result ? 'true' : 'false' ) );
    }

    /**
     * Deletes a training from the plugin option based on the specified ID.
     *
     * @param Request $request The server request object.
     * @param array $params An array of parameters passed to the method.
     * @return ResponseInterface The redirect response.
     */
    public function delete( Request $request, $params )
    {
        $id = $params['id'] ?? null;

        if ( !$id ) {
            return redirect( 'admin.php?page=dt_home&tab=training' );
        }

        $result = $this->trainings_source->delete( $id );

        container()->get( Analytics::class )->event( 'deleted-training-video', [ 'action' => 'snapshot', 'lib_name' => __CLASS__ ] );

        $this->trainings_source->capture_analytics_metric_counts( __CLASS__ );

        // Save and return updated bool result.
        return redirect( 'admin.php?page=dt_home&tab=training&updated=' . ( $result ? 'true' : 'false' ) );
    }

    /**
     * Retrieves the training data from the plugin option, finds the specified training by its ID, and moves it up in the sort order.
     * If successful, it saves the updated array to the plugin option and redirects to the training page.
     * If the specified ID is not found or the training is already at the top, it redirects back to the training page without making any changes.
     *
     * @param Request $request The current server request object.
     * @param array $params An array of parameters passed to the method.
     * @return ResponseInterface A redirect response to the training page with an "updated" parameter set to "true".
     */
    public function up( Request $request, $params ) {
        $result = $this->trainings->move( $params['id'] ?? '', 'up' );
        return redirect( 'admin.php?page=dt_home&tab=training&updated=' . ( $result ? 'true' : 'false' ) );
    }

    /**
     * Handles the request to move the training down in the list.
     *
     * @param Request $request The HTTP Request object.
     * @param array $params The request parameters.
     *
     * @return ResponseInterface The redirect response to the admin page.
     */
    public function down( Request $request, $params ) {
        $result = $this->trainings->move( $params['id'] ?? '', 'down' );
        return redirect( 'admin.php?page=dt_home&tab=training&updated=' . ( $result ? 'true' : 'false' ) );
    }
}
