<?php
use Core\Http\Query;

$updatePath = Query::build(Query::getController(), 'update', $user->id);

?>
<?php if($error): ?>
    <h1><?= $error; ?></h1>
<?php else: ?>
    <h1><?= $user->pseudo; ?> profile</h1>
    <p>Informations : (<a href="<?= $updatePath ?>">edit</a>)</p>
    <?= $user->getInfos(); ?>
<?php endif; ?>