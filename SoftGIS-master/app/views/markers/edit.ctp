<script>

var markerIconPath = "<?php echo $this->Html->url('/markericons/'); ?>";


$( document ).ready(function() {

    var initialPos, coords;
    if ($("#MarkerLatlng").val()) {
        coords = $("#MarkerLatlng").val().split(",", 2);
    } else {
        coords = ["64.94216", "26.235352"];
    }
    initialPos = new google.maps.LatLng( coords[0], coords[1] );

    var map = new google.maps.Map(
        $( "#map" ).get()[0],
        {
            disableDoubleClickZoom: true,
            zoom: 6,
            center: initialPos,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        }
    );
    var marker = new google.maps.Marker({
        map: map,
        draggable: true,
        position: initialPos,
        icon: $("#MarkerIcon").val() != "default" ? (markerIconPath + $("#MarkerIcon").val()) : null
    });

    $( "#MarkerEditForm" ).submit(function() {
        var latLng = marker.getPosition();
        $( "#MarkerLatlng" ).val( latLng.lat() + "," + latLng.lng() );
    });

    $( "#MarkerIcon" ).change(function() {
        var icon = $( this ).val();
        if ( icon == "default" ) {
            marker.setIcon( null );
        } else {
            marker.setIcon( markerIconPath + icon );
        }
    });

});

</script>

<h2>Karttamerkki</h2>

<?php echo $this->Form->create('Marker'); ?>
<?php echo $this->Form->input('name', array('label' => 'Nimi')); ?>
<?php echo $this->Form->input('content', array('label' => 'Sisältö')); ?>
<?php echo $this->Form->input('icon', array('label' => 'Kuvake')); ?>
<?php echo $this->Form->input('latlng', array('type' => 'hidden')); ?>
<div class="input map-container">
    <label>Sijainti</label>
    <div id="map" class="map">
    </div>
</div>
<button type="submit">Tallenna karttamerkki</button>
<?php echo $this->Html->link(
    'Peruuta',
    array('action' => 'index'),
    array('class' => 'button cancel')
); ?>
<?php echo $this->Form->end(); ?>