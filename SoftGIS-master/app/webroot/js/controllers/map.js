var Map = Spine.Controller.create({
    proxied: ["setMap", "hide", "initMap", "getMapAnswer", "setMapAnswer",
        "clearPublicAnswers", "createPublicAnswers", "addAnswer", "clearAnswer" ],
    init: function() {
        this.answer = null;
        this.answerType = null;
        this.el.hide();
    },
    initMap: function() {
        /*
        This function is automatically called by this.setMap(), when the map is needed the first time
        it sets up the map object and sets the questions markers, paths and owerlays, wich have been 
        set to this object at it's init().
        */
        this.el.show();

        /*
        If the div where the map is going is hidde while the map loads, it breaks.
        So load map only to a div wich is totally loaded and visible!
        More info: http://stackoverflow.com/questions/4358582/google-map-shows-only-partially
        */


        //create the map applet and place it to the right object on the page
        this.map = new google.maps.Map(this.el.get()[0], {
            zoom: 6,
            center: new google.maps.LatLng(64.94216, 26.235352),
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            disableDoubleClickZoom: true
        });

        var me = this;
        google.maps.event.addListener(this.map, 'click', function(event) {
            me.addAnswer(event.latLng);
        });

        //create the markers, paths and owerlays used at the poll
        if (this.markers) {
            _.each(this.markers, function(data) {
                var marker = new google.maps.Marker({
                    map: this.map,
                    title: data.name,
                    position: new google.maps.LatLng(data.lat,data.lng),
                    // Use icon if set
                    icon: data.icon ? markerIconPath + data.icon : null
                });
                var infoWindow = new google.maps.InfoWindow({
                    content: data.content
                });
                google.maps.event.addListener(marker, "click", function() {
                    infoWindow.open(this.map, marker);
                });
            }, this);
        }

        if (this.paths) {
            _.each(this.paths, function(data) {
                var encodedPaths = data.coordinates.split( " " );
                for (var i in encodedPaths) {
                    var encodedPath = encodedPaths[i];
                    encodedPath = encodedPath.replace(/\\\\/g, "\\");

                    var decodedPath = google.maps.geometry.encoding.decodePath(
                        encodedPath);

                    var path;
                    if (data.type == 1) {
                        path = new google.maps.Polyline({
                            map: this.map,
                            clickable: false,
                            strokeColor: data.stroke_color ? "#" + data.stroke_color : "#333",
                            strokeOpacity: data.stroke_opacity ? data.stroke_opacity : 1,
                            strokeWeight: data.stroke_weight ? data.stroke_weight : 1,
                            path: decodedPath
                        });
                    } else {
                        path = new google.maps.Polygon({
                            map: this.map,
                            clickable: false,
                            strokeColor: data.stroke_color ? "#" + data.stroke_color : "#333",
                            strokeOpacity: data.stroke_opacity ? data.stroke_opacity : 1,
                            strokeWeight: data.stroke_weight ? data.stroke_weight : 1,
                            fillColor: data.fill_color ? "#" + data.fill_color : "#333",
                            fillOpacity: data.fill_opacity ? data.fill_opacity : 0.5,
                            path: decodedPath
                        });
                    }
                }
            }, this);
        }

        if (this.overlays) {
            _.each(this.overlays, function(data) {
                var neLatLng, 
                    swLatLng,
                    overlayBounds;

                neLatLng = new google.maps.LatLng(data.ne_lat, data.ne_lng);
                swLatLng = new google.maps.LatLng(data.sw_lat, data.sw_lng);
                overlayBounds = new google.maps.LatLngBounds(swLatLng, neLatLng);
                overlay = new google.maps.GroundOverlay(
                    overlayPath + data.image,
                    overlayBounds,
                    {
                        map: this.map,
                        clickable: false
                    }
                );
            });
        }
    },
    setMap: function(mode, lat, lng, zoom) {
        /*
        When the page moves to a new question, the map should be initialized with this.
        Allways init the map with this function atleast with the mode: 0, or the map will not be displayed.
        This function should take care of:
            hiding/showing the map
            clearing old answers
            clearing old public answers

        mapQuestionType aka mode:
            0 = no map
            1 = map, no answer
            2 = map, 1 marker
            3 = map, multiple markers
            4 = map, 1 line
            5 = map, 1 area
        */

        if(mode > 0) {
            if (!this.map) {
                this.initMap(); //Map can be initialized only after the page is totally loaded and the map div is visible
            }

            this.clearAnswer(); //clear previous answer
            this.el.show();

            mode = parseInt(mode, 10);
            this.answerType = mode;

            if (lat && lng) {
                var pos = new google.maps.LatLng(lat, lng);
                this.map.setCenter(pos);
            }

            if (zoom) {
                zoom = parseInt(zoom, 10); 
                this.map.setZoom(zoom);
            }
        } else { //there is no map on this question
            this.clearAnswer(); //clear previous answer

            this.answerType = 0;
            this.el.hide();
        }
    },
    addAnswer: function(pos) {
        /*
        When the user clicks a map to enter the map answer, the magig will happen here.
        */
        var me = this;
        if (this.answerType < 2) {
            //these type of maps can't have merkers
            return false;
        }

        else if (this.answerType == 2 || this.answerType == 3){
            if(!this.answer){ // if this is the first answer, init a list
                this.answer = [];
            }
            if(this.answer.length < 1 || this.answerType == 3){
                var marker = new google.maps.Marker({
                    position: pos,
                    map: this.map,
                    draggable: true,
                    icon: answerIcon
                });
                google.maps.event.addListener(marker, 'rightclick', function() {
                    //set a listener, wich removes selected marker from the map and from the array
                    for (var i = 0; i < me.answer.length; i++) {
                        if (me.answer[i].__gm_id == this.__gm_id) {
                            me.answer[i].setMap(null);
                            me.answer.splice(i,1);
                            break;
                        }
                    }
                });
                this.answer.push(marker);
            }
        }

        else if (this.answerType == 4 || this.answerType == 5){
            if (!this.answer){ // if this is the firs answer, create a object to put points in to
                if (this.answerType == 4) { //if map type is line
                    this.answer = new google.maps.Polyline({
                        strokeColor: '#060',
                        strokeOpacity: 0.8,
                        strokeWeight: 3,
                        editable: true,
                        map: this.map
                    });

                } else { //otherwise it must be an area
                    this.answer = new google.maps.Polygon({
                        strokeColor: '#060',
                        strokeOpacity: 0.8,
                        strokeWeight: 3,
                        fillColor: '#060',
                        fillOpacity: 0.35,
                        editable: true,
                        map: this.map,
                    });
                }

                // add a listener to delete a point
                google.maps.event.addListener(this.answer, 'rightclick', function(event) {
                    var path = me.answer.getPath();
                    for (var i = 0; i < path.length; i++) {
                        if (path.getAt(i) == event.latLng) {
                            path.removeAt(i);
                            break;
                        }
                    };
                });
            }

            //add point
            var path = this.answer.getPath();
            path.push(pos);
        }
    },
    clearAnswer: function() { //clear previous answers
        if (this.answer){
            if (this.answerType == 2 || this.answerType == 3) {
                for (var i = 0; i < this.answer.length; i++){
                    this.answer[i].setMap(null);
                }
            } else { //otherwise it is type 4 or 5
                this.answer.setMap(null);
            }
        }

        /*
        if(this.map){
            //google.maps.event.clearInstanceListeners(this.map, "click");
            //usage of the function above breaks the map, so we have to figure out something
        }
        */
        this.clearPublicAnswers();
        this.answer = null;
        this.answerType = null;
    },
    getMapAnswer: function() {
        /*
        This returns the current answer's position list decrypted with google map api encryption thing
        more info about the encryption: 
        https://developers.google.com/maps/documentation/javascript/reference#encoding
        */
        if (this.answerType == 2 || this.answerType == 3) {
            if (this.answer){
                var answer = new google.maps.MVCArray();
                for (var i = 0; i < this.answer.length; i++){
                    answer.push(this.answer[i].getPosition());
                }
                return google.maps.geometry.encoding.encodePath(answer);
            } else {
                return "";
            }
        } else if (this.answerType == 4 || this.answerType == 5){
            if (this.answer){
                return google.maps.geometry.encoding.encodePath(this.answer.getPath());
            } else {
                return "";
            }
        } else {
            return "";
        }
    },
    setMapAnswer: function(encodedAnswer) {
        /*
        If you want to set a preanswer to the map, inset it here encoded with the google maps encoder
        This function decodes the patha and sends the locaton points to the addAnswer function
            wich places the locations to the map.
        */
        //console.log('setMapAnswer', encodedAnswer);
        if (encodedAnswer){
            var decodedPath = google.maps.geometry.encoding.decodePath(encodedAnswer);
            for (var i = 0; i < decodedPath.length; i++) {
                this.addAnswer(decodedPath[i]);
            };
        }
        //todo:
        //  decode
        //  send the list to addAnswer
        //      modify the addAnswer to place the list to the answer
        //  or:
        //  send the answers one by one to addAnswer
    },
    hide: function() {// There should not be need for use this function.
        /*
        Hides the map element from the web page
        */
        this.el.hide();
    },
    createPublicAnswers: function(answers) {
        /*
        Here we create the objects to the map of previous answers that are public
        Firs we create an array this.publicAnswers where we'll place all the objects
        The objects are the same type as the answer aka. this.answerType
        */
        this.publicAnswers = [];
        _.each(answers, function(answer){
            if (answer.map){
/*                
                var infoWindow = new google.maps.InfoWindow({
                    content: answer.answer
                });
*/
                var decodedPath = google.maps.geometry.encoding.decodePath(answer.map);

                if (this.answerType == 2 || this.answerType == 3){
                    var publicAnswer = [];
                    
                    if (decodedPath.length > 0){
                        for (var i = 0; i < decodedPath.length; i++){
                            var marker = new google.maps.Marker({
                                position: decodedPath[i],
                                map: this.map,
                                clickable: false,
                                title: answer.answer,
                                icon: publicAnswerIcon
                            });

/* // if you have lots of answers, it's better that they have not any markers on them
                            google.maps.event.addListener(marker, "click", function() {
                                infoWindow.open(this.map, marker);
                            });
*/                            

                            publicAnswer.push(marker);
                        }
                    }
                }

                else if (this.answerType == 4 || this.answerType == 5){
                    if (this.answerType == 4) { //if map type is line
                        publicAnswer = new google.maps.Polyline({
                            title: answer.answer, // well these don't support this property but...
                            strokeColor: '#006',
                            strokeOpacity: 0.6,
                            strokeWeight: 3,
                            editable: false,
                            clickable: false,
                            map: this.map
                        });

                    } else { //otherwise it must be an area
                        publicAnswer = new google.maps.Polygon({
                            title: answer.answer,
                            strokeColor: '#006',
                            strokeOpacity: 0.6,
                            strokeWeight: 3,
                            fillColor: '#006',
                            fillOpacity: 0.10,
                            editable: false,
                            clickable: false,
                            map: this.map,
                        });
                    }
                    
                    publicAnswer.setPath(decodedPath);

/*
                    google.maps.event.addListener(publicAnswer, "click", function() {
                        infoWindow.open(this.map, publicAnswer);
                    }); // polygon or polyline seems to not accept this kind of property
*/                    
                }

                this.publicAnswers.push(publicAnswer);
            }
        }, this);
    },
    clearPublicAnswers: function() {
        /*
        And here we destroy all the public answers
        */
        if (this.publicAnswers) {
            _.each(this.publicAnswers, function(answer) {
                if (this.answerType == 2 || this.answerType == 3) {
                    for (var i = 0; i < answer.length; i++){
                        answer[i].setMap(null);
                    }
                } else if (this.answerType == 4 || this.answerType == 5) {
                    answer.setMap(null);
                }
                delete answer;
            }, this);
            this.publicAnswers = null;
        }
    }
});
