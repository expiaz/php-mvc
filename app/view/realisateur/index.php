<h1>Realisateurs</h1>
<ul>
    <?php foreach ($realisateurs as $realisateur): ?>
        <li><a href="<?=$realisateur->getLink()?>"><?=$realisateur->getName()?></a></li>
    <?php endforeach; ?>
</ul>
<a href="<?=$addLink?>">ajouter un realisateur</a>