<div class="subnav">
    <?php echo $this->Html->link(
        'Takaisin',
        array(
            'action' => 'view',
            $pollId
        ),
        array(
            'class' => 'button'
        )
    ); ?>
</div>

<div class="input textarea">
    <label>Vastaukset</label>
    <textarea rows="20">
<?php foreach ($answers as $a): ?>
<?php echo $a; ?>

<?php endforeach; ?>
    </textarea>
</div>

<div class="help">
    <p>Vastaukset on esitetty CSV muodossa siten että käyttäjän vastaukset ovat yhdellä rivillä pilkulla erotettuina.</p>
    <p>Rivin ensimmäinen arvo on vastausaika. Sen jälkeen arvot noudattavat seuraavaa sarjaa jokaiselle vastaukselle: vastaus, leveyspiiri, pituuspiiri.</p>
</div>