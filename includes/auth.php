<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('is_logged_in')) {
    function is_logged_in(): bool {
        return isset($_SESSION['user_id']);
    }
}

if (!function_exists('logout_user')) {
    function logout_user(): void {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $p['path'],
                $p['domain'],
                $p['secure'],
                $p['httponly']
            );
        }
        session_destroy();
    }
}

if (!function_exists('require_login')) {
    function require_login(): void {
        if (!is_logged_in()) {
            header('Location: index.php?menu=7');
            exit;
        }
    }
}



if (!function_exists('can_access_cms')) {
    function can_access_cms(): bool {
        return is_logged_in() && (int)($_SESSION['user_cms'] ?? 0) === 1;
    }
}

if (!function_exists('require_cms')) {
    function require_cms(): void {
        require_login();
        if (!can_access_cms()) {
            header('Location: index.php?menu=1');
            exit;
        }
    }
}



if (!function_exists('role')) {
    function role(): string {
        return (string)($_SESSION['user_rola'] ?? 'user');
    }
}

if (!function_exists('require_role')) {
    function require_role(array $allowed): void {
        require_cms();
        if (!in_array(role(), $allowed, true)) {
            header('Location: index.php?menu=11');
            exit;
        }
    }
}
