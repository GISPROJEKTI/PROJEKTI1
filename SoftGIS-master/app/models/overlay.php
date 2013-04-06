<?php

class Overlay extends AppModel
{
    public $hasAndBelongsToMany = array(
        'Poll' => array(
            'joinTable' => 'polls_overlays'
        )
    );

    public $validate = array(
        'name' => array(
            'not_empty' => array(
                'rule' => 'notEmpty',
                'message' => 'Anna kuvalle nimi'
            ),
            'unique' => array(
                'rule' => 'isUnique',
                'on' => 'update',
                'message' => 'Tämänniminen kuva on jo olemassa.'
            )
        ),
        'image' => array(
            'unique' => array(
                'rule' => array('extension', array('gif', 'jpeg', 'png', 'jpg')),
                'message' => 'Kuvatiedoston on oltava .png tai .jpg -tyyppinen.'
            ),
            'not_empty' => array(
                'rule' => 'notEmpty',
                'message' => 'Valitse kuvatiedosto'
            )
        ),
        'ne_lat' => array(
            'rule' => array('decimal'),
            'on' => 'update',
            'message' => 'Koordinaatin on oltava desimaaliluku. (Tässä desimaaliosa erotetaan pisteellä.)'
        ),
        'ne_lng' => array(
            'rule' => array('decimal'),
            'on' => 'update',
            'message' => 'Koordinaatin on oltava desimaaliluku. (Tässä desimaaliosa erotetaan pisteellä.)'
        ),
        'sw_lat' => array(
            'rule' => array('decimal'),
            'on' => 'update',
            'message' => 'Koordinaatin on oltava desimaaliluku. (Tässä desimaaliosa erotetaan pisteellä.)'
        ),
        'sw_lng' => array(
            'rule' => array('decimal'),
            'on' => 'update',
            'message' => 'Koordinaatin on oltava desimaaliluku. (Tässä desimaaliosa erotetaan pisteellä.)'
        ),
    );
}