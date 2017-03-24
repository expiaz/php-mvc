<?php
use Core\Form\Form;

$form = Form::buildFromEntity($user ,[
    'method' => 'POST'
]);

?>

<h1>Edit <?=$user->pseudo?></h1>
<?=$form?>