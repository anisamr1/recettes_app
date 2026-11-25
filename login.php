<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/fonctions.php';
require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $error = 'Veuillez saisir votre adresse e-mail et votre mot de passe.';
    } else {
        $pdo = getPDO();
        if (attemptLogin($pdo, $email, $password)) {
            header('Location: index.php');
            exit;
        }
        $error = 'Identifiants invalides.';
    }
}

$pageTitle = 'Connexion';
require __DIR__ . '/vues/header.php';
?>

<section class="page-header">
    <h1>Connexion</h1>
    <p class="muted">Connectez-vous pour gérer les recettes.</p>
</section>

<div class="form-box">
    <?php if ($error !== ''): ?>
        <div class="alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" action="login.php">
        <div class="form-group">
            <label for="email">Adresse e-mail</label>
            <input type="email" id="email" name="email" value="<?= e($email) ?>" required>
        </div>

        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit" class="btn">Se connecter</button>
    </form>

    <p class="muted" style="margin-top: 1rem; font-size: 0.88rem;">
        Comptes de test : <code>admin@recettes.local</code> / <code>Admin123!</code> ou <code>lecteur@recettes.local</code> / <code>Lecteur123!</code>
    </p>
</div>

<?php require __DIR__ . '/vues/footer.php'; ?>
