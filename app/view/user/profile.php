<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>

<?php if($error): ?>
    <h1><?= $error; ?></h1>
<?php else: ?>
    <h1><?= $user->getPseudo(); ?> profile</h1>
    <p>Informations : </p>
    <ul>
        <li><?= $user->getInfos(); ?></li>
    </ul>
<?php endif; ?>

</body>
</html>