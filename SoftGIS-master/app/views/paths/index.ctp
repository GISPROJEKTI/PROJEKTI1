<h2>Reitit ja alueet</h2>
<div class="subnav">
<!-- Tätä ominaisuutta ei ole vielä toteutettu
    <?php echo $this->Html->link(
        'Luo uusi reitti',
        array('action' => 'edit'),
        array('class' => 'button')
    ); ?>
-->

    <?php echo $this->Html->link(
        'Tuo reitti tiedostosta',
        array('action' => 'import'),
        array('class' => 'button')
    ); ?>
</div>
<ul>
    <?php foreach ($paths as $m) {
        echo '<li>' . $this->Html->link(
            $m['Path']['name'], 
            array('action' => 'edit', $m['Path']['id'])
        ) . '</li>';
    } ?>
</ul>