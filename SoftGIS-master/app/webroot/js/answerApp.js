var AnswerApp = Spine.Controller.create({
    events: {
        "click .start" : "initQuestion",
        "click .nextQues": "nextQuestion",
        "click .prevQues": "prevQuestion"
    },
    elements: {
        "#map":             "mapEl", 
        "#question":        "questionEl",
        "#publicAnswers":   "publicsEl",
        "#noAnswerCont":  "noAnswerContEl",
        "#noAnswer":      "noAnswerCheckbox"
    },
    proxied: ["initQuestion", "saveAnswer", "finish", "clearPublicAnswers", "createPublicAnswers",
        "nextQuestion", "prevQuestion", "setNotes", "removeNotes", "saveAnswer", "setPreviousAnswers"],
    init: function() {
        /*
        To set up ewerythin needed for answerApp
        */
        var me = this;
        // Create map controller
        this.map = Map.init({ 
            el: this.mapEl,
            paths: this.data.Path,
            markers: this.data.Marker,
            overlays: this.data.Overlay
        }); //the map inits hidden

        // Stored answers
        this.answers = [];

        // Create question models
        for (var i in this.data.Question) {
            Question.create(this.data.Question[i]);
            this.answers.push({text: '', loc: '', no_asw: 0});
        }

        // Current question num
        this.questionNum = 0;
        this.nextQuestionNum = 1;

        // Show welcome text
        this.questionEl.html($.tmpl("welcomeTmpl", this.data.Poll));
        //this.map.hide(); // Hide map
        this.noAnswerContEl.hide(); // Hide no location checkbox
        this.publicsEl.hide();
        document.getElementById("map_note").style.display="none";

        // Confirms leaving the page
        this.promptBeforeUnload = true;

        $(window).bind("beforeunload", function() {
            if (me.promptBeforeUnload) {
                return "Vastauksiasi ei tallenneta, kun poistut sivulta. Haluatko varmasti poistua sivulta?";
            }
        });
    },
    initQuestion: function() {
        /*
            Init new question: nextQuestionNum
            Do/delegate all the stuff to get the question ready for answering
        */
        var me = this;

        this.questionNum = this.nextQuestionNum;

        this.activeQuestion = Question.select(function(q) {
            if (q.num == me.questionNum) {
                return true;
            }
        })[0];

        if (!this.activeQuestion) {
            // No more questions
            this.finish();
            return;
        }

        // Remove public answers from map and dom
        this.clearPublicAnswers();

        // Display and reset no location checkbox
        this.noAnswerCheckbox.removeAttr('checked');
        this.noAnswerContEl.show();


        // Form new question
        this.questionEl.html($.tmpl("questionTmpl", this.activeQuestion));

        // Update map location or hide it if not needed
        // old database maps
        if (this.activeQuestion.map_type == null){
            if ( this.activeQuestion.lat && this.activeQuestion.lng ) {
                this.activeQuestion.map_type = 1;
            } else {
                this.activeQuestion.map_type = 0;
            }
        }
        //update map to this question
        this.map.setMap(
            this.activeQuestion.map_type,
            this.activeQuestion.lat, 
            this.activeQuestion.lng,
            this.activeQuestion.zoom
        );

        //this digs the previous public answers from url: publicAnswersPath
        if (this.activeQuestion.answer_visible == 1){
            $.getJSON(publicAnswersPath, {question: this.activeQuestion.id}, function(data) {
                me.createPublicAnswers(data.answers);
            });
        }

        //set the prevQuest -button hidden or visible
        if (this.questionNum === 1){
            this.questionEl.find('.prevQues').hide();
        }else{
            this.questionEl.find('.prevQues').show();
        }

        //Hide 'no map answer' when not needed
        if (this.activeQuestion.map_type < 2){
            this.noAnswerContEl.hide();
        }else{
            this.noAnswerContEl.show();
        }

        //Update the question info text
        this.questionEl.find(".info").html("Kysymys numero " + this.questionNum + "/" + this.answers.length + " ");

        //Update the map_note text
        if (this.activeQuestion.map_type == 1){
            document.getElementById("map_note").style.display="block";
            document.getElementById("map_note").innerHTML = "Kartalle ei voi laittaa merkkejä";
        } else if (this.activeQuestion.map_type == 2){
            document.getElementById("map_note").style.display="block";
            document.getElementById("map_note").innerHTML = "Voit asettaa kartalle yhden merkin";
        } else if (this.activeQuestion.map_type == 3){
            document.getElementById("map_note").style.display="block";
            document.getElementById("map_note").innerHTML = "Voit asettaa kartalle monta merkkiä";
        } else if (this.activeQuestion.map_type == 4){
            document.getElementById("map_note").style.display="block";
            document.getElementById("map_note").innerHTML = "Voit luoda kartalle polun";
        } else if (this.activeQuestion.map_type == 5){
            document.getElementById("map_note").style.display="block";
            document.getElementById("map_note").innerHTML = "Voit luoda kartalle alueen";
        } else {
            document.getElementById("map_note").style.display="none";
            document.getElementById("map_note").innerHTML = "";
        }

        

        this.setPreviousAnswers()
        //console.log(this.questionNum, this.answers, this.answers.length);
    },
    setPreviousAnswers: function() {
        /*
            If user has answered allready this question, set old answers in place
        */
        //console.log(this.answers[this.questionNum-1]);

        if (this.answers[this.questionNum-1].no_asw === 1) { // jos on valittuna 'en halua vastata' -valinta
            this.noAnswerCheckbox.attr('checked','checked');
        } else {
            //Map answer
            if (this.answers[this.questionNum-1].loc != "") {
                this.map.setMapAnswer(this.answers[this.questionNum-1].loc);
            }
        }

        //Text answer
        if ( this.activeQuestion.type == 5) {
            var elements = this.questionEl.find("input");
                for (var i = 0; i < elements.length; i++) {
                    if (this.answers[this.questionNum-1].text[i] && this.answers[this.questionNum-1].text[i].chek) {
                        if (elements[i].name == "text"){
                            elements[i].checked = true;
                        }
                        //joku muu mikä boksin sisältö, lisätään vain jos kentässä on tekstiä
                        if (elements[i].name == "other"){
                            elements[i].value = this.answers[this.questionNum-1].text[i].val;
                        }
                    }
                };
            
        }else{
            if (this.answers[this.questionNum-1].text != "") { // jos kysymykseen on jo vastattu
                if ( this.activeQuestion.type == 1 ) {
                    this.questionEl.find("textarea").val(this.answers[this.questionNum-1].text);
                } else {
                    var elements = this.questionEl.find("input");
                    for (var i = elements.length - 1; i >= 0; i--) {
                        //console.log(elements[i].value);
                        if (elements[i].value == this.answers[this.questionNum-1].text){
                            elements[i].checked = true;
                            break;
                        }
                    };
                }
            }
        }
    },
    nextQuestion: function() {
        /*
            When user has pressed the next question button
        */
        this.nextQuestionNum = this.questionNum +1;
        this.setNotes();
    },
    prevQuestion: function() {
        /*
            When user has pressed the previous question button
        */
        if (this.questionNum > 1) {
            //ollaan ensimmäisessä kysymyksessa
            this.nextQuestionNum = this.questionNum -1;
        }
        this.removeNotes();
    },
    setNotes: function() {
        /*
            Check that user has answered all the fields needed at this question
        */
        var continueSubmit = true;

        // User dosen't want to answer to map
        //Map answer
        if ( !this.noAnswerCheckbox.is(':checked') && this.activeQuestion.map_type > 1 && this.map.getMapAnswer() == "" ) {
            // If map can have enswers, check that user has selected location, or notify
            this.mapEl.qtip({
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
            });
            continueSubmit = false;
        } else {
            this.mapEl.qtip( "destroy" );
        } 

        //Text answer
        var answerSelector;
        if(this.activeQuestion.type !== 0)
            if ( this.activeQuestion.type == 1 ) {
                answerSelector = "textarea";
            } else {
                answerSelector = "input:checked";
            }
            // Make sure user has answered something
            var answerVal = this.questionEl.find( answerSelector ).val();
            var answerVal2 = this.questionEl.find( "input:text" ).val();
            //console.log(answerVal, answerVal2);
            
            
            if ( this.activeQuestion.type > 0 && !answerVal && !answerVal2 ) {
                $( answerSelector ).focus();
                $( ".answer-field", this.el ).qtip({
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
                $( ".answer-field", this.el ).qtip( "destroy" );
            }



        //If all answers are ok, then continue
        if (continueSubmit) {
            this.removeNotes();
        }
    },
    removeNotes: function() {
        /*
            clear 'answer needed' (set by setNotes) notifications from the page.
        */
        this.mapEl.qtip( "destroy" );
        $( ".answer-field", this.el ).qtip( "destroy" );
        //and continue
        this.saveAnswer();
    },
    saveAnswer: function() {
        /*
            Saves the answers to the list during the answer session 
                (answers are sent to the server only at the end).
        */
        var answerVal = "";
        var answerLoc = "";
        var noAnsw = 0;

        //en halua vastata kartalle
        if (this.noAnswerCheckbox.is(':checked')) {
            noAnsw = 1;
        } else {
            //Kartta
            answerLoc = this.map.getMapAnswer();
        }

        //Teksti
        //tässä katsotaan onko kysely monivalinta jos on niin answerval-muuttujaan lisätään kaikki vastaukset putkeen

            if ( this.activeQuestion.type == 5) {
                answerVal = [];
                var elements = this.questionEl.find("input");
                    for (var i = 0; i < elements.length; i++) {
                        //console.log(elements[i].value);
                        if (elements[i].name == "text"){
                            answerVal.push({chek: elements[i].checked, val: elements[i].value});
                        }
                        //joku muu mikä boksin sisältö, lisätään vain jos kentässä on tekstiä
                        if (elements[i].name == "other"){
                            answerVal.push({chek: 'text', val: elements[i].value});
                        }
                    };
                
            }else{
                if ( this.activeQuestion.type == 1 ) {
                    answerVal = this.questionEl.find("textarea").val();
                } else if(this.activeQuestion.type == 0){
                    var answerVal = "";

                }else {
                    answerVal = this.questionEl.find("input:checked").val();
                }
            }
            //console.log(answerVal);
        

        //tallenna vastaukset
        this.answers.splice((this.questionNum - 1),1,{
            text: answerVal,
            loc: answerLoc,
            no_asw: noAnsw
        });
        //console.log(this.answers);

        this.initQuestion();

        return false;
    },
    clearPublicAnswers: function() {
        /*
            Clear public answers from the web page
        */
        this.publicsEl.hide().find(".answers").html("");
        this.map.clearPublicAnswers();
    },
    createPublicAnswers: function(answers) {
        /*
            Create previous answers to the page and to the map
        */
        this.publicsEl.show();
        var answersEl = this.publicsEl.find(".answers");
        _.each(answers, function(answer) {
            answersEl.append($.tmpl("publicAnswerTmpl", answer));
        }, this);
        this.map.createPublicAnswers(answers);
    },
    finish: function() {
        /*
        Send answers to server for saving and move to finish page
        */
        //parse the multichoise values
        //console.log(this.answers);
        for (var i = 0; i < this.data.Question.length; i++) {
            
            if (this.data.Question[i].type == 5) {
                var text = "";
                var answer = this.answers[i];
                //console.log(answer);
                for (var x = 0; x < answer.text.length; x++) {
                    if (answer.text[x].chek) {
                        text = text + answer.text[x].val + ",";
                    }
                }
                text = text + "";
                //console.log(text);
                this.answers.splice(i,1,{
                    text: text,
                    loc: answer.loc,
                    no_asw: answer.no_asw
                });
            }
        };
        //console.log(this.answers);

        //save
        var answers = JSON.stringify(this.answers);
        //console.log(answers);
        this.el.find( "#dataField" ).val(answers);
        this.promptBeforeUnload = false;
        this.el.find( "#postForm" ).submit();
    }
});
