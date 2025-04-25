-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : mar. 15 avr. 2025 à 08:22
-- Version du serveur : 10.11.6-MariaDB-0+deb12u1
-- Version de PHP : 8.2.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `projetalves`
--

-- --------------------------------------------------------

--
-- Structure de la table `GLPI`
--

CREATE TABLE `GLPI` (
  `user` text NOT NULL,
  `password` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `profile_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `USER`
--

CREATE TABLE `USER` (
  `alias` varchar(255) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `mail` varchar(255) NOT NULL,
  `mdp` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `USER`
--

INSERT INTO `USER` (`alias`, `nom`, `prenom`, `mail`, `mdp`) VALUES
('Alain', 'Affleflou', 'Alain', 'alain.affleflou@gmail.com', '$2y$10$27uRZMMcgPzO5D1GYtsWeeiV6RyhVXnWkJmRQA1MDMWuQOEYjcz4i'),
('ecamus71100', 'CAMUS', 'Enzo', 'ecamus@mathias.com', '$2y$10$GTPMFHzdTVWCCvTLNOfraOECpp/lpf28RiQrINhonqX3ngamniHBG'),
('paul12', 'Bretin', 'Paul', 'paul@gmail.com', '$2y$10$NeDT5xRF585X09qacthxFuKhYERbounw.q5lcj8MJUbE.ikTwW9/e');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `USER`
--
ALTER TABLE `USER`
  ADD PRIMARY KEY (`alias`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
