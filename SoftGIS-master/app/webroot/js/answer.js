var map, questionMarker, questionLocation, questionZoom, locRequired = false;

function initQuestion(question) {

    if ( question.lat && question.lng ) {
        // Question has pos
        questionLocation = new google.maps.LatLng(
            question.lat,
            question.lng
        );

        map = new google.maps.Map( $( "#map" ).get()[0], {
                zoom: question.zoom ? parseInt(question.zoom, 10) : 14,
                center: questionLocation,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            }
        );

        questionMarker = new google.maps.Marker({
            map: map,
            position: questionLocation,
            icon: "/img/question_icon.png"
        });
        
        if ( question.answer_location == 1 ) {
            locRequired = true;

            var answerMarker;
            google.maps.event.addListenerOnce(map, "click", function(e) {
                answerMarker = new google.maps.Marker({
                    map: map,
                    position: e.latLng,
                    draggable: true
                });

                // Updates answer location immediately when marker is dropped
                google.maps.event.addListener(
                    answerMarker, 
                    "dragend", 
                    function(e) {
                        $( "#lat" ).val( e.latLng.lat() );
                        $( "#lng" ).val( e.latLng.lng() );
                    }
                );

                // Store initial position
                google.maps.event.trigger( answerMarker, "dragend", e );
            });
        }
    }

    var answerSelector;
    if ( question.type == 1 ) {
        answerSelector = "textarea";
    } else {
        answerSelector = "input:checked";
    }

    // Validate answer before submit
    $( "#answer-form" ).submit(function() {
        var continueSubmit = true;

        if ( locRequired ) {
            // Make sure user has selected location
            var lat = $( "#lat" ).val();
            var lng = $( "#lng" ).val();

            if ( !lat || !lng ) {
                $( "#map" ).qtip({
                    content: "Et ole valinnut sijaintia kartalta",
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
                })
                continueSubmit = false;
            } else {
                $( "#map" ).qtip( "destroy" );
            }
        }

        // Make sure user has answered something
        var val = $( this ).find( answerSelector ).val();
        if ( !val ) {
            $( answerSelector ).focus();
            $( "#answerField" ).qtip({
                content: "Et ole vastannut kysymykseen",
                position: {
                    my: "top center",
                    at: "bottom center",
                    adjust: {
                        x: -200
                    }
                },
                show: {
                    ready: true,
                    event: "focus"
                },
                hide: {
                    event: null
                },
                style: {
                    classes: "ui-tooltip-shadow ui-tooltip-rounded ui-tooltip-red"
                }
            });
            continueSubmit = false;
        } else {
            $( "#answerField" ).qtip( "destroy" );
        }

        return continueSubmit;
    });

}

function initMarkers(markers) {
    if ( markers ) {
        _.each( markers, function(marker) {
            createMarker(marker);
        });
    }
}
function initPaths(paths) {
    if ( paths ) {
        _.each( paths, function(path) {
            createPath(path);
        });
    }

}

function initAnswers(answers) {
    if ( !map ) {
        return;
    }

    _.each( answers, function(answer) {
        answer = answer.Answer;
        if ( answer.lat && answer.lng ) {
            createMarker({
                name: answer.answer,
                content: answer.answer,
                lat: answer.lat,
                lng: answer.lng
            });
        }
    });
}

function createMarker(data) {
    var marker = new google.maps.Marker({
        map: map,
        title: data.name,
        position: new google.maps.LatLng(
            data.lat,
            data.lng
        ),
        // Use icon if set
        icon: data.icon ? markerIconPath + data.icon : null
    });
    var infoWindow = new google.maps.InfoWindow({
        content: data.content
    });
    google.maps.event.addListener(marker, "click", function() {
        infoWindow.open(map, marker);
    });
}

function createPath(data) {
    var infoWindow = new google.maps.InfoWindow({
        content: data.content
    });
    var encodedPaths = data.coordinates.split( " " );
    for (var i in encodedPaths) {
        var encodedPath = encodedPaths[i];
        encodedPath = encodedPath.replace(/\\\\/g, "\\");

        var decodedPath = google.maps.geometry.encoding.decodePath(
            encodedPath
        );

        if (data.type == 1) {
            var path = new google.maps.Polyline({
                map: map,
                strokeColor: data.stroke_color ? "#" + data.stroke_color : "#333",
                strokeOpacity: data.stroke_opacity ? data.stroke_opacity : 1,
                strokeWeight: data.stroke_weight ? data.stroke_weight : 1,
                path: decodedPath
            });
        } else {
            var path = new google.maps.Polygon({
                map: map,
                strokeColor: data.stroke_color ? "#" + data.stroke_color : "#333",
                strokeOpacity: data.stroke_opacity ? data.stroke_opacity : 1,
                strokeWeight: data.stroke_weight ? data.stroke_weight : 1,
                fillColor: data.fill_color ? "#" + data.fill_color : "#333",
                fillOpacity: data.fill_opacity ? data.fill_opacity : 0.5,
                path: decodedPath
            });
        }
    }
}

