<script>


var map;
var elements = [];

var decodedCoordinates = new google.maps.MVCArray();

$( document ).ready(function() {

    // Help toggle
    $( ".help" ).hide();
    $( "#toggleHelp" ).click(function() {
        $( ".help" ).fadeToggle(400, "swing");
        return false;
    });

    $("#PathStrokeOpacity").spinner({
        min: 0.0,
        max: 1.0,
        step: 0.05
    });

    $("#PathFillOpacity").spinner({
        min: 0.0,
        max: 1.0,
        step: 0.05
    });

    $("#PathStrokeWeight").spinner({
        min: 0.0,
        max: 10.0,
        step: 0.5
    });

    //alustetaan markkerit kartalle
    encodeCoordinates()

    //get the route's position from array
    var pos = null;
    if (decodedCoordinates.getLength() > 0) {
        pos = {
            pos: decodedCoordinates.getAt(0)[0],
            zoom: 8
        };
    }

    map = new google.maps.Map( 
        $( "#map" ).get()[0],
        {
            zoom: pos ? pos.zoom : 5,
            center: pos ? pos.pos : new google.maps.LatLng(64.94216, 26.235352),
            streetViewControl: false,
            disableDoubleClickZoom: true,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        }
    );

    google.maps.event.addListener(map, 'click', addLatLng); //add point

    //Lisää aineistot kartalle:
    decodedCoordinates.forEach(newElement);
    //console.info(elements);

    //Aineistojen attribuuttien muuttuessa:
    $( "#PathEditForm input" ).change(function() {
        var val = $(this).val();
        _.each(elements, function(el) {
            el.setOptions({ 
                strokeColor: "#" + $("#PathStrokeColor").val(),
                strokeOpacity: $("#PathStrokeOpacity").val(),
                strokeWeight: $("#PathStrokeWeight").val(),
                fillColor: "#" + $("#PathFillColor").val(),
                fillOpacity: $("#PathFillOpacity").val(),
            });
        });
    });

    $( "#submit" ).click(function() {
        var encodedCoordinates = "";
        for (var i = 0; i < elements.length; i++) {
            var path = elements[i].getPath();
            if (path.length > 1) {
                encodedCoordinates = encodedCoordinates + google.maps.geometry.encoding.encodePath(path) + " ";
            }
        };
        document.getElementById("PathCoordinates").innerHTML = encodedCoordinates;
    });

    //Piilotetaan vain alueelle tarkoitetut attribuutit, kun niiden disabloiminen ei toimi.
    document.getElementById('PathType').onchange = function() {
        setMarkerType();
    };

    //alussa laitetaan oikeat laatikot näkyviin
    setMarkerType();
});

function encodeCoordinates(){ //Decode the pathdata
    var encodedCoordinates = $('#PathCoordinates').val();
    if (encodedCoordinates != '') {
        //console.info(encodedCoordinates);
        encodedCoordinates = encodedCoordinates.split( " " );
        _.each(encodedCoordinates, function(i) {
            decodedCoordinates.push(google.maps.geometry.encoding.decodePath(i));
        });
        //console.info(decodedCoordinates);
    }
}

function setMarkerType(){ //update page and elements on the map according the new type
    if ($('#PathType').val() == 1){
        document.getElementById("PathFillOpacity").disabled = true;
        document.getElementById("PathFillColor").disabled = true;
        document.getElementById("areaOnly").hidden = true;
    }else{
        document.getElementById("PathFillOpacity").disabled = false;
        document.getElementById("PathFillColor").disabled = false;
        document.getElementById("areaOnly").hidden = false;
    }

    var old = elements.slice(0);
    elements = [];
    for (var i = 0; i < old.length; i++) {
        newElement(old[i].getPath());
        old[i].setMap(null);
    };
}

function newElement(path) { // crate a new element (path = array())
    //console.log(path);
    var el;
    if ($('#PathType').val() == 1) {
        el = new google.maps.Polyline({
            map: map,
            strokeColor: "#" + $("#PathStrokeColor").val(),
            strokeOpacity: $("#PathStrokeOpacity").val(),
            strokeWeight: $("#PathStrokeWeight").val(),
            editable: true,
            path: path
        });
    } else {
        el = new google.maps.Polygon({
            map: map,
            strokeColor: "#" + $("#PathStrokeColor").val(),
            strokeOpacity: $("#PathStrokeOpacity").val(),
            strokeWeight: $("#PathStrokeWeight").val(),
            fillColor: "#" + $("#PathFillColor").val(),
            fillOpacity: $("#PathFillOpacity").val(),
            editable: true,
            path: path
        });
    }

    google.maps.event.addListener(el, 'rightclick', function(event) {
        deletePoint(event.latLng);
    });
    elements.push(el);
}

