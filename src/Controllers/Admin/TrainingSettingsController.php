<?php

namespace DT\Home\Controllers\Admin;

use DT\Home\GuzzleHttp\Psr7\ServerRequest as Request;
use DT\Home\Psr\Http\Message\ResponseInterface;
use DT\Home\Services\Trainings;
use DT\Home\Sources\Trainings as TrainingsSource;
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

        // Save and return updated bool result.
        return redirect( 'admin.php?page=dt_home&tab=training&updated=' . ( $result ? 'true' : 'false' ) );
    }

    /**
     * Handle GET-based reorder requests.
     *
     * @param Request $request The request object.
     *
     * @return ResponseInterface
     */
    public function reorder_get( Request $request )
    {
        $input = extract_request_input( $request );
        $ordered_ids = isset( $input['ordered_ids'] ) ? explode( ',', $input['ordered_ids'] ) : [];

        if ( empty( $ordered_ids ) ) {
            return redirect( 'admin.php?page=dt_home&tab=training&error=1' );
        }

        // Get current training data
        $trainings_array = get_plugin_option( 'trainings' );

        // Create a lookup array for existing data
        $trainings_lookup = [];
        foreach ( $trainings_array as $training ) {
            if ( isset( $training['id'] ) ) {
                $trainings_lookup[$training['id']] = $training;
            }
        }

        // Reorder based on the provided IDs and update sort values
        $reordered_trainings = [];
        $processed_ids = [];

        foreach ( $ordered_ids as $index => $training_id ) {
            if ( isset( $trainings_lookup[$training_id] ) ) {
                $training = $trainings_lookup[$training_id];
                $training['sort'] = $index + 1;
                $reordered_trainings[] = $training;
                $processed_ids[] = $training_id;
            }
        }

        // Add any missing items to the end to prevent data loss
        foreach ( $trainings_array as $training ) {
            if ( isset( $training['id'] ) && !in_array( $training['id'], $processed_ids ) ) {
                $training['sort'] = count( $reordered_trainings ) + 1;
                $reordered_trainings[] = $training;
            }
        }

        // Save the updated training order back to the option
        set_plugin_option( 'trainings', $reordered_trainings );

        return redirect( 'admin.php?page=dt_home&tab=training&updated=true' );
    }
}
