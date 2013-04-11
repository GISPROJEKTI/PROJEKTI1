<h2>Karttakuvat</h2>
<div class="subnav">
    <?php echo $this->Html->link(
        'Luo uusi karttakuva',
        array('action' => 'upload'),
        array('class' => 'button')
    ); ?>
</div>
<ul>
    <?php foreach ($overlay as $m) {
        echo '<li>' . $this->Html->link(
            $m['Overlay']['name'], 
            array('action' => 'view', $m['Overlay']['id'])
        ) . '</li>';
    } ?>
</ul>