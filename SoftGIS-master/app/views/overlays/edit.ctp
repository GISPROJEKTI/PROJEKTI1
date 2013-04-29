
<script>

var map = null;
var image_url = null;
var overlay = null;
var markers = [];

$(document).ready(function() { // init when page has loaded

    // Help toggle
    $( ".help" ).hide();
    $( "#toggleHelp" ).click(function() {
        $( ".help" ).fadeToggle(400, "swing");
        return false;
    });

    var pos = center();

    map = new google.maps.Map( 
        $( "#map" ).get()[0],
        {
            zoom: pos ? pos.zoom : 5,
            center: pos ? pos.pos : new google.maps.LatLng(64.94216, 26.235352),
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            disableDoubleClickZoom: true
        }
    );

    google.maps.event.addListener(map, 'click', function(event) {
        mapClick(event.latLng);
    });

    document.getElementById("OverlayNeLat").addEventListener ('focusout', input, false);
    document.getElementById("OverlayNeLng").addEventListener ('focusout', input, false);
    document.getElementById("OverlaySwLat").addEventListener ('focusout', input, false);
    document.getElementById("OverlaySwLng").addEventListener ('focusout', input, false);

    var asd = document.URL.split('overlays');
    image_url = asd[0] + 'overlayimages/' + document.getElementById("OverlayImage").value;

    input();
    //console.log(NaN == NaN, isNaN(NaN));
});

function input(){ // update the map according to the values of the fields
    deleteMarkers();

    //check that smaller position is smaller and bigger is bigger
    var ne_lat = parseFloat(document.getElementById("OverlayNeLat").value),
        ne_lng = parseFloat(document.getElementById("OverlayNeLng").value),
        sw_lat = parseFloat(document.getElementById("OverlaySwLat").value),
        sw_lng = parseFloat(document.getElementById("OverlaySwLng").value);
    if (ne_lat === 0 || isNaN(ne_lat)) {
        ne_lat = false;
    }
    if (ne_lng === 0 || isNaN(ne_lng)) {
        ne_lng = false;
    }
    if (sw_lat === 0 || isNaN(sw_lat)) {
        sw_lat = false;
    }
    if (sw_lng === 0 || isNaN(sw_lng)) {
        sw_lng = false;
    }

    if (ne_lat && ne_lng && sw_lat && sw_lng){
        if (ne_lat < sw_lat) {
            document.getElementById("OverlayNeLat").value = sw_lat;
            document.getElementById("OverlaySwLat").value = ne_lat;
        }

        if (ne_lng < sw_lng) {
            document.getElementById("OverlayNeLng").value = sw_lng;
            document.getElementById("OverlaySwLng").value = ne_lng;
        }
    }

    //Set the map
    var ne_lat = parseFloat(document.getElementById("OverlayNeLat").value),
        ne_lng = parseFloat(document.getElementById("OverlayNeLng").value),
        sw_lat = parseFloat(document.getElementById("OverlaySwLat").value),
        sw_lng = parseFloat(document.getElementById("OverlaySwLng").value);
    if (ne_lat === 0 || isNaN(ne_lat)) {
        ne_lat = false;
    }
    if (ne_lng === 0 || isNaN(ne_lng)) {
        ne_lng = false;
    }
    if (sw_lat === 0 || isNaN(sw_lat)) {
        sw_lat = false;
    }
    if (sw_lng === 0 || isNaN(sw_lng)) {
        sw_lng = false;
    }
    if (sw_lat && sw_lng) {
        createMarker(new google.maps.LatLng(sw_lat,sw_lng), 'SW');
    }
    if (ne_lat && ne_lng) {
        createMarker(new google.maps.LatLng(ne_lat,ne_lng), 'NE');
    }
    overlayUpdate();

}

function createMarker(pos, title) {

    marker = new google.maps.Marker({
        position: pos,
        map: map,
        title: title,
        draggable: true
    });

    google.maps.event.addListener(marker, 'mouseup', whichButton); //JS mixes dragging and rightclick, so we need to find out which it was

    markers.push(marker);
}

function deleteMarkers() {
    for (var i = 0; i < markers.length; i++) {
        markers[i].setMap(null);
    }
    markers = [];
}

