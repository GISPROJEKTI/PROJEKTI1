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
            $this->data['Path']['author_id'] = $this->Auth->user('id');
            $this->data['Path']['coordinates'] = addslashes(
                $this->data['Path']['coordinates']
            );

            if ($this->Path->save($this->data)) {
                $this->redirect(array('action' => 'edit', $this->Path->id));
            } else {
                $this->Session->setFlash('Reitin luonti epäonnistui');
            }
        }
    }

    public function edit($id = null)
    {
        if (!empty($this->data)) {
            // debug($this->data);die;
            if ($id != null) {
                $this->Path->id = $id;
            } else {
                $this->Path->create();
                $this->data['Path']['author_id'] = $this->Auth->user('id');
                $this->data['Path']['coordinates'] = addslashes(
                    $this->data['Path']['coordinates']
                );
            }
            if ($this->Path->save($this->data)) {
                $this->Session->setFlash('Reitti tallennettu');
                $this->redirect(
                    array('controller' => 'paths', 'action' => 'index')
                );
            }
        } else {
            $this->Path->recursive = -1;
            $this->Path->id = $id;
            $this->data = $this->Path->read();
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