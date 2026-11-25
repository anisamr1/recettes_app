<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/fonctions.php';
require_once __DIR__ . '/includes/auth.php';

$pdo = getPDO();
$id = (int) ($_GET['id'] ?? 0);
$recette = getRecetteById($pdo, $id);

if ($recette === null) {
    http_response_code(404);
    $pageTitle = 'Recette introuvable';
    require __DIR__ . '/vues/header.php';
    echo '<div class="alert-error">Recette introuvable.</div>';
    echo '<a href="index.php" class="btn btn-outline">Retour à l\'accueil</a>';
    require __DIR__ . '/vues/footer.php';
    exit;
}

$pageTitle = (string) $recette['titre'];
require __DIR__ . '/vues/header.php';
?>

<article class="recette-detail">
    <div class="meta-row" style="margin-bottom: 0.5rem;">
        <?php if (!empty($recette['categorie_nom'])): ?>
            <span class="tag"><?= e($recette['categorie_nom']) ?></span>
        <?php endif; ?>
        <span>Préparation : <?= (int) $recette['temps_preparation'] ?> min</span>
        <span>Auteur : <?= e($recette['auteur_nom'] ?? 'Auteur inconnu') ?></span>
        <span>Publiée le : <?= e((string) $recette['date_creation']) ?></span>
    </div>

    <h1><?= e($recette['titre']) ?></h1>
    <p class="muted"><?= e($recette['resume']) ?></p>

    <div class="recette-contenu"><?= e($recette['contenu']) ?></div>

    <div class="card-actions" style="margin-top: 1.75rem;">
        <a href="index.php" class="btn btn-outline">Retour aux recettes</a>
        <?php if (canManageRecettes()): ?>
            <a href="modifier.php?id=<?= (int) $recette['id'] ?>" class="btn">Modifier</a>
        <?php endif; ?>
        <?php if (canDeleteRecette()): ?>
            <a href="supprimer.php?id=<?= (int) $recette['id'] ?>" class="btn btn-danger">Supprimer</a>
        <?php endif; ?>
    </div>
</article>

<?php require __DIR__ . '/vues/footer.php'; ?>
