<?php echo $this->Html->script('locationpicker'); ?>

<script>
var pathSearchUrl = <?php echo json_encode($reittiarray); ?>;
var markerSearchUrl = <?php echo json_encode($merkkiarray); ?>;
var overlaySearchUrl = <?php echo json_encode($overlayarray); ?>;
var locationPicker;

var questionDatas = <?php echo json_encode($poll['Question']); ?>;
var questions = [];
_.each(questionDatas, function(data) {
    questions.push(new Question(data));
});

//connect items with observableArrays
  ko.bindingHandlers.sortableList = {
      init: function(element, valueAccessor) {
          $(element).sortable({
              update: function(event, ui) {
                  //retrieve our actual data item
                  var item = ui.item.tmplItem().data;
                  //figure out its new position
                  var position = ko.utils.arrayIndexOf(ui.item.parent().children(), ui.item[0]);
                  //remove the item and add it back in the right spot
                  if (position >= 0) {
                      questions.splice(item.num()-1,1);
                      questions.splice(position, 0, item);
                  }
                  arrnageQuestion();
              }
          });
      }
  };

function arrnageQuestion() {
    for(i=0; i < questions.length; i++){
        questions[i].num(i+1);
    }
}

var viewModel = {

    poll: new Poll(<?php echo json_encode($poll['Poll']); ?>),

    questions: ko.observableArray(questions),

    paths: ko.observableArray(<?php echo json_encode($poll['Path']); ?>),
    markers: ko.observableArray(<?php echo json_encode($poll['Marker']); ?>),
    overlays: ko.observableArray(<?php echo json_encode($poll['Overlay']); ?>),

    // List of question types
    types: [
        { id: 0, label: "Ei tekstivastausta"},
        { id: 1, label: "Teksti" },
        { id: 2, label: "Kyllä, Ei, En osaa sanoa" },
        { id: 3, label: "1-5, En osaa sanoa" },
        { id: 4, label: "1-7, En osaa sanoa" },
		{ id: 5, label: "Monivalinta (max 9)" }
    ],
    // List of map types on question
    mapTypes: [
        { id: 0, label: "Ei karttaa" },
        { id: 1, label: "Kartta, ei vastausta" },
        { id: 2, label: "Kartta, 1 merkki" },
        { id: 3, label: "Kartta, monta merkkiä" },
        { id: 4, label: "Kartta, viiva" },
        { id: 5, label: "Kartta, alue" }
    ],
    newQuestion: function() {
        var question = new Question({
            num: this.questions().length + 1
        });
        question.toggle();
        this.questions.push(question);
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
	
	this.choice1 = ko.observable( data.choice1 ? data.choice1 : null );
	this.choice2 = ko.observable( data.choice2 ? data.choice2 : null );
	this.choice3 = ko.observable( data.choice3 ? data.choice3 : null );
	this.choice4 = ko.observable( data.choice4 ? data.choice4 : null );
	this.choice5 = ko.observable( data.choice5 ? data.choice5 : null );
	this.choice6 = ko.observable( data.choice6 ? data.choice6 : null );
	this.choice7 = ko.observable( data.choice7 ? data.choice7 : null );
	this.choice8 = ko.observable( data.choice8 ? data.choice8 : null );
	
	this.otherchoice = ko.observable( data.otherchoice && data.otherchoice != "0" ? true : null );
	
    this.map_type = ko.observable( data.map_type ? data.map_type : null );

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

Question.prototype.poista = function(){
    if(confirm("Haluatko varmasti poistaa kysymyksen numero "+ this.num() + ', ' + this.text())) {
        //poista
        viewModel.questions.splice(this.num() -1,1);
        //aseta uudet numerot
        arrnageQuestion();
    }
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

    // Help toggle
    $( ".help" ).hide();
    $( "#toggleHelp" ).click(function() {
        $( ".help" ).fadeToggle(400, "swing");
        return false;
    });

    ko.applyBindings( viewModel );

    // Init lockation picker
    locationPicker = $( "#loc-picker" ).locationpicker();


         // Path selector init
    $( "#paths" ).tokenInput(pathSearchUrl, {
        prePopulate: viewModel.paths(),
        noResultsText : 'Viivaa tai aluetta ei löytynyt, Lisää viiva tai alue  "Vektoriaineistot" välilehden kautta',
        preventDuplicates: true,
        minChars: 0,
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
        noResultsText : 'Merkkiä ei löytynyt, Lisää merkki  "Karttamerkit" välilehden kautta',
        minChars: 0,
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
        minChars: 0,
        noResultsText : 'Kuvaa ei löytynyt, Lisää kuva  "Tuo kuva" välilehden kautta',
        onAdd: function(item) {
            viewModel.overlays.push( item );
 
        },
        onDelete: function(item) {
            viewModel.overlays.remove( item );
        } 
    });

    $( "#saveButton" ).click(function() {
        //Tarkistetaan että tarvittavat tiedot löytyvät lähetettävästä lomakkeesta
        var kaikkiok = true;
        var errors = "";
       
        if(questions.length === 0){
            errors = errors + "Kyselyssä pitää olla vähintään yksi kysymys\n";
            kaikkiok = false;
        }
        questions.forEach(function(i){
            if( i.text() == null || i.text() == ""){
                errors = errors + "Lisää kysymysteksti kysymykselle "+ i.num() + "\n";
                kaikkiok = false;
            }
            if(i.type() == 5){
			
				if((i.choice1() == null || i.choice1() == "")
					&& (i.choice2() == null || i.choice2() == "")
					&& (i.choice3() == null || i.choice3() == "")
					&& (i.choice4() == null || i.choice4() == "")
					&& (i.choice5() == null || i.choice5() == "")
					&& (i.choice6() == null || i.choice6() == "")
					&& (i.choice7() == null || i.choice7() == "")
					&& (i.choice8() == null || i.choice8() == "")
					&& (i.otherchoice() == null || i.otherchoice() == false || i.otherchoice() == 0)){
				
					errors = errors + "Anna ainakin yksi monivalinnan vaihtoehto kysymykselle " + i.num() + "\n";
					kaikkiok = false;
				}
				

            }
        });

         if(viewModel.poll.name() === null ||viewModel.poll.name() === ""){
            errors = errors + "Anna kyselylle nimi\n";
            kaikkiok =  false;

        }
        if(kaikkiok){

            var data = ko.toJSON(viewModel);
            $( "#data" ).val( data ); 

        }else{
            alert(errors);
            return false;

        }
    });

});

</script>

<div class="answerMenu">
    <a href="#help" class="button" id="toggleHelp">Ohje</a>
</div>

<h2>Kyselyn tiedot</h2>

<div class="help">
    <h2>Kyselyn muokkaaminen</h2>
    <p><h3>Kyselyn tiedot</h3></p>
        <p><b>Nimi (pakollinen), kuvaus, kiitosteksti ja Kaikille avoin -valinta</b></p>
            <p>Oletusarvona kaikille avoin -valinta on ruksattu, jolloin kyselyyn voidaan julkaisemisen jälkeen vastata kyselyn identifioivalla linkillä. Jos kyselyyn halutaan vastata ns. henkilökohtaisilla linkeillä (=varmenteilla), ”täppä” tulee poistaa.</p>
        <p><b>Kyselyyn liittyvät merkit, aineistot ja kuvat</b></p>
            <p>Näihin kenttiin voi valita kyselyssä näkyviä karttamerkkejä, vektoriaineistoja ja karttakuvia. Kenttään painettaessa avautuu lista lisättävistä aineistoista, jota painamalla aineisto tulee valittua kyseyyn mukaan (listalla näkyvät vain käyttäjän aineistot, ei muiden). Voit valita useita aineistoja jokaiseen kenttään. Valittujen aineistojen perässä on ruksi, josta painamalla valinnan voi poistaa. Voit luoda näitä aineistoja, yllä olevilla välilehdillä.</p>
    <p><h3>Kysymykset</h3></p>
        <p><b>Luo uusi kysymys</b></p>
            <p>Luo uusi kysymys -painikkeesta voit luoda kysymyksiä kyselyyn. Kyselyssä tulee olla vähintään yksi kysymys, että se voidaan tallentaa.</p>
        <p><b>Kysymyksen otsikko</b></p>
            <p>Kysymyksen otsikossa näkyy kysymyksen järjestysnumero, kysymysteksti, sekä näytä ja poista painikkeet. Näytä painikke pienentää tai avaa kysymyksen tiedot ja poista painike poistaa kyseisen kysymyksen kyselystä. Voit uudellenjärjestää kysymyksiä vetämällä niitä otsikosta uuteen järjestykseen.</p>
        <p><b>Kysymysteksti (pakollinen)</b></p>
            <p>Kysymysteksti, joka näkyy vastaajalle vastatessa kyysmykseen.</p>
        <p><b>Tekstivastauksen tyyppi</b></p>
            <p>Listasta voi valita millaisen tekstivastauksen kysymykseen haluaa. 'Ei tekstivastausta', 'Teksti' ja 'Kyllä, Ei, En osaa sanoa' eivät anna lisävalintoja. '1-5, En osaa sanoa' ja '1-7, En osaa sanoa' vastauksiin tulee lisätä ennen ensimmäistä vaihtoehtoa (pienin) ja viimeisen vaihtoehdon jälkeen (suurin) näkyvät tekstit. 'Monivalinta' vastaukseen voit itse määrittää haluamasi määrän vaihtoehtoja sekä 'Joku muu, mikä?' -valinta lisää vaihtoehdon, johon vastaaja voi itse kirjoittaa vastuksen. Vain täytetyt kentät tulevat mukaan vastausvaihtoehdoiksi ja vähintään yksi kenttä tulee täyttää, että kusely voidaan tallentaa.</p>
        <p><b>Karttavastauksen tyyppi</b></p>
            <p>Listalta voi valita millaisen karttavastauksen kysymykseen haluaa. 'Ei karttaa' valinnalla kysymykseen vastattaessa ei ole karttaa. 'Kartta, ei vastausta' valinnalla kysymyksessä on kartta, mutta siihen ei voi lisätä merkkejä. Muilla valinnoilla vastaaja voi lisätä vastauskartalle valitun tyyppisen/tyyppisiä merkin/merkkejä vastaukseksi. Muilla paitsi 'Ei karttaa' valinnalla alle avautuu 'Siajinti' lisävaihtoehdot. 'Koordinaatti' ja 'Zoom-taso' -tekstikenttiin voit kirjoittaa haluamasi vastauskartan sijainnin, tai 'Valitse sijainti kartalta' painikkeen kautta voit kohdistaa kartan haluamaasi vastaussijaintiin.</p>
        <p><b>Vastaukset näkyvissä muille vastaajille</b></p>
            <p>Oletusarvoisesti edelliset vastaukset eivät näy seuraaville vastaajille, mutta valittuasi 'Vastaukset näkyvissä muille vastaajille' -valinan kysymykseen aiemmin vastatut vastaukset näkyvät seuraaville vastaajille.</p>
</div>

<!-- Form -->
<div class="input text">
    <label>Nimi</label>
    <input type="text" data-bind="value: poll.name" placeholder="Kyselyn nimi", required =  "true" />
</div>

<div class="input textarea">
    <label>Kyselyn kuvaus</label>
    <textarea data-bind="value: poll.welcome_text" rows="6" placeholder="Kyselyn kuvaus näkyy vastaajalle ennen kysymyksiä"></textarea>
</div>

<div class="input textarea">
    <label>Kiitosteksti</label>
    <textarea data-bind="value: poll.thanks_text" rows="6" placeholder="Kiitosteksti näkyy vastaajalle kysymysten jälkeen"></textarea>
</div>

<div class="input checkbox">
    <input type="checkbox" data-bind="checked: poll.public"/>
    <label for="PollPublic">Kaikille avoin</label>
</div>

<div class="input text">
    <label>Viivat ja alueet</label>
    <input type="text" id="paths" />
</div>

<div class="input text">
    <label>Karttamerkit</label>
    <input type="text" id="markers" />
</div>

<div class="input text">
    <label>Karttakuvat</label>
    <input type="text" id="overlays" />
</div>


<div class="input">
    <label>Kysymykset</label>
    <ul id="questions" 
        data-bind=" template: {
            name: 'questionTmpl',
            foreach: questions
        },sortableList: questions">
    </ul>
    <button type="button" id="create-question" data-bind="click: newQuestion">
        Luo uusi kysymys
    </button>
