<?php

class Response extends AppModel
{
    public $belongsTo = array(
        'Poll'
    );

    public $hasMany = array(
        'Answer' => array(
            'order' => 'Answer.question_id ASC'
        )
    );
}
