<?php

echo password_hash('gilles', PASSWORD_BCRYPT, [
    'salt' => 'thisisachainof22characters'
]);