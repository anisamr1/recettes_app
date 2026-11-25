<?php
declare(strict_types=1);

$pageTitle = $pageTitle ?? 'Gestion de recettes de cuisine';
$user = currentUser();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> - Recettes</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a href="index.php" class="brand">Recettes &amp; Saveurs</a>
        <nav class="main-nav" aria-label="Navigation principale">
            <a href="index.php">Accueil</a>
            <?php if (canManageRecettes()): ?>
                <a href="ajouter.php">Ajouter une recette</a>
            <?php endif; ?>
            <?php if ($user !== null): ?>
                <span class="user-badge">
                    <?= e($user['nom']) ?> (<?= e($user['role']) ?>)
                </span>
                <a href="logout.php" class="btn btn-outline">Déconnexion</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-primary">Connexion</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main class="container page-content">
