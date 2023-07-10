<?php

require_once '../vendor/autoload.php';
require_once '../Config/config.php';
require_once 'DB.php'; // Assurez-vous que ceci pointe vers votre fichier de classe DB

use Faker\Factory;

$faker = Factory::create();

// Instanciation de la classe DB
$db = DB::getInstance();

// Les IDs des utilisateurs pour lesquels nous allons créer des articles
$userIds = [1, 2];

// Création de 10 articles pour chaque utilisateur
foreach ($userIds as $userId) {
    for ($i = 0; $i < 10; $i++) {
        // Génération des données de l'article
        $articleData = [
            'user_id' => $userId,
            'article_id' => $faker->unique()->uuid,
            'title' => $faker->sentence($nbWords = 6, $variableNbWords = true),
            'content' => $faker->paragraphs($nb = 3, $asText = true),
            'preview_img' => $faker->imageUrl($width = 200, $height = 200),
        ];

        // Insertion de l'article dans la base de données
        $db->dbInsert('blog_articles', $articleData);

        // Récupération de l'ID de l'article inséré
        $insertedArticleId = $db->lastInsertId();

        // Création de 3 tags pour chaque article
        for ($j = 0; $j < 3; $j++) {
            // Génération du nom du tag
            $tagName = $faker->word;

            // Vérification de l'existence du tag dans la base de données
            $db->query("SELECT * FROM blog_tags WHERE name = :name");
            $db->bind(':name', $tagName);
            $tag = $db->fetchRow();

            if (!$tag) {
                // Si le tag n'existe pas, on le crée
                $db->dbInsert('blog_tags', ['name' => $tagName]);

                // Récupération de l'ID du tag inséré
                $insertedTagId = $db->lastInsertId();
            } else {
                $insertedTagId = $tag->id;
            }

            // Création de la liaison entre l'article et le tag
            $db->dbInsert('blog_article_tags', [
                'article_id' => $insertedArticleId,
                'tag_id' => $insertedTagId
            ]);
        }
    }
}
