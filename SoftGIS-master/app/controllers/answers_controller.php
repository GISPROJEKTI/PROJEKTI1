<?php

class AnswersController extends AppController
{
    public $uses = array('Answer', 'Poll', 'Response', 'Hash', 'Question');

    public function beforeFilter()
    {
        parent::beforeFilter();
        // error_reporting(E_ALL);
        $this->Auth->allow('index', 'poll', 'welcome', 'answer', 'finish', 'publicanswers');
        //debug($this->Auth->allowedActions); die;
    }

    public function index($pollId = null, $hash = null)
    {
        $this->Poll->id = $pollId;
        if ($this->Poll->exists()) {

            // Make sure poll is active
            $launch = $this->Poll->field('launch');
            $end = $this->Poll->field('end');

            if (!$launch || strtotime($launch) > time()) {
                $this->cakeError('pollNotPublished');
            } else if($end && strtotime('+1 day', strtotime($end)) < time()) {
                $this->cakeError('pollClosed');
            }

            if (!$this->Poll->field('public')) {
                // Validate hash if poll is not public
                if (!$this->Poll->validHash($hash)) {
                    $this->cakeError('invalidHash');
                }
            } 

            $this->Session->write(
                'answer', 
                array(
                    'poll' => $pollId,
                    'hash' => $hash,
                    'test' => false
                )
            );
            $this->redirect(array('action' => 'answer'));
        } else {
            $this->cakeError('pollNotFound');
        }
    }

    /**
     * Authed only action for testing polls
     *
     */
    public function test($pollId = null)
    {
        $this->Session->write('answer', array()); // Clear session
        $hash = null;
        $this->Poll->id = $pollId;

        if ($this->Poll->exists()) {
            $this->Session->write(
                'answer', 
                array(
                    'poll' => $pollId,
                    'hash' => $hash,
                    'test' => true
                )
            );

            $this->redirect(array('action' => 'answer'));
        } else {
            $this->cakeError('pollNotFound');
        }
    }

    public function answer()
    {
        $session = $this->Session->read('answer');
        if (empty($session)) {
            $this->cakeError('pollNotFound');
        }

        $this->Poll->contain('Marker', 'Path', 'Question', 'Response', 'Overlay');
        $this->Poll->id = $session['poll'];
        $poll = $this->Poll->read();
        $this->set('poll', $poll);
        
    }

    public function finish()
    {
        $data = json_decode($this->data);

        $session = $this->Session->read('answer');
        if (empty($session)) {
            $this->cakeError('pollNotFound');
        }
        $this->Poll->id = $session['poll'];
        $this->Poll->contain('Question');
        $poll = $this->Poll->read();

        // debug($session);die;

        if ($session['test'] == 0) {
            // Not test answer, update db
            $hash = $session['hash'];

            // Create response entry
            $this->Response->create(
                array(
                    'poll_id' => $session['poll'],
                    'created' => date('Y-m-d H:i:s'),
                    'hash' => $session['hash']
                )
            );
            $this->Response->save();
            $responseId = $this->Response->id;

            foreach ($data as $i => $a) {
                if (!isset($a->text) || !isset($a->loc)) {
                    break;
                }
                if (!isset($poll['Question'][$i])) {
                    break;
                }
                $question = $poll['Question'][$i];

                /*
                if ($question['answer_location'] == 1) {
                    $latLng = explode(',', $a->loc);
                    $lat = !empty($latLng[0]) ? (float)$latLng[0] : "";
                    $lng = !empty($latLng[1]) ? (float)$latLng[1] : "";
                } else {
                    $lat = '';
                    $lng = '';
                }
                */

                $this->Answer->create(
                    array(
                        'response_id' => $responseId,
                        'question_id' => $question['id'],
                        'answer' => strip_tags(trim($a->text)),
                        'map' => strip_tags(trim($a->loc))
                    )
                );
                $this->Answer->save();
            }

            // Tag hash as used
            if (!empty($session['hash'])) {
                $hashEntry = $this->Hash->findByHash($hash);
                $hashEntry['Hash']['used'] = 1;
                $this->Hash->save($hashEntry);
            }
        }

        // Clear answer session
        $this->Session->write('answer', array());

        $this->set('poll', $poll);
        $this->set('test', $session['test']);
        $this->render('finish');
    }

