<?php

class PathsController extends AppController
{
    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->layout = 'author';
    }

    public function index()
    {
        $this->Path->recursive = -1;
        $paths = $this->Path->find('all');
        $this->set('paths', $paths);
    }


    public function import()
    {
        if (!empty($this->data)) {
            //save the data to session and reload it at modify
            $this->Session->write('path_temp', $this->data);
            //koska tällä luokalla ei ole omaa viewiä, meidän pitää ohjata jollekkin toiselle viewille
            $this->redirect(array('action' => 'edit'));
        }
    }

    public function edit($id = null)
    {
        if (empty($this->data)) { //load data

            if ($this->Session->check('path_temp')) { //if there is saved data in the session read it
                $this->data = $this->Session->read('path_temp');
                $this->Session->delete('path_temp');
            } else if ($id != null) { // read data from db
                $this->Path->recursive = -1;
                $this->Path->id = $id;
                $this->data = $this->Path->read();

                $this->data['Path']['coordinates'] = stripslashes(
                    $this->data['Path']['coordinates']
                );
            } else { //create a new data
                //$this->Session->setFlash('Aineistoa ei löytynyt');
                //$this->redirect(array('action' => 'index'));
            }

        } else { //Save data
            if ($id == null) { //create new
                $this->Path->create();
                $this->data['Path']['author_id'] = $this->Auth->user('id');
            } else {
                $this->data['Path']['id'] = $id;
                $author = $this->Path->find('first', array( 'conditions' => array('Path.id' => $id), 'recursive' => -1, 'fields' => array('author_id')));
                 $this->data['Path']['author_id'] = $author['Path']['author_id'];
            }

            $this->data['Path']['coordinates'] = addslashes(
                $this->data['Path']['coordinates']
            );
            //debug($this->data);
            if ($this->data['Path']['author_id'] == $this->Auth->user('id') && $this->Path->save($this->data)) {
                $this->Session->setFlash('Aineisto tallennettu');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Tallentaminen epäonnistui');
                //$this->redirect(array('action' => 'index'));

                $this->data['Path']['coordinates'] = stripslashes(
                    $this->data['Path']['coordinates']
                );
            }
        }
    }

    public function search()
    {
        if (!empty($this->params['url']['q'])) {
            $q = $this->params['url']['q'];
            $paths = $this->Path->find(
                'list',
                array(
                    'conditions' => array(
                        'Path.name LIKE' => '%' . $q . '%'
                    )
                )
            );
        } else {
            $paths = array();
        }

        $this->set('paths', $paths);
    }
}