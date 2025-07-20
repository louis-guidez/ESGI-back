SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `utilisateur_annonce`;
DROP TABLE IF EXISTS `annonce_categorie`;
DROP TABLE IF EXISTS `message`;
DROP TABLE IF EXISTS `conversation`;
DROP TABLE IF EXISTS `reservation`;
DROP TABLE IF EXISTS `photo`;
DROP TABLE IF EXISTS `annonce`;
DROP TABLE IF EXISTS `categorie`;
DROP TABLE IF EXISTS `utilisateur`;

CREATE TABLE `utilisateur` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(180) NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) NOT NULL,
  `nom` varchar(255) DEFAULT NULL,
  `prenom` varchar(255) DEFAULT NULL,
  `date_inscription` datetime DEFAULT NULL,
  `cagnotte` decimal(10,2) DEFAULT NULL,
  `email_is_verified` tinyint(1) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `postal_code` int DEFAULT NULL,
  `ville` varchar(255) DEFAULT NULL,
  `pays` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `utilisateur` (`id`, `email`, `roles`, `password`, `nom`, `prenom`, `date_inscription`, `cagnotte`, `email_is_verified`, `adresse`, `postal_code`, `ville`, `pays`) VALUES
(1, 'alice@example.com', '["ROLE_USER"]', 'passhash1', 'Alice', 'Liddell', '2025-01-10 10:00:00', 100.00, 1, '1 Rue de Paris', 75001, 'Paris', 'France'),
(2, 'bob@example.com', '["ROLE_USER"]', 'passhash2', 'Bob', 'Builder', '2025-02-15 12:00:00', 50.00, 0, '2 Avenue de Lyon', 69000, 'Lyon', 'France');