    public function publicanswers()
    {
        $questionId = $this->params['url']['question'];
        $this->Answer->Question->id = $questionId;
        if ($this->Answer->Question->exists() 
            && $this->Answer->Question->field('answer_visible') == 1) {
            $this->Answer->recursive = -1;
            $answers = $this->Answer->findAllByQuestionId($questionId);
        } else {
            $answers = array();
        }
        $this->set('answers', $answers);
    }
	
	function delete($pollId = NULL){
		//$this->Poll->delete($pollId);
		
		//tämä funktio on tietyn kyselyn ja kaikki siihen liittyvien tietojen poisto tietokannasta
		
		//poistetaan polls taulusta kysely id:n mukaan
		$stats="DELETE FROM polls WHERE id='$pollId'";
		$result = mysql_query($stats);
		
		//haetaan ja tallennetaan responses taulusta response id
		$stats="SELECT * FROM responses WHERE poll_id='$pollId'";
		$result = mysql_query($stats);
		
		while($row = mysql_fetch_array($result)) {
			$resp = $row['id'];
			//poistetaan answers taulusta kaikki joissa on aiemmin haettu response id
			$stats="DELETE FROM answers WHERE response_id='$resp'";
			$result2 = mysql_query($stats);
		}
		
		
		//poistetaan response taulusta tavara
		$stats="DELETE FROM responses WHERE poll_id='$pollId'";
		$result = mysql_query($stats);
		
		//poistetaan hashes taulusta tavara
		$stats="DELETE FROM hashes WHERE poll_id='$pollId'";
		$result = mysql_query($stats);
		
		//poistetaan polls_markers taulusta tavara
		$stats="DELETE FROM polls_markers WHERE poll_id='$pollId'";
		$result = mysql_query($stats);
		
		//poistetaan polls_overlays taulusta tavara
		$stats="DELETE FROM polls_overlays WHERE poll_id='$pollId'";
		$result = mysql_query($stats);
		
		//poistetaan polls_paths taulusta tavara
		$stats="DELETE FROM polls_paths WHERE poll_id='$pollId'";
		$result = mysql_query($stats);
		
		//poistetaan questions taulusta tavara
		$stats="DELETE FROM questions WHERE poll_id='$pollId'";
		$result = mysql_query($stats);
		
		//tulostetaan viesti ja takaisin index sivulle
		$this->Session->setFlash('Kysely on poistettu');
		$this->redirect(array('controller'=>'polls', 'action'=>'index'));
	
	}
	
