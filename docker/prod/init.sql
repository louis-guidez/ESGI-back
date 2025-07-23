-- Adminer 5.3.0 MySQL 8.0.42 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `annonce`;
CREATE TABLE `annonce` (
                           `id` int NOT NULL AUTO_INCREMENT,
                           `titre` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                           `description` longtext COLLATE utf8mb4_unicode_ci,
                           `prix` decimal(10,2) DEFAULT NULL,
                           `statut` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                           `date_creation` datetime DEFAULT NULL,
                           `utilisateur_id` int DEFAULT NULL,
                           PRIMARY KEY (`id`),
                           KEY `IDX_F65593E5FB88E14F` (`utilisateur_id`),
                           CONSTRAINT `FK_ANNONCE_UTILISATEUR` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `annonce_categorie`;
CREATE TABLE `annonce_categorie` (
                                     `annonce_id` int NOT NULL,
                                     `categorie_id` int NOT NULL,
                                     PRIMARY KEY (`annonce_id`,`categorie_id`),
                                     KEY `IDX_3C5A3DA68805AB2F` (`annonce_id`),
                                     KEY `IDX_3C5A3DA6BCF5E72D` (`categorie_id`),
                                     CONSTRAINT `FK_AC_ANNONCE` FOREIGN KEY (`annonce_id`) REFERENCES `annonce` (`id`) ON DELETE CASCADE,
                                     CONSTRAINT `FK_AC_CATEGORIE` FOREIGN KEY (`categorie_id`) REFERENCES `categorie` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `categorie`;
CREATE TABLE `categorie` (
                             `id` int NOT NULL AUTO_INCREMENT,
                             `label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                             PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categorie` (`id`, `label`) VALUES
                                            (3,	'Immobilier'),
                                            (4,	'Véhicules'),
                                            (5,	'Mode'),
                                            (6,	'Maison & Jardin'),
                                            (7,	'Famille'),
                                            (8,	'Électronique'),
                                            (10,	'Loisirs'),
                                            (11,	'Autre'),
                                            (12,	'Sport');

DROP TABLE IF EXISTS `conversation`;
CREATE TABLE `conversation` (
                                `id` int NOT NULL AUTO_INCREMENT,
                                `date_creation` datetime DEFAULT NULL,
                                `utilisateur_a_id` int NOT NULL,
                                `utilisateur_b_id` int NOT NULL,
                                PRIMARY KEY (`id`),
                                KEY `IDX_8A8E26E9F25F3F62` (`utilisateur_a_id`),
                                KEY `IDX_8A8E26E9E0EA908C` (`utilisateur_b_id`),
                                CONSTRAINT `FK_CONV_A` FOREIGN KEY (`utilisateur_a_id`) REFERENCES `utilisateur` (`id`),
                                CONSTRAINT `FK_CONV_B` FOREIGN KEY (`utilisateur_b_id`) REFERENCES `utilisateur` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `message`;
CREATE TABLE `message` (
                           `id` int NOT NULL AUTO_INCREMENT,
                           `contenu` longtext COLLATE utf8mb4_unicode_ci,
                           `date_envoi` datetime DEFAULT NULL,
                           `sender_id` int DEFAULT NULL,
                           `receiver_id` int DEFAULT NULL,
                           `conversation_id` int NOT NULL,
                           PRIMARY KEY (`id`),
                           KEY `IDX_B6BD307FF624B39D` (`sender_id`),
                           KEY `IDX_B6BD307FCD53EDB6` (`receiver_id`),
                           KEY `IDX_B6BD307F9AC0396` (`conversation_id`),
                           CONSTRAINT `FK_MESSAGE_CONV` FOREIGN KEY (`conversation_id`) REFERENCES `conversation` (`id`),
                           CONSTRAINT `FK_MESSAGE_RECEIVER` FOREIGN KEY (`receiver_id`) REFERENCES `utilisateur` (`id`),
                           CONSTRAINT `FK_MESSAGE_SENDER` FOREIGN KEY (`sender_id`) REFERENCES `utilisateur` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `photo`;
CREATE TABLE `photo` (
                         `id` int NOT NULL AUTO_INCREMENT,
                         `image_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                         `date_upload` datetime DEFAULT NULL,
                         `annonce_id` int DEFAULT NULL,
                         PRIMARY KEY (`id`),
                         KEY `IDX_14B784188805AB2F` (`annonce_id`),
                         CONSTRAINT `FK_PHOTO_ANNONCE` FOREIGN KEY (`annonce_id`) REFERENCES `annonce` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `reservation`;
CREATE TABLE `reservation` (
                               `id` int NOT NULL AUTO_INCREMENT,
                               `date_debut` datetime DEFAULT NULL,
                               `date_fin` datetime DEFAULT NULL,
                               `statut` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                               `annonce_id` int DEFAULT NULL,
                               `utilisateur_id` int DEFAULT NULL,
                               `stripe_amount` double DEFAULT NULL,
                               PRIMARY KEY (`id`),
                               KEY `IDX_42C849558805AB2F` (`annonce_id`),
                               KEY `IDX_42C84955FB88E14F` (`utilisateur_id`),
                               CONSTRAINT `FK_RESERVATION_ANNONCE` FOREIGN KEY (`annonce_id`) REFERENCES `annonce` (`id`),
                               CONSTRAINT `FK_RESERVATION_UTILISATEUR` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `transaction`;
CREATE TABLE `transaction` (
                               `id` int NOT NULL AUTO_INCREMENT,
                               `reservation_id` int DEFAULT NULL,
                               `utilisateur_id` int DEFAULT NULL,
                               `stripe_intent_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                               `amount` decimal(10,2) NOT NULL,
                               `currency` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
                               `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                               PRIMARY KEY (`id`),
                               KEY `IDX_723705D1B83297E7` (`reservation_id`),
                               KEY `IDX_723705D1FB88E14F` (`utilisateur_id`),
                               CONSTRAINT `FK_723705D1B83297E7` FOREIGN KEY (`reservation_id`) REFERENCES `reservation` (`id`),
                               CONSTRAINT `FK_723705D1FB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE `utilisateur` (
                               `id` int NOT NULL AUTO_INCREMENT,
                               `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
                               `roles` json NOT NULL,
                               `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                               `nom` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                               `prenom` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                               `date_inscription` datetime DEFAULT NULL,
                               `cagnotte` decimal(10,2) DEFAULT NULL,
                               `email_is_verified` tinyint(1) DEFAULT NULL,
                               `adresse` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                               `postal_code` int DEFAULT NULL,
                               `ville` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                               `pays` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                               PRIMARY KEY (`id`),
                               UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `utilisateur_annonce`;
CREATE TABLE `utilisateur_annonce` (
                                       `utilisateur_id` int NOT NULL,
                                       `annonce_id` int NOT NULL,
                                       PRIMARY KEY (`utilisateur_id`,`annonce_id`),
                                       KEY `IDX_8C5E64778805AB2F` (`annonce_id`),
                                       CONSTRAINT `FK_UA_ANNONCE` FOREIGN KEY (`annonce_id`) REFERENCES `annonce` (`id`) ON DELETE CASCADE,
                                       CONSTRAINT `FK_UA_UTILISATEUR` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 2025-07-23 20:00:53 UTC