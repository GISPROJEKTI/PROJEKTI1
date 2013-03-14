<?php echo $this->Html->script('locationpicker'); ?>

<script>

var pathSearchUrl = "<?php echo $this->Html->url(
        array('controller' => 'paths', 'action' => 'search.json')
    ); ?>";

var markerSearchUrl = "<?php echo $this->Html->url(
        array('controller' => 'markers', 'action' => 'search.json')
    ); ?>";

var overlaySearchUrl = "<?php echo $this->Html->url(
        array('controller' => 'overlays', 'action' => 'search.json')
    ); ?>";

var locationPicker;

var questionDatas = <?php echo json_encode($poll['Question']); ?>;
var questions = [];
_.each(questionDatas, function(data) {
    questions.push(new Question(data));
});

var viewModel = {

    poll: new Poll(<?php echo json_encode($poll['Poll']); ?>),

    questions: ko.observableArray(questions),

    paths: ko.observableArray(<?php echo json_encode($poll['Path']); ?>),
    markers: ko.observableArray(<?php echo json_encode($poll['Marker']); ?>),
    overlays: ko.observableArray(<?php echo json_encode($poll['Overlay']); ?>),

    // List of question types
    types: [
        { id: 1, label: "Teksti" },
        { id: 2, label: "Kyllä, Ei, En osaa sanoa" },
        { id: 3, label: "1-5, En osaa sanoa" },
        { id: 4, label: "1-7, En osaa sanoa" }
    ],
    newQuestion: function() {
        var question = new Question({
            num: this.questions().length + 1
        });
        question.toggle();
        this.questions.push(question);
    },
	//kysymysten poisto -funktio
    deleteQuestion: function() {

		//haetaan arvo jonka käyttäjä on syöttänyt Poistettavan kysymyksen numero tekstikenttään
		var arvo = document.getElementById("arvo").value;
		arvo =  parseInt(arvo);
		
		//varmistetaan haluaako käyttäjä poistaa kysymyksen
		
		var ok = confirm("Haluatko varmasti poistaa kysymyksen " + arvo + ": " + this.questions()[(arvo-1)].getText())
		
		if(ok==true){
			//loopataan kaikki kysymykset läpi ja katsotaan missä käyttäjän syöttämä arvo ja kysymyksen num mätsää
			//ja poistetaan se mikä mätsää
			for(i=0; i < this.questions().length; i++){
				if(this.questions()[i].getNum() == arvo){
					this.questions.splice(i,1);
				}
			}
			// tässä järjestetään uudestaan kysymysten numerot
			for(i=0; i < this.questions().length; i++){
				var uusiArvo = i+1;
				this.questions()[i].setNum(uusiArvo);
			}
		
		}
    }
}

function Poll(data) {
    this.id = ko.observable( data.id ? data.id : null );
    this.name = ko.observable( data.name ? data.name : null );
    this.public = ko.observable( data.public == "0" ? false : true );
    this.welcome_text = ko.observable( data.welcome_text ? data.welcome_text : null );
    this.thanks_text = ko.observable( data.thanks_text ? data.thanks_text : null );
}

function Question(data, visible) {
    // console.info(data);
    this.id = ko.observable( data.id ? data.id : null );
    this.text = ko.observable( data.text ? data.text : null );
    this.num = ko.observable( data.num ? data.num : null );
    this.type = ko.observable( data.type ? data.type : null );
    this.low_text = ko.observable( data.low_text ? data.low_text : null );
    this.high_text = ko.observable( data.high_text ? data.high_text : null );
    this.latlng = ko.observable( data.latlng ? data.latlng : null );
    this.zoom = ko.observable( data.zoom ? data.zoom : null );

    // Pfft, Cake thinks 0 is false
    this.answer_location = ko.observable( 
        data.answer_location && data.answer_location != "0" ? true : false 
    );
    this.answer_visible = ko.observable( 
        data.answer_visible && data.answer_visible != "0" ? true : null 
    );
    this.comments = ko.observable( 
        data.comments && data.comments != "0" ? true : false 
    );

    this.visible = ko.observable( visible ? true : false );
}

Question.prototype.toggle = function() {
    this.visible( !this.visible() );
}

Question.prototype.getNum = function() {
	return(this.num());
}

Question.prototype.getText = function() {
	return(this.text());
}

Question.prototype.setNum = function(arvo) {
	this.num(arvo);
}

Question.prototype.pickLocation = function() {
    var me = this;
    locationPicker.locationpicker(
        "open",
        this.latlng(),
        this.zoom(),
        function(newPos, zoom ) {
            me.latlng( newPos );
            me.zoom( zoom );
        }
    );
}

