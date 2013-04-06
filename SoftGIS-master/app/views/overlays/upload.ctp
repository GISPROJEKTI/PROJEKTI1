
<h1>Karttakuvan lataaminen</h1>

<?php echo $this->Form->create('Overlay', array('type' => 'file')); ?>
<?php echo $this->Form->file('file', array('type' => 'file')); ?>

<br>
<?php echo $this->Form->button('Jatka', 
    array('type'=>'submit')); ?>
<?php echo $this->Html->link('Peruuta', 
    array('action' => 'index'), array('class' => 'button cancel')); ?>

<?php echo $this->Form->end(); ?>


