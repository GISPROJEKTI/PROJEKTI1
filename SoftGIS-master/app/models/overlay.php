<?php

class Overlay extends AppModel
{
    public $hasAndBelongsToMany = array(
        'Poll' => array(
            'joinTable' => 'polls_overlays'
        )
    );
}