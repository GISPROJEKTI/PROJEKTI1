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
        //debug($this->data);
        //debug($data);

        $session = $this->Session->read('answer');
        if (empty($session)) {
            $this->cakeError('pollNotFound');
        }
        $this->Poll->id = $session['poll'];
        $this->Poll->contain('Question');
        $poll = $this->Poll->read();

        //debug($session); //die;

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
                if (!isset($poll['Question'][$i])) {
                    break;
                }
                $question = $poll['Question'][$i];

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
}

