<?php

class Answer extends AppModel
{
    public $belongsTo = array(
        'Question',
        'Response'
    );
}