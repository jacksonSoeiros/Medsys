<?php

return [

    'host' => $_ENV['DB_HOST'] ?? 'localhost',

    'port' => $_ENV['DB_PORT'] ?? '5432',

    'database' => $_ENV['DB_DATABASE'] ?? 'med_sys_db',

    'username' => $_ENV['DB_USERNAME'] ?? 'med_sys_user',

    'password' => $_ENV['DB_PASSWORD'] ?? ''

];

?>
