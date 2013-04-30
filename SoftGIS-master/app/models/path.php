<?php

class Path extends AppModel
{
    public $hasAndBelongsToMany = array(
        'Poll' => array(
            'joinTable' => 'polls_paths'
        )
    );

    public $validate = array(
        'name' => array(
            'not_empty' => array(
                'rule' => 'notEmpty',
                'message' => 'Anna aineistolle nimi.'
            ),
            'unique' => array(
                'rule' => array('uniqueByUser', 'author_id'),
                'message' => 'Tämänniminen aineisto on jo olemassa, kokeile toista nimeä.'
            )
        ),
        'coordinates' => array(
            'not_empty' => array(
                'rule' => 'notEmpty',
                'message' => 'Aineistolla täytyy olla sijainti, lisää se kartalle.'
            )
        )
    );

    function uniqueByUser($check, $field){
        //$check = automaattisesti tarkastettava kenttä, $field = käyttäjän tunniste
        //debug($check);
        //debug($field);
        //debug($this->data['Path']);

        if (empty($this->data['Path']['id'])){ // jos on tallennettu
            $conditions = array(
                $field => $this->data['Path'][$field],  //Parametrinä annetu kenttä sisältää saman datan (eli esim. on käyttäjän oma data, eikä jonkun muun)
                'OR' => $check //sisältää tarkastetavan tiedon

            );
        } else { // jos ei ole tallennettu
            $conditions = array(
                $field => $this->data['Path'][$field],  //Parametrinä annetu kenttä sisältää saman datan (eli esim. on käyttäjän oma data, eikä jonkun muun)
                'OR' => $check, //sisältää tarkastetavan tiedon
                'NOT' => array(
                    'Path.id' => $this->data['Path']['id'] //Mutta ei laske itseään mukaan
                )
            );
        }

        $sameNameCount = $this->find('count', array('conditions' => $conditions));
        //debug($sameNameCount == 0); die;
        return $sameNameCount == 0; // jos ehdoilla löytyy osumia, niin ei ole uniikki käyttäjälle
    }
}