$( document ).ready(function() {
    ko.applyBindings( viewModel );

    // Init lockation picker
    locationPicker = $( "#loc-picker" ).locationpicker();


    // Path selector init
    $( "#paths" ).tokenInput(pathSearchUrl, {
        prePopulate: viewModel.paths(),
        preventDuplicates: true,
        onAdd: function(item) {
            viewModel.paths.push( item );
        },
        onDelete: function(item) {
            viewModel.paths.remove( item );
        }
    });

    // Marker selector init
    $( "#markers" ).tokenInput(markerSearchUrl, {
        prePopulate: viewModel.markers(),
        preventDuplicates: true,
        onAdd: function(item) {
            viewModel.markers.push( item );
        },
        onDelete: function(item) {
            viewModel.markers.remove( item );
        }
    });

    $( "#overlays" ).tokenInput(overlaySearchUrl, {
        prePopulate: viewModel.overlays(),
        preventDuplicates: true,
        onAdd: function(item) {
            viewModel.overlays.push( item );
        },
        onDelete: function(item) {
            viewModel.overlays.remove( item );
        }
    });

    $( "#saveButton" ).click(function() {
        var data = ko.toJSON(viewModel);
        $( "#data" ).val( data );
    });
});

</script>


<h2>Kysely</h2>
<!-- Form -->
<div class="input text">
    <label>Nimi</label>
    <input type="text" data-bind="value: poll.name" />
</div>

<div class="input textarea">
    <label>Kyselyn kuvaus</label>
    <textarea data-bind="value: poll.welcome_text" rows="6"></textarea>
</div>

<div class="input textarea">
    <label>Kiitosteksti</label>
    <textarea data-bind="value: poll.thanks_text" rows="6"></textarea>
</div>

<div class="input checkbox">
    <input type="checkbox" data-bind="checked: poll.public"/>
    <label for="PollPublic">Kaikille avoin</label>
</div>

<div class="input text">
    <label>Reitit</label>
    <input type="text" id="paths" />
</div>

<div class="input text">
    <label>Karttamerkit</label>
    <input type="text" id="markers" />
</div>

<div class="input text">
    <label>Kuvat</label>
    <input type="text" id="overlays" />
</div>


<div class="input">
    <label>Kysymykset</label>
    <ul id="questions" 
        data-bind="template: {
            name: 'questionTmpl',
            foreach: questions
        }">
    </ul>
    <button type="button" id="create-question" data-bind="click: newQuestion">
        Luo uusi kysymys
    </button>
	
	<!-- Tässä kysymykseen poistoa varten tekstikenttä ja nappi, nappi kutsuu klikatessa poisto-funktiota-->
	<hr/>
	<label>Poistettavan kysymyksen numero</label>
	<input type="text" class="small" name="arvo" id="arvo" value="" maxlength="3"/> <br/>     
	<button type="button" id="delete-question" data-bind="click: deleteQuestion">
		Poista kysymys
	</button>
	<hr/>
	<!-- Tässä loppuu-->
</div>

<form method="post">
    <input type="hidden" name="data" id="data"/>
    <button type="submit" id="saveButton">
        Tallenna kysely
    </button>
    <?php 
    if (!empty($poll['Poll']['id'])) {
        $url = array('action' => 'view', $poll['Poll']['id']);
    } else {
        $url = array('action' => 'index');
    }
    echo $this->Html->link(
        'Peruuta',
        $url,
        array(
            'class' => 'button cancel'
        )
    ); 
    ?>
</form>


<div id="loc-picker"></div>


<!-- Question Template -->
<script type="text/x-jquery-tmpl" id="questionTmpl">

<li class="question">
    <table class="header">
        <tr>
            <td class="num" data-bind="text: num"></td>
            <td>&nbsp;<span class="text" data-bind="text: text"></span></td>
            <td class="button" data-bind="click: toggle">
                <div class="expand">Näytä</div>
            </td>
        </tr>
    </table>
    <div class="details" data-bind="visible: visible">

        <div class="input textarea">
            <label>Kysymys</label>
            <textarea class="text" data-bind="value: text"></textarea> 
        </div>

        <div class="input select">
            <label>Vastaus</label>
            <select data-bind="options: viewModel.types,
                optionsText: 'label', optionsValue: 'id',
                value: type" />
        </div>

        <div class="input text" data-bind="visible: type() > 2">
            <label>Ääripäiden tekstit</label>
            <div>
                <div class="inline">
                    <label>Pienin</label>
                    <input type="text" 
                        class="small" 
                        data-bind="value: low_text"/>
                </div>
                <div class="inline">
                    <label>Suurin</label>
                    <input type="text" 
                        class="small" 
                        data-bind="value: high_text" />
                </div>
            </div>
        </div>

        <div class="input text">
            <label>Sijainti</label>
            <button class="pick-location" 
                type="button"
                data-bind="click: pickLocation">
                Valitse sijainti kartalta
            </button>
            <div>
                <div class="inline">
                    <label>Koordinaatti</label>
                    <input type="text" 
                        class="latlng"
                        data-bind="value: latlng"/>
                </div>
                <div class="inline">
                    <label>Zoom-taso</label>
                    <input type="text"
                        class="zoom"
                        data-bind="value: zoom"/>
                </div>
            </div>
        </div>

        <div class="input checkbox" data-bind="visible: latlng()">
            <input type="checkbox"
                data-bind="checked: answer_location" />
            <label>Kohteen merkitseminen kartalle</label>
        </div>

        <div class="input checkbox">
            <input type="checkbox"
                data-bind="checked: answer_visible" />
            <label>Vastaukset näkyvissä muille vastaajille</label>
        </div>

        <div class="input checkbox">
            <input type="checkbox"
                data-bind="checked: comments" />
            <label>Vastausten kommentointi</label>
        </div>
    </div>
</li>
</script>
