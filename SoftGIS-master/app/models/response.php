<?php

class Response extends AppModel
{
    public $belongsTo = array(
        'Poll'
    );

    public $hasMany = array(
        'Answer'
    );
}