function deleteMarker(e) { //Actually we delete the values from the fields and then update markers accordingly
    for (var i = 0; i < markers.length; i++) {
        if (markers[i].getPosition() == e.latLng) {
            if (markers[i].title == 'NE') {
                document.getElementById("OverlayNeLat").value = 0;
                document.getElementById("OverlayNeLng").value = 0;
                break;
            } else {
                document.getElementById("OverlaySwLat").value = 0;
                document.getElementById("OverlaySwLng").value = 0;
                break;
            }
        }
    }
    input();
}

function overlayUpdate() {
    if (markers.length > 1 && overlay == null){
        createOverlay();
    } else if (markers.length < 2 && overlay != null) {
        deleteOverlay();
    } else if (markers.length > 1 && overlay != null) {
        deleteOverlay();
        createOverlay();

    }
}

function createOverlay() { //SW, NE
    var overlayBounds = new google.maps.LatLngBounds(markers[0].getPosition(), markers[1].getPosition());

    overlay = new google.maps.GroundOverlay(
        image_url,
        overlayBounds,
        {
            map: map,
            clickable: false,
            opacity: 0.5
        }
    );
}

function deleteOverlay() {
    overlay.setMap(null);
    overlay = null;
}

function whichButton(e){// determine, wich button was lifted
    // Kiitos avusta: http://javascript.info/tutorial/mouse-events
    //Saa nähdä miten toimii IE:llä...
    //console.log(e);
    
    if (e.Sa.button == 2) {
        deleteMarker(e);
    } else{
        dragMarker();
    }

}

function dragMarker(){ // if a marker is dragged
    //if we have only one marker, update it's position to the correct field
    if (markers.length == 1){
        if (markers[0].title == 'NE') {
            pos = markers[0].getPosition();
            document.getElementById("OverlayNeLat").value = pos.lat();
            document.getElementById("OverlayNeLng").value = pos.lng();
        } else {
            pos = markers[0].getPosition();
            document.getElementById("OverlaySwLat").value = pos.lat();
            document.getElementById("OverlaySwLng").value = pos.lng();
        }
        //If we have both markers, put the higer value to higer field and smaller value to lower field
    } else if (markers.length == 2) {
        pos1 = markers[0].getPosition();
        pos2 = markers[1].getPosition();

        if (pos1.lat() > pos2.lat()) {
            document.getElementById("OverlayNeLat").value = pos1.lat();
            document.getElementById("OverlaySwLat").value = pos2.lat();
        } else {
            document.getElementById("OverlayNeLat").value = pos2.lat();
            document.getElementById("OverlaySwLat").value = pos1.lat();
        }

        if (pos1.lng() > pos2.lng()) {
            document.getElementById("OverlayNeLng").value = pos1.lng();
            document.getElementById("OverlaySwLng").value = pos2.lng();
        } else {
            document.getElementById("OverlayNeLng").value = pos2.lng();
            document.getElementById("OverlaySwLng").value = pos1.lng();
        }

    }
    //and then update the map according to the new positions
    input()
}

function mapClick(pos) { //If map has clicked, determine do we need a new marker and wich slot to put it?
    if (markers.length < 2) {
        var ne_lat = parseFloat(document.getElementById("OverlayNeLat").value),
            ne_lng = parseFloat(document.getElementById("OverlayNeLng").value),
            sw_lat = parseFloat(document.getElementById("OverlaySwLat").value),
            sw_lng = parseFloat(document.getElementById("OverlaySwLng").value);
        if (ne_lat === 0 || isNaN(ne_lat)) {
            ne_lat = false;
        }
        if (ne_lng === 0 || isNaN(ne_lng)) {
            ne_lng = false;
        }
        if (sw_lat === 0 || isNaN(sw_lat)) {
            sw_lat = false;
        }
        if (sw_lng === 0 || isNaN(sw_lng)) {
            sw_lng = false;
        }

        if (markers.length == 1) {
            if (ne_lat && ne_lng) {
                document.getElementById("OverlaySwLat").value = pos.lat();
                document.getElementById("OverlaySwLng").value = pos.lng();
            } else {
                document.getElementById("OverlayNeLat").value = pos.lat();
                document.getElementById("OverlayNeLng").value = pos.lng();
            }
        } else {
            if (ne_lat || ne_lng) {
                document.getElementById("OverlaySwLat").value = pos.lat();
                document.getElementById("OverlaySwLng").value = pos.lng();
            } else {
                document.getElementById("OverlayNeLat").value = pos.lat();
                document.getElementById("OverlayNeLng").value = pos.lng();
            }
        }
        input();
    }
}

