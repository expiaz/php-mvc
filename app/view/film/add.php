<h1>Add a film</h1>
<a href="<?=\Core\Facade\Contracts\UrlFacade::create('/realisateur/add')?>">Ajouter un réalisateur</a><br/>
<a href="<?=\Core\Facade\Contracts\UrlFacade::create('/acteur/add')?>">Ajouter un acteur</a>
<br/>
<br/>
<?=$addForm?>

<?=$error??''?>