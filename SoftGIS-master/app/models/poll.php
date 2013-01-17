<?php

class Poll extends AppModel
{
    public $hasMany = array(
        'Question' => array(
            'foreign_key' => 'poll_id',
            'order' => 'Question.num ASC'
        ),
        'Response' => array(
            'order' => 'Response.created ASC'
        ),
        'Hash' => array(
            'order' => 'Hash.used ASC'
        )
    );

    public $hasAndBelongsToMany = array(
        'Path' => array(
            'joinTable' => 'polls_paths',
            'unique' => true
        ),
        'Marker' => array(
            'joinTable' => 'polls_markers',
            'unique' => true
        ),
        'Overlay' => array(
            'joinTable' => 'polls_overlays',
            'unique' => true
        )
    );

    public $validate = array(
        'name' => array(
            'rule' => 'notEmpty',
            'message' => 'Anna kyselylle nimi'
        ),
    );

    public function validHash($hashStr)
    {
        App::import('Model', 'Hash');
        $hashModel = new Hash();

        $hash = $hashModel->find(
            'first',
            array(
                'conditions' => array(
                    'Hash.hash' => $hashStr,
                    'Hash.poll_id' => $this->id,
                    'Hash.used' => '0'
                )
            )
        );
        return !empty($hash);
    }

    public function generateHashes($count)
    {
        App::import('Model', 'Hash');

        $hash = new Hash();

        for ($i = 0; $i < $count; $i++) {
            $str = md5(uniqid(rand(), true));
            $hash->create(
                array(
                    'poll_id' => $this->id,
                    'hash' => $str,
                    'used' => 0
                )
            );
            $hash->save();
        }

    }

    public function beforeSave($options = array())
    {
        if (isset($this->data['Poll']['launch'])) {
            if (empty($this->data['Poll']['launch'])) {
                $this->data['Poll']['launch'] = null;
            }
        }
        if (isset($this->data['Poll']['end'])) {
            if (empty($this->data['Poll']['end'])) {
                $this->data['Poll']['end'] = null;
            }
        }
        return true;
    }
}