function addLatLng(event) { // if map is clicked, add a point
    var done = false;
    for (var el = 0; el < elements.length; el++) { // if there are a line that contains only 1 point, increase that
        if (elements[el].getPath().length < 2) {
            elements[el].getPath().push(event.latLng);
            done = true;
            break;
        }
    }
    if (!done) { //else creat a new element
        newElement([new google.maps.LatLng(event.latLng.lat(), event.latLng.lng())]);
    }
}

function deletePoint(pos){ // if a point is secondclicked, delete it
    //console.log(pos);
    for (var el = 0; el < elements.length; el++) {
        var path = elements[el].getPath();
        for (var i = 0; i < path.length; i++) {
            if (path.getAt(i) == pos) {
                path.removeAt(i);
                elements[el].setPath(path);
                break;
                break;
            }
        }
    }
}


</script>

<div class="answerMenu">
    <a href="#help" class="button" id="toggleHelp">Ohje</a>
</div>

<h1>Aineiston tiedot</h1>

<div class="help">
    <h2>Aineiston muokkaaminen</h2>
    <p><b>Aineiston tyyppi</b></p>
    <p>Valitse muokattavan aineiston tyypiksi joko viiva tai alue. Kartalla olevat aineistot päivittyvät valinnan mukaan.</p>
    <p><b>Viivan väri, opasiteetti ja paksuus</b></p>
    <p>Jos aineiston tyypiksi on valittu alue, viiva tarkoittaa alueen ulkoreunan viivaa, muuten näkyvissä on vain viiva. Voit valita viivalle värin, opasiteetin (läpinäkyvyysasteen) sekä paksuuden.</p>
    <p><b>Täytön väri ja opasiteetti</b></p>
    <p>Jos tyypiksi on valittu alue, voit valita alueen täyttövärin, sekä täytön opasiteetin (läpinäkyvyyden).</p>
    <h2>Aineisto kartalla</h2>
    <p><b>Lisääminen</b></p>
    <p>Klikkaamalla kartalle voit luoda uuden aineiston. Jos kartalla on aineisto, jossa on 1 kulmapiste, lisääminen luo tähän aineistoon toisen pisteen, muuten luodaan uusi aineito. Kartalla olevia aineistoja, jotka koostuvat vain yhdestä kulmapisteestä, ei tallenneta.</p>
    <p><b>Muokkaaminen</b></p>
    <p>Kun kartalla on aineisto, jossa on vähintään 2 kulmapistettä, kulmapisteistä vetämällä voit siirtää niiden paikkaa. Kulmapisteiden välissä olevasta haaleammasta pallukasta voit luoda uuden kulmapisteen aineistoon.</p>
    <p><b>Poistaminen</b></p>
    <p>Klikkaamalla aineiston kulmapistettä hiiren oikealla painikkeella voit poistaa kyseisen kulmpisteen.</p>
</div>

<?php echo $this->Form->create('Path'); ?>
<?php echo $this->Form->input(
    'name', 
    array('label' => 'Nimi','placeholder'=>'Anna nimi','required'=> true)
); ?>
<?php echo $this->Form->input(
    'content', 
    array('label' => 'Sisältö')
); ?>
<?php echo $this->Form->input(
    'type', 
    array('label' => 'Aineiston tyyppi', 'options' => array('none','Viiva', 'Alue'))
); ?>
<?php echo $this->Form->input(
    'stroke_color', 
    array('label' => 'Viivan väri', 'class' => 'color small')
); ?>
<?php echo $this->Form->input(
    'stroke_opacity', 
    array('label' => 'Viivan opasitetti', 'class' => 'small')
); ?>
<?php echo $this->Form->input(
    'stroke_weight', 
    array('label' => 'Viivan paksuus', 'class' => 'small')
); ?>
<div id='areaOnly'>
    <?php echo $this->Form->input(
        'fill_color', 
        array('label' => 'Täytön väri', 'class' => 'color small')
    );?>
    <?php echo $this->Form->input(
        'fill_opacity', 
        array('label' => 'Täytön opasitetti', 'class' => 'small')
    ); ?>
</div>
<div hidden>
    <?php echo $this->Form->input(
        'coordinates', 
        array('label' => 'Koordinaatit')
    ); ?>
</div>
<div class="input map-container">
    <label>Esikatselu</label>
    <div id="map" class="map">
    </div>
</div>
<button type="submit" id="submit" class="button">Tallenna</button>
<?php echo $this->Html->link(
    'Peruuta',
    array('action' => 'index'),
    array('class' => 'button cancel')
); ?>
<?php echo $this->Form->end(); 
//debug($this->data);
?>


