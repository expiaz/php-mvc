<h1>Films</h1>
<ul>
<?php foreach ($films as $film): ?>
    <li><a href="<?=$film->getLink()?>"><?=$film->getTitle()?></a></li>
<?php endforeach; ?>
</ul>