</div>

<hr>
<form method="post">
    <input type="hidden" name="data" id="data"/>
    <button type="submit" id="saveButton"  onsubmit="">
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
    <table class="header" data-bind="click: toggle">
        <tr>
            <td class="num" data-bind="text: num"></td>
            <td>&nbsp;<span class="text" data-bind="text: text"></span></td>
			<td class="button" data-bind="click: poista">
			<div class="expand">Poista</div>
			</td>
            <td class="button" data-bind="click: toggle">
                <div class="expand">Näytä</div>
            </td>
        </tr>
    </table>
    <div class="details" data-bind="visible: visible">

        <div class="input textarea">
            <label>Kysymysteksti</label>
            <textarea class="text" data-bind="value: text" required = "1"></textarea> 
        </div>

        <div class="input select">
            <label>Tekstivastauksen tyyppi</label>
            <select data-bind="options: viewModel.types,
                optionsText: 'label', optionsValue: 'id',
                value: type" />
        </div>

        <div class="input text" data-bind="visible: type() == 3 || type() == 4">
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
		
		<!-- Tässä mitä näytetään jos valitaan "Monivalinta" vastauslistasta-->
		
		<div class="input text" data-bind="visible: type() == 5">
            <label>Vastauksen vaihtoehdot</label>
			HUOM: Täytä niin monta kenttää kuin haluat vaihtoehtoja <br/>
			<br/>
			Jos valitset Joku muu mikä? -vaihtoehdon niin muiden vaihtoehtojen lisäksi vastaajalle näytetään tekstikenttä, johon hän voi kirjoittaa oman vaihtoehtonsa. <br/>
			<br/>
			<br/>
            <div>
                <div class="inline">
                    <label>1. vaihtoehto</label>
                    <input type="text" 
                        class="small" 
                        data-bind="value: choice1"/>
                </div>
                <div class="inline">
                    <label>2. vaihtoehto</label>
                    <input type="text" 
                        class="small" 
                        data-bind="value: choice2" />
                </div>
				<div class="inline">
                    <label>3. vaihtoehto</label>
                    <input type="text" 
                        class="small" 
                        data-bind="value: choice3" />
                </div>
				<div class="inline">
                    <label>4. vaihtoehto</label>
                    <input type="text" 
                        class="small" 
                        data-bind="value: choice4" />
                </div>
				<div class="inline">
                    <label>5. vaihtoehto</label>
                    <input type="text" 
                        class="small" 
                        data-bind="value: choice5" />
                </div>
				<div class="inline">
                    <label>6. vaihtoehto</label>
                    <input type="text" 
                        class="small" 
                        data-bind="value: choice6" />
                </div>
				<div class="inline">
                    <label>7. vaihtoehto</label>
                    <input type="text" 
                        class="small" 
                        data-bind="value: choice7" />
                </div>
				<div class="inline">
                    <label>8. vaihtoehto</label>
                    <input type="text" 
                        class="small" 
                        data-bind="value: choice8" />
                </div>
				<div class="inline">
                    <label>Joku muu, mikä?</label>
                    <input type="checkbox" 
                        class="small" 
                        data-bind="checked: otherchoice" />
                </div>
            </div>
        </div>
		
		<!-- Tässä loppuu-->

        <div class="input select">
            <label>Karttavastauksen tyyppi</label>
            <select data-bind="options: viewModel.mapTypes,
                optionsText: 'label', optionsValue: 'id',
                value: map_type" />
        </div>
        <div class="input text" data-bind="visible: map_type() > 0">
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

        <div class="input checkbox">
            <input type="checkbox"
                data-bind="checked: answer_visible" />
            <label>Vastaukset näkyvissä muille vastaajille</label>
        </div>
    </div>
</li>
</script>

<!-- Nämä palikat otettu pois ylläolevasta templatesta, koska en osaa kommentoida niitä piiloon:

        <div class="input checkbox" data-bind="visible: latlng()">
            <input type="checkbox"
                data-bind="checked: answer_location" />
            <label>Kohteen merkitseminen kartalle</label>
        </div>

        <div class="input checkbox data-bind="visible: answer_visible()">
            <input type="checkbox"
                data-bind="checked: comments" />
            <label>Vastausten kommentointi</label>
        </div>
-->

