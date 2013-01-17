

var viewModel = {
    questions: ko.observableArray([]),

}




$( document ).ready(function() {

    var questionList = $( "#questions" ); // Questions list

    // Arrenging questions
    $( "#questions" ).sortable({
        axis: "y",
        // containment: "parent",
        stop: function (event, ui) {
            var questions = questionList.children( ".question" );
            questions.each(function(index) {
                // Update number indicator and hidden value for each question
                $( this ).find( "td.num" ).text( index + 1);
                $( this ).find( "input.num" ).val( index + 1 );
            });
        }
   });



    // Create question
    $.template( "question", $( "#question-tmpl") );
    $( "#create-question" ).click(function() {
        
        var questionCount = questionList.children( ".question" ).length;
        createQuestion( 
            {
                i: questionIndex, // Defined in view
                num: questionCount + 1
            }
        ).find( ".details" ).fadeIn();
        questionIndex++;
        return false;
    });



    // Question details toggling
    questionList.delegate( ".expand", "click", function() {
        $( this ).closest( ".header" ).next( ".details" ).fadeToggle();
    });



    // Update header text when question input loses focus
    questionList.delegate( "textarea.text", "blur", function() {
        var text = $( this ).val();
        $( this ).closest( ".details" ).prev( ".header" )
            .find( "span.text" ).text( text );
    });

    // Location picking
    var locationPicker = $( "#loc-picker" ).locationpicker();
    questionList.delegate( "button.pick-location", "click", function() {
        var input = $( this ).siblings( "input.latlng" );
        locationPicker.locationpicker( 
            "open", 
            input.val(), 
            function(newPos) {
                input.val( newPos );
            }
        );
        return false;
    });


    function createQuestion(data) {
        var question = $.tmpl( "question", data ).appendTo( questionList );
        // console.info(data);
        // question.find( "input.latlng" ).locationPicker();
        return question;
    }
});
