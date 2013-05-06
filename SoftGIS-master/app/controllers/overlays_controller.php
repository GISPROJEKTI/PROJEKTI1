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
        //virheilmoitus, jos tiedostoon ei ole käyttöoikeuksia
        if (!file_exists(APP.'webroot'.DS.'overlayimages'.DS) || !is_writable(APP.'webroot'.DS.'overlayimages'.DS)) {
            $this->Session->setFlash(APP.'webroot'.DS.'overlayimages'.DS .' hakemistoa ei ole tai sinne ei ole kirjoitusoikeuksia. Lähetä tämä virheilmoitus palvelimen ylläpitäjälle, hän voi korjata asian.');
        }

        $this->Overlay->recursive = 1;
        $overlays = $this->Overlay->findAllByAuthorId($this->Auth->user('id'), array('id','name','modified','image'), array('id'));
        $this->set('overlays', $overlays);
        /*$othersOverlays = $this->Overlay->find('all', array(
            'conditions' => array('NOT' => array('Overlay.author_id' => $this->Auth->user('id'))), 
            'recursive' => -1,
            'fields' => array('id','name'),
            'order' => array('id')
            ));
        $this->set('others_overlays', $othersOverlays);*/ //Muiden käyttäjien tiedot poistettu käytöstä mahdollisten aineistojen lisenssiongelmien vuoksi
        //debug($othersOverlays);
    }


    public function view($id = null)
    {
        if (!empty($id)){ //read data from db
            $this->Overlay->id = $id;
            $this->data = $this->Overlay->read();

            $this->set('author', $this->Auth->user('id'));
        } else {
            $this->Session->setFlash('Karttakuvaa ei löytynyt');
            $this->redirect(array('action' => 'index'));
        }
    }


    public function edit($id = null)
    {
        if (empty($this->data)) {
            if ($this->Session->check('overlay_temp')) { //if there is saved data in the session read it
                $this->data = $this->Session->read('overlay_temp');
                $this->Session->delete('overlay_temp');
            } else if (!empty($id)){ //read data from db
                $this->Overlay->id = $id;
                $this->data = $this->Overlay->read();
                if ($this->data['Overlay']['author_id'] != $this->Auth->user('id')) { //vain omia kuvia voi muokata
                    $this->Session->setFlash('Voit muokata vain omia kuvia');
                    $this->redirect(array('action' => 'index'));
                }
            } else {
                $this->Session->setFlash('Karttakuvaa ei löytynyt');
                $this->redirect(array('action' => 'index'));
            }
        } else {
            if ($id == null) { //create new
                $this->Overlay->create();
                $this->data['Overlay']['author_id'] = $this->Auth->user('id');
            } else {
                $this->data['Overlay']['id'] = $id;
                $author = $this->Overlay->find('first', array( 'conditions' => array('Overlay.id' => $id), 'recursive' => -1, 'fields' => array('author_id')));
                $this->data['Overlay']['author_id'] = $author['Overlay']['author_id'];
            }

            $this->data['Overlay']['modified'] = date('Y-m-d');
            if ($this->data['Overlay']['author_id'] == $this->Auth->user('id') && $this->Overlay->save($this->data)) {
                $this->Session->setFlash('Karttakuva tallennettu');
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

    public function copy($id = null)
    {
        if (!empty($id)) {
            $this->Overlay->recursive = -1;
            $this->Overlay->id = $id;
            $this->data = $this->Overlay->read();
            $this->data['Overlay']['id'] = null;
            $this->data['Overlay']['author_id'] = $this->Auth->user('id');

            $filePath = APP.'webroot'.DS.'overlayimages'.DS;

            if (file_exists($filePath. $this->data['Overlay']['image'])) {

                if ($cut = strrpos($this->data['Overlay']['image'], '.', -0)) {
                    $fileExtension = substr($this->data['Overlay']['image'], $cut);
                }
                $newName = String::uuid().$fileExtension;

                //debug($filePath.$newName); die;
                    
                if (copy($filePath. $this->data['Overlay']['image'], $filePath.$newName)) {
                    $this->data['Overlay']['image'] = $newName;
                    //$this->Session->setFlash('Karttakuva kopioitu');
                } else {
                    $this->data['Overlay']['image'] = null;
                    $this->Session->setFlash('kuvatiedotoa ei löytynyt');
                }
            } else {
                    $this->data['Overlay']['image'] = null;
                    $this->Session->setFlash('kuvatiedotoa ei löytynyt');
                }
            //debug($this->data); die;

            //save the data to session and reload it at modify
            $this->Session->write('overlay_temp', $this->data);
            $this->redirect(array('action' => 'edit'));
        } else {
            $this->Session->setFlash('Karttakuvaa ei löytynyt');
            $this->redirect(array('action' => 'index'));
        }

    }

    public function delete($id = null)
    {
        if (!empty($id)) {
            $this->Overlay->id = $id;
            $this->data = $this->Overlay->find('first', array( 'conditions' => array('Overlay.id' => $id), 'recursive' => -1, 'fields' => array('author_id', 'image')));


            if (empty($this->data) || $this->data['Overlay']['author_id'] != $this->Auth->user('id')) {
                $this->Session->setFlash('Poistaminen ei onnistunut');
            } else { //poistetaan
                //debug($this->data); die;

                if (file_exists(APP.'webroot'.DS.'overlayimages'.DS. $this->data['Overlay']['image'])) {
                    if (unlink(APP.'webroot'.DS.'overlayimages'.DS. $this->data['Overlay']['image'])) {
                        $this->Session->setFlash('Karttakuva poistettu');
                    } else {
                        $this->Session->setFlash('Merkintä poistettu, kuvatiedotoa ei ollut');
                    }
                }

                $this->Overlay->delete($id, false);

                //$this->Session->setFlash('Karttakuva poistettu');
            }
        } else {
            $this->Session->setFlash('Karttakuvaa ei löytynyt');
        }

        $this->redirect(array('action' => 'index'));

    }

    public function upload()
    {
        /*
        *Tämä toteutus tallentaa kuvat palvelimelle, tarkastamatta sitä, tulevatko ne käyttöön vai ei.
        */

        //virheilmoitus, jos tiedostoon ei ole käyttöoikeuksia
        if (!file_exists(APP.'webroot'.DS.'overlayimages'.DS) || !is_writable(APP.'webroot'.DS.'overlayimages'.DS)) {
            $this->Session->setFlash(APP.'webroot'.DS.'overlayimages'.DS .' hakemistoa ei ole tai sinne ei ole kirjoitusoikeuksia. Lähetä tämä virheilmoitus palvelimen ylläpitäjälle, hän voi korjata asian.');
        }

        if (!empty($this->data)) {
            //debug($this->data);die;
            $file = $this->data['Overlay']['file'];
            //debug($file); //die;
            if ($file['error'] == 4){
                $this->Session->setFlash('Valitse ladattava tiedosto');
                $this->redirect(array('action' => 'upload'));
            }
            else if ($file['size'] > 1500000 || $file['error'] == 1 || $file['error'] == 2){
                //debug($file);
                $this->Session->setFlash('Tiedosto on liian suuri');
                $this->redirect(array('action' => 'upload'));
            }
            else if ($file['error'] != 0){
                $this->Session->setFlash('Tapahtui joku virhe, yritä uudelleen (virhekoodi ' . $file['error'] . ')');
                $this->redirect(array('action' => 'upload'));
            }
            else { //Kaikki ok

                $this->data['Overlay']['name'] = $file['name'];
                $this->data['Overlay']['image'] = String::uuid().str_replace("image/", ".", $file['type']);
                //debug($overlay);//die;

                if ($this->Overlay->validates(array('fieldList' => array('image'))) && move_uploaded_file($file['tmp_name'],  APP.'webroot'.DS.'overlayimages'.DS. $this->data['Overlay']['image'])) {

                    //save the data to session and reload it at modify
                    $this->Session->write('overlay_temp', $this->data);
                    $this->redirect(array('action' => 'edit'));
                } else {
                    $this->Session->setFlash('Tallentaminen epäonnistui');
                    $errors = $this->Overlay->validationErrors;
                    //debug($errors);die;
                    foreach ($errors as $err) {
                        $this->Session->setFlash($err);
                    }
                }
                //debug($this->data);
            }
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

}