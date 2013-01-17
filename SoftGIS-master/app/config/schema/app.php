<?php

class AppSchema extends CakeSchema 
{

    var $name = 'App';

    var $polls = array(
        'id' => array(
            'type' => 'integer', 
            'null' => false, 
            'key' => 'primary'
        ),
        'name' => array(
            'type' => 'string',
            'null' => false,
            'length' => '255'
        ),
        'author_id' => array(
            'type' => 'integer',
            'null' => false,
        ),
        'public' => array(
            'type' => 'boolean',
            'null' => false,
            'default' => 0
        ),
        'launch' => array(
            'type' => 'date',
            'null' => true,
            'default' => null
        ),
        'end' => array(
            'type' => 'date',
            'null' => true,
            'default' => null
        ),
        'welcome_text' => array(
            'type' => 'text',
            'null' => true,
            'default' => ''
        ),
        'thanks_text' => array(
            'type' => 'text',
            'null' => true,
            'default' => ''
        ),
    );

    var $hashes = array(
        'id' => array(
            'type' => 'integer', 
            'null' => false, 
            'key' => 'primary'
        ),
        'poll_id' => array(
            'type' => 'integer',
            'null' => false,
            'default' => null
        ),
        'hash' => array(
            'type' => 'string',
            'null' => false,
            'length' => '255'
        ),
        'used' => array(
            'type' => 'boolean',
            'null' => false,
            'default' => 0
        ),
    );

    var $questions = array(
        'id' => array(
            'type' => 'integer', 
            'null' => false, 
            'key' => 'primary'
        ),
        'poll_id' => array(
            'type' => 'integer', 
            'null' => false 
        ),
        'num' => array(
            'type' => 'integer',
            'null' => false
        ),
        'type' => array(
            'type' => 'integer',
            'null' => false
        ),
        'text' => array(
            'type' => 'text',
            'null' => false
        ),
        'low_text' => array(
            'type' => 'string',
            'null' => true,
            'default' => null
        ),
        'high_text' => array(
            'type' => 'string',
            'null' => true,
            'default' => null
        ),
        'lat' => array(
            'type' => 'float',
            'null' => true,
            'default' => null
        ),
        'lng' => array(
            'type' => 'float',
            'null' => true,
            'default' => null
        ),
        'zoom' => array(
            'type' => 'integer',
            'null' => true,
            'default' => 12
        ),
        'answer_location' => array(
            'type' => 'boolean',
            'null' => false,
            'default' => 0
        ),
        'answer_visible' => array(
            'type' => 'boolean',
            'null' => false,
            'default' => 0
        ),
        'comments' => array(
            'type' => 'boolean',
            'null' => false,
            'default' => 0
        ),
    );

    var $responses = array(
        'id' => array(
            'type' => 'integer', 
            'null' => false, 
            'key' => 'primary'
        ),
        'poll_id' => array(
            'type' => 'integer', 
            'null' => false 
        ),
        'created' => array(
            'type' => 'datetime',
            'null' => false
        ),
        'hash' => array(
            'type' => 'string',
            'null' => true,
            'length' => '255'
        )
    );

    var $answers = array(
        'id' => array(
            'type' => 'integer', 
            'null' => false, 
            'key' => 'primary'
        ),
        'response_id' => array(
            'type' => 'integer', 
            'null' => false 
        ),
        'question_id' => array(
            'type' => 'integer', 
            'null' => false 
        ),
        'answer' => array(
            'type' => 'text',
            'null' => false
        ),
        'lat' => array(
            'type' => 'float',
            'null' => true,
            'default' => null
        ),
        'lng' => array(
            'type' => 'float',
            'null' => true,
            'default' => null
        ),
    );

    var $authors = array(
        'id' => array(
            'type'=>'integer', 
            'null' => false, 
            'key' => 'primary'
        ),
        'username' => array(
            'type' => 'string',
            'null' => false,
            'length' => '50'
        ),
        'password' => array(
            'type' => 'string',
            'null' => '40',
        )
    );


    var $polls_paths = array(
        'id' => array(
            'type'=>'integer', 
            'null' => false, 
            'key' => 'primary'
        ),
        'poll_id' => array(
            'type'=>'integer', 
            'null' => false
        ),
        'path_id' => array(
            'type'=>'integer', 
            'null' => false
        )
    );

    var $polls_markers = array(
        'id' => array(
            'type'=>'integer', 
            'null' => false, 
            'key' => 'primary'
        ),
        'poll_id' => array(
            'type'=>'integer', 
            'null' => false
        ),
        'marker_id' => array(
            'type'=>'integer', 
            'null' => false
        )
    );

    var $polls_overlays = array(
        'id' => array(
            'type'=>'integer', 
            'null' => false, 
            'key' => 'primary'
        ),
        'poll_id' => array(
            'type'=>'integer', 
            'null' => false
        ),
        'overlay_id' => array(
            'type'=>'integer', 
            'null' => false
        )
    );

    var $paths = array(
        'id' => array(
            'type'=>'integer', 
            'null' => false, 
            'key' => 'primary'
        ),
        'name' => array(
            'type' => 'string',
            'length' => '50',
            'null' => true
        ),
        'author_id' => array(
            'type' => 'integer',
            'null' => false
        ),
        /**
         * 1 = Polyline
         * 2 = Polygon
         */
        'type' => array(
            'type' => 'integer',
            'null' => false,
            'default' => 1
        ),
        'content' => array(
            'type' => 'text'
        ),
        'stroke_color' => array(
            'type' => 'string',
            'length' => 6,
            'default' => '333333'
        ),
        'stroke_opacity' => array(
            'type' => 'float',
            'default' => 0.8
        ),
        'stroke_weight' => array(
            'type' => 'float',
            'default' => 1.0
        ),
        'fill_color' => array(
            'type' => 'string',
            'length' => 6,
            'default' => '333333'
        ),
        'fill_opacity' => array(
            'type' => 'float',
            'default' => 0.2
        ),
        'coordinates' => array(
            'type' => 'text',
            'null' => false
        )
    );

    var $markers = array(
        'id' => array(
            'type'=>'integer', 
            'null' => false, 
            'key' => 'primary'
        ),
        'name' => array(
            'type' => 'string',
            'length' => '50',
            'null' => false
        ),
        'content' => array(
            'type' => 'text'
        ),
        'icon' => array(
            'type' => 'string',
            'length' => '50',
            'null' => true,
            'default' => NULL
        ),
        'lat' => array(
            'type' => 'float',
            'null' => false
        ),
        'lng' => array(
            'type' => 'float',
            'null' => false
        ),
    );

    var $overlays = array(
        'id' => array(
            'type'=>'integer', 
            'null' => false, 
            'key' => 'primary'
        ),
        'name' => array(
            'type' => 'string',
            'length' => '50',
            'null' => false
        ),
        'image' => array(
            'type' => 'string',
            'length' => '50',
            'null' => false
        ),
        'ne_lat' => array(
            'type' => 'float',
            'null' => false
        ),
        'ne_lng' => array(
            'type' => 'float',
            'null' => false
        ),
        'sw_lat' => array(
            'type' => 'float',
            'null' => false
        ),
        'sw_lng' => array(
            'type' => 'float',
            'null' => false
        )
    );

}