CREATE TABLE `categorie` (
  `id` int NOT NULL AUTO_INCREMENT,
  `label` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categorie` (`id`, `label`) VALUES
(1, 'Mobilier'),
(2, 'Décoration');

CREATE TABLE `annonce` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) DEFAULT NULL,
  `description` longtext,
  `prix` decimal(10,2) DEFAULT NULL,
  `statut` varchar(255) DEFAULT NULL,
  `date_creation` datetime DEFAULT NULL,
  `utilisateur_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_ANNONCE_UTILISATEUR` (`utilisateur_id`),
  CONSTRAINT `FK_ANNONCE_UTILISATEUR` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `annonce` (`id`, `titre`, `description`, `prix`, `statut`, `date_creation`, `utilisateur_id`) VALUES
(1, 'Chaise vintage', 'Une chaise confortable', 40.00, 'disponible', '2025-07-01 09:00:00', 1),
(2, 'Table en bois', 'Grande table 6 personnes', 120.00, 'disponible', '2025-07-02 10:00:00', 2);

CREATE TABLE `annonce_categorie` (
  `annonce_id` int NOT NULL,
  `categorie_id` int NOT NULL,
  PRIMARY KEY (`annonce_id`, `categorie_id`),
  KEY `IDX_ANNONCE_CATEGORIE_ANNONCE` (`annonce_id`),
  KEY `IDX_ANNONCE_CATEGORIE_CATEGORIE` (`categorie_id`),
  CONSTRAINT `FK_AC_ANNONCE` FOREIGN KEY (`annonce_id`) REFERENCES `annonce` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_AC_CATEGORIE` FOREIGN KEY (`categorie_id`) REFERENCES `categorie` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `annonce_categorie` (`annonce_id`, `categorie_id`) VALUES
(1,1),
(2,1),
(2,2);

CREATE TABLE `photo` (
  `id` int NOT NULL AUTO_INCREMENT,
  `image_name` varchar(255) DEFAULT NULL,
  `date_upload` datetime DEFAULT NULL,
  `annonce_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_PHOTO_ANNONCE` (`annonce_id`),
  CONSTRAINT `FK_PHOTO_ANNONCE` FOREIGN KEY (`annonce_id`) REFERENCES `annonce` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `photo` (`id`, `image_name`, `date_upload`, `annonce_id`) VALUES
(1, 'chaise.png', '2025-07-05 08:00:00', 1),
(2, 'table.png', '2025-07-05 09:00:00', 2);

CREATE TABLE `reservation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date_debut` datetime DEFAULT NULL,
  `date_fin` datetime DEFAULT NULL,
  `statut` varchar(255) DEFAULT NULL,
  `annonce_id` int DEFAULT NULL,
  `utilisateur_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_RESERVATION_ANNONCE` (`annonce_id`),
  KEY `IDX_RESERVATION_UTILISATEUR` (`utilisateur_id`),
  CONSTRAINT `FK_RESERVATION_ANNONCE` FOREIGN KEY (`annonce_id`) REFERENCES `annonce` (`id`),
  CONSTRAINT `FK_RESERVATION_UTILISATEUR` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `reservation` (`id`, `date_debut`, `date_fin`, `statut`, `annonce_id`, `utilisateur_id`) VALUES
(1, '2025-07-10 12:00:00', '2025-07-12 12:00:00', 'en cours', 1, 2),
(2, '2025-07-15 09:00:00', '2025-07-18 09:00:00', 'confirmee', 2, 1);

CREATE TABLE `conversation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date_creation` datetime DEFAULT NULL,
  `utilisateur_a_id` int NOT NULL,
  `utilisateur_b_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_CONV_A` (`utilisateur_a_id`),
  KEY `IDX_CONV_B` (`utilisateur_b_id`),
  CONSTRAINT `FK_CONV_A` FOREIGN KEY (`utilisateur_a_id`) REFERENCES `utilisateur` (`id`),
  CONSTRAINT `FK_CONV_B` FOREIGN KEY (`utilisateur_b_id`) REFERENCES `utilisateur` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `conversation` (`id`, `date_creation`, `utilisateur_a_id`, `utilisateur_b_id`) VALUES
(1, '2025-07-03 10:00:00', 1, 2),
(2, '2025-07-04 11:00:00', 2, 1);

CREATE TABLE `message` (
  `id` int NOT NULL AUTO_INCREMENT,
  `contenu` longtext,
  `date_envoi` datetime DEFAULT NULL,
  `sender_id` int DEFAULT NULL,
  `receiver_id` int DEFAULT NULL,
  `conversation_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_MESSAGE_SENDER` (`sender_id`),
  KEY `IDX_MESSAGE_RECEIVER` (`receiver_id`),
  KEY `IDX_MESSAGE_CONV` (`conversation_id`),
  CONSTRAINT `FK_MESSAGE_SENDER` FOREIGN KEY (`sender_id`) REFERENCES `utilisateur` (`id`),
  CONSTRAINT `FK_MESSAGE_RECEIVER` FOREIGN KEY (`receiver_id`) REFERENCES `utilisateur` (`id`),
  CONSTRAINT `FK_MESSAGE_CONV` FOREIGN KEY (`conversation_id`) REFERENCES `conversation` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `message` (`id`, `contenu`, `date_envoi`, `sender_id`, `receiver_id`, `conversation_id`) VALUES
(1, 'Bonjour', '2025-07-03 10:05:00', 1, 2, 1),
(2, 'Salut', '2025-07-03 10:06:00', 2, 1, 1);

CREATE TABLE `utilisateur_annonce` (
  `utilisateur_id` int NOT NULL,
  `annonce_id` int NOT NULL,
  PRIMARY KEY (`utilisateur_id`, `annonce_id`),
  KEY `IDX_UA_ANNONCE` (`annonce_id`),
  CONSTRAINT `FK_UA_UTILISATEUR` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_UA_ANNONCE` FOREIGN KEY (`annonce_id`) REFERENCES `annonce` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `utilisateur_annonce` (`utilisateur_id`, `annonce_id`) VALUES
(1, 2),
(2, 1);

SET foreign_key_checks = 1;
