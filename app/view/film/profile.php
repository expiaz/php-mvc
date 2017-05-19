<h1><?=$film->getTitle()?></h1>
<?php if(! empty($film->getAffiche())): ?>
    <img src="<?=WEBASSET . $film->getAffiche()?>" width="100%"/>
<?php endif; ?>

<ul>
    <li>Date : <?=$film->getDate()?></li>
    <li>Avis du public : <?=$film->getRate()?> / 10</li>
    <li>Réalisateur : <a href="<?=$realisateur->getLink()?>"><?=$realisateur->getName()?></a></li>
    <li>
        Acteurs :
        <ul>
            <?php foreach($acteurs as $acteur): ?>
                <li><a href="<?=$acteur->getLink()?>"><?=$acteur->getName()?></a></li>
            <?php endforeach; ?>
        </ul>
    </li>
</ul>
<div><a href="<?=$film->getEditLink()?>">edit</a></div>