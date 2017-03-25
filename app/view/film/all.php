<h1>Films</h1>
<ul>
<?php foreach ($films as $film): ?>
    <li><?=$film->getInfos()?></li>
<?php endforeach; ?>
</ul>
