<?php

class MarkersController extends AppController
{
    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->layout = 'author';
    }

    public function index()
    {
        $this->Marker->recursive = 1;
        $markers = $this->Marker->findAllByAuthorId($this->Auth->user('id'), array('id','name','modified'), array('id'));
        $this->set('markers', $markers);
        $othersMarkers = $this->Marker->find('all', array(
            'conditions' => array('NOT' => array('Marker.author_id' => $this->Auth->user('id'))), 
            'recursive' => -1,
            'fields' => array('id','name'),
            'order' => array('id')
            ));
        $this->set('others_markers', $othersMarkers);
    }

    public function view($id = null)
    {
        if ($id != null) { // read data from db
            $this->Marker->recursive = -1;
            $this->Marker->id = $id;
            $this->data = $this->Marker->read();

            $this->set('author', $this->Auth->user('id'));
        } else {
            $this->Session->setFlash('Merkkiä ei löytynyt');
            $this->redirect(array('action' => 'index'));
        }
    }

    public function edit($id = null)
    {
        if (empty($this->data)) { //load data

            if ($this->Session->check('marker_temp')) { //if there is saved data in the session read it
                $this->data = $this->Session->read('marker_temp');
                $this->Session->delete('marker_temp');
            } else if ($id != null) { // read data from db
                $this->Marker->recursive = -1;
                $this->Marker->id = $id;
                $this->data = $this->Marker->read();
                if ($this->data['Marker']['author_id'] != $this->Auth->user('id')) { //vain omia merkkejä voi muokata
                    $this->Session->setFlash('Voit muokata vain omia karttamerkkejä');
                    $this->redirect(array('action' => 'index'));
                }

            } else { //create a new data
                //$this->Session->setFlash('Aineistoa ei löytynyt');
                //$this->redirect(array('action' => 'index'));
            }

        } else { //Save data
            if ($id == null) { //create new
                $this->Marker->create();
                $this->data['Marker']['author_id'] = $this->Auth->user('id');
            } else { //update existing
                $this->data['Marker']['id'] = $id;
                $author = $this->Marker->find('first', array( 'conditions' => array('Marker.id' => $id), 'recursive' => -1, 'fields' => array('author_id')));
                $this->data['Marker']['author_id'] = $author['Marker']['author_id'];
            }

            $this->data['Marker']['modified'] = date('Y-m-d');
            
            //debug($this->data); die;
            if ($this->data['Marker']['author_id'] == $this->Auth->user('id') && $this->Marker->save($this->data)) {
                $this->Session->setFlash('Merkki tallennettu');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Tallentaminen epäonnistui');
                //$this->redirect(array('action' => 'index'));

                $errors = $this->Marker->validationErrors;
                foreach ($errors as $err) {
                    $this->Session->setFlash($err);
                }
            }
        }

        //Merkkikuvien hakeminen
        $icons = array( 'default' => '' );
        $iconDir = WWW_ROOT . 'markericons/';
        $dh = opendir($iconDir);
        while (($file = readdir($dh)) !== false) {
            if (is_file($iconDir . $file)) {
                $icons[$file] = $file;
                if ($cut = strrpos($file, '.', -0)) { // poista tiedostopääte nimestä
                    $icons[$file] = substr($file, 0, $cut);
                }

            }
        }
        asort($icons);
        $this->set('icons', $icons);
    }

    public function copy($id = null)
    {
        if (!empty($id)) {
            $this->Marker->recursive = -1;
            $this->Marker->id = $id;
            $this->data = $this->Marker->read();
            $this->data['Marker']['id'] = null;
            $this->data['Marker']['author_id'] = $this->Auth->user('id');
            //debug($this->data); die;

            //save the data to session and reload it at modify
            $this->Session->write('marker_temp', $this->data);
            $this->redirect(array('action' => 'edit'));
        } else {
            $this->Session->setFlash('Merkkiä ei löytynyt');
            $this->redirect(array('action' => 'index'));
        }

    }

    public function delete($id = null)
    {
        if (!empty($id)) {
            $this->Marker->id = $id;
            $this->data = $this->Marker->find('first', array( 'conditions' => array('Marker.id' => $id), 'recursive' => -1, 'fields' => array('author_id')));


            if (empty($this->data) || $this->data['Marker']['author_id'] != $this->Auth->user('id')) {
                $this->Session->setFlash('Poistaminen ei onnistunut');
            } else { //poistetaan
                //debug($this->data); die;

                $this->Marker->delete($id, false);

                $this->Session->setFlash('Merkki poistettu');
            }
        } else {
            $this->Session->setFlash('Merkkiä ei löytynyt');
        }

        $this->redirect(array('action' => 'index'));

    }

    public function search()
    {
        if (!empty($this->params['url']['q'])) {
            $q = $this->params['url']['q'];
            $markers = $this->Marker->find(
                'list',
                array(
                    'conditions' => array(
                        'Marker.name LIKE' => '%' . $q . '%'
                    )
                )
            );
        } else {
            $markers = array();
        }

        $this->set('markers', $markers);
    }
}
