<script>

function gisPathToArray(gis) {
   var lines = gis.split( /\n/ );
   
   var paths = new google.maps.MVCArray();

   // var firstCoord;
   var currentPath = null;
   for ( var i in lines ) {

        var line = lines[i];
        if ( line.match(/PLINE \d*/i) ) {
            if ( currentPath ) {
                paths.push(currentPath);
            } 
            currentPath = new google.maps.MVCArray();

        } else if ( line.match(/^\d*.\d* \d*.\d*$/) ) {
            var coords = line.split( " " );
            if ( coords.length == 2 ) {
                var latLng = new google.maps.LatLng(coords[1], coords[0]);
                currentPath.push(latLng);
            }
        }
    }

    // Add last path
    if ( currentPath ) {
        paths.push(currentPath);
    }
    return paths;
}

function gisRegionToArray(gis) {
   var lines = gis.split( /\n/ );
   
   var regions = new google.maps.MVCArray();
   var currentRegion = null;

   for ( var i in lines ) {

       var line = lines[i];
        if ( line.match(/REGION \d*/i) ) {
            if ( currentRegion ) {
                regions.push(currentRegion);
            } 
            currentRegion = new google.maps.MVCArray();

        } else if ( line.match(/^\d*.\d* \d*.\d*$/) ) {
            var coords = line.split( " " );
            if ( coords.length == 2 ) {
                var latLng = new google.maps.LatLng(coords[1], coords[0]);
                currentRegion.push(latLng);
            }
        }
    }

    // Add last path
    if ( currentRegion ) {
        regions.push(currentRegion);
    }
    return regions;
}

function detectType(gis) {
    if (gis.match(/PLINE \d*/im)) {
        return 1;
    } else if (gis.match(/REGION \d*/im)) {
        return 2;
    } else {
        return 0;
    }
}

$( document ).ready(function() {
    $( "#import" ).submit(function() {
        var gis = $( "#coordinates" ).val();
        var type = detectType( gis );
        $( "#type" ).val( type );
        var coordinates;
        try {
            if ( type == 1 ) {
                coordinates = gisPathToArray( gis );
            } else if ( type == 2 ) {
                coordinates = gisRegionToArray( gis );
            } else {
                alert("Reittidatan muunto epäonnistui.");
                return false;
            }
        } catch (err) {
            alert("Reittidatan muunto epäonnistui");
            return false;
        }
        var encodedPaths = [];
        coordinates.forEach(function(path) {
            encodedPaths.push( google.maps.geometry.encoding.encodePath(path) );
        });
        $( "#coordinates" ).val( encodedPaths.join(" ") );
        return true;
    });
});

</script>


<h2>Tuo reittidata</h2>

<form method="post" id="import">
    <div class="input textarea">
        <textarea name="data[Path][coordinates]" 
            id="coordinates" rows="20"></textarea>
    </div>
    <input type="hidden" name="data[Path][type]" id="type" />
    <button type="submit" class="button">Jatka</button>
</form>

<div class="help">
    <p>Kopioi mif-päätteisen <strong>MapInfo Interchange Format</strong> tiedoston sisältö sellaisenaan tekstikenttään. Ohjelma muuntaa sen automaattisesti Google Mapsin ymmärtämään muotoon.</p>
</div>
