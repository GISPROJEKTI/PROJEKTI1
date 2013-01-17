<script>
$(document).ready(function() {
    $( document ).ready(function() {
        $('input[title]').qtip({
            show: {
                event: "mouseenter focusin"
            },
            hide: {
                event: "mouseleave focusout"
            },
            position: {
                my: "bottom center",
                at: "top center"
            }
        });
    });
});
</script>

<?php
    echo $this->Session->flash('auth');
    echo $this->Form->create('Author');
    echo $this->Form->input('username', array('label' => 'Käyttäjänimi'));
    echo $this->Form->input('password', array('label' => 'Salasana'));
?>

<div class="input text">
    <label for="secter">Tunniste</label>
    <input type="text" name="data[secret]" id="secret" 
        title="Tunnisteen saat sivuston ylläpitäjältä"/>
    <?php if (!empty($secretWrong)): ?>
        <div class="error-message">
            Tunniste ei täsmännyt. Saat tunnisteen sivuston ylläpitäjältä.
        </div>
    <?php endif; ?>

</div>
<button type="submit">Rekisteröidy</button>
<?php echo $this->Form->end(); ?>
