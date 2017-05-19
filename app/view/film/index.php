<h1>Films</h1>
<ul>
<?php foreach ($films as $film): ?>
    <li><a href="<?=$film->getLink()?>"><?=$film->getTitle()?> (<?=$film->getDate()?>)</a></li>
<?php endforeach; ?>
</ul>
<a href="<?=$addLink?>">add a film</a>