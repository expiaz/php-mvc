<?php


if (isset($_FILES['affiche'])) {
    $fichier = basename($_FILES['affiche']['name']);
    $taille_maxi = 100000;
    $taille = filesize($_FILES['affiche']['tmp_name']);
    $extensions = array('.png', '.gif', '.jpg', '.jpeg');
    $extension = strrchr($_FILES['affiche']['name'], '.');
//Début des vérifications de sécurité...
    if (in_array($extension, $extensions) && $taille <= $taille_maxi) //Si l'extension n'est pas dans le tableau
    {
        $fichier = strtr($fichier,
            'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ',
            'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
        $fichier = preg_replace('/([^.a-z0-9]+)/i', '-', $fichier);
        move_uploaded_file($_FILES['affiche']['tmp_name'], PUB . 'assets' . DS . 'img' . DS . $fichier);
    }
    $post->affiche = WEBASSET . 'img/' . $fichier;
}
