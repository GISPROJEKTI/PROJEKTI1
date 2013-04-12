<h3>
    <?php echo $poll['Poll']['name']; ?>
</h3>
<div class="thanksText">
    <?php echo $poll['Poll']['thanks_text']; ?>
    <p>Voit sulkea selainikkunan</p>
</div>

<?php if ($test): ?>
    <br/><br/><br/><br/>
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
<?php endif; ?>
