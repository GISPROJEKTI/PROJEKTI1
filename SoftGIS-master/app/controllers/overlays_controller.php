<?php

class OverlaysController extends AppController
{
    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->layout = 'author';
    }

    public function index()
    {
        $this->Overlay->recursive = -1;
        $overlay = $this->Overlay->find('all');
        $this->set('overlay', $overlay);
    }


    public function view($id = null)
    {
        $this->Overlay->id = $id;
        if (empty($this->data)) {
            if (!empty($id)){
                $this->data = $this->Overlay->read();
            } else {
                $this->Session->setFlash('Karttakuvaa ei löytynyt');
                $this->redirect(array('action' => 'index'));
            }
        } else {
            //debug($this->data);//die;
            if ($this->Overlay->save($this->data)) {
                $this->Session->setFlash('Karttakuva tallennettu.');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Tallentaminen epäonnistui');
                $errors = $this->Overlay->validationErrors;
                foreach ($errors as $err) {
                    $this->Session->setFlash($err);
                }
            }
        }
    }

    public function upload()
    {
        //TODO: virheilmoitus, jos tiedostoon ei ole käyttöoikeuksia!!!!!!!
        if (!empty($this->data)) {
            //debug($this->data);die;
            $file = $this->data['Overlay']['file'];
            //debug($file);

            $overlay['name'] = $file['name'];
            $overlay['image'] = String::uuid().str_replace("image/", ".", $file['type']);
            //debug($overlay);die;

            if ($this->Overlay->save($overlay) && move_uploaded_file($file['tmp_name'],  APP.'webroot'.DS.'img'.DS.'overlays'.DS. $overlay['image'])) {
                $this->Session->setFlash('Karttakuva tallennettu.');
                $this->redirect(array('action' => 'view', $this->Overlay->id));
            } else {
                $this->Session->setFlash('Tallentaminen epäonnistui');
                $errors = $this->Overlay->validationErrors;
                foreach ($errors as $err) {
                    $this->Session->setFlash($err);
                }
            }
            //debug($this->data);
        }
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

    public function madekoski_png()
    {
        $this->Overlay->save(
            array(
                'name' => 'madekoski_png',
                'image' => 'esimerkki1png.png',
                'ne_lat' => 64.96810673,
                'ne_lng' => 25.69183056,
                'sw_lat' => 64.95191426,
                'sw_lng' => 25.63935328
            )
        );
        $this->redirect(array('action' => 'view', $this->Overlay->id));
    }

    public function madekoski_jpg()
    {
        $this->Overlay->save(
            array(
                'name' => 'madekoski_jpg2',
                'image' => 'esimerkki1jpg.jpg',
            )
        );
        $this->redirect(array('action' => 'view', $this->Overlay->id));
    }

}