<?php
/**
 * CSRF helpers.
 * Requires an active session before any function is called.
 */

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="'
        . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Verify CSRF token from either:
 *  - POST field  'csrf_token'
 *  - Request header  X-CSRF-Token  (for jQuery AJAX calls)
 *
 * Dies with 403 on failure.
 */
function verify_csrf(): void {
    $session_token = $_SESSION['csrf_token'] ?? '';
    $request_token = $_POST['csrf_token']
        ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');

    if ($session_token === '' || !hash_equals($session_token, $request_token)) {
        http_response_code(403);
        die('CSRF validation failed.');
    }
}
