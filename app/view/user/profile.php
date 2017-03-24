<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>

<?php if($error): ?>
    <h1><?= $error; ?></h1>
<?php else: ?>
    <h1><?= $user->pseudo; ?> profile</h1>
    <p>Informations : (<a href="<?= $updatePath ?>">edit</a>)</p>
    <?= $user->getInfos(); ?>
<?php endif; ?>

</body>
</html>