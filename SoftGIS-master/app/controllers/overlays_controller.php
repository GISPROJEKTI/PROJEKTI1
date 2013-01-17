<?php

class OverlaysController extends AppController
{
    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->layout = 'author';
    }


    public function view($id = null)
    {
        $this->Overlay->id = $id;
        $overlay = $this->Overlay->read();

        $overlay['Overlay']['image_url'] = Router::url(
            '/overlays/' . $overlay['Overlay']['image']
        );

        $this->set('overlay', $overlay);
    }

    public function search()
    {
        if (!empty($this->params['url']['q'])) {
            $q = $this->params['url']['q'];
            $overlays = $this->Overlay->find(
                'list',
                array(
                    'conditions' => array(
                        'Overlay.name LIKE' => '%' . $q . '%'
                    )
                )
            );
        } else {
            $overlays = array();
        }

        $this->set('overlays', $overlays);
    }

    public function create_kello()
    {
        $this->Overlay->save(
            array(
                'name' => 'Kello',
                'image' => 'kartta.gif',
                'ne_lat' => 65.185451075719,
                'ne_lng' => 25.462366091057,
                'sw_lat' => 65.075000443105,
                'sw_lng' => 25.213506789578
            )
        );
        echo 'Kello luotu';die;
    }

    public function create_small()
    {
        $this->Overlay->save(
            array(
                'name' => 'Kello (pieni kartta)',
                'image' => 'taustakartta.png',
                'ne_lat' => 65.132707281856,
                'ne_lng' => 25.367764787762,
                'sw_lat' => 65.119487725926,
                'sw_lng' => 25.343042502545
            )
        );
        $this->redirect(array('action' => 'view', $this->Overlay->id));
    }
}