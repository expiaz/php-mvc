<h1>Acteurs</h1>
<ul>
    <?php foreach ($acteurs as $acteur): ?>
        <li><a href="<?=$acteur->getLink()?>"><?=$acteur->getName()?></a></li>
    <?php endforeach; ?>
</ul>
<a href="<?=$addLink?>">ajouter un acteur</a>