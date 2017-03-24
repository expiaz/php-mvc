<?php
use Core\Form\Form;
use Core\Form\Field;

$form = Form::buildFromScratch([
    new Field('text','login',$login ?? null,'login',true,'login'),
    new Field('password','pwd',null,'password',true,'pwd')
], [
    'method' => 'POST'
]);
?>

<h1>Connexion</h1>
<? if($error): ?>
<p><?=$error?></p>
<? endif; ?>
<?=$form?>