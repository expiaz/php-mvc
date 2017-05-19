<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="<?=WEBASSET . 'css/style.css'?>"/>
    <title><?=$title?></title>
</head>
<body>
<nav>
    <ul>
        <li><a href="<?=$home?>">Accueil</a></li>
        <li><a href="<?=\Core\Facade\Contracts\UrlFacade::create('/film')?>">Films</a></li>
        <li><a href="<?=\Core\Facade\Contracts\UrlFacade::create('/realisateur')?>">Réalisateurs</a></li>
        <li><a href="<?=\Core\Facade\Contracts\UrlFacade::create('/acteur')?>">Acteurs</a></li>
        <li><a href="<?=$connected['link']?>"><?=$connected['message']?></a></li>
    </ul>
</nav>
<div class="container">

