<?php

return [

    'host' => $_ENV['DB_HOST'] ?? 'localhost',

    'port' => $_ENV['DB_PORT'] ?? '5432',

    'database' => $_ENV['DB_DATABASE'] ?? 'medcare_db',

    'username' => $_ENV['DB_USERNAME'] ?? 'medcare_user',

    'password' => $_ENV['DB_PASSWORD'] ?? ''

];

?>