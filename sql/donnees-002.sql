USE recettes_db;

INSERT INTO categories (nom, description) VALUES
('Entrées', 'Entrées froides et chaudes pour débuter le repas.'),
('Plats principaux', 'Plats complets, mijotés, viandes, poissons et plats végétariens.'),
('Desserts', 'Pâtisseries, entremets, gâteaux et douceurs sucrées.'),
('Boissons', 'Jus frais, infusions, boissons chaudes et rafraîchissements.')
ON DUPLICATE KEY UPDATE description = VALUES(description);

INSERT INTO auteurs (nom, specialite) VALUES
('kamar', 'Cuisine méditerranéenne'),
('Anis', 'Pâtisserie et desserts'),
('wassim', 'Cuisine familiale et rapide');

INSERT INTO recettes (titre, resume, temps_preparation, contenu, categorie_id, auteur_id) VALUES
('Salade méditerranéenne aux pois chiches', 'Salade fraîche et nutritive au citron, huile d''olive et herbes.', 15,
 'Ingrédients :\n- 250 g de pois chiches cuits\n- 2 tomates\n- 1/2 concombre\n- 3 c. à soupe d''huile d''olive\n- Jus d''un citron, sel, poivre, persil\n\nPréparation :\n1. Couper les tomates et le concombre en dés.\n2. Mélanger avec les pois chiches et le persil ciselé.\n3. Assaisonner avec l''huile d''olive, le citron, le sel et le poivre.',
 1, 1),
('Soupe veloutée de courge et carottes', 'Velouté doux et onctueux idéal pour les soirées fraîches.', 35,
 'Ingrédients :\n- 500 g de courge\n- 3 carottes\n- 1 oignon\n- 1 L de bouillon de légumes\n- 1 c. à soupe d''huile d''olive\n\nPréparation :\n1. Faire revenir l''oignon émincé dans l''huile.\n2. Ajouter la courge, les carottes en morceaux et le bouillon.\n3. Cuire 25 minutes puis mixer jusqu''à obtenir une texture lisse.',
 1, 3),
('Couscous aux légumes et pois chiches', 'Plat traditionnel parfumé aux épices douces et légumes fondants.', 60,
 'Ingrédients :\n- 400 g de semoule moyenne\n- 2 carottes, 2 courgettes, 1 navet\n- 150 g de pois chiches\n- Cumin, cannelle, sel, poivre, huile d''olive\n\nPréparation :\n1. Préparer le bouillon épicé avec les légumes et les pois chiches.\n2. Hydrater et cuire la semoule à la vapeur ou selon l''emballage.\n3. Servir la semoule arrosée de bouillon et garnie de légumes.',
 2, 1),
('Poulet rôti au citron et romarin', 'Volaille dorée et parfumée accompagnée de pommes de terre.', 50,
 'Ingrédients :\n- 4 cuisses de poulet\n- 500 g de pommes de terre\n- 1 citron, quelques brins de romarin\n- 2 c. à soupe d''huile d''olive, ail, sel, poivre\n\nPréparation :\n1. Préchauffer le four à 200 °C.\n2. Disposer le poulet, les pommes de terre, l''ail, le citron et le romarin dans un plat.\n3. Arroser d''huile d''olive, assaisonner et enfourner 40 à 45 minutes.',
 2, 3),
('Gâteau fondant au chocolat noir', 'Dessert intense au chocolat avec un cœur moelleux.', 30,
 'Ingrédients :\n- 200 g de chocolat noir\n- 120 g de beurre\n- 100 g de sucre\n- 3 œufs\n- 50 g de farine\n\nPréparation :\n1. Faire fondre le chocolat et le beurre ensemble.\n2. Incorporer le sucre, les œufs un à un, puis la farine.\n3. Verser dans un moule beurré et cuire 18 minutes à 180 °C.',
 3, 2),
('Tarte aux pommes caramélisées', 'Pâte dorée garnie de lamelles de pommes fondantes.', 45,
 'Ingrédients :\n- 1 pâte brisée\n- 4 pommes\n- 40 g de beurre, 40 g de sucre\n- 1 pincée de cannelle\n\nPréparation :\n1. Faire revenir les pommes en lamelles avec le beurre, le sucre et la cannelle.\n2. Garnir la pâte disposée dans un moule.\n3. Cuire 30 minutes à 180 °C.',
 3, 2),
('Citronnade maison à la menthe', 'Boisson fraîche désaltérante au citron pressé et feuilles de menthe.', 10,
 'Ingrédients :\n- 3 citrons jaunes\n- 1 L d''eau fraîche\n- 3 c. à soupe de sucre ou miel\n- Quelques feuilles de menthe fraîche\n\nPréparation :\n1. Presser les citrons et mélanger le jus avec le sucre et un peu d''eau.\n2. Compléter avec le reste de l''eau fraîche et la menthe.\n3. Servir bien frais.',
 4, 1);

-- Mot de passe administrateur : Admin123!
-- Mot de passe utilisateur standard : Lecteur123!
INSERT INTO utilisateurs (nom, email, mot_de_passe, role) VALUES
('kamar', 'admin@recettes.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Anis', 'lecteur@recettes.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'utilisateur')
ON DUPLICATE KEY UPDATE nom = VALUES(nom), role = VALUES(role);
