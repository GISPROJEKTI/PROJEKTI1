<?php echo $this->Html->script('create_entry'); ?>

<h1>Luo merkki kartalle</h1>


<!-- Form -->
<?php echo $this->Form->create('Entry', array('id' => 'entry')); ?>
<?php echo $this->Form->input(
    'name',
    array('label' => 'Nimi')
); ?>

<?php echo $this->Form->input(
    'content',
    array('label' => 'Sisältö')
); ?>

<?php echo $this->Form->input(
    'type',
    array(
        'label' => 'Tyyppi',
        'options' => array(
            'Marker' => 'Merkki',
            // 'Polyline' => 'Polku'
        )
    )
); ?>

<?php echo $this->Form->input(
    'Entry.modifiers.icon',
    array('label' => 'Kuvake')
); ?>

<? /*
<?php echo $this->Form->input(
    'Entry.modifiers.strokeColor',
    array('label' => 'Väri')
); ?>

<?php echo $this->Form->input(
    'Entry.modifiers.strokeWeight',
    array('label' => 'Viivan paksuus')
); ?>
*/ ?>

<div class="input map-container">
    <div id="map" class="map">
    </div>

    <div class="coords">
        <ul id="coords">
        </ul>
    </div>
</div>

<?php echo $this->Form->end('Luo'); ?>