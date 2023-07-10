-- Utilisation de la base de données
\c blog

-- Suppression des tables si elles existent
DROP TABLE IF EXISTS blog_login_tokens;
DROP TABLE IF EXISTS blog_forgot_password_tokens;
DROP TABLE IF EXISTS blog_email_verification_tokens;
DROP TABLE IF EXISTS blog_article_views;
DROP TABLE IF EXISTS blog_comment_likes;
DROP TABLE IF EXISTS blog_comments;
DROP TABLE IF EXISTS blog_article_tags;
DROP TABLE IF EXISTS blog_tags;
DROP TABLE IF EXISTS blog_drafts;
DROP TABLE IF EXISTS blog_articles;
DROP TABLE IF EXISTS blog_user_roles;
DROP TABLE IF EXISTS blog_roles;
DROP TABLE IF EXISTS blog_users;

-- Suppression des fonctions si elles existent
DROP FUNCTION IF EXISTS blog_delete_article_aliases(INTEGER);
DROP FUNCTION IF EXISTS blog_delete_comment_aliases(INTEGER);

-- Création de la table des utilisateurs
CREATE TABLE blog_users (
	id serial PRIMARY KEY,
	uniq_id varchar(32) NOT NULL,
	email varchar(300) NOT NULL,
	username varchar(30) NOT NULL,
	pseudo varchar(30) NOT NULL,
	password varchar(300) NOT NULL,
	about varchar(300) ,
	created_at timestamp NOT NULL DEFAULT current_timestamp,
	profile_img varchar(200) ,
	verified boolean NOT NULL DEFAULT false
);


-- Création de la table des roles 
CREATE TABLE blog_roles (
    id SERIAL PRIMARY KEY,
    role_name VARCHAR(30) UNIQUE NOT NULL
);


-- Création de la table des roles des utilisateurs
CREATE TABLE blog_user_roles (
    user_id INTEGER NOT NULL REFERENCES blog_users(id),
    role_id INTEGER NOT NULL REFERENCES blog_roles(id),
    PRIMARY KEY (user_id, role_id)
);


-- Création de la table des articles
CREATE TABLE blog_articles (
	id SERIAL PRIMARY KEY,
	user_id INTEGER NOT NULL REFERENCES blog_users(id),
	article_id VARCHAR(32) NOT NULL,
	title VARCHAR(300) NOT NULL,
	tagline VARCHAR(600) DEFAULT NULL,
	content TEXT NOT NULL,
	created_at TIMESTAMP NOT NULL DEFAULT current_timestamp,
	last_updated TIMESTAMP NOT NULL DEFAULT current_timestamp,
	preview_img VARCHAR(200) 
);

-- Création de la table des drafts
CREATE TABLE blog_drafts (
	id SERIAL PRIMARY KEY,
	user_id INTEGER NOT NULL REFERENCES blog_users(id),
	draft_id VARCHAR(32) DEFAULT NULL,
	draft_name VARCHAR(255) NOT NULL,
	title VARCHAR(1000) DEFAULT NULL,
	tagline VARCHAR(2500) DEFAULT NULL,
	content TEXT DEFAULT NULL,
	created_at TIMESTAMP NOT NULL DEFAULT current_timestamp,
	last_updated TIMESTAMP NULL DEFAULT current_timestamp
);

-- Création de la table des tags
CREATE TABLE blog_article_tags (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES blog_users(id),
    tag VARCHAR(12) NOT NULL,
    article_id VARCHAR(32) NOT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT current_timestamp
);


