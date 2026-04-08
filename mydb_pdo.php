<?php
require_once __DIR__ . '/env.php';
load_env(__DIR__ . '/.env');

define('DB_DSN',      getenv('DB_DSN'));
define('DB_USERNAME', getenv('DB_USERNAME'));
define('DB_PASSWORD', getenv('DB_PASSWORD'));
