<?php echo $this->Html->script('json2'); ?>
<?php echo $this->Html->script('spine'); ?>
<?php echo $this->Html->script('controllers/map'); ?>
<?php echo $this->Html->script('models/poll'); ?>
<?php echo $this->Html->script('models/question'); ?>
<?php echo $this->Html->script('answerApp'); ?>

<script>
var markerIconPath = "<?php echo $this->Html->url('/markericons/'); ?>";
var overlayPath = "<?php echo $this->Html->url('/img/overlays/'); ?>";
var publicAnswersPath = "<?php echo $this->Html->url('/answers/publicanswers.json'); ?>";
var publicAnswerIcon = "<?php echo $this->Html->url('/img/public_answer.png'); ?>";
var answerIcon = "<?php echo $this->Html->url('/img/answer.png'); ?>";


var answerApp;
$( document ).ready(function() {
    var data = <?php echo json_encode($poll); ?>;

    $.template("questionTmpl", $("#questionTmpl"));
    $.template("welcomeTmpl", $("#welcomeTmpl"));
    $.template("publicAnswerTmpl", $("#publicAnswerTmpl"));

    answerApp = AnswerApp.init({
        el: $("body"),
        data: data,
    });  

    // Help toggle
    $( ".help" ).hide();
    $( "#toggleHelp" ).click(function() {
        $( ".help" ).fadeToggle(400, "swing", function() {
            $( "#map" ).qtip("reposition");
            $( ".answer-field" ).qtip("reposition");
        });
        return false;
    });
});
</script>

<div class="answerMenu">
    <?php echo $this->Html->link(
        'Apua',
        '#help',
        array('class' => 'button', 'id' => 'toggleHelp')
    ); ?>
</div>

<div class="help">
    <h2>Vastausohjeet</h2>
    <p>Kyselyyn vastataan joko tekstikenttään tai monivalintaan, sekä mahdolliseen karttavastaukseen.</p>
    <p>Monivalinnassa valitse vaihtehdoista mielestäsi asiaa parhaiten kuvaava vaihtoehto.</p>
    <p>Joidenkin kysymysten yhteydessä kartalla voi näkyä kysymykseen liittyvältä karttamerkkejä, reittejä, alueita sekä kuvia.</p>
    <h2>Karttaan vastaaminen</h2>
    <p> Karttaa voi liikuttaa hiirellä vetämällä ja zoomata hiiren rullalla, mutta oletusarvoisesti se on kyselyn laatijan määräämässä sijannissa.</p>
    <p>Kysymyksestä riippuen, karttaan joko ei voi vastata, siihen voi laittaa merkin, useita merkkejä, polun tai alueen. Nämät kaikki asetetaan klikkaamalla karttaan hiiren ensimmäisellä painikkeella. Kartalla olevaa pistettä voi vetää hiirellä paikasta toiseen ja toisella hiiren painikkeella poistaa. Poluissa ja alueissa kulmapisteiden välissä olevista pallukoista vetämällä voi luoda uuden kulmapisteen.</p>
</div>

<script id="welcomeTmpl" type="text/x-jquery-tmpl">
    <h3>
        ${name}
    </h3>
    <div class="welcomeText">
        ${welcome_text}
    </div>
    <div class="answerNav">
        <button type="button" class="start">
            Aloita kysely
        </button>
    </div>
</script>

<script id="questionTmpl" type="text/x-jquery-tmpl">
    <div class="answerNav">
        <table class="answer"><tr>
            <td><button type="button" class="prevQues" id = "prev">Edellinen kysymys</button></td>
            <td><div class="info" id="info">Kysymys numero #</div></td>
            <td><button type="button" class="nextQues">Seuraava kysymys</button></td>
        </tr></table>
    </div>
    <h3>${text}</h3>
    <div class="answer-field">
        <div class="input">
            {{if type == 1}}
                <textarea name="text" id="texta"></textarea>
            {{else type == 2}}
                <input type="radio" name="texts" class="joo"  id="1" value="Kyllä"/>Kyllä
                <input type="radio" name="text" id="2" value="Ei"/>Ei
                <input type="radio" name="text" id="Eos" value="En osaa sanoa"/>En osaa sanoaa
            {{else type == 3}}
                ${low_text}
                <input type="radio" name="text" class="1" id="1" value="1"/>
                <input type="radio" name="text" id="2" value="2"/>
                <input type="radio" name="text" id="3" value="3"/>
                <input type="radio" name="text" id="4" value="4"/>
                <input type="radio" name="text" id="5" value="5"/>
                ${high_text}
                <input type="radio" name="text" id="Eos" value="En osaa sanoa"/>En osaa sanoa
            {{else type == 4}}
                ${low_text}
                <input type="radio" name="text" class="1"  id="1" value="1"/>
                <input type="radio" name="text" id="2" value="2"/>
                <input type="radio" name="text" id="3" value="3"/>
                <input type="radio" name="text" id="4" value="4"/>
                <input type="radio" name="text" id="5" value="5"/>
                <input type="radio" name="text" id="6" value="6"/>
                <input type="radio" name="text" id="7" value="7"/>
                ${high_text}
                <input type="radio" name="text" id="Eos" value="En osaa sanoa"/>En osaa sanoa
            {{/if}}
        </div>
    </div>
</script>

<script id="publicAnswerTmpl" type="text/x-jquery-tmpl">
    <div class="publicAnswer">
        ${answer}
    </div>
</script>

<div id="question" class="answer"></div>
<div class="answer">
    <div id="noAnswerCont">
        <input type="checkbox" id="noAnswer" />
        <label>En halua vastata kartlle</label>
    </div>
        <div id="map" class="map">
    </div>
</div>
<form method="POST" 
    action="<?php echo $this->Html->url(array('action' => 'finish')); ?>" 
    id="postForm">
    <input type="hidden" id="dataField" name="data"/>
</form>

<div id="publicAnswers" class="publicAnswers">
    <h3>Aiemmat vastaukset</h3>
    <div class="answers">
    </div>
</div>
