
<script>

var map,
    overlayData = <?php echo json_encode($overlay['Overlay']); ?>,
    overlay;

$(document).ready(function() {
    var neLatLng, 
        swLatLng,
        neMarker,
        swMarker,
        overlayBounds;

    map = new google.maps.Map( 
        $( "#map" ).get()[0],
        {
            zoom: 8,
            center: new google.maps.LatLng(
                overlayData.ne_lat, 
                overlayData.ne_lng
            ),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        }
    );
    neLatLng = new google.maps.LatLng(overlayData.ne_lat, overlayData.ne_lng);
    swLatLng = new google.maps.LatLng(overlayData.sw_lat, overlayData.sw_lng);
    neMarker = new google.maps.Marker({
        position: neLatLng,
        map: map,
        title: 'NE'
    });
    swMarker = new google.maps.Marker({
        position: swLatLng,
        map: map,
        title: 'SW'
    });
    overlayBounds = new google.maps.LatLngBounds(swLatLng, neLatLng);

    overlay = new google.maps.GroundOverlay(
        overlayData.image_url,
        overlayBounds,
        {
            map: map
        }
    );
    
});

</script>

<div class="input map-container">
    <label>Esikatselu</label>
    <div id="map" class="map">
    </div>
</div>