function center(){ //we try to center and zoom the map on init
    var ne_lat = parseFloat(document.getElementById("OverlayNeLat").value),
        ne_lng = parseFloat(document.getElementById("OverlayNeLng").value),
        sw_lat = parseFloat(document.getElementById("OverlaySwLat").value),
        sw_lng = parseFloat(document.getElementById("OverlaySwLng").value);
    if (ne_lat === 0 || isNaN(ne_lat)) {
        ne_lat = false;
    }
    if (ne_lng === 0 || isNaN(ne_lng)) {
        ne_lng = false;
    }
    if (sw_lat === 0 || isNaN(sw_lat)) {
        sw_lat = false;
    }
    if (sw_lng === 0 || isNaN(sw_lng)) {
        sw_lng = false;
    }

    var pos = null;

    if (ne_lat && ne_lng && sw_lat && sw_lng){
        var lat = (ne_lat + sw_lat) /2;
        var lng = (ne_lng + sw_lng) /2;

        var difLat = Math.abs(ne_lat - sw_lat);
        var difLng = Math.abs(ne_lng - sw_lng);

        if (difLat > difLng) {
            var bigDif = difLat;
        } else {
            var bigDif = difLng;
        }

        if (bigDif > 1) {
            var zoom = 5;
        } else if (bigDif > 0.1) {
            var zoom = 8;
        } else {
            var zoom = 11;
        }
        pos = {
            pos: new google.maps.LatLng(lat, lng),
            zoom: zoom
        }
    } else {
        pos = {
            pos: new google.maps.LatLng(64.94216, 26.235352),
            zoom: 5
        };
    }
    return pos;
}

</script>




<div class="answerMenu">
    <a href="#help" class="button" id="toggleHelp">Ohje</a>
</div>

<div class="form">
    <h1>Karttakuvan tiedot</h1>

    <div class="help">
        <h2>Koordinaattien syöttäminen</h2>
        <p>Voit syöttää kuvan lounais- ja koiliskulman koordinaatit niille varattuihin kenttiin ja ohjelma asettaa karttakuvan kartalle. Esiaktselussa karttakuva on läpinäkyvä kohdistamisen helpottamiseksi, mutta kyselyssä läpinäkyvyyttä ei ole.</p>
        <h2>Kulmamerkit kartalle</h2>
        <p>Voit myös asettaa karttakuvan kohdalleen klikkaamalla karttaa karttakuvan lounais- ja koiliskulmien kohdalta. Kartalle ilmestyy merkit kuvan kulmien paikaksi. Kulmien merkkejä voi vetämällä siirtää, tai hiiren oikealla klikkauksella poistaa.</p>
    </div>

    <div class="value">
        <?php echo $this->Form->create('Overlay'); ?>
        <?php echo $this->Form->input('name', array('label' => 'Nimi','placeholder'=>'Anna nimi','required'=> true)); ?>
        <?php echo $this->Form->input('content', array('label' => 'Sisältö')); ?>
    </div>
    <div class="input map-container">
        <label>Esikatselu</label>
        <?php if (!file_exists(APP.'webroot'.DS.'overlayimages'.DS.$this->data['Overlay']['image'])) {
            echo '<div style="color:#FF0000">Kuvatiedostoa ei löytynyt</div>';
        } ?>
        <div id="map" class="map">
        </div>
    </div>

    <div class="details">
        <?php echo $this->Form->input('image', array('label' => 'Kuvatiedosto', 'type' => 'hidden')); ?>
        <h3>Koordinaatit</h3>
        <?php echo $this->Form->input('ne_lat', 
            array('label' => 'Pohjoinen (NE lat) ', 'div' => "inline", "class" => "small")); ?>
        <?php echo $this->Form->input('ne_lng', 
            array('label' => 'Itä (NE lng) ', 'div' => "inline", "class" => "small")); ?>
        <?php echo $this->Form->input('sw_lat', 
            array('label' => 'Etelä (SW lat) ', 'div' => "inline", "class" => "small")); ?>
        <?php echo $this->Form->input('sw_lng', 
            array('label' => 'Länsi (SW lng) ', 'div' => "inline", "class" => "small")); ?>

    </div>

    <div class="submit">
        <br>
        <?php echo $this->Form->button('Tallenna', 
            array('type'=>'submit')); ?>
        <?php echo $this->Html->link('Peruuta', 
            array('action' => 'index'), array('class' => 'button cancel')); ?>

        <?php echo $this->Form->end(); ?>
    </div>
</div>

