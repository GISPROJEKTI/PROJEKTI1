<h2>Karttamerkit</h2>
<div class="subnav">
    <?php echo $this->Html->link(
        'Luo uusi karttamerkki',
        array('action' => 'edit'),
        array('class' => 'button')
    ); ?>
</div>

<h3>Käyttäjän karttamerkit</h3>
<table class="list">
    <thead>
        <tr>
            <th>Nimi</th>
            <th>Muokkaa</th>
            <th>Muokattu</th>
            <th>Kopioi</th>
            <th>Poista</th>
            <th>Käytössä</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($markers as $marker): ?>
            <tr>
                <td>
                    <?php echo $this->Html->link(
                        $marker['Marker']['name'], 
                        array('action' => 'view', $marker['Marker']['id']),
                        array('title' => 'Katsele karttakerkkiä')
                        );
                    ?>
                </td>
                <td>
                    <?php echo $this->Html->link(
                        'muokkaa', 
                        array('action' => 'edit', $marker['Marker']['id']),
                        array('title' => 'Muokkaa karttakerkkiä')
                        );
                    ?>
                </td>
                <td>
                    <?php echo $marker['Marker']['modified']; ?>
                </td>
                <td>
                    <?php echo $this->Html->link(
                        'kopioi', 
                        array('action' => 'copy', $marker['Marker']['id']),
                        array('title' => 'Kopioi karttamerkki'),
                        'Oletko varma että haluat kopioida karttakerkin?'
                        );
                    ?>
                </td>
                <td>
                    <?php echo $this->Html->link(
                        'poista', 
                        array('action' => 'delete', $marker['Marker']['id']),
                        array('title' => 'Poista karttamerkki'),
                        'Oletko varma että haluat poistaa karttakerkin?'
                        );
                    ?>
                </td>
                <td>
                    <?php echo count($marker['Poll']); ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- <h3>Muiden karttamerkit</h3> //Muiden käyttäjien tiedot poistettu käytöstä mahdollisten aineistojen lisenssiongelmien vuoksi
<table class="list">
    <thead>
        <tr>
            <th>Nimi</th>
            <th>Kopioi</th>
        </tr>
    </thead>
    <tbody>
        <?php #foreach ($others_markers as $marker): ?>
            <tr>
                <td>
                    <?php echo $this->Html->link(
                        $marker['Marker']['name'], 
                        array('action' => 'view', $marker['Marker']['id']),
                        array('title' => 'Katsele aineistoa')
                        );
                    ?>
                </td>
                <td>
                    <?php echo $this->Html->link(
                        'kopioi', 
                        array('action' => 'copy', $marker['Marker']['id']),
                        array('title' => 'Kopioi aineisto'),
                        'Oletko varma että haluat kopioida aineiston?'
                        );
                    ?>
                </td>
            </tr>
        <?php #endforeach; ?>
    </tbody>
</table> -->

