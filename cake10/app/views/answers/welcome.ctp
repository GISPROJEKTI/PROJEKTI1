<h3>
    <?php echo $poll['Poll']['name']; ?>
</h3>
<div class="welcomeText">
    <?php echo $poll['Poll']['welcome_text']; ?>
</div>
<div class="answerNav">
    <a href="<?php echo $this->Html->url(array('action'=>'answer')); ?>"
        class="button">
        Aloita kysely
    </a>
</a>