<h1>Films</h1>
<ul>
<?php foreach ($films as $film): ?>
    <li><?=$film->getTitle()?></li>
<?php endforeach; ?>
</ul>