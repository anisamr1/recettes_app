# Répartition du Travail

Projet réalisé en **monôme** par **Anis**.

## Modules développés
1. **Base de données & Sécurité (`sql/`, `includes/`) :** Conception relationnelle (4 tables), requêtes préparées PDO, hachage des mots de passe et protection XSS.
2. **Catalogue & Recherche (`index.php`, `recette.php`) :** Affichage des recettes, filtrage par catégorie et recherche par mot-clé.
3. **Authentification & Rôles (`login.php`, `logout.php`) :** Gestion des sessions et contrôle d'accès (`admin` vs `lecteur`).
4. **Gestion CRUD (`ajouter.php`, `modifier.php`, `supprimer.php`) :** Ajout, édition et suppression sécurisée des recettes.
