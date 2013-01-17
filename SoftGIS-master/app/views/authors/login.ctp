<?php
    echo $this->Session->flash('auth');
    echo $this->Form->create('Author');
    echo $this->Form->input(
        'username', 
        array('label' => 'Käyttäjänimi', 'autofocus' => 'autofocus')
    );
    echo $this->Form->input(
        'password',
        array('label' => 'Salasana')
    );
?>
<button type="submit">Kirjaudu</button>
<?php echo $this->Form->end(); ?>
