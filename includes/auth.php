<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user']) && is_array($_SESSION['user']);
}

function currentUser(): ?array
{
    return isLoggedIn() ? $_SESSION['user'] : null;
}

function hasRole(string $role): bool
{
    $user = currentUser();
    return $user !== null && ($user['role'] ?? '') === $role;
}

function canManageRecettes(): bool
{
    $user = currentUser();
    if ($user === null) {
        return false;
    }
    return in_array($user['role'] ?? '', ['admin', 'utilisateur'], true);
}

function canDeleteRecette(): bool
{
    return hasRole('admin');
}

function requireLogin(string $redirectTo = 'login.php'): void
{
    if (!isLoggedIn()) {
        header('Location: ' . $redirectTo);
        exit;
    }
}

function requireAdmin(string $redirectTo = 'index.php'): void
{
    requireLogin('login.php');
    if (!hasRole('admin')) {
        http_response_code(403);
        echo 'Accès refusé : droits administrateur requis.';
        exit;
    }
}

function attemptLogin(PDO $pdo, string $email, string $password): bool
{
    $stmt = $pdo->prepare('SELECT id, nom, email, mot_de_passe, role FROM utilisateurs WHERE email = :email LIMIT 1');
    $stmt->bindValue(':email', trim($email), PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch();

    if (!$user) {
        return false;
    }

    if (!password_verify($password, (string) $user['mot_de_passe'])) {
        return false;
    }

    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id' => (int) $user['id'],
        'nom' => $user['nom'],
        'email' => $user['email'],
        'role' => $user['role'],
    ];

    return true;
}

function logoutUser(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            (bool) $params['secure'],
            (bool) $params['httponly']
        );
    }
    session_destroy();
}
