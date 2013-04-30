<?php

class Marker extends AppModel
{
    public $actsAs = array(
        'LatLng'
    );

    public $hasAndBelongsToMany = array(
        'Poll' => array(
            'joinTable' => 'polls_markers'
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

    public $validate = array(
        'name' => array(
            'not_empty' => array(
                'rule' => 'notEmpty',
                'message' => 'Anna merkille nimi.'
            ),
            'unique' => array(
                'rule' => array('uniqueByUser', 'author_id'),
                'message' => 'Tämänniminen merkki on jo olemassa, kokeile toista nimeä.'
            )
        ),
        'lat' => array(
            'not_empty' => array(
                'rule' => 'notEmpty',
                'message' => 'Merkillä täytyy olla sijainti, lisää se kartalle.'
            )
        ),
        'lng' => array(
            'not_empty' => array(
                'rule' => 'notEmpty',
                'message' => 'Merkillä täytyy olla sijainti, lisää se kartalle.'
            )
        )
    );

    function uniqueByUser($check, $field){
        //$check = automaattisesti tarkastettava kenttä, $field = käyttäjän tunniste
        //debug($check);
        //debug($field);
        //debug($this->data['Marker']);

        if (empty($this->data['Marker']['id'])){ // jos on tallennettu
            $conditions = array(
                $field => $this->data['Marker'][$field],  //Parametrinä annetu kenttä sisältää saman datan (eli esim. on käyttäjän oma data, eikä jonkun muun)
                'OR' => $check //sisältää tarkastetavan tiedon

            );
        } else { // jos ei ole tallennettu
            $conditions = array(
                $field => $this->data['Marker'][$field],  //Parametrinä annetu kenttä sisältää saman datan (eli esim. on käyttäjän oma data, eikä jonkun muun)
                'OR' => $check, //sisältää tarkastetavan tiedon
                'NOT' => array(
                    'Marker.id' => $this->data['Marker']['id'] //Mutta ei laske itseään mukaan
                )
            );
        }

        $sameNameCount = $this->find('count', array('conditions' => $conditions));
        //debug($sameNameCount == 0); die;
        return $sameNameCount == 0; // jos ehdoilla löytyy osumia, niin ei ole uniikki käyttäjälle
    }
}
