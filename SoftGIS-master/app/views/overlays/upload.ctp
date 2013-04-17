<script>
$(document).ready(function() { // init when page has loaded

    // Help toggle
    $( ".help" ).hide();
    $( "#toggleHelp" ).click(function() {
        $( ".help" ).fadeToggle(400, "swing");
        return false;
    });
});
</script>

<div class="answerMenu">
    <a href="#help" class="button" id="toggleHelp">Ohje</a>
</div>
<h1>Karttakuvan lataaminen</h1>

<div class="help">
    <h2>Karttakuvan lataaminen</h2>
	<p>Voit tuoda kuvatiedoston omalta tietokoneeltasi painamalla "Selaa.." -nappia. </p>
    <p>Karttakuva voi olla gif, jpeg, png tai jpg tyyppinen ja enintään 1,5Mt kokoinen.</p>
</div>
Hyväksytyt tiedostoformaatit: jpeg, jpg, png ja gif<br/>
Max tiedostokoko: 1,5Mt
<br/>
<br/>
<div class="form">
	<?php echo $this->Form->create('Overlay', array('type' => 'file')); ?>
	<?php echo $this->Form->file('file', array('type' => 'file')); ?>

	<br>
	<?php echo $this->Form->button('Jatka', 
	    array('type'=>'submit')); ?>
	<?php echo $this->Html->link('Peruuta', 
	    array('action' => 'index'), array('class' => 'button cancel')); ?>

	<?php echo $this->Form->end(); ?>
</div>