-- Création de la table des commentaires
CREATE TABLE blog_article_comments (
	id SERIAL PRIMARY KEY,
	user_id INTEGER NOT NULL REFERENCES blog_users(id),
	article_id VARCHAR(32) NOT NULL,
	parent_id INTEGER DEFAULT NULL,
	content VARCHAR(1500) NOT NULL,
	is_edited SMALLINT NOT NULL DEFAULT 0,
	created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Création de la table des likes
CREATE TABLE blog_article_comment_likes (
	id SERIAL PRIMARY KEY,
	user_id INTEGER NOT NULL REFERENCES blog_users(id),
	article_id VARCHAR(32) NOT NULL,
	comment_id INTEGER NOT NULL REFERENCES blog_article_comments(id),
	created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);


-- Création de la table des vues
CREATE TABLE blog_article_views (
	id SERIAL PRIMARY KEY,
	user_id INTEGER NOT NULL REFERENCES blog_users(id),
	article_id VARCHAR(32) NOT NULL,
	created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Création de la table des tokens de vérification de courrier électronique
CREATE TABLE blog_email_verification_tokens (
	id SERIAL PRIMARY KEY,
	email VARCHAR(300) NOT NULL,
	token VARCHAR(300) NOT NULL,
	created_at TIMESTAMP NOT NULL DEFAULT current_timestamp
);

-- Création de la table des tokens oubliés de mot de passe
CREATE TABLE blog_forgot_password_tokens (
	id SERIAL PRIMARY KEY,
	email VARCHAR(300) NOT NULL,
	token VARCHAR(300) NOT NULL,
	created_at TIMESTAMP NOT NULL DEFAULT current_timestamp,
	is_used BOOLEAN NOT NULL DEFAULT FALSE
);

-- Création de la table des tokens de connexion
CREATE TABLE blog_login_tokens (
	id SERIAL PRIMARY KEY,
	user_id INTEGER NOT NULL REFERENCES blog_users(id),
	token VARCHAR(300) NOT NULL,
	created_at TIMESTAMP NOT NULL DEFAULT current_timestamp
);

-- Création du rôle 'user'
INSERT INTO blog_roles (role_name) VALUES ('user');
-- Création du rôle 'admin'
INSERT INTO blog_roles (role_name) VALUES ('admin');

-- Insertion de l'utilisateur admin
INSERT INTO "blog_users" ("id", "uniq_id", "email", "username", "pseudo", "password", "about", "created_at", "profile_img", "verified") VALUES
(1,	'1e38946f03415cadd277b7500fd51250',	'benfaez07@gmail.com',	'Faez',	'Faez',	'$2y$10$AFzADRyZRqMmfIBTqgR31.ZB7OjI32gs31ynssTTznCAZu/2n2FTS',	'hgzureazhriughihiuerg',	'2023-07-09 00:32:33.418428',	'https://res.cloudinary.com/dlwjk6xka/image/upload/v1688951964/blog_images/itjj1yex7qtzcnkkt3ca.jpg',	't');

INSERT INTO "blog_user_roles" ("user_id", "role_id") VALUES
(1,	2);

-- Création de la fonction de suppression des articles
CREATE OR REPLACE FUNCTION blog_delete_article_aliases(articleID VARCHAR(32))
RETURNS VOID AS $$
BEGIN
	DELETE FROM blog_articles WHERE article_id = articleID;
	DELETE FROM blog_article_comments WHERE article_id = articleID;
	DELETE FROM blog_article_comment_likes WHERE article_id = articleID;
	DELETE FROM blog_article_tags WHERE article_id = articleID;
	DELETE FROM blog_article_views WHERE article_id = articleID;
END;
$$ LANGUAGE plpgsql;

-- Création de la fonction de suppression des commentaires
CREATE OR REPLACE FUNCTION blog_delete_comment_aliases(commentID INTEGER)
RETURNS VOID AS $$
BEGIN
	-- delete replies
	IF EXISTS(SELECT 1 FROM blog_article_comments WHERE id = commentID AND parent_id = 0) THEN 
		DELETE FROM blog_article_comments WHERE parent_id = commentID;
		DELETE FROM blog_article_comment_likes WHERE comment_id IN (SELECT id FROM blog_article_comments WHERE parent_id = commentID);
	END IF;

	DELETE FROM blog_article_comments WHERE id = commentID;
	DELETE FROM blog_article_comment_likes WHERE comment_id = commentID;
END;
$$ LANGUAGE plpgsql;