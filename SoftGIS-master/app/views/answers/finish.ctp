<h3>
    <?php echo $poll['Poll']['name']; ?>
</h3>
<div class="thanksText">
    <?php echo $poll['Poll']['thanks_text']; ?>
</div>

<br/><br/><br/><br/>

<?php if ($test): ?>
    <h3>Huom. Testivastaus, vastauksia ei tallennettu</h3>
    <?php echo $this->Html->link(
        'Takaisin kyselynäkymään',
        array(
            'controller' => 'polls',
            'action' => 'view',
            $poll['Poll']['id']
        ),
        array('class' => 'button')
    ); ?>
<?php else: ?>
    <p><h3>Voit sulkea selainikkunan</h3></p>
<?php endif; ?>
