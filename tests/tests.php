<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: text/plain; charset=utf-8');

$passed = 0;
$failed = 0;

function assertTrue(bool $condition, string $label): void
{
    global $passed, $failed;
    if ($condition) {
        $passed++;
        echo "[PASS] $label\n";
    } else {
        $failed++;
        echo "[FAIL] $label\n";
    }
}

echo "=== Exécution des tests unitaires et d'intégration ===\n\n";


$escaped = e('<script>alert("xss")</script>');
assertTrue(!str_contains($escaped, '<script>'), 'Échappement des balises HTML dans e()');


$pdo = getPDO();
assertTrue($pdo instanceof PDO, 'Connexion PDO établie avec succès');

$categories = getCategories($pdo);
assertTrue(count($categories) >= 4, 'Récupération des catégories depuis la base');


$page1 = getRecettesPaginated($pdo, '', 0, 1, 4);
assertTrue(isset($page1['items'], $page1['total_pages'], $page1['current_page']), 'Structure de pagination valide');
assertTrue(count($page1['items']) <= 4, 'Respect de la limite par page (4 éléments)');

$searchRes = getRecettesPaginated($pdo, 'chocolat', 0, 1, 4);
assertTrue($searchRes['total_items'] >= 1, 'Recherche par mot-clé ("chocolat") fonctionnelle');


$grouped = getRecettesGroupedByCategory($pdo);
assertTrue(count($grouped) >= 4, 'Groupement des recettes par catégorie fonctionnel');


$_SESSION = [];
assertTrue(!isLoggedIn(), 'Utilisateur non connecté par défaut');
assertTrue(!canManageRecettes(), 'Visiteur non connecté ne peut pas gérer les recettes');
assertTrue(!canDeleteRecette(), 'Visiteur non connecté ne peut pas supprimer de recette');

$loginAdmin = attemptLogin($pdo, 'admin@recettes.local', 'Admin123!');
assertTrue($loginAdmin && isLoggedIn() && hasRole('admin'), 'Connexion administrateur valide et rôle admin vérifié');
assertTrue(canManageRecettes() && canDeleteRecette(), 'Administrateur a les droits de gestion et de suppression');

logoutUser();
assertTrue(!isLoggedIn(), 'Déconnexion effective de l\'utilisateur');

echo "\n=== Résultat : $passed réussi(s), $failed échoué(s) ===\n";
