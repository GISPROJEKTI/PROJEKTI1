<script type="text/javascript">
    var responses = <?php echo json_encode($answers); ?>;
    var head = <?php echo json_encode($header); ?>;
    var text = "";
    var pollNam = "<?php echo($pollNam); ?>";

    //header
    text = text + head.date;
    for (var x = 0; x < head.answer.length; x++) {
        text = text + ',"' + head.answer[x].text.replace(/"/g,'""') + '","' + head.answer[x].map + '"';
    }
    text = text + "\n";

    //answers
    for (var i = 0; i < responses.length; i++) {
        text = text + responses[i].date;
        for (var x = 0; x < responses[i].answer.length; x++) {
            var map = responses[i].answer[x].map; //if the old database does not contain a value at map.
            if (map) {
                map = google.maps.geometry.encoding.decodePath(map).toString();
            } else {
                map = "";
            }
            text = text + ',"' + responses[i].answer[x].text.replace(/"/g,'""') + '","' + map + '"';
        }
        text = text + "\n";
    }

    function setContent() {
        //Update answer content according the arswers
        document.getElementById("answer").innerHTML = text;
        document.getElementById("lataus").href = "data:application/csv;charset=utf-8," + text.replace(/\n/g,'%0A');
        document.getElementById("lataus").download = pollNam + '_vastaukset.csv';
    }

    window.addEventListener("load", setContent, false);
</script>

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
    <textarea rows="20" id="answer" wrap="off">

    </textarea>
    <a class="button" id="lataus" download="example.csv" href="data:application/csv;charset=utf-8,Col1%2CCol2%0AVal1%2CVal2">Lataa tiedostona</a>

</div>

<div class="help">
    <p>Vastaukset on esitetty CSV muodossa siten että jokaisen vastaajan vastaukset ovat yhdellä rivillä pilkulla erotettuina.</p>
    <p>Ensimmäinen rivi on otsikko, jossa ensin on teksti 'aika', sen jälkeen jokaista kysymystä kohden: tekstivastauksen tyyppi, karttavastauksen tyyppi. vastausten tyyppien selitteet ohjeen lopussa.</p>
    <p>Lopuilla riveilla rivin ensimmäinen arvo on vastausaika. Sen jälkeen arvot noudattavat seuraavaa sarjaa jokaista hysymystä kohden: vastaus, (leveyspiiri, pituuspiiri), jossa leveys- ja pituuspiirisarjoja voi olla monta tai ei yhtään kapplaetta</p>
    <p>Voit ladata vastaukset valmiiksi .csv -tiedostona 'Lataa tiedostona' -painikkeesta ohjeen yläpuolella, tai kopioida haluamasi rivit yllä ikkunasta.</p>

    <p><b>Tekstivastauksen tyyppi:</b></p>
        <p>0 = Ei tekstivastausta</p>
        <p>1 = Teksti</p>
        <p>2 = Kyllä, ei, en osaa sanoa</p>
        <p>3 = 1 - 5, en osaa sanoa</p>
        <p>4 = 1 - 7, en osaa sanoa</p>
        <p>5 = Monivalinta (max 9)</p>
    <p><b>Karttavastauksen tyyppi:</b></p>
        <p>0 = Ei karttaa</p>
        <p>1 = Kartta, ei vastausta</p>
        <p>2 = Kartta, 1 merkki</p>
        <p>3 = Kartta, monta merkkiä</p>
        <p>4 = Kartta, viiva</p>
        <p>5 = Kartta, alue</p>
</div>

