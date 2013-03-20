<script>

$(document).ready(function() {
    $(".detailed > .details").hide();
    var current = null;
    $(".detailed > .header").click(function() {
        var thisDetails = $(this).next();

        thisDetails.slideToggle();
        if (current) {
            if (current.get(0) == thisDetails.get(0)) {
                current = null;
            } else {
                current.slideToggle();
                current = thisDetails;
            }
        } else {
            current = thisDetails;
        }
        return false;
    });
});


</script>


<h2><?php echo $poll['name']; ?></h2>

<div class="subnav">
    <?php
    echo $this->Html->link(
        'Muokkaa',
        array(
            'action' => 'modify',
            $poll['id']
        ),
        array(
            'class' => 'button',
            'title' => 'Muokkaa kyselyä'
        )
    );
    echo $this->Html->link(
        'Kokeile',
        array(
            'controller' => 'answers',
            'action' => 'test',
            $poll['id']
        ),
        array(
            'class' => 'button',
            'title' => 'Voit kokeilla kyselyyn vastaamista ennen sen julkaisua. Kokeiluvastauksia ei tallenneta.'
        )
    );
    echo $this->Html->link(
        'Aseta aukioloaika',
        array(
            'action' => 'launch',
            $poll['id']
        ),
        array(
            'class' => 'button',
            'title' => 'Määrittele mistä mihin kysely on vastattavissa.'
        )
    );
    if ($poll['public'] == 0) {
        echo $this->Html->link(
            'Varmenteet',
            array(
                'action' => 'hashes',
                $poll['id']
            ),
            array(
                'class' => 'button',
                'title' => 'Luo ja tarkastele varmenteita, joiden avulla kyselyyn vastaajat todennetaan.'
            )
        );
    };
    echo $this->Html->link(
        'Vastaukset',
        array(
            'action' => 'answers',
            $poll['id']
        ),
        array(
            'class' => 'button',
            'title' => 'Tarkastele kyselyn vastauksia'
        )
    );
    ?>
</div>

<h3>Yleistiedot</h3>
<table class="details">
    <tr>
        <th class="fixed">Vastauksia</th>
        <td><?php echo $responseCount; ?></td>
    </tr>
    <tr>
        <th>Aukioloaika</th>
        <td>
            <?php
                $launch = $poll['launch'];
                if ($launch) {
                    echo date('j.n.Y', strtotime($launch)) . ' - ';
                    $end = $poll['end'];
                    if ($end) {
                        echo date('j.n.Y', strtotime($end));
                    } else {
                        echo 'ei päättymispäivää';
                    }
                } else {
                    echo 'Ei aukioloaikaa';
                }
            ?>
        </td>
    </tr>
    <tr>
        <th>Kaikille avoin</th>
        <td><?php echo $poll['public'] ? 'Kyllä' : 'Ei'; ?></td>
    </tr>
    <tr>
        <th>Kuvaus</th>
        <td><?php echo $poll['welcome_text']; ?></td>
    </tr>
    <tr>
        <th>Kiitosteksti</th>
        <td><?php echo $poll['thanks_text']; ?></td>
    </tr>
    <tr>
        <th>Vastausosoite</th>
        <td>
            <?php if ($poll['public'] == 0) {
                echo $this->Html->link(
                    'Katso varmenteet',
                    array(
                        'action' => 'hashes',
                        $poll['id']
                    )
                );
            }else{
                echo FULL_BASE_URL . $this->Html->url(
                    array(
                        'controller' => 'answers',
                        'action' => 'index',
                        $poll['id']
                    )
                );
            }; ?>
        </td>
    </tr>
</table>

<h3>Reitit ja alueet</h3>
<table class="details">
    <?php if (!empty($paths)): ?>
        <?php foreach ($paths as $path): ?>
            <tr>
                <th class="mediumfixed"><?php echo $path['name']; ?></th>
                <td><?php echo $path['content']; ?></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td>Kyselyn yhteydessä ei ole näytettäviä reittejä/alueita</td></tr>
    <?php endif; ?>
</table>

<h3>Karttamerkit</h3>
<table class="details">
    <?php if (!empty($markers)): ?>
        <?php foreach ($markers as $marker): ?>
            <tr>
                <th class="mediumfixed"><?php echo $marker['name']; ?></th>
                <td><?php echo $marker['content']; ?></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td>Kyselyn yhteydessä ei ole näytettäviä karttamerkkejä</td></tr>
    <?php endif; ?>
</table>

<h3>Kysymykset</h3>

<div>
<?php foreach ($questions as $q): ?>
    <div class="detailed">
        <div class="header">
            <span class="num"><?php echo $q['num']; ?></span>
            <span class="text"><?php echo $q['text']; ?></span>
        </div>
        <div class="details">
            <table class="details">
                <tr>
                    <th class="longfixed">Vastaus</th>
                    <td colspan="3"><?php echo $answers[$q['type']]; ?></td>
                </tr>
                <tr>
                    <th>Sijainti</th>
                    <td> 
                        <?php echo empty($q['latlng']) ? 'Ei' : $q['latlng']; ?>
                    </td>
                </tr>
                <tr>
                    <th>Zoom-taso</th>
                    <td> 
                        <?php echo empty($q['zoom']) ? 'Ei' : $q['zoom']; ?>
                    </td>
                </tr>
                <tr>
                    <th>Kohteen merkitseminen kartalle</th>
                    <td> 
                        <?php echo $q['answer_location'] ? 'Kyllä' : 'Ei'; ?>
                    </td>
                </tr>
                <tr>
                    <th>Vastaukset näkyvissä muille vastaajille</th>
                    <td> 
                        <?php echo $q['answer_visible'] ? 'Kyllä' : 'Ei'; ?>
                    </td>
                </tr>
                <tr>
                    <th>Vastausten kommentointi</th>
                    <td colspan="3"> 
                        <?php echo $q['comments'] ? 'Kyllä' : 'Ei'; ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>
<?php endforeach; ?>
</div>

<?php
/*  <!-- Sama asia, kuin yllä, kommentoitu pois -->
<table class="details">
    <?php foreach ($questions as $q): ?>
        <tr>
            <td colspan="4" class="ul"><h4><?php echo $q['num']; ?></h4></td>
        </tr>
        <tr>
            <th class="fixed">Kysymys</th>
            <td colspan="3"><?php echo $q['text']; ?></td>
        </tr>
        <tr>
            <th>Vastaus</th>
            <td colspan="3"><?php echo $answers[$q['type']]; ?></td>
        </tr>
        <tr>
            <th>Sijainti</th>
            <td> 
                <?php echo empty($q['latlng']) ? 'Ei' : $q['latlng']; ?>
            </td>
            <th>Zoom-taso</th>
            <td> 
                <?php echo empty($q['zoom']) ? 'Ei' : $q['zoom']; ?>
            </td>
        </tr>
        <tr>
            <th>Kohteen merkitseminen kartalle</th>
            <td> 
                <?php echo $q['answer_location'] ? 'Kyllä' : 'Ei'; ?>
            </td>
            <th>Vastaukset näkyvissä muille vastaajille</th>
            <td> 
                <?php echo $q['answer_visible'] ? 'Kyllä' : 'Ei'; ?>
            </td>
        </tr>
        <tr>
            <th>Vastausten kommentointi</th>
            <td colspan="3"> 
                <?php echo $q['comments'] ? 'Kyllä' : 'Ei'; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
*/
?>