<script type="text/javascript">
    var data = <?php echo json_encode($answers); ?>;
    var text = "";
    var pollNam = "<?php echo($pollNam); ?>";

    for (var i = 0; i < data.length; i++) {
        text = text + data[i].date;
        for (var x = 0; x < data[i].answer.length; x++) {
            var map = data[i].answer[x].map; //if the old database does not contain a value at map.
            if (map) {
                map = google.maps.geometry.encoding.decodePath(map).toString();
            } else {
                map = "";
            }
            text = text + ',"' + data[i].answer[x].text.replace(/"/g,'""') + '","' + map + '"';
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
    <p>Vastaukset on esitetty CSV muodossa siten että käyttäjän vastaukset ovat yhdellä rivillä pilkulla erotettuina.</p>
    <p>Rivin ensimmäinen arvo on vastausaika. Sen jälkeen arvot noudattavat seuraavaa sarjaa jokaista hysymystä kohden: vastaus, (leveyspiiri, pituuspiiri), jossa leveys- ja pituuspiirisarjoja voi olla monta tai ei yhtään kapplaetta</p>
    <p>Voit ladata vastaukset valmiiksi .csv -tiedostona 'Lataa tiedostona' -painikkeesta ohjeen yläpuolella</p>
</div>

