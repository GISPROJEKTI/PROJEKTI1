var AnswerApp = Spine.Controller.create({
    events: {
        "click .submit": "answer",
        "click .start" : "initNextQuestion" 
    },
    elements: {
        "#map":             "mapEl", 
        "#question":        "questionEl",
        "#publicAnswers":   "publicsEl",
        "#noAnswerCont":  "noAnswerContEl",
        "#noAnswer":      "noAnswerCheckbox"
    },
    proxied: ["initNextQuestion", "answer", "finish", "clearPublicAnswers",
        "createPublicAnswers"],
    init: function() {
        var me = this;
        // Create map controller
        this.map = Map.init({ 
            el: this.mapEl,
            paths: this.data.Path,
            markers: this.data.Marker,
            overlays: this.data.Overlay
        });

        // Create question models
        for (var i in this.data.Question) {
            Question.create(this.data.Question[i]);
        }

        // Current question num
        this.questionNum = 0;
        // Stored answers
        this.answers = [];

        // Show welcome text
        this.questionEl.html($.tmpl("welcomeTmpl", this.data.Poll));
        this.map.hide(); // Hide map
        this.noAnswerContEl.hide(); // Hide no location checkbox

        // Confirms leaving the page
        this.promptBeforeUnload = true;
        $(window).bind("beforeunload", function() {
            if (me.promptBeforeUnload) {
                return "Vastauksiasi ei tallenneta, kun poistut sivulta. Haluatko varmasti poistua sivulta?";
            }
        });
        this.publicsEl.hide();
    },
    initNextQuestion: function() {
        this.questionNum++;
        var num = this.questionNum;
        this.activeQuestion = Question.select(function(q) {
            if (q.num == num) {
                return true;
            }
        })[0];

        if (!this.activeQuestion) {
            // No more questions
            this.finish();
            return;
        }

        // Update map location or hide it if not needed
        if ( this.activeQuestion.lat && this.activeQuestion.lng ) {
            this.map.setCenter(
                this.activeQuestion.lat, 
                this.activeQuestion.lng,
                this.activeQuestion.zoom);
            if (this.activeQuestion.answer_location == "1") {
                this.map.setSelectable(true);
            } else {
                this.map.setSelectable(false);
            }
        } else {
            this.map.hide();
        }
        // Form new question
        this.questionEl.html($.tmpl("questionTmpl", this.activeQuestion));

        // Remove public answers from map and dom
        this.map.clearPublicAnswers();
        this.clearPublicAnswers();


        // if (this.activeQuestion.answer_location == "1") {
        //     // Display and reset no location checkbox
        this.noAnswerCheckbox.removeAttr('checked');
        this.noAnswerContEl.show();
        // } else {
        //     // Hide checkbox
        //     this.noAnswerContEl.hide();
        // }

        var me = this;
        $.getJSON(publicAnswersPath, {question: this.activeQuestion.id}, function(data) {
            if (me.activeQuestion.answer_location == "1") {
                me.map.createPublicAnswers(data.answers);
            } else {
                me.createPublicAnswers(data.answers);
            }
        });
    },
    clearPublicAnswers: function() {
        this.publicsEl.hide().find(".answers").html("");
    },
    createPublicAnswers: function(answers) {
        this.publicsEl.show();
        var answersEl = this.publicsEl.find(".answers");
        _.each(answers, function(answer) {
            answersEl.append($.tmpl("publicAnswerTmpl", answer));
        }, this);
    },
    finish: function() {
        var answers = JSON.stringify(this.answers);
        this.el.find( "#dataField" ).val(answers);
        this.promptBeforeUnload = false;
        this.el.find( "#postForm" ).submit();
    },
    answer: function() {
        var answerSelector;
        if ( this.activeQuestion.type == 1 ) {
            answerSelector = "textarea";
        } else {
            answerSelector = "input:checked";
        }

        
        // User dosn't want to answer
        if (this.noAnswerCheckbox.is(':checked')) {
            this.answers.push({
                text: '',
                loc: ''
            });
            this.mapEl.qtip( "destroy" );
            $( ".answer-field", this.el ).qtip( "destroy" );
            this.initNextQuestion();
            return false;
        }


        var continueSubmit = true;

        var answerLoc = "";

        if ( this.activeQuestion.answer_location == "1" ) {
            // Make sure user has selected location
            answerLoc = this.map.getAnswerLoc();

            if ( !answerLoc ) {
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
        } else {
            this.mapEl.qtip( "destroy" );
        }

        // Make sure user has answered something
        var answerVal = this.questionEl.find( answerSelector ).val();
        if ( !answerVal ) {
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
        
        if (continueSubmit) {
            this.answers.push({
                text: answerVal,
                loc: answerLoc
            });
            this.initNextQuestion();
        }

        return false;
    }
});