<?php

namespace DT\Home\Controllers\MagicLink;

use DT\Home\Psr\Http\Message\ResponseInterface;
use DT\Home\Sources\Trainings;
use function DT\Home\template;

class TrainingController
{
    private Trainings $trainings;

    public function __construct( Trainings $trainings )
    {
        $this->trainings = $trainings;
    }

    /**
     * Retrieves all training data and returns it as a JSON response.
     *
     * @return ResponseInterface
     */
    public function show()
    {
        $training_data = array_values( $this->trainings->all() );
        $data = json_encode( $training_data );
        $training_data_json_escaped = htmlspecialchars( $data );
        $page_title = __( 'Training', 'dt-home' );

        return template('training', compact(
            'data',
            'training_data',
            'training_data_json_escaped',
            'page_title'
        ));
    }
}
