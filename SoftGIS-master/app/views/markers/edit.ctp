<script>

var markerIconPath = "<?php echo $this->Html->url('/markericons/'); ?>";

var map, marker = null;

$( document ).ready(function() {

    var initialPos, coords = null;
    if ($("#MarkerLatlng").val()) {
        coords = $("#MarkerLatlng").val().split(",", 2);
        initialPos = new google.maps.LatLng( coords[0], coords[1] );
    }

    map = new google.maps.Map(
        $( "#map" ).get()[0],
        {
            zoom: initialPos ? 8 : 5,
            center: initialPos ? initialPos : new google.maps.LatLng(64.94216, 26.235352),
            streetViewControl: false,
            disableDoubleClickZoom: true,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        }
    );

    google.maps.event.addListener(map, 'click', function(event) {
        addMarker(event.latLng);
    });

    if (initialPos) {
        addMarker(initialPos);
    }

    $( "#MarkerEditForm" ).submit(function() {
        if (marker) {
            var latLng = marker.getPosition();
            $( "#MarkerLatlng" ).val( latLng.lat() + "," + latLng.lng() );
            $( "#map" ).qtip( "destroy" );
        } else {
            $( "#map" ).qtip({
                content: "Aseta merkin sijainti kartalle",
                position: {
                    my: "bottom center",
                    at: "top center",
                    adjust: {
                        x: 200
                    }
                },
                show: {
                    ready: true,
                    event: null
                },
                hide: {
                    event: null
                },
                style: {
                    classes: "ui-tooltip-shadow ui-tooltip-rounded ui-tooltip-red"
                }
            });
            return false;
        }
    });

    $( "#MarkerIcon" ).change(function() {
        if (marker){
            var icon = $( this ).val();
            if ( icon == "default" ) {
                marker.setIcon( null );
            } else {
                marker.setIcon( markerIconPath + icon );
            }
        }
    });

});


function addMarker(location) {
    if(!marker){
        mar = new google.maps.Marker({
            position: location,
            map: map,
            draggable: true,
            icon: $("#MarkerIcon").val() != "default" ? (markerIconPath + $("#MarkerIcon").val()) : null
        });
        marker = mar;

        google.maps.event.addListener(marker, 'rightclick', function() {
            if (marker){
                marker.setMap(null);
                marker = null;
            }
        });
        $( "#map" ).qtip( "destroy" );
    }
}

</script>

<h2>Karttamerkki</h2>

<?php echo $this->Form->create('Marker'); ?>
<?php echo $this->Form->input('name', array('label' => 'Nimi','placeholder'=>'Anna nimi','required'=> true)); ?>
<?php echo $this->Form->input('content', array('label' => 'Sisältö')); ?>
<?php echo $this->Form->input('icon', array('label' => 'Kuvake')); ?>
<?php echo $this->Form->input('latlng', array('type' => 'hidden')); ?>
<div class="input map-container">
    <label>Sijainti</label>
    <div id="map" class="map">
    </div>
</div>
<button type="submit">Tallenna</button>
<?php echo $this->Html->link(
    'Peruuta',
    array('action' => 'index'),
    array('class' => 'button cancel')
); ?>
<?php echo $this->Form->end(); ?>