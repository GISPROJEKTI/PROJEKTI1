<h2>Viivat ja alueet</h2>
<div class="subnav">
    <?php echo $this->Html->link(
        'Luo uusi aineisto',
        array('action' => 'edit'),
        array('class' => 'button')
    );
    echo $this->Html->link(
        'Tuo aineisto tiedostosta',
        array('action' => 'import'),
        array('class' => 'button')
    ); ?>
</div>

<h3>Käyttäjän aineistot</h3>
<table class="list"><!--Käyttäjän omat aineistot-->
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
        <?php foreach ($paths as $path): ?>
            <tr>
                <td>
                    <?php echo $this->Html->link(
                        $path['Path']['name'], 
                        array('action' => 'view', $path['Path']['id']),
                        array('title' => 'Katsele aineistoa')
                        );
                    ?>
                </td>
                <td>
                    <?php echo $this->Html->link(
                        'muokkaa', 
                        array('action' => 'edit', $path['Path']['id']),
                        array('title' => 'Muokkaa aineistoa')
                        );
                    ?>
                </td>
                <td>
                    <?php echo $path['Path']['modified']; ?>
                </td>
                <td>
                    <?php echo $this->Html->link(
                        'kopioi', 
                        array('action' => 'copy', $path['Path']['id']),
                        array('title' => 'Kopioi aineisto'),
                        'Oletko varma että haluat kopioida aineiston?'
                        );
                    ?>
                </td>
                <td>
                    <?php echo $this->Html->link(
                        'poista', 
                        array('action' => 'delete', $path['Path']['id']),
                        array('title' => 'Poista aineisto'),
                        'Oletko varma että haluat poistaa aineiston?'
                        );
                    ?>
                </td>
                <td>
                    <?php echo count($path['Poll']); ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h3>Muiden aineistot</h3>
<table class="list"> <!--Muiden aineistot-->
    <thead>
        <tr>
            <th>Nimi</th>
            <th>Kopioi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($others_paths as $path): ?>
            <tr>
                <td>
                    <?php echo $this->Html->link(
                        $path['Path']['name'], 
                        array('action' => 'view', $path['Path']['id']),
                        array('title' => 'Katsele aineistoa')
                        );
                    ?>
                </td>
                <td>
                    <?php echo $this->Html->link(
                        'kopioi', 
                        array('action' => 'copy', $path['Path']['id']),
                        array('title' => 'Kopioi aineisto'),
                        'Oletko varma että haluat kopioida aineiston?'
                        );
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

