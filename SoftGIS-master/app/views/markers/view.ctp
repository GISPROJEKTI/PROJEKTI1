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
            zoom: 8,
            center: initialPos,
            clickable: false,
            streetViewControl: false,
            disableDoubleClickZoom: true,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        }
    );
    var marker = new google.maps.Marker({
        map: map,
        position: initialPos,
        icon: $("#MarkerIcon").val() != "default" ? (markerIconPath + $("#MarkerIcon").val()) : null
    });

    $("#otsikko").html("<h3>" + $("#MarkerName").val() + "</h3>");
    $("#sisältö").html($("#MarkerContent").val());
});

</script>

<h2>Karttamerkki</h2>

<?php 
    echo $this->Html->link(
        'muokkaa', 
        array('action' => 'edit', $this->data['Marker']['id']),
        array('class' => 'button','title' => 'Muokkaa karttakerkkiä')
    );
    echo $this->Html->link(
        'kopioi', 
        array('action' => 'copy', $this->data['Marker']['id']),
        array('class' => 'button','title' => 'Kopioi karttamerkki'),
        'Oletko varma että haluat kopioida karttakerkin?'
    );
    echo $this->Html->link(
        'poista', 
        array('action' => 'delete', $this->data['Marker']['id']),
        array('class' => 'button','title' => 'Poista karttamerkki'),
        'Oletko varma että haluat poistaa karttakerkin?'
    );
?>

<div id="otsikko"></div>
<div id="sisältö"></div>

<div hidden>
    <?php echo $this->Form->create('Marker'); ?>
    <?php echo $this->Form->input('name', array('label' => 'Nimi','placeholder'=>'Anna nimi','required'=> true)); ?>
    <?php echo $this->Form->input('content', array('label' => 'Sisältö')); ?>
    <?php echo $this->Form->input('icon', array('label' => 'Kuvake')); ?>
    <?php echo $this->Form->input('latlng', array('type' => 'hidden')); ?>
</div>
<div class="input map-container">
    <div id="map" class="map">
    </div>
</div>
<?php echo $this->Html->link(
    'Takaisin',
    array('action' => 'index'),
    array('class' => 'button cancel')
); ?>
<?php echo $this->Form->end(); ?>