-- phpMyAdmin SQL Dump — POO-Combat (Darktide)
-- Structure et données de référence.
-- Importez ce fichier pour recréer la base depuis zéro.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- ─────────────────────────────────────────────────────────────────────────────
-- Base de données : `poo-combat`
-- ─────────────────────────────────────────────────────────────────────────────

-- ── Table `heroes` ───────────────────────────────────────────────────────────

CREATE TABLE `heroes` (
  `id`           INT          NOT NULL AUTO_INCREMENT,
  `name`         VARCHAR(50)  COLLATE utf8mb4_general_ci NOT NULL,
  `class`        VARCHAR(50)  COLLATE utf8mb4_general_ci NOT NULL,
  `health_point` INT          NOT NULL DEFAULT 100,
  `victories`    INT          NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Classes disponibles : psyker | zealot | veteran | ogryn | arbites

-- ── Table `monsters` ─────────────────────────────────────────────────────────
-- (Réservée pour un futur historique de combats — non utilisée en v1.)

CREATE TABLE `monsters` (
  `id`           INT          NOT NULL AUTO_INCREMENT,
  `name`         VARCHAR(50)  COLLATE utf8mb4_general_ci NOT NULL,
  `class`        VARCHAR(50)  COLLATE utf8mb4_general_ci NOT NULL,
  `health_point` INT          NOT NULL DEFAULT 100,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Classes de monstres : infantry | roamer | specialist | elite | monstrosity | captain | ritualist

COMMIT;
