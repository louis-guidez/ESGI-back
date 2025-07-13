-- TODO : delete and use a fresh init
-- Adminer 5.3.0 MySQL 8.0.42 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `annonce`;
CREATE TABLE `annonce` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `prix` decimal(10,2) DEFAULT NULL,
  `statut` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_creation` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `annonce` (`id`, `titre`, `description`, `prix`, `statut`, `date_creation`) VALUES
(5,	'Annonce Une INIT',	'Table',	120.00,	NULL,	NULL),
(6,	'Annonce 2',	'Chaise',	40.00,	NULL,	NULL),
(7,	NULL,	NULL,	NULL,	NULL,	NULL),
(8,	NULL,	NULL,	NULL,	NULL,	NULL),
(9,	'tirer',	'feuybfbe',	0.00,	'',	NULL),
(10,	'wowowo',	'wow-wow',	0.00,	'wowow',	NULL),
(11,	'Annonce une',	'une annonce',	25.00,	'Ã  traiter',	NULL),
(12,	'',	'',	0.00,	'',	NULL),
(13,	'',	'',	0.00,	'',	NULL);

DROP TABLE IF EXISTS `conversation`;
CREATE TABLE `conversation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date_creation` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20250712125400',	'2025-07-12 12:54:04',	22);

DROP TABLE IF EXISTS `message`;
CREATE TABLE `message` (
  `id` int NOT NULL AUTO_INCREMENT,
  `contenu` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `date_envoi` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `photo`;
CREATE TABLE `photo` (
  `id` int NOT NULL AUTO_INCREMENT,
  `image_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_upload` datetime DEFAULT NULL,
  `annonce_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_14B784188805AB2F` (`annonce_id`),
  CONSTRAINT `FK_3EAF8E95F675F31B` FOREIGN KEY (`annonce_id`) REFERENCES `annonce` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `photo` (`id`, `image_name`, `date_upload`, `annonce_id`) VALUES
(15,	'test-6872667e84866713291230.png',	'2025-07-12 13:43:26',	12),
(16,	'7007ee2c-635a-4718-896e-4c398a32c093-687266a198ed2076114662.png',	'2025-07-12 13:44:01',	13);

DROP TABLE IF EXISTS `reservation`;
CREATE TABLE `reservation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date_debut` datetime DEFAULT NULL,
  `date_fin` datetime DEFAULT NULL,
  `statut` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE `utilisateur` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prenom` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_inscription` datetime DEFAULT NULL,
  `cagnotte` decimal(10,2) DEFAULT NULL,
  `email_is_verified` tinyint(1) DEFAULT NULL,
  `adresse` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` int DEFAULT NULL,
  `ville` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pays` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `utilisateur` (`id`, `email`, `roles`, `password`, `nom`, `prenom`, `date_inscription`, `cagnotte`, `email_is_verified`, `adresse`, `postal_code`, `ville`, `pays`) VALUES
(1,	'dev@app.com',	'[]',	'$2y$13$viVmsFXJ/u7OeIhXmKEHMugPPYMa/CvSdn8wXQmqwnpBQ9byYX5vO',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL);

DROP TABLE IF EXISTS `utilisateur_conversation`;
CREATE TABLE `utilisateur_conversation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int DEFAULT NULL,
  `conversation_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_E37C021AFB88E14F` (`utilisateur_id`),
  KEY `IDX_E37C021A9AC0396` (`conversation_id`),
  CONSTRAINT `FK_E37C021A9AC0396` FOREIGN KEY (`conversation_id`) REFERENCES `conversation` (`id`),
  CONSTRAINT `FK_E37C021AFB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 2025-07-13 14:31:28 UTC