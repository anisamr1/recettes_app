<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/fonctions.php';
require_once __DIR__ . '/includes/auth.php';

$pdo = getPDO();
$categories = getCategories($pdo);

$search = trim((string) ($_GET['q'] ?? ''));
$categorieId = (int) ($_GET['categorie'] ?? 0);
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 4;

$paginated = getRecettesPaginated($pdo, $search, $categorieId, $page, $perPage);
$recettes = $paginated['items'];
$totalPages = $paginated['total_pages'];
$currentPage = $paginated['current_page'];

$groupedByCategory = getRecettesGroupedByCategory($pdo);

$pageTitle = 'Accueil';
require __DIR__ . '/vues/header.php';
?>

<section class="page-header">
    <h1>Découvrir nos recettes</h1>
    <p class="muted">Recherchez, filtrez par catégorie et parcourez les recettes disponibles.</p>
</section>

<?php if (!empty($_GET['deleted'])): ?>
    <div class="alert-success">La recette a bien été supprimée.</div>
<?php endif; ?>

<form method="get" action="index.php" class="filter-bar">
    <div>
        <label for="q" class="muted">Recherche par mot-clé</label>
        <input type="text" id="q" name="q" value="<?= e($search) ?>" placeholder="Ex. : chocolat, soupe, citron...">
    </div>
    <div>
        <label for="categorie" class="muted">Catégorie</label>
        <select id="categorie" name="categorie">
            <option value="0">Toutes les catégories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= (int) $cat['id'] ?>" <?= $categorieId === (int) $cat['id'] ? 'selected' : '' ?>>
                    <?= e($cat['nom']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div style="align-self: end; display: flex; gap: 0.4rem;">
        <button type="submit" class="btn">Filtrer</button>
        <?php if ($search !== '' || $categorieId > 0): ?>
            <a href="index.php" class="btn btn-outline">Réinitialiser</a>
        <?php endif; ?>
    </div>
</form>

<section>
    <p class="muted">
        <?= (int) $paginated['total_items'] ?> recette(s) trouvée(s) — Page <?= (int) $currentPage ?> sur <?= (int) $totalPages ?>.
    </p>

    <?php if ($recettes === []): ?>
        <p class="muted">Aucune recette ne correspond à vos critères.</p>
    <?php else: ?>
        <div class="cards-grid">
            <?php foreach ($recettes as $recette): ?>
                <article class="card">
                    <div>
                        <div class="meta-row">
                            <?php if (!empty($recette['categorie_nom'])): ?>
                                <span class="tag"><?= e($recette['categorie_nom']) ?></span>
                            <?php endif; ?>
                            <span><?= (int) $recette['temps_preparation'] ?> min</span>
                        </div>
                        <h2><a href="recette.php?id=<?= (int) $recette['id'] ?>" style="color: inherit; text-decoration: none;"><?= e($recette['titre']) ?></a></h2>
                        <p class="muted"><?= e($recette['resume']) ?></p>
                    </div>
                    <div>
                        <div class="meta-row" style="margin-bottom: 0.5rem;">
                            <span>Par <?= e($recette['auteur_nom'] ?? 'Auteur inconnu') ?></span>
                        </div>
                        <div class="card-actions">
                            <a href="recette.php?id=<?= (int) $recette['id'] ?>" class="btn btn-sm btn-outline">Voir la recette</a>
                            <?php if (canManageRecettes()): ?>
                                <a href="modifier.php?id=<?= (int) $recette['id'] ?>" class="btn btn-sm btn-outline">Modifier</a>
                            <?php endif; ?>
                            <?php if (canDeleteRecette()): ?>
                                <a href="supprimer.php?id=<?= (int) $recette['id'] ?>" class="btn btn-sm btn-danger">Supprimer</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($totalPages > 1): ?>
        <nav class="pagination" aria-label="Pagination des recettes">
            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                <?php
                $queryParams = ['page' => $p];
                if ($search !== '') {
                    $queryParams['q'] = $search;
                }
                if ($categorieId > 0) {
                    $queryParams['categorie'] = $categorieId;
                }
                $url = 'index.php?' . http_build_query($queryParams);
                ?>
                <?php if ($p === $currentPage): ?>
                    <span class="active"><?= $p ?></span>
                <?php else: ?>
                    <a href="<?= e($url) ?>"><?= $p ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        </nav>
    <?php endif; ?>
</section>

<section class="category-section">
    <h2>Recettes groupées par catégorie</h2>
    <?php foreach ($groupedByCategory as $group): ?>
        <div style="margin-top: 1.5rem;">
            <h3><?= e($group['nom']) ?></h3>
            <?php if (!empty($group['description'])): ?>
                <p class="muted"><?= e($group['description']) ?></p>
            <?php endif; ?>

            <?php if ($group['recettes'] === []): ?>
                <p class="muted">Aucune recette dans cette catégorie pour le moment.</p>
            <?php else: ?>
                <div class="cards-grid">
                    <?php foreach ($group['recettes'] as $item): ?>
                        <article class="card">
                            <div>
                                <span class="meta-row"><?= (int) $item['temps_preparation'] ?> min</span>
                                <h4><?= e($item['titre']) ?></h4>
                                <p class="muted"><?= e($item['resume']) ?></p>
                            </div>
                            <div class="card-actions">
                                <a href="recette.php?id=<?= (int) $item['id'] ?>" class="btn btn-sm btn-outline">Consulter</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</section>

<?php require __DIR__ . '/vues/footer.php'; ?>
