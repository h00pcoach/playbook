<?php
/**
 * Minimal .env file loader.
 * Call load_env() once at bootstrap (already called in mydb_pdo.php).
 */
function load_env(string $path): void {
    if (!file_exists($path)) {
        return;
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') {
            continue;
        }
        [$name, $value] = array_pad(explode('=', $line, 2), 2, '');
        $name  = trim($name);
        $value = trim($value);
        if ($name === '') {
            continue;
        }
        putenv("$name=$value");
        $_ENV[$name]    = $value;
        $_SERVER[$name] = $value;
    }
}
