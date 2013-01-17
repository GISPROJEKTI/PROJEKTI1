<?php

class ObjectsController extends AppController
{
    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->layout = 'author';
    }


    public function create()
    {
        $authorId = $this->Auth->user('id');

        if (!empty($this->data)) {
            if ($this->Object->save($this->data)) {
                $this->data = null;
                $this->Session->setFlash('Merkki luotu');
            } else {
                // debug($this->Entry->invalidFields());die;
            }
        }
    }
}