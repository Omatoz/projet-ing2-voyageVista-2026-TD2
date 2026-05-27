-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : mer. 27 mai 2026 à 07:35
-- Version du serveur : 5.7.24
-- Version de PHP : 8.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `voyagevista`
--

-- --------------------------------------------------------

--
-- Structure de la table `briques_voyage`
--

CREATE TABLE `briques_voyage` (
  `id` int(11) NOT NULL,
  `titre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `prix` decimal(10,2) NOT NULL,
  `categorie` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_brique` enum('destination','transport','hebergement','activite') COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta_rating` int(11) DEFAULT '5',
  `couleur_css` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT 'bg-bali',
  `image_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `briques_voyage`
--

INSERT INTO `briques_voyage` (`id`, `titre`, `description`, `prix`, `categorie`, `type_brique`, `meta_rating`, `couleur_css`, `image_url`, `date_creation`) VALUES
(1, 'Bali, Indonésie', 'Échappée tropicale complète : immersion entre rizières sacrées d\'Ubud, temples séculaires et plages de sable noir.', '789.00', 'plages,detente', 'destination', 5, 'bg-bali', 'images/bali.png', '2026-05-26 09:39:56'),
(2, 'Ligne de Bus — Europe', 'Transit éco-responsable et fluide. Traversez les plus belles capitales européennes à bord d\'un autocar grand confort connecté.', '159.00', 'aventures', 'transport', 4, 'bg-bus', 'images/europe.png', '2026-05-26 09:39:56'),
(3, 'Aventure en Suisse', 'Expédition alpine exclusive : randonnées sportives sur les glaciers, panoramas à couper le souffle et nuits insolites en refuge.', '1249.00', 'aventures,montagnes', 'destination', 5, 'bg-suisse', 'images/suisse.png', '2026-05-26 09:39:56'),
(4, 'Chamonix Mont-Blanc', 'Le fleuron de la haute montagne. Séjour grand air combinant forfait remontées mécaniques, pistes légendaires et hôtel de charme.', '450.00', 'montagnes,aventures', 'destination', 4, 'bg-chamonix', 'images/chamonix.png', '2026-05-26 09:39:56'),
(5, 'Maldives — Farniente', 'Le paradis azur absolu. Séjour en villa sur pilotis privée, barrière de corail exclusive et formule gastronomique tout inclus.', '1899.00', 'plages,detente', 'destination', 5, 'bg-maldives', NULL, '2026-05-26 09:39:56'),
(6, 'Circuit Train Interrail', 'La liberté géométrique des rails. Un pass ferroviaire complet et flexible pour explorer l\'Europe à votre rythme.', '299.00', 'aventures', 'transport', 5, 'bg-train', NULL, '2026-05-26 09:39:56');

-- --------------------------------------------------------

--
-- Structure de la table `composition_itineraire`
--

CREATE TABLE `composition_itineraire` (
  `id_itineraire` int(11) NOT NULL,
  `id_brique` int(11) NOT NULL,
  `quantite` int(11) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `itineraires`
--

CREATE TABLE `itineraires` (
  `id` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `statut` enum('en_cours','valide') COLLATE utf8mb4_unicode_ci DEFAULT 'en_cours',
  `date_depart` date DEFAULT NULL,
  `date_retour` date DEFAULT NULL,
  `nbr_voyageurs` int(11) DEFAULT '1',
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mot_de_passe` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('client','organisateur','admin') COLLATE utf8mb4_unicode_ci DEFAULT 'client',
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `briques_voyage`
--
ALTER TABLE `briques_voyage`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `composition_itineraire`
--
ALTER TABLE `composition_itineraire`
  ADD PRIMARY KEY (`id_itineraire`,`id_brique`),
  ADD KEY `fk_comp_brique` (`id_brique`);

--
-- Index pour la table `itineraires`
--
ALTER TABLE `itineraires`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_itineraire_user` (`id_utilisateur`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `briques_voyage`
--
ALTER TABLE `briques_voyage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `itineraires`
--
ALTER TABLE `itineraires`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `composition_itineraire`
--
ALTER TABLE `composition_itineraire`
  ADD CONSTRAINT `fk_comp_brique` FOREIGN KEY (`id_brique`) REFERENCES `briques_voyage` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_comp_itineraire` FOREIGN KEY (`id_itineraire`) REFERENCES `itineraires` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `itineraires`
--
ALTER TABLE `itineraires`
  ADD CONSTRAINT `fk_itineraire_user` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
