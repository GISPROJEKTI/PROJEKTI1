
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
            clickable: false,
            disableDoubleClickZoom: true
        }
    );

    var asd = document.URL.split('overlays');
    image_url = asd[0] + 'overlayimages/' + document.getElementById("OverlayImage").value;

    input();

    $("#otsikko").html("<h3>" + $("#OverlayName").val() + "</h3>");
    $("#sisältö").html($("#OverlayContent").val());

    //console.log(NaN == NaN, isNaN(NaN));
});

function input(){ // update the map according to the values of the fields
    //deleteMarkers();

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
        //createMarker(new google.maps.LatLng(sw_lat,sw_lng), 'SW');
        markers.push(new google.maps.LatLng(sw_lat,sw_lng));
    }
    if (ne_lat && ne_lng) {
        //createMarker(new google.maps.LatLng(ne_lat,ne_lng), 'NE');
        markers.push(new google.maps.LatLng(ne_lat,ne_lng));
    }
    //overlayUpdate();
    createOverlay();

}

function createOverlay() { //SW, NE
    var overlayBounds = new google.maps.LatLngBounds(markers[0], markers[1]);

    overlay = new google.maps.GroundOverlay(
        image_url,
        overlayBounds,
        {
            map: map,
            clickable: false
        }
    );
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




<div class="form">
    <h1>Karttakuvan tiedot</h1>
    <div class="subnav">
        <?php if ($this->data['Overlay']['author_id'] == $author) {
            echo $this->Html->link(
                'Muokkaa', 
                array('action' => 'edit', $this->data['Overlay']['id']),
                array('class' => 'button','title' => 'Muokkaa karttakuvaa')
            );
        } ?>

        <?php echo $this->Html->link(
            'Kopioi', 
            array('action' => 'copy', $this->data['Overlay']['id']),
            array('class' => 'button','title' => 'Kopioi aineisto'),
            'Oletko varma että haluat kopioida karttakuvan?'
            );
        ?>

        <?php if ($this->data['Overlay']['author_id'] == $author) {
            echo $this->Html->link(
                'Poista', 
                array('action' => 'delete', $this->data['Overlay']['id']),
                array('class' => 'button','title' => 'Poista aineisto'),
                'Oletko varma että haluat poistaa karttakuvan?'
            );
        } ?>
    </div>
    <div id="otsikko"></div>
    <div id="sisältö"></div>

    <div hidden>
        <?php echo $this->Form->create('Overlay'); ?>
        <?php echo $this->Form->input('name', array('label' => 'Nimi','placeholder'=>'Anna nimi','required'=> true)); ?>
        <?php echo $this->Form->input('content', array('label' => 'Sisältö')); ?>
    </div>
    <div class="input map-container">
        <?php if (!file_exists(APP.'webroot'.DS.'overlayimages'.DS.$this->data['Overlay']['image'])) {
            echo '<div style="color:#FF0000">Kuvatiedostoa ei löytynyt</div>';
        } ?>
        <div id="map" class="map"></div>
    </div>

    <div hidden>
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
        <?php echo $this->Html->link('Takaisin', 
            array('action' => 'index'), 
            array('class' => 'button cancel')); 
        ?>

        <?php echo $this->Form->end(); ?>
    </div>
</div>