	function duplicate($pollId = NULL){
		
		//tämä funktio on kyselyn ja kaikkien siihen liittyvien tietojen kopiointi uudeksi kyselyksi uudella id:llä
		
		//valitaan kaikki tiedot pollin id:n perusteella
		$stats="SELECT * FROM polls WHERE id='$pollId'";
		$result = mysql_query($stats);
		
		//if($result == FALSE){
		//	die(mysql_error());
		//}
		
		//haetaan tarvittavien fieldien arvot eri muuttujiin
		$row = mysql_fetch_array($result);
		$pollName = $row['name'] . "_copy";
		$pollAuthor = $row['author_id'];
		$pollWelcome = $row['welcome_text'];
		$pollThanks = $row['thanks_text'];
		
		//echo $pollName;
		//die();
		
		//insertataan tiedot uudestaan polls tauluun, jolloin se saa uuden id:n
		$stats="INSERT INTO polls (name, author_id, public, welcome_text, thanks_text) VALUES ('$pollName', '$pollAuthor', 1, '$pollWelcome', '$pollThanks')";
		$result = mysql_query($stats);
		
		//haetaan kyseisen authorin suurin id polls taulusta joka on juuri insertatun id-fieldin arvo
		$stats="SELECT MAX(id) AS SuurinID FROM polls WHERE author_id='$pollAuthor'";
		$result = mysql_query($stats);
		$row2 = mysql_fetch_array($result);
		$pollId2 = $row2['SuurinID'];
		//echo $pollId2;
		//die(mysql_error());
		
		//haetaan alkuperäisen kyselyn kysymysten tiedot ja insertataan ne questions tauluun uuden kyselyn id:llä 
		
		$stats="SELECT * FROM questions WHERE poll_id='$pollId'";
		$result = mysql_query($stats);
		
		while($row3 = mysql_fetch_array($result)) {
		
			$queNum= $row3['num'];
			$queType = $row3['type'];
			$queLowText = $row3['low_text'];
			$queText = $row3['text'];
			$queHighText = $row3['high_text'];
			$queLat = $row3['lat'];
			$queLng = $row3['lng'];
			$queZoom = $row3['zoom'];
			$queAnswerLocation = $row3['answer_location'];
			$queAnswerVisible = $row3['answer_visible'];
			$queComments = $row3['comments'];
			
			$stats="INSERT INTO questions (poll_id, num, type, low_text, text, high_text, lat, lng, zoom, answer_location, answer_visible, comments)
			VALUES ('$pollId2', '$queNum', '$queType', '$queLowText', '$queText', '$queHighText', '$queLat', '$queLng', '$queZoom', '$queAnswerLocation', 
			'$queAnswerVisible', '$queComments')";
			$result2 = mysql_query($stats);
		
		}
		//haetaan alkuperäisen kyselyn polls_markers tiedot ja insertataan ne polls_markers tauluun uuden kyselyn id:llä 
		
		$stats="SELECT * FROM polls_markers WHERE poll_id='$pollId'";
		$result = mysql_query($stats);
		
		while($row4 = mysql_fetch_array($result)) {
		
			$markerId= $row4['marker_id'];
			
			$stats="INSERT INTO polls_markers (poll_id, marker_id) VALUES ('$pollId2', '$markerId')";
			$result2 = mysql_query($stats);
		
		}
		
		//haetaan alkuperäisen kyselyn polls_paths tiedot ja insertataan ne polls_paths tauluun uuden kyselyn id:llä 
		
		$stats="SELECT * FROM polls_paths WHERE poll_id='$pollId'";
		$result = mysql_query($stats);
		
		while($row5 = mysql_fetch_array($result)) {
		
			$pathId= $row5['path_id'];
			
			$stats="INSERT INTO polls_paths (poll_id, path_id) VALUES ('$pollId2', '$pathId')";
			$result2 = mysql_query($stats);
		
		}
		
		//haetaan alkuperäisen kyselyn polls_overlays tiedot ja insertataan ne polls_overlays tauluun uuden kyselyn id:llä
		
		$stats="SELECT * FROM polls_overlays WHERE poll_id='$pollId'";
		$result = mysql_query($stats);
		
		while($row6 = mysql_fetch_array($result)) {
		
			$overlayId= $row6['overlay_id'];
			
			$stats="INSERT INTO polls_overlays (poll_id, path_id) VALUES ('$pollId2', '$overlayId')";
			$result2 = mysql_query($stats);
		
		}
		
		//nyt kaikki kyselyyn liittyvät tiedot on kopioitu tietokantaan ja liitetty uuteen kyselyyn pl. hashit, responset ja answerit
		
		//tulostetaan viesti ja takaisin index sivulle
		$this->Session->setFlash("Kysely on kopioitu");
		$this->redirect(array('controller'=>'polls', 'action'=>'index'));
	
	}
}