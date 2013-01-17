<?php

$type = $question['type'];
$lowText = $question['low_text'];
$highText = $question['high_text'];

?>

<div class="answer-field" id="answerField">

<?php if ($type == 1): ?>
    <textarea name="data[Answer][answer]"></textarea>
<?php elseif ($type == 2): ?>
    <input type="radio" name="data[Answer][answer]" value="Kyllä"/>Kyllä
    <input type="radio" name="data[Answer][answer]" value="Ei"/>Ei
    <input type="radio" name="data[Answer][answer]" value="En osaa sanoa"/>En osaa sanoa
<?php elseif ($type == 3): ?>
    <?php echo $lowText; ?>
    <input type="radio" name="data[Answer][answer]" value="1"/>
    <input type="radio" name="data[Answer][answer]" value="2"/>
    <input type="radio" name="data[Answer][answer]" value="3"/>
    <input type="radio" name="data[Answer][answer]" value="4"/>
    <input type="radio" name="data[Answer][answer]" value="5"/>
    <?php echo $highText; ?>
    <input type="radio" name="data[Answer][answer]" value="En osaa sanoa"/>En osaa sanoa
<?php elseif ($type == 4): ?>
    <?php echo $lowText; ?>
    <input type="radio" name="data[Answer][answer]" value="1"/>
    <input type="radio" name="data[Answer][answer]" value="2"/>
    <input type="radio" name="data[Answer][answer]" value="3"/>
    <input type="radio" name="data[Answer][answer]" value="4"/>
    <input type="radio" name="data[Answer][answer]" value="5"/>
    <input type="radio" name="data[Answer][answer]" value="6"/>
    <input type="radio" name="data[Answer][answer]" value="7"/>
    <?php echo $highText; ?>
    <input type="radio" name="data[Answer][answer]" value="En osaa sanoa"/>En osaa sanoa
<?php endif; ?>
</div>
    