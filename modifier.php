<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/fonctions.php';
require_once __DIR__ . '/includes/auth.php';

requireLogin('login.php');
if (!canManageRecettes()) {
    http_response_code(403);
    echo 'Accès refusé.';
    exit;
}

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

$categories = getCategories($pdo);
$auteurs = getAuteurs($pdo);

$errors = [];
$titre = (string) $recette['titre'];
$resume = (string) $recette['resume'];
$tempsPreparation = (int) $recette['temps_preparation'];
$contenu = (string) $recette['contenu'];
$categorieId = (int) $recette['categorie_id'];
$auteurId = (int) $recette['auteur_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim((string) ($_POST['titre'] ?? ''));
    $resume = trim((string) ($_POST['resume'] ?? ''));
    $tempsPreparation = (int) ($_POST['temps_preparation'] ?? 0);
    $contenu = trim((string) ($_POST['contenu'] ?? ''));
    $categorieId = (int) ($_POST['categorie_id'] ?? 0);
    $auteurId = (int) ($_POST['auteur_id'] ?? 0);

    if ($titre === '') {
        $errors[] = 'Le titre est obligatoire.';
    }
    if ($resume === '') {
        $errors[] = 'Le résumé est obligatoire.';
    }
    if ($tempsPreparation <= 0) {
        $errors[] = 'Le temps de préparation doit être supérieur à 0 minute.';
    }
    if ($contenu === '') {
        $errors[] = 'Le contenu de la recette est obligatoire.';
    }
    if ($categorieId <= 0) {
        $errors[] = 'Veuillez sélectionner une catégorie.';
    }
    if ($auteurId <= 0) {
        $errors[] = 'Veuillez sélectionner un auteur.';
    }

    if ($errors === []) {
        $sql = 'UPDATE recettes
                SET titre = :titre,
                    resume = :resume,
                    temps_preparation = :temps_preparation,
                    contenu = :contenu,
                    categorie_id = :categorie_id,
                    auteur_id = :auteur_id
                WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':titre', $titre, PDO::PARAM_STR);
        $stmt->bindValue(':resume', $resume, PDO::PARAM_STR);
        $stmt->bindValue(':temps_preparation', $tempsPreparation, PDO::PARAM_INT);
        $stmt->bindValue(':contenu', $contenu, PDO::PARAM_STR);
        $stmt->bindValue(':categorie_id', $categorieId, PDO::PARAM_INT);
        $stmt->bindValue(':auteur_id', $auteurId, PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        header('Location: recette.php?id=' . $id);
        exit;
    }
}

$pageTitle = 'Modifier la recette';
require __DIR__ . '/vues/header.php';
?>

<section class="page-header">
    <h1>Modifier la recette</h1>
    <p class="muted">Mise à jour de : <?= e($titre) ?></p>
</section>

<div class="form-box">
    <?php if ($errors !== []): ?>
        <div class="alert-error">
            <ul style="margin: 0; padding-left: 1.2rem;">
                <?php foreach ($errors as $err): ?>
                    <li><?= e($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="modifier.php?id=<?= $id ?>">
        <div class="form-group">
            <label for="titre">Titre</label>
            <input type="text" id="titre" name="titre" value="<?= e($titre) ?>" required>
        </div>

        <div class="form-group">
            <label for="resume">Résumé</label>
            <input type="text" id="resume" name="resume" value="<?= e($resume) ?>" required>
        </div>

        <div class="form-group">
            <label for="temps_preparation">Temps de préparation (en minutes)</label>
            <input type="number" id="temps_preparation" name="temps_preparation" min="1" value="<?= (int) $tempsPreparation ?>" required>
        </div>

        <div class="form-group">
            <label for="categorie_id">Catégorie</label>
            <select id="categorie_id" name="categorie_id" required>
                <option value="">-- Choisir une catégorie --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= (int) $cat['id'] ?>" <?= $categorieId === (int) $cat['id'] ? 'selected' : '' ?>>
                        <?= e($cat['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="auteur_id">Auteur</label>
            <select id="auteur_id" name="auteur_id" required>
                <option value="">-- Choisir un auteur --</option>
                <?php foreach ($auteurs as $auteur): ?>
                    <option value="<?= (int) $auteur['id'] ?>" <?= $auteurId === (int) $auteur['id'] ? 'selected' : '' ?>>
                        <?= e($auteur['nom']) ?> (<?= e($auteur['specialite']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="contenu">Ingrédients et préparation</label>
            <textarea id="contenu" name="contenu" required><?= e($contenu) ?></textarea>
        </div>

        <div class="card-actions">
            <button type="submit" class="btn">Enregistrer les modifications</button>
            <a href="recette.php?id=<?= $id ?>" class="btn btn-outline">Annuler</a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/vues/footer.php'; ?>
