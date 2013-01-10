<script>

var encodedCoordinates = "<?php echo $data['Path']['coordinates']; ?>";
var decodedCoordinates = new google.maps.MVCArray();
var type = <?php echo $data['Path']['type']; ?>;
var map;
console.info(encodedCoordinates);
encodedCoordinates = encodedCoordinates.split( " " );
_.each(encodedCoordinates, function(i) {
    decodedCoordinates.push(google.maps.geometry.encoding.decodePath(i));
});

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

    map = new google.maps.Map( 
        $( "#map" ).get()[0],
        {
            zoom: 14,
            center: decodedCoordinates.getAt(0)[0],
            mapTypeId: google.maps.MapTypeId.ROADMAP
        }
    );

    var elements = [];
    decodedCoordinates.forEach(function(path) {
        var el;
        if (type == 1) {
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
<?php if ($this->data['Path']['type'] == 2) {
    echo $this->Form->input(
        'fill_color', 
        array('label' => 'Täytön väri', 'class' => 'color small')
    );
    echo $this->Form->input(
        'fill_opacity', 
        array('label' => 'Täytön opasitetti', 'class' => 'small')
    ); 
} ?>
<div class="input map-container">
    <label>Esikatselu</label>
    <div id="map" class="map">
    </div>
</div>
<button type="submit" class="button">Tallenna</button>
<?php echo $this->Form->end(); ?>

