<h1>all users</h1>
<p>users : </p>
<ul>
<?php foreach($users as $u): ?>
    <li><a href="<?=$u->getProfileLink();?>"><?=$u->pseudo?></a></li>
<?php endforeach; ?>
</ul>
