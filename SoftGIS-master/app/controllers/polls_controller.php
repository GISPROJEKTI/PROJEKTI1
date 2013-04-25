<?php

class PollsController extends AppController
{
    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->layout = 'author';
    }


    public function index()
    {
        $authorId = $this->Auth->user('id');
        $this->Poll->contain('Response');
        $polls = $this->Poll->findAllByAuthorId($authorId);
        $this->set('polls', $polls);
    }

    public function view($id = null)
    {
        $authorId = $this->Auth->user('id');
        $this->Poll->id = $id;
        if (!$this->Poll->exists() 
            || $this->Poll->field('author_id') != $authorId) {
            $this->cakeError('pollNotFound');
        }
        $this->Poll->contain('Question', 'Marker', 'Path', 'Overlay');
        $poll = $this->Poll->read();
        $this->set('poll', $poll['Poll']);
        $this->set('questions', $poll['Question']);
        $this->set('markers', $poll['Marker']);
        $this->set('paths', $poll['Path']);
        $this->set('overlays', $poll['Overlay']);

        // Set response count
        $responseCount = $this->Poll->Response->find(
            'count', 
            array(
                'conditions' => array('Response.poll_id' => $poll['Poll']['id'])
            )
        );
        $this->set('responseCount', $responseCount);

        $answers = array(
            1 => 'Teksti',
            2 => 'Kyllä, ei, en osaa sanoa',
            3 => '1 - 5, en osaa sanoa',
            4 => '1 - 7, en osaa sanoa',
			5 => 'Monivalinta (max 9)'
        );
        $this->set('answers', $answers);
        //debug($poll);
    }

    public function launch($id = null)
    {
        $authorId = $this->Auth->user('id');
        $this->Poll->id = $id;
        if (!$this->Poll->exists() 
            || $this->Poll->field('author_id') != $authorId) {
            $this->cakeError('pollNotFound');
        }

        $this->Poll->recursive = -1;
        $poll = $this->Poll->read();
        // debug($poll);die;

        $launch = $poll['Poll']['launch'];
        $end = $poll['Poll']['end'];

        // if (empty($launch) || strtotime($launch) > time()) {
        //     // Poll not launched yet
        //     $editable = array('launch', 'end');
        // } else if (empty($end) || strtotime($end) > time()) {
        //     // Poll currently active
        //     $editable = array('end');
        // } else {
        //     // Poll has ended
        //     $editable = array('end');
        // }
        $editable = array('launch', 'end');

        if (!empty($this->data)) {
            if ($this->Poll->save($this->data, null, $editable)) {
                $this->Session->setFlash('Muutokset tallennettu');
                $this->redirect(array('action' => 'view', $id));
            }
        } else {
            $this->data = $poll;
        }


        $this->set('poll', $poll);

        $times = array();
        foreach (range(0,23) as $hour) {
            $hour = str_pad($hour, 2, "0", STR_PAD_LEFT);
            $time = $hour . ':00';
            $times[$time] = $time;
        }
        $this->set('times', $times);
    }

    public function modify($id = null)
    {
        $authorId = $this->Auth->user('id');

        //Haetaan merkit
        $markers = $this->Poll->Marker->find('all', array('recursive' => -1,'fields' => array('id', 'name')));
        $merkkiarray = array();
        foreach ($markers as $marker) {
            // Se lsita, jossa nämä näytettään räjätää, jos seassa on alkioita joilla tyhjä nimi.
            if ($marker['Marker']['name'] != null && $marker['Marker']['name'] != "") {
                array_push($merkkiarray, $marker['Marker']);
            }
        }
        $this->set('merkkiarray',$merkkiarray);

        //Haetaan polut
        $paths = $this->Poll->Path->find('all', array('recursive' => -1,'fields' => array('id', 'name')));
        $reittiarray = array();
        foreach ($paths as $path) {
            if ($path['Path']['name'] != null && $path['Path']['name'] != "") {
                array_push($reittiarray, $path['Path']);
            }
        }
        $this->set('reittiarray',$reittiarray);

        //Haetaan overlayt
        $overlays = $this->Poll->Overlay->find('all', array('recursive' => -1,'fields' => array('id', 'name')));
        $overlayarray = array();
        foreach ($overlays as $overlay) {
            if ($overlay['Overlay']['name'] != null && $overlay['Overlay']['name'] != "") {
                array_push($overlayarray, $overlay['Overlay']);
            }
        }
        $this->set('overlayarray',$overlayarray);

        if (!empty($id)) {
            $poll = $this->Poll->find(
                'first',
                array(
                    'conditions' => array(
                        'Poll.id' => $id
                    ),
                    'contain' => array(
                        'Question',
                        'Path' => array(
                            'id',
                            'name'
                        ),
                        'Marker' => array(
                            'id',
                            'name'
                        ),
                        'Overlay' => array(
                            'id',
                            'name'
                        )
                    )
                )
            );
            // Poll not found or someone elses
            if (empty($poll) || $poll['Poll']['author_id'] != $authorId) {
                $this->cakeError('pollNotFound');
            }

            // Polls that have responses shouldn't be edited anymore
            $responseCount = $this->Poll->Response->find(
                'count', 
                array(
                    'conditions' => array('Response.poll_id' => $poll['Poll']['id'])
                )
            );

            if ($responseCount > 0) {
                $this->Session->setFlash('Kyselyyn on vastattu, joten sitä ei voida enään muokata');
                $this->redirect(array('action' => 'view', $id));
            }

        } else {
            // Empty poll
            $poll = array(
                'Poll' => array(
                    'name' => null,
                    'public' => null,
                    'published' => null,
                    'welcome_text' => null,
                    'thanks_text' => null
                ),
                'Question' => array(),
                'Path' => array(),
                'Marker' => array(),
                'Overlay' => array()
            );
        }
        //debug($poll);

        // Save
        if (!empty($this->data)) {
            //debug($this->data);//die;
            $data = $this->_jsonToPollModel($this->data);
            //debug($data); debug($poll); die;
			
			//Samannimisen kyselyn tarkistus ALKAA
			
			$pollName = $data['Poll']['name'];
			$pollName2 = $pollName . "(";
			$pollAuthor = $data['Poll']['author_id'];;
			
			$conditions = array(
			'OR' => array (
					array(
						'Poll.name' => $pollName,
						'Poll.author_id' => $pollAuthor
					),
					array (
						'Poll.name LIKE' => "$pollName2%",
						'Poll.author_id' => $pollAuthor
					)
				)	
			);			

			$sameNameCount = $this->Poll->find('count', array('conditions' => $conditions));
			
			//jos löytyy samannimisiä kyselyitä
			if ($sameNameCount > 0){
			
				$questionCount =  false;
				
				foreach ($poll['Question'] as $ii => $qq) {
					$questionCount = true;
				}	

				//jos kyselyllä ei ole kysymyksiä kannassa --> uusi kysely --> jolloin laitetaan sulut ja numero
				//jos kyselyllä on kysymyksiä kannassa --> olemassa olevan kyselyn muokkaus --> ei laiteta sulkuja ja numeroa
				if($questionCount == false){
				
					$data['Poll']['name'] = $pollName . "(" . $sameNameCount . ")";
				}
			}
			
			//Samannimisen kyselyn tarkistus LOPPUU


            //kysymysten poisto osa1: merkataan kaikki kysymykset poistettaviksi
            foreach ($poll['Question'] as $i => $q) {
                $poll['Question'][$i]['exists'] = false;
            }

            // Make sure questions have correct num
            if (!empty($data['Question'])){
                foreach ($data['Question'] as $i => $q) {
                    $q['num'] = $i+1;
                    $data['Question'][$i] = $q;

                    //kysymysten poisto osa2: Merkataan ne kysymykset olemassaolevaksi, jotka ovat
                    foreach ($poll['Question'] as $pi => $pq) {
                        if ($pq['id'] == $q['id']){
                            $poll['Question'][$pi]['exists'] = true;
                            break;
                        }
                    }
                }
            } //debug($poll);

            if (!empty($data['Question']) && $this->Poll->saveAll($data, array('validate'=>'first'))){
                //kysymysten poisto osa3: Jos kysely tallennettiin, poista ne kysymykset, joita ei ole merkattu olemassaoleviksi.
                foreach ($poll['Question'] as $i => $q) {
                    if(!$q['exists']){
                        $this->Poll->Question->delete($q['id'], false);
                    }
                }
                $this->Session->setFlash('Kysely tallennettu');
                $this->redirect(array('action' => 'view', $this->Poll->id));
            } else {
                $this->Session->setFlash('Tallentaminen epäonnistui');
                $poll = $data;
                $errors = $this->Poll->validationErrors;
                foreach ($errors as $err) {
                    $this->Session->setFlash($err);
                }
            }
            //debug($errors);//die;
        }



        $this->set('poll', $poll);
    }

    public function copy($id = null)
    {
    //Kopioidaan parametrinä oleva kysely uudeksi kyselyksi kirjautuneelle käyttäjälle
        //Haetaan tiedot kopioitavasta kyselystä
        if (!empty($id)) {
            $poll = $this->Poll->find(
                'first',
                array(
                    'conditions' => array(
                        'Poll.id' => $id
                    ),
                    'contain' => array(
                        'Question',
                        'Path' => array(
                            'id',
                            'name'
                        ),
                        'Marker' => array(
                            'id',
                            'name'
                        ),
                        'Overlay' => array(
                            'id',
                            'name'
                        )
                    )
                )
            );



            // Poll not found or someone elses
            if (empty($poll) || $poll['Poll']['author_id'] != $this->Auth->user('id')) {
                $this->cakeError('pollNotFound');
                $this->redirect(array('action' => 'index'));
            } else {

                //muutetaan kopioitavan kyselyn yksilöivät tiedot, että tämä voidaan tallentaa uutena
                $poll['Poll']['id'] = null;
                $poll['Poll']['name'] = $poll['Poll']['name'] . '_copy';
                $poll['Poll']['author_id'] = $this->Auth->user('id');
                $poll['Poll']['launch'] = null;
                $poll['Poll']['end'] = null;
                if (!empty($poll['Question'])){
                    foreach ($poll['Question'] as $i => $q) {
                        $poll['Question'][$i]['id'] = null;
                        $poll['Question'][$i]['poll_id'] = null;
                    }
                }

                $path = array();
                foreach ($poll['Path'] as $i => $v) {
                    $path[$i] = $v['id'];
                }
                $poll['Path'] = $path;

                $marker = array();
                foreach ($poll['Marker'] as $i => $v) {
                    $marker[$i] = $v['id'];
                }
                $poll['Marker'] = $marker;

                $overlay = array();
                foreach ($poll['Overlay'] as $i => $v) {
                    $overlay[$i] = $v['id'];
                }
                $poll['Overlay'] = $overlay;

                //tallennetaan kysely
                if ($this->Poll->saveAll($poll, array('validate'=>'first'))){
                    $this->Session->setFlash('Kysely tallennettu');
                    $this->redirect(array('action' => 'modify', $this->Poll->id));
                } else {
                    $this->Session->setFlash('Tallentaminen epäonnistui');
                    $errors = $this->Poll->validationErrors;
                    foreach ($errors as $err) {
                        $this->Session->setFlash($err);
                    }
                    //koska tällä luokalla ei ole omaa viewiä, meidän pitää ohjata jollekkin toiselle viewille
                    $this->redirect(array('action' => 'index'));
                }
            }
        } else {
            // jos kyselyä ei löytynyt
            $this->cakeError('pollNotFound');
            $this->redirect(array('action' => 'index'));
        }
    }

    public function delete($id = null) {
        $authorId = $this->Auth->user('id');

        if (!empty($id)) {
            $poll = $this->Poll->find(
                'first',
                array(
                    'conditions' => array(
                        'Poll.id' => $id
                    ),
                    'contain' => array(
                        'Question' => array('id'),
                        'Response'=> array('id'),
                        'Hash' => array('id'),
                        'Path' => array('id'),
                        'Marker' => array('id' ),
                        'Overlay' => array('id')
                    )
                )
            );
            // Poll not found or someone elses
            if (empty($poll) || $poll['Poll']['author_id'] != $authorId) {
                $this->cakeError('pollNotFound');
                $this->redirect(array('action' => 'index'));
            } else {
                //Haetaan myös vastaukset
                $poll['Answer'] = array();
                foreach ($poll['Response'] as $i => $v) {
                    $poll['Answer'][$i] = $this->Answer->find('all', array('conditions' => array('Answer.response_id' => $v['id']), 'recursive' => -1, 'fields' => array('id')));
                }

                //sitten poistamaan:
                foreach ($poll['Answer'] as $i => $v) {
                    foreach ($v as $vi => $vv) {
                        $this->Answer->delete($vv['Answer']['id'], false);
                    }
                }

                foreach ($poll['Overlay'] as $i => $v) {
                    $this->Poll->Overlay->PollsOverlay->delete($v['PollsOverlay']['id'], false);
                }
                foreach ($poll['Marker'] as $i => $v) {
                    $this->Poll->Marker->PollsMarker->delete($v['PollsMarker']['id'], false);
                }
                foreach ($poll['Path'] as $i => $v) {
                    $this->Poll->Path->PollsPath->delete($v['PollsPath']['id'], false);
                }
                foreach ($poll['Hash'] as $i => $v) {
                    $this->Poll->Hash->delete($v['id'], false);
                }
                foreach ($poll['Response'] as $i => $v) {
                    $this->Poll->Response->delete($v['id'], false);
                }
                foreach ($poll['Question'] as $i => $v) {
                    $this->Poll->Question->delete($v['id'], false);
                }
                $this->Poll->delete($poll['Poll']['id'], false);


                $this->Session->setFlash('Kysely poistettu');
                $this->redirect(array('action' => 'index'));

            }
        } else {
            // jos kyselyä ei löytynyt
            $this->cakeError('pollNotFound');
            $this->redirect(array('action' => 'index'));
        }
    }


    public function answers($pollId = null)
    {
        $authorId = $this->Auth->user('id');
        $this->Poll->id = $pollId;
        if (!$this->Poll->exists() 
            || $this->Poll->field('author_id') != $authorId) {
            $this->cakeError('pollNotFound');
        }

        $this->loadModel('Response');
        $this->Response->contain('Answer');
        $responses = $this->Response->findAllByPollId($pollId);

        $lines = array();
        foreach($responses as $response) {
            $line = array();
            foreach ($response['Answer'] as $answer) {
                $text = str_replace(
                    array("\r\n", "\r", "\n", "\t"),
                    " ",
                    $answer['answer']
                );

                $line[] = array(
                    "text" => $text,
                    "map" => $answer['map']
                    );
            }
            $lines[] = array(
                "date" => $response['Response']['created'],
                "answer" => $line
                );
        }
        $this->set('pollId', $pollId);
        $this->set('answers', $lines);
        //debug($lines);
        $poll = $this->Poll->read();
        $this->set('pollNam', $poll['Poll']['name']);
    }


    /**
     * Converts json string to array and then array to correct form for
     * Poll model
     *
     * @param string $data Json string
     * @return array
     */
    public function _jsonToPollModel($data)
    {
        $json = json_decode($data, true);
        $data = array(
            'Poll' => $json['poll'],
            'Question' => array(),
            'Path' => array(),
            'Marker' => array(),
            'Overlay' => array()
        );
        $data['Poll']['author_id'] = $this->Auth->user('id');
        $data['Poll']['public'] = empty($data['Poll']['public']) ? 0 : 1;

        foreach ($json['questions'] as $q) {
            if (empty($q)) {
                // Ignore NULL questions
                // Somekind of IE js bug
                continue;
            }

            #This is the old system and not in use anymore
            #Actually the 'answer_location' is only in use at the view page, not in the question anymore
            #if (empty($q['latlng'])) {
            #    // Answer cant have location if quest dont have
            #    $q['answer_location'] = 0;
            #} else {
                //$q['answer_location'] = empty($q['answer_location']) ? 0 : 1;
                if ($q['map_type'] > 1) { // If map type can have location, we set it so
                    $q['answer_location'] = 1;
                } else {
                    $q['answer_location'] = 0;
                }
            #}
            $q['answer_visible'] = empty($q['answer_visible']) ? 0 : 1;
			//monivalinnalle
			$q['otherchoice'] = empty($q['otherchoice']) ? 0 : 1;
            $q['comments'] = empty($q['comments']) ? 0 : 1;
            unset($q['visible']);
            $data['Question'][] = $q;
        }
        //if (empty($data['Question'])) {
        //    unset($data['Question']);
        //}

        foreach ($json['paths'] as $p) {
            $data['Path'][] = $p['id'];
        }
        if (empty($data['Path'])) {
            $data['Path'] = array();
        }

        foreach ($json['markers'] as $m) {
            $data['Marker'][] = $m['id'];
        }
        if (empty($data['Marker'])) {
            $data['Marker'] = array();
        }

        foreach ($json['overlays'] as $m) {
            $data['Overlay'][] = $m['id'];
        }
        if (empty($data['Overlay'])) {
            $data['Overlay'] = array();
        }
        return $data;
    }


    public function publish($pollId = null)
    {
        $authorId = $this->Auth->user('id');
        $this->Poll->id = $pollId;

        if (!$this->Poll->exists() 
            || $this->Poll->field('author_id') != $authorId) {
            $this->cakeError('pollNotFound');
        }

        if ($this->Poll->field('published') == null) {
            $this->Poll->saveField('published', date('Y-m-d H:i:s'));
            $this->Session->setFlash('Kysely julkaistu.');
        } else {
            $this->Session->setFlash('Kysely on jo julkaistu');
        }

        $this->redirect(array('action' => 'view', $pollId));
    }


    /**
     * View poll hashes
     */
    public function hashes($pollId = null)
    {
        $authorId = $this->Auth->user('id');
        $this->Poll->id = $pollId;

        if (!$this->Poll->exists()
            || $this->Poll->field('author_id') != $authorId) {
            $this->cakeError('pollNotFound');
        }

        $hashes = $this->Poll->Hash->findAllByPollId($pollId);
        $this->set('hashes', $hashes);
        $this->set('pollId', $pollId);
    }


    /**
     * Generate new hashes
     */
    public function generatehashes($pollId = null)
    {
        $authorId = $this->Auth->user('id');
        $this->Poll->id = $pollId;

        if (!$this->Poll->exists()
            || $this->Poll->field('author_id') != $authorId) {
            $this->cakeError('pollNotFound');
        }
        
        $count = $this->data['count'];

        if (!is_numeric($count)) {
            $this->Session->setFlas('Virheellinen lukumäärä');
        } else {
            $this->Poll->generateHashes($count);
        }

        $this->redirect(array('action' => 'hashes', $pollId));
    }

    public function openClosed($id = null) {
        if (!empty($id)) {
            $authorId = $this->Auth->user('id');
            $this->Poll->id = $id;
            if (!$this->Poll->exists() || $this->Poll->field('author_id') != $authorId) {
                $this->cakeError('pollNotFound');
                $this->redirect(array('action' => 'index'));
            } else {
                $poll = $this->Poll->read();
                //debug($poll);
                $edit['id'] = $id;
                if ($poll['Poll']['public']) {
                    $edit['public'] = 0;
                } else {
                    $edit['public'] = 1;
                }
                //debug($edit); die;
                if ($this->Poll->save($edit)) {
                    $this->Session->setFlash('Muutokset tallennettu');
                    $this->redirect(array('action' => 'view', $id));
                } else {
                    $this->Session->setFlash('Tallennus epäonnistui');
                }
            }
        } else {
            // jos kyselyä ei löytynyt
            $this->cakeError('pollNotFound');
            $this->redirect(array('action' => 'index'));
        }
    }


}








