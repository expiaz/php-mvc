<h1><?=$projet->getTitre()?></h1>

<h3>Etudiants : </h3>
<ul>
<?php foreach ($projet->getEtudiantsModel() as $etudiant): ?>
    <li><a href="<?=$etudiant->getLink()?>"><?=$etudiant->getUserModel()->getNom()?> <?=$etudiant->getPrenom()?></a></li>
<?php endforeach; ?>
</ul>
