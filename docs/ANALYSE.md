# Analyse du projet

## 1. Objectif du site
Le site « Gestion de recettes de cuisine » permet aux visiteurs de consulter, rechercher, filtrer et paginer des recettes de cuisine ainsi que d'afficher leur fiche détaillée et un regroupement par catégorie. Les utilisateurs authentifiés selon leur rôle peuvent gérer les recettes (ajout, modification et suppression) via des pages protégées. L'application repose sur PHP et MySQL avec des requêtes préparées PDO.

## 2. Les données
- Catégories : identifiant, nom de catégorie, description.
- Auteurs / Cuisiniers : identifiant, nom, spécialité.
- Recettes : identifiant, titre, résumé, temps de préparation (en minutes), ingrédients et étapes, date de création, référence vers la catégorie et l'auteur.
- Utilisateurs : identifiant, nom, courriel, empreinte du mot de passe, rôle (`utilisateur` ou `admin`).

## 3. Les objets et leurs relations
- **Objet `categories` :** `id`, `nom`, `description`.
- **Objet `auteurs` :** `id`, `nom`, `specialite`.
- **Objet `recettes` :** `id`, `titre`, `resume`, `temps_preparation`, `contenu`, `date_creation`, `categorie_id`, `auteur_id`.
- **Objet `utilisateurs` :** `id`, `nom`, `email`, `mot_de_passe`, `role`.

**Relations et cardinalités (lues dans les deux sens) :**
- `categories` (1) — (N) `recettes` : une catégorie regroupe plusieurs recettes ; une recette appartient à une seule catégorie (`recettes.categorie_id` -> `categories.id`).
- `auteurs` (1) — (N) `recettes` : un auteur peut proposer plusieurs recettes ; une recette est associée à un seul auteur (`recettes.auteur_id` -> `auteurs.id`).

Schéma textuel :
`categories (1) ----< (N) recettes >---- (1) auteurs`

## 4. Les traitements
- **Consultation et recherche du catalogue :** entrée (mot-clé `q`, catégorie, critère de tri, numéro de page) ; étapes (validation/nettoyage des paramètres, calcul du total et de la pagination, requête préparée avec `LIKE` échappé et `LEFT JOIN`) ; sortie (liste paginée des recettes et liens conservant les critères).
- **Ajout / Modification d'une recette :** entrée (données du formulaire en `POST`, `id` optionnel) ; étapes (vérification connexion/rôle, validation côté serveur, requête préparée `INSERT` ou `UPDATE`, redirection `POST -> Redirect -> GET` avec message `msg`) ; sortie (recette enregistrée et message de confirmation).
- **Suppression d'une recette :** entrée (`id` de la recette) ; étapes (affichage d'une page de confirmation en `GET` avec le titre de la recette, puis exécution d'un `DELETE` préparé avec `WHERE` en `POST` et redirection) ; sortie (recette supprimée ou `404` si déjà supprimée).
- **Authentification :** entrée (courriel et mot de passe en `POST`) ; étapes (vérification via `password_verify()`, message générique unique en cas d'échec, `session_regenerate_id(true)` en cas de succès) ; sortie (session ouverte ou refus d'accès).

## 5. Les tâches
Formule : $E = (O + 4 \times P + Pe) \div 6$

| Tâche | Responsable | O | P | Pe | E | Temps réel |
| :--- | :--- | :---: | :---: | :---: | :---: | :---: |
| Analyse et schéma SQL (`001-schema.sql`, `002-donnees.sql`) | Binôme | 1h | 2h | 3h | 2h | - |
| Interface HTML5/CSS3 responsive et gabarits PHP | Membre 1 | 2h | 3h | 4h | 3h | - |
| Fonctions (`includes/fonctions.php`), vues et tests unitaires | Binôme | 2h | 3h | 5h | 3.2h | - |
| Catalogue (recherche, tri, pagination, détail, regroupement) | Membre 1 | 2h | 4h | 6h | 4h | - |
| CRUD complet (ajout, modification, suppression en 2 temps) | Membre 2 | 2h | 4h | 6h | 4h | - |
| Comptes, connexion, rôles, pages protégées et messages flash | Membre 2 | 2h | 3h | 5h | 3.2h | - |
| Documentation finale (`README.md`, `CHANGELOG.md`, `REPARTITION.md`) et `v1.0.0` | Binôme | 1h | 2h | 3h | 2h | - |

## 6. Bilan
À compléter à la fin du projet : comparaison entre les estimations prévues et le temps réel passé, ainsi que l'explication des écarts éventuels.
