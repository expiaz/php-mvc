<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="<?=WEBASSET . 'css/bootstrap.min.css'?>"/>
    <link rel="stylesheet" href="<?=WEBASSET . 'css/style.css'?>"/>
    <title><?=$title?></title>
</head>
<body>
<div class="container">
<nav>
    <ul>
        <li><a href="<?=$home?>">Accueil</a></li>
        <li><a href="<?=\Core\Facade\Contracts\UrlFacade::create('/profile')?>">Profil</a></li>
        <li><a href="<?=\Core\Facade\Contracts\UrlFacade::create('/projets')?>">Projets</a></li>
        <li><a href="<?=$connected['link']?>"><?=$connected['message']?></a></li>
    </ul>
</nav>


