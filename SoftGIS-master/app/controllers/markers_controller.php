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
        $this->Marker->recursive = -1;
        $markers = $this->Marker->find('all');
        $this->set('markers', $markers);
    }

    public function edit($id = null)
    {
        if (!empty($this->data)) {
            // debug($this->data);die;
            if ($id != null) {
                $this->Marker->id = $id;
            } else {
                $this->Marker->create();
            }
            if ($this->Marker->save($this->data)) {
                $this->Session->setFlash('Karttamerkki tallennettu');
                $this->redirect(
                    array('controller' => 'markers', 'action' => 'index')
                );
            }
        } else {
            $this->Marker->recursive = -1;
            $this->Marker->id = $id;
            $this->data = $this->Marker->read();
        }

        $icons = array( 'default' => '' );
        $iconDir = WWW_ROOT . 'markericons/';
        $dh = opendir($iconDir);
        while (($file = readdir($dh)) !== false) {
            if (is_file($iconDir . $file)) {
                $icons[$file] = $file;
            }
        }
        asort($icons);
        $this->set('icons', $icons);
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