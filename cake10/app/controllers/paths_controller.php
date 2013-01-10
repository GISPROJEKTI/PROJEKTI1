<?php

class PathsController extends AppController
{
    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->layout = 'author';
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
        $this->Path->id = $id;
        if (!$this->Path->exists()) {
            $this->redirect(array('action' => 'import'));
        }

        if (!empty($this->data)) {
            if ($this->Path->save($this->data)) {
                $this->data = null;
                $this->Session->setFlash('Reitti tallennettu');
                $this->redirect(array('action' => 'import'));
            }
        } else {
            $this->data = $this->Path->read();
            $this->set('data', $this->data);
        }
        // $this->set('coordinates', $path['Path']['coordinates']);
        // $this->set('type', $path['Path']['coordinates']);
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