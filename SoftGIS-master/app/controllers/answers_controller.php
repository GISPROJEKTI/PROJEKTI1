<?php

class AnswersController extends AppController
{
    public $uses = array('Answer', 'Poll', 'Response', 'Hash');

    public function beforeFilter()
    {
        parent::beforeFilter();
        // error_reporting(E_ALL);
        $this->Auth->allow('index', 'poll', 'welcome', 'answer', 'finish');
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

                if ($question['answer_location'] == 1) {
                    $latLng = explode(',', $a->loc);
                    $lat = !empty($latLng[0]) ? (float)$latLng[0] : "";
                    $lng = !empty($latLng[1]) ? (float)$latLng[1] : "";
                } else {
                    $lat = '';
                    $lng = '';
                }

                $this->Answer->create(
                    array(
                        'response_id' => $responseId,
                        'question_id' => $question['id'],
                        'answer' => strip_tags(trim($a->text)),
                        'lat' => $lat,
                        'lng' => $lng
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
}