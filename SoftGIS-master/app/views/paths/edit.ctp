<script>

/*
TODO:
-If you import a route and in the edit page you cancel the edition, the route gets saved to the database without a name, and then can't be edited/showd anymore.
-The color change object can't find pictures to use.
*/

var map;

var decodedCoordinates = new google.maps.MVCArray();

function encodeCoordinates(){
    var encodedCoordinates = $('#PathCoordinates').val();
    console.info(encodedCoordinates);
    encodedCoordinates = encodedCoordinates.split( " " );
    _.each(encodedCoordinates, function(i) {
        decodedCoordinates.push(google.maps.geometry.encoding.decodePath(i));
    });
    console.info(decodedCoordinates);
}

function setMarkerType(){
    if ($('#PathType').val() == 1){
        document.getElementById("PathFillOpacity").disabled = true;
        document.getElementById("PathFillColor").disabled = true;
        document.getElementById("areaOnly").hidden = true;
    }else{
        document.getElementById("PathFillOpacity").disabled = false;
        document.getElementById("PathFillColor").disabled = false;
        document.getElementById("areaOnly").hidden = false;
    }
}


$( document ).ready(function() {

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
    var pos;
    var zoom;
    if (decodedCoordinates.getLength() > 0) {
        pos = decodedCoordinates.getAt(0)[0];
        zoom = 12;
    }

    //in case we don't get a position from the array
    if (typeof pos == 'undefined'){
        pos = new google.maps.LatLng("64.94216", "26.235352");
        zoom = 6;
    }

    map = new google.maps.Map( 
        $( "#map" ).get()[0],
        {
            zoom: zoom,
            center: pos,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        }
    );

    var elements = [];
    decodedCoordinates.forEach(function(path) {
        var el;
        if ($('#PathType').val() == 1) {
            el = new google.maps.Polyline({
                map: map,
                strokeColor: "#" + $("#PathStrokeColor").val(),
                strokeOpacity: $("#PathStrokeOpacity").val(),
                strokeWeight: $("#PathStrokeWeight").val(),
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
                path: path
            });
        }
        elements.push(el);

    });
    console.info(elements);

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

    //Piilotetaan vain alueelle atrkoitetut attribuutit, kun niiden disabloiminen ei toimi.
    document.getElementById('PathType').onchange = function() {
        setMarkerType();
    };

    //alussa laitetaan oikeat laatikot näkyviin
    setMarkerType();
    
});
</script>


<h1>Reitin tiedot</h1>

<?php echo $this->Form->create('Path'); ?>
<?php echo $this->Form->input(
    'name', 
    array('label' => 'Nimi')
); ?>
<?php echo $this->Form->input(
    'content', 
    array('label' => 'Sisältö')
); ?>
<?php echo $this->Form->input(
    'type', 
    array('label' => 'Objektin tyyppi', 'options' => array('none','Polku', 'Alue'))
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
<button type="submit" class="button">Tallenna</button>
<?php echo $this->Html->link(
    'Peruuta',
    array('action' => 'index'),
    array('class' => 'button cancel')
); ?>
<?php echo $this->Form->end(); 
//debug($this->data);
?>


