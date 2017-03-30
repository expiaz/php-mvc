<?php
use Core\Form\FormBuilder;
use Core\Form\Form;
use Core\Form\Field;

$form = FormBuilder::buildFromEntity($film)->field((new Field())->type('submit')->name('submit')->value('modifier'))->build();
?>
<?=$form?>
