
$( document ).ready(function() {

    var map = new google.maps.Map(
        $( "#map" ).get()[0],
        {
            disableDoubleClickZoom: true,
            zoom: 3,
            center:  new google.maps.LatLng( "64.94216", "26.235352" ),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        }
    );
    var polyline = new google.maps.Polyline({
        path: new google.maps.MVCArray()
    });

    var coords = polyline.getPath(); // Some kind of bug


    var marker = new google.maps.Marker({
        draggable: true,
        center: new google.maps.LatLng(0,0) 
    });

    var mapObject;

    // var type = $( "#EntryType" ).val();

    $( "#EntryType" ).change(function() {

        var type = $( this ).val();

        switch ( type ) {
            case "Marker":
                marker.setMap( map );
                polyline.setMap( null );
                break;
            case "Polyline":
                marker.setMap( null );
                polyline.setMap( map );
                
        }
        // polyline.setPath( new google.maps.MVCArray() );
    }).change();


    google.maps.event.addListener( map, "dblclick", function(e) {
        addCoord( e.latLng );
    });


    $( "#coords" ).delegate( "li", "click", function() {
        removeCoord( $(this).index() );
    });


    function addCoord(latLng) {

        if ( coords.getLength() == 0 ) {
            marker.setPosition( latLng );
        }
        coords.push( latLng );

        // $( "<li>" ).text( latLng.toString() ).appendTo( $("#coords") );
    }

    function removeCoord(index) {
        coords.removeAt( index );
        // $( "#coords" ).children().eq( index ).remove();
    }



    $( "#entry" ).submit(function() {

        // var serialized = google.maps.geometry.encoding.encodePath( coords );
        var serialized = google.maps.geometry.encoding.encodePath(
            [marker.getPosition()]
        );

        console.info( serialized );
        $( "<input type='hidden'>" ).attr( "name", "data[Entry][latlngs]" )
            .val( serialized ).appendTo( this );
        
        // return false;
    });

});