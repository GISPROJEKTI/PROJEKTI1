<?php

class Marker extends AppModel
{
    public $actsAs = array(
        'LatLng'
    );

    public $hasAndBelongsToMany = array(
        'Poll' => array(
            'joinTable' => 'polls_paths'
        )
    );

    public function afterFind($results, $primary)
    {
        if (!$primary) {
            return $this->Behaviors->LatLng->afterFind($this, $results, false);
        } else {
            return $results;
        }
    }
}