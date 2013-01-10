<h2>Karttamerkit</h2>
<div class="subnav">
    <?php echo $this->Html->link(
        'Luo uusi karttamerkki',
        array('action' => 'edit'),
        array('class' => 'button')
    ); ?>
</div>
<ul>
    <?php foreach ($markers as $m) {
        echo '<li>' . $this->Html->link(
            $m['Marker']['name'], 
            array('action' => 'edit', $m['Marker']['id'])
        ) . '</li>';
    } ?>
</ul>