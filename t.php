<?php


require_once 'core/app/ObBuffer.php';

$buff = new \Core\App\ObBuffer();

echo "bf view \n";

ob_start();

$content = 'a';

require_once 'app/view/index.php';

$view = "view : " . ob_get_clean() . "\n";

echo "af view \n";

echo $buff->unbufferize();

echo $view;
