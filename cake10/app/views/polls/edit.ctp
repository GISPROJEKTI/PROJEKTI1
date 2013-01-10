<?php echo $this->Html->script('backbone'); ?>
<?php echo $this->Html->script('pollEditApp'); ?>
<?php //echo $this->Html->script('liveController'); ?>
<?php echo $this->Html->script('models/poll'); ?>
<?php echo $this->Html->script('models/question'); ?>

<script>

var data = <?php echo json_encode($poll); ?>

</script>

<div id="poll">
    
</div>

<script type="text/x-jquery-tmpl" id="pollTmpl">
    <div class="input text">
        <label>Nimi</label>
        <input type="text" name="name" value="${name}" />
    </div>

    <div class="input textarea">
        <label>Kyselyn kuvaus</label>
        <textarea name="welcome_text" rows="6">${welcome_text}</textarea>
    </div>

    <div class="input textarea">
        <label>Kiitosteksti</label>
        <textarea name="thanks_text" rows="6">${thanks_text}</textarea>
    </div>

    <div class="input checkbox">
        <input type="checkbox" name="public"
            {{if public }}
                checked="checked"
            {{/if}}/>
        <label>Kaikille avoin</label>
    </div>

    <div class="input text">
        <label>Reitit</label>
        <input type="text" id="paths" />
    </div>

    <div class="input text">
        <label>Merkit</label>
        <input type="text" id="markers" />
    </div>
</script>
