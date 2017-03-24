<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <h1>all users</h1>
    <p>users : </p>
    <ul>
    <?php foreach($users as $u): ?>
        <li><?=$u->getPseudo()?></li>
    <?php endforeach; ?>
    </ul>
</body>
</html>