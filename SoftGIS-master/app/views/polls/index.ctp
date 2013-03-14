<script>

var confirmPublish = "Haluatko varmasti julkaista kyselyn? Julkaisun jälkeen kyselyä ei voida enää muokata";

$( document ).ready(function() {
    $( "a.publish" ).click(function() {
        return confirm( confirmPublish );
    });
});

</script>

<h2>Omat kyselyt</h2>
<table class="list">
    <thead>
        <tr>
            <th>Nimi</th>
            <th>Testaa</th>
            <th>Julkinen</th>
			<!-- Tähän on lisätty Poista kenttä-->
            <th>Poista</th>
			<!-- Tässä loppuu-->
            <th>Vastauksia</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($polls as $poll): ?>
            <tr>
                <td>
                    <?php echo $this->Html->link(
                        $poll['Poll']['name'],
                        array(
                            'controller' => 'polls', 
                            'action' => 'view',
                            $poll['Poll']['id']
                        ),
                        array(
                            'title' => 'Tarkastele kyselyä'
                        )
                    ); ?>
                </td>
                <td>
                    <?php echo $this->Html->link(
                        'Testaa',
                        array(
                            'controller' => 'answers', 
                            'action' => 'test',
                            $poll['Poll']['id']
                        ),
                        array(
                            'title' => 'Testikäytä kyselyä'
                        )
                    ); ?>
                </td>
                <td>
                    <?php if ($poll['Poll']['public']) {
                        echo 'Kyllä';
                    } else {
                        echo 'Ei, ';
                        echo $this->Html->link(
                            'hashit',
                            array('action' => 'hashes', $poll['Poll']['id'])
                        ); 
                    } ?>
                </td>
				<!-- Tässä Poista napin toiminnallisuus-->
				<td>
                    <?php echo $this->Html->link(
                        'Poista',
                        array(
                            'controller' => 'answers', 
                            'action' => 'delete',
                            $poll['Poll']['id'],
                        ),
                        array(
                            'title' => 'Poista kysely'
                        ),
						'Oletko varma että haluat poistaa kyselyn?'
                    ); ?>
                </td>
				<!--Tässä loppuu-->
				
                <td><?php echo count($poll['Response']); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>