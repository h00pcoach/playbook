<?php
/**
 * Minimal .env file loader.
 * Stores values in $_ENV so they work even when putenv/getenv are
 * restricted by the host (e.g. GoDaddy shared hosting).
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
        $_ENV[$name] = $value;
        // attempt putenv too, but don't rely on it
        @putenv("$name=$value");
    }
}

function env(string $key, string $default = ''): string {
    return $_ENV[$key] ?? getenv($key) ?: $default;
}
