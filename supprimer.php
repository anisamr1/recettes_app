<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/fonctions.php';
require_once __DIR__ . '/includes/auth.php';

requireAdmin('index.php');

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $confirmId = (int) ($_POST['id'] ?? 0);
    if ($confirmId === $id) {
        $stmt = $pdo->prepare('DELETE FROM recettes WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        header('Location: index.php?deleted=1');
        exit;
    }
}

$pageTitle = 'Supprimer la recette';
require __DIR__ . '/vues/header.php';
?>

<section class="page-header">
    <h1>Confirmer la suppression</h1>
    <p class="muted">Cette action est irréversible.</p>
</section>

<div class="form-box">
    <div class="alert-error">
        Voulez-vous vraiment supprimer la recette <strong><?= e((string) $recette['titre']) ?></strong> ?
    </div>

    <form method="post" action="supprimer.php?id=<?= $id ?>" class="card-actions">
        <input type="hidden" name="id" value="<?= $id ?>">
        <button type="submit" class="btn btn-danger">Oui, supprimer</button>
        <a href="recette.php?id=<?= $id ?>" class="btn btn-outline">Annuler</a>
    </form>
</div>

<?php require __DIR__ . '/vues/footer.php'; ?>
