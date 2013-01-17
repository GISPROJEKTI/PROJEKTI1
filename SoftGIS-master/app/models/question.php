<?php

class Question extends AppModel
{
    public $actsAs = array(
        'LatLng'
    );

    public $hasMany = array(
        'Answer'
    );

    public $belongsTo = array(
        'Poll'
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