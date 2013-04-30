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
                'rule' => array('uniqueByUser', 'author_id'),
                'message' => 'Tämänniminen kuva on jo olemassa'
            )
        ),
        'image' => array(
            'unique' => array(
                'rule' => array('extension', array('gif', 'jpeg', 'png', 'jpg')),
                'message' => 'Kuvatiedoston on oltava .png tai .jpg -tyyppinen'
            ),
            'not_empty' => array(
                'rule' => 'notEmpty',
                'message' => 'Valitse kuvatiedosto'
            )
        ),
        'ne_lat' => array(
            'rule' => array('decimal'),
            'message' => 'Koordinaatin on oltava desimaaliluku. (Tässä desimaaliosa erotetaan pisteellä.)'
        ),
        'ne_lng' => array(
            'rule' => array('decimal'),
            'message' => 'Koordinaatin on oltava desimaaliluku. (Tässä desimaaliosa erotetaan pisteellä.)'
        ),
        'sw_lat' => array(
            'rule' => array('decimal'),
            'message' => 'Koordinaatin on oltava desimaaliluku. (Tässä desimaaliosa erotetaan pisteellä.)'
        ),
        'sw_lng' => array(
            'rule' => array('decimal'),
            'message' => 'Koordinaatin on oltava desimaaliluku. (Tässä desimaaliosa erotetaan pisteellä.)'
        ),
    );

    function uniqueByUser($check, $field){
        //$check = automaattisesti tarkastettava kenttä, $field = käyttäjän tunniste
        //debug($check);
        //debug($field);
        //debug($this->data['Overlay']);

        if (empty($this->data['Overlay']['id'])){ // jos on tallennettu
            $conditions = array(
                $field => $this->data['Overlay'][$field],  //Parametrinä annetu kenttä sisältää saman datan (eli esim. on käyttäjän oma data, eikä jonkun muun)
                'OR' => $check //sisältää tarkastetavan tiedon

            );
        } else { // jos ei ole tallennettu
            $conditions = array(
                $field => $this->data['Overlay'][$field],  //Parametrinä annetu kenttä sisältää saman datan (eli esim. on käyttäjän oma data, eikä jonkun muun)
                'OR' => $check, //sisältää tarkastetavan tiedon
                'NOT' => array(
                    'Overlay.id' => $this->data['Overlay']['id'] //Mutta ei laske itseään mukaan
                )
            );
        }

        $sameNameCount = $this->find('count', array('conditions' => $conditions));
        //debug($sameNameCount == 0); die;
        return $sameNameCount == 0; // jos ehdoilla löytyy osumia, niin ei ole uniikki käyttäjälle
    }
}
