<script>

$(document).ready(function() {
    $("#PollLaunch").datepicker();
    $("#PollEnd").datepicker();
});

</script>

<div class="subnav">
    <?php echo $this->Html->link(
        'Takaisin',
        array(
            'action' => 'view',
            $poll['Poll']['id']
        ),
        array(
            'class' => 'button'
        )
    ); ?>
</div>

<?php echo $this->Form->create('Poll'); ?>

<div class="input text">
    <label>Alkamispäivä</label>
    <?php echo $this->Form->text(
        'launch',
        array(
            'type' => 'text', 
            'class' => 'small',
        )
    ); ?>
</div>

<div class="input text">
    <label>Päättymispäivä</label>
    <?php echo $this->Form->text(
        'end',
        array(
            'type' => 'text', 
            'class' => 'small',
        )
    ); ?>
</div>

<button type="submit" id="saveButton">
    Tallenna muutokset
</button>
<?php echo $this->Html->link(
    'Peruuta',
    array(
        'action' => 'view',
        $poll['Poll']['id']
    ),
    array(
        'class' => 'button cancel small'
    )
); ?>
<?php echo $this->Form->end(); ?>

<div class="help">
    <p>Kyselyn aukioloaikana käyttäjät voivat vastata kyselyyn. Alkamis- ja päättymispäivä sisältyvät aukioloaikaan.</p>
    <p>Kysely on suljettu, jos alkamispäivämäärää ei ole asettetu.</p>
</div>