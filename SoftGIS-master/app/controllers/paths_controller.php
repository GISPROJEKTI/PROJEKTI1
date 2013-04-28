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
        $this->Path->recursive = 1;
        $paths = $this->Path->findAllByAuthorId($this->Auth->user('id'), array('id','name','modified'), array('id'));
        $this->set('paths', $paths);
        $othersPaths = $this->Path->find('all', array(
            'conditions' => array('NOT' => array('Path.author_id' => $this->Auth->user('id'))), 
            'recursive' => -1,
            'fields' => array('id','name'),
            'order' => array('id')
            ));
        $this->set('others_paths', $othersPaths);
    }

    public function view($id = null)
    {
        if ($id != null) { // read data from db
            $this->Path->recursive = -1;
            $this->Path->id = $id;
            $this->data = $this->Path->read();

            $this->data['Path']['coordinates'] = stripslashes(
                $this->data['Path']['coordinates']
            );
        } else {
            $this->Session->setFlash('Aineistoa ei löytynyt');
            $this->redirect(array('action' => 'index'));
        }
    }

    public function import()
    {
        if (!empty($this->data)) {
            //save the data to session and reload it at modify
            $this->Session->write('path_temp', $this->data);
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
            $this->data['Path']['modified'] = date('Y-m-d');
            
            //debug($this->data); die;
            if ($this->data['Path']['author_id'] == $this->Auth->user('id') && $this->Path->save($this->data)) {
                $this->Session->setFlash('Aineisto tallennettu');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Tallentaminen epäonnistui');
                //$this->redirect(array('action' => 'index'));

                $this->data['Path']['coordinates'] = stripslashes(
                    $this->data['Path']['coordinates']
                );
                $errors = $this->Path->validationErrors;
                foreach ($errors as $err) {
                    $this->Session->setFlash($err);
                }
            }
        }
    }

    public function copy($id = null)
    {
        if (!empty($id)) {
            $this->Path->recursive = -1;
            $this->Path->id = $id;
            $this->data = $this->Path->read();
            $this->data['Path']['id'] = null;
            $this->data['Path']['author_id'] = $this->Auth->user('id');
            //debug($this->data); die;

            //save the data to session and reload it at modify
            $this->Session->write('path_temp', $this->data);
            $this->redirect(array('action' => 'edit'));
        } else {
            $this->Session->setFlash('Aineistoa ei löytynyt');
            $this->redirect(array('action' => 'index'));
        }

    }

    public function delete($id = null)
    {
        if (!empty($id)) {
            $this->Path->id = $id;
            $this->data = $this->Path->find('first', array( 'conditions' => array('Path.id' => $id), 'recursive' => -1, 'fields' => array('author_id')));


            if (empty($this->data) || $this->data['Path']['author_id'] != $this->Auth->user('id')) {
                $this->Session->setFlash('Poistaminen ei onnistunut');
            } else { //poistetaan
                //debug($this->data); die;

                $this->Path->delete($id, false);

                $this->Session->setFlash('Karttakuva poistettu');
            }
        } else {
            $this->Session->setFlash('Aineistoa ei löytynyt');
        }

        $this->redirect(array('action' => 'index'));

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