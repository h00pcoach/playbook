<?php
require_once __DIR__ . '/env.php';
load_env(__DIR__ . '/.env');

define('DB_DSN',      env('DB_DSN'));
define('DB_USERNAME', env('DB_USERNAME'));
define('DB_PASSWORD', env('DB_PASSWORD'));
