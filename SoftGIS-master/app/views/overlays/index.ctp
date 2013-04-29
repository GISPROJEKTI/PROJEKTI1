<h2>Karttakuvat</h2>
<div class="subnav">
    <?php echo $this->Html->link(
        'Luo uusi karttakuva',
        array('action' => 'upload'),
        array('class' => 'button')
    ); ?>
</div>

<h3>Käyttäjän karttakuvat</h3>
<table class="list">
    <thead>
        <tr>
            <th>Nimi</th>
            <th>Muokkaa</th>
            <th>Muokattu</th>
            <th>Kopioi</th>
            <th>Poista</th>
            <th>Käytössä</th>
            <th>Kuvatiedosto</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($overlays as $overlay): ?>
            <tr>
                <td>
                    <?php echo $this->Html->link(
                        $overlay['Overlay']['name'], 
                        array('action' => 'view', $overlay['Overlay']['id']),
                        array('title' => 'Katsele karttakuvaa')
                        );
                    ?>
                </td>
                <td>
                    <?php echo $this->Html->link(
                        'muokkaa', 
                        array('action' => 'edit', $overlay['Overlay']['id']),
                        array('title' => 'Muokkaa karttakuvaa')
                        );
                    ?>
                </td>
                <td>
                    <?php echo $overlay['Overlay']['modified']; ?>
                </td>
                <td>
                    <?php echo $this->Html->link(
                        'kopioi', 
                        array('action' => 'copy', $overlay['Overlay']['id']),
                        array('title' => 'Kopioi aineisto'),
                        'Oletko varma että haluat kopioida karttakuvan?'
                        );
                    ?>
                </td>
                <td>
                    <?php echo $this->Html->link(
                        'poista', 
                        array('action' => 'delete', $overlay['Overlay']['id']),
                        array('title' => 'Poista aineisto'),
                        'Oletko varma että haluat poistaa karttakuvan?'
                        );
                    ?>
                </td>
                <td>
                    <?php echo count($overlay['Poll']); ?>
                </td>
                <td>
                    <?php if (file_exists(APP.'webroot'.DS.'overlayimages'.DS.$overlay['Overlay']['image'])) {
                        echo 'Löytyy';
                    } else {
                        echo 'Hukassa';
                    } ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h3>Muiden karttakuvat</h3>
<table class="list">
    <thead>
        <tr>
            <th>Nimi</th>
            <th>Kopioi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($others_overlays as $overlay): ?>
            <tr>
                <td>
                    <?php echo $this->Html->link(
                        $overlay['Overlay']['name'], 
                        array('action' => 'view', $overlay['Overlay']['id']),
                        array('title' => 'Katsele karttakuvaa')
                        );
                    ?>
                </td>
                <td>
                    <?php echo $this->Html->link(
                        'kopioi', 
                        array('action' => 'copy', $overlay['Overlay']['id']),
                        array('title' => 'Kopioi aineisto'),
                        'Oletko varma että haluat kopioida karttakuvan?'
                        );
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

