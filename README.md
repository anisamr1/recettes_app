# Gestion des Recettes de Cuisine

Application web PHP/MySQL permettant de consulter, rechercher et gérer des recettes de cuisine avec gestion des rôles (`admin` et `lecteur`).

## Prérequis
- XAMPP (Apache + MySQL + PHP 8+)
- Navigateur web moderne

## Installation
1. Placer le dossier `recettes_app` dans `C:\xampp\htdocs\`.
2. Démarrer **Apache** et **MySQL** via le panneau de contrôle XAMPP.
3. Ouvrir `http://localhost/phpmyadmin`, créer la base de données `recettes_db` et importer `sql/schema-001.sql` puis `sql/donnees-002.sql`.
4. Copier `includes/config.example.php` vers `includes/config.local.php` et ajuster les identifiants MySQL si nécessaire.
5. Accéder à `http://localhost/recettes_app`.

## Comptes de test
- **Administrateur :** `admin@recettes.local` / `Admin123!`
- **Lecteur :** `lecteur@recettes.local` / `Lecteur123!`
