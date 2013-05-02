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
                alert("Aineiston muunto epäonnistui.");
                return false;
            }
        } catch (err) {
            alert("Aineiston muunto epäonnistui");
            return false;
        }
        var encodedPaths = [];
        coordinates.forEach(function(path) {
            encodedPaths.push( google.maps.geometry.encoding.encodePath(path) );
        });
        $( "#coordinates" ).val( encodedPaths.join(" ") );
        return true;
    });

    // Help toggle
    //$( ".help" ).hide();
    $( "#toggleHelp" ).click(function() {
        $( ".help" ).fadeToggle(400, "swing");
        return false;
    });
});

function handleFiles(files) {
    if (files.length > 0) {
        var file = files[0];
        if (file) {
            var reader = new FileReader();
            reader.readAsText(file, "UTF-8");
            reader.onload = function (evt) {
                document.getElementById("coordinates").innerHTML = evt.target.result;
            }
            reader.onerror = function (evt) {
            //    document.getElementById("coordinates").innerHTML = "error reading file";
                alert('Tiedoston lukeminen epäonnistui');
            }
        }
    }
}

</script>

<div class="answerMenu">
    <a href="#help" class="button" id="toggleHelp">Ohje</a>
</div>


<h2>Tuo aineisto</h2>

<div class="help">
    <h2>Aineiston tuominen</h2>
    <p>Voit tuoda aineiston <b>mif-päätteisestä</b> <i>(MapInfo Interchange Format)</i> tiedostosta joko valitsemalla tiedoston painikkeesta tai kopioimalla tiedoston koko sisällön laatikkoon alle. Ohjelma muuntaa sen automaattisesti Google Mapsin ymmärtämään muotoon.</p>
    <p></p>
</div>

<form method="post" id="import">
    <input type="file" id="fileInput" onchange="handleFiles(this.files)">
    <br><br>
    <div class="input textarea">
        <textarea name="data[Path][coordinates]" 
            id="coordinates" rows="15"></textarea>
    </div>

    <input type="hidden" name="data[Path][type]" id="type" />

    <button type="submit" class="button">Jatka</button>
    <?php echo $this->Html->link(
        'Takaisin',
        array('action' => 'index'),
        array('class' => 'button cancel')
    ); ?>
</form>
