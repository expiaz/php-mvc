<h1><?=$realisateur->getName()?></h1>
<?php if(count($films)): ?>
    <h2>Films : </h2>
    <ul>
        <?php foreach ($realisateur->getFilmsModel() as $film): ?>
            <li><a href="<?=$film->getLink()?>"><?=$film->getTitle()?> (<?=$film->getDate()?>)</a></li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <h2>Pas de films.</h2>
<?php endif; ?>
<div><a href="<?=$realisateur->getEditLink()?>">edit</a></div>

