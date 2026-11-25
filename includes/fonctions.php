<?php
declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function getCategories(PDO $pdo): array
{
    $stmt = $pdo->query('SELECT id, nom, description FROM categories ORDER BY nom ASC');
    return $stmt->fetchAll();
}

function getAuteurs(PDO $pdo): array
{
    $stmt = $pdo->query('SELECT id, nom, specialite FROM auteurs ORDER BY nom ASC');
    return $stmt->fetchAll();
}

function getRecettesPaginated(PDO $pdo, string $search = '', int $categorieId = 0, int $page = 1, int $perPage = 4): array
{
    $page = max(1, $page);
    $perPage = max(1, min(50, $perPage));

    $where = [];
    $params = [];

    if ($search !== '') {
        $pattern = '%' . $search . '%';
        $where[] = '(r.titre LIKE :search_titre OR r.resume LIKE :search_resume OR r.contenu LIKE :search_contenu)';
        $params[':search_titre'] = $pattern;
        $params[':search_resume'] = $pattern;
        $params[':search_contenu'] = $pattern;
    }

    if ($categorieId > 0) {
        $where[] = 'r.categorie_id = :categorie_id';
        $params[':categorie_id'] = $categorieId;
    }

    $whereSql = $where !== [] ? 'WHERE ' . implode(' AND ', $where) : '';

    $countSql = "SELECT COUNT(*) FROM recettes r $whereSql";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $totalItems = (int) $countStmt->fetchColumn();
    $totalPages = max(1, (int) ceil($totalItems / $perPage));
    $page = min($page, $totalPages);
    $offset = ($page - 1) * $perPage;

    $sql = "SELECT r.id, r.titre, r.resume, r.temps_preparation, r.date_creation,
                   c.nom AS categorie_nom, a.nom AS auteur_nom
            FROM recettes r
            LEFT JOIN categories c ON r.categorie_id = c.id
            LEFT JOIN auteurs a ON r.auteur_id = a.id
            $whereSql
            ORDER BY r.date_creation DESC, r.id DESC
            LIMIT :limit OFFSET :offset";

    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $val) {
        if ($key === ':categorie_id') {
            $stmt->bindValue($key, (int) $val, PDO::PARAM_INT);
        } else {
            $stmt->bindValue($key, (string) $val, PDO::PARAM_STR);
        }
    }
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    return [
        'items' => $stmt->fetchAll(),
        'total_items' => $totalItems,
        'total_pages' => $totalPages,
        'current_page' => $page,
        'per_page' => $perPage,
    ];
}


function getRecetteById(PDO $pdo, int $id): ?array
{
    if ($id <= 0) {
        return null;
    }

    $sql = "SELECT r.*, c.nom AS categorie_nom, a.nom AS auteur_nom, a.specialite AS auteur_specialite
            FROM recettes r
            LEFT JOIN categories c ON r.categorie_id = c.id
            LEFT JOIN auteurs a ON r.auteur_id = a.id
            WHERE r.id = :id
            LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $recette = $stmt->fetch();

    return $recette ?: null;
}

function getRecettesGroupedByCategory(PDO $pdo): array
{
    $sql = "SELECT c.id AS categorie_id, c.nom AS categorie_nom, c.description AS categorie_description,
                   r.id AS recette_id, r.titre, r.resume, r.temps_preparation
            FROM categories c
            LEFT JOIN recettes r ON r.categorie_id = c.id
            ORDER BY c.nom ASC, r.titre ASC";

    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll();

    $grouped = [];
    foreach ($rows as $row) {
        $catId = (int) $row['categorie_id'];
        if (!isset($grouped[$catId])) {
            $grouped[$catId] = [
                'id' => $catId,
                'nom' => $row['categorie_nom'],
                'description' => $row['categorie_description'],
                'recettes' => [],
            ];
        }
        if ($row['recette_id'] !== null) {
            $grouped[$catId]['recettes'][] = [
                'id' => (int) $row['recette_id'],
                'titre' => $row['titre'],
                'resume' => $row['resume'],
                'temps_preparation' => (int) $row['temps_preparation'],
            ];
        }
    }

    return array_values($grouped);
}
