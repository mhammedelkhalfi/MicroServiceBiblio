-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : sam. 11 jan. 2025 à 15:30
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `microserviceebook`
--

-- --------------------------------------------------------

--
-- Structure de la table `facture`
--

CREATE TABLE `facture` (
  `idFacture` int(11) NOT NULL,
  `idUtilisateur` int(11) NOT NULL,
  `idLivre` int(11) NOT NULL,
  `montant_total` double NOT NULL,
  `montant_payer` double NOT NULL,
  `montant_rest` double NOT NULL,
  `datePayment` date DEFAULT NULL,
  `dateLimite` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `facture`
--

INSERT INTO `facture` (`idFacture`, `idUtilisateur`, `idLivre`, `montant_total`, `montant_payer`, `montant_rest`, `datePayment`, `dateLimite`) VALUES
(1, 1, 1, 100, 50, 50, '2025-01-05', '2025-01-10'),
(2, 2, 2, 200, 150, 50, '2025-01-06', '2025-01-11'),
(3, 3, 3, 300, 200, 100, '2025-01-07', '2025-01-12'),
(4, 4, 4, 400, 300, 100, '2025-01-08', '2025-01-13'),
(5, 5, 5, 500, 400, 100, '2025-01-09', '2025-01-14'),
(6, 6, 6, 600, 500, 100, '2025-01-10', '2025-01-15'),
(7, 7, 7, 700, 600, 100, '2025-01-11', '2025-01-16'),
(8, 8, 8, 800, 700, 100, '2025-01-12', '2025-01-17'),
(9, 9, 9, 900, 800, 100, '2025-01-13', '2025-01-18'),
(10, 10, 10, 1000, 900, 100, '2025-01-14', '2025-01-19');

-- --------------------------------------------------------

--
-- Structure de la table `livre`
--

CREATE TABLE `livre` (
  `idLivre` int(11) NOT NULL,
  `titre` varchar(100) NOT NULL,
  `auteur` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `idUtilisateur` int(11) DEFAULT NULL,
  `disponibilite` tinyint(1) NOT NULL,
  `type` enum('location','vendre','pret') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `livre`
--

INSERT INTO `livre` (`idLivre`, `titre`, `auteur`, `image`, `idUtilisateur`, `disponibilite`, `type`) VALUES
(1, 'Livre A', 'Auteur 1', 'image1.jpg', 1, 0, 'pret'),
(2, 'Livre B', 'Auteur 2', 'image2.jpg', 2, 1, 'location'),
(3, 'Livre C', 'Auteur 3', 'image3.jpg', 3, 0, 'vendre'),
(4, 'Livre D', 'Auteur 4', 'image4.jpg', 4, 1, 'location'),
(5, 'Livre E', 'Auteur 5', 'image5.jpg', 5, 0, 'location'),
(6, 'Livre F', 'Auteur 6', 'image6.jpg', 6, 1, 'vendre'),
(7, 'Livre G', 'Auteur 7', 'image7.jpg', 7, 0, 'location'),
(8, 'Livre H', 'Auteur 8', 'image8.jpg', 8, 1, 'vendre'),
(9, 'Livre I', 'Auteur 9', 'image9.jpg', 9, 0, 'location'),
(10, 'Livre J', 'Auteur 10', 'image10.jpg', 10, 1, 'pret');

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `livres_de_location_disponibles`
-- (Voir ci-dessous la vue réelle)
--
CREATE TABLE `livres_de_location_disponibles` (
`idLivre` int(11)
,`titre` varchar(100)
,`auteur` varchar(100)
,`image` varchar(255)
,`disponibilite` tinyint(1)
,`prix` double
,`duree` varchar(50)
,`date_emprunt` date
,`date_retour` date
);

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `livres_et_prets_disponibles`
-- (Voir ci-dessous la vue réelle)
--
CREATE TABLE `livres_et_prets_disponibles` (
`idLivre` int(11)
,`titre` varchar(100)
,`auteur` varchar(100)
,`image` varchar(255)
,`disponibilite` tinyint(1)
,`duree` varchar(50)
,`date_emprunt` date
,`date_retour` date
);

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `livres_et_vendres_disponibles`
-- (Voir ci-dessous la vue réelle)
--
CREATE TABLE `livres_et_vendres_disponibles` (
`idLivre` int(11)
,`titre` varchar(100)
,`auteur` varchar(100)
,`image` varchar(255)
,`disponibilite` tinyint(1)
,`prix` double
);

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `livres_nodisponible_a_emprunter`
-- (Voir ci-dessous la vue réelle)
--
CREATE TABLE `livres_nodisponible_a_emprunter` (
`idLivre` int(11)
,`titre` varchar(100)
,`auteur` varchar(100)
,`image` varchar(255)
,`disponibilite` tinyint(1)
,`duree` varchar(50)
,`date_emprunt` date
,`date_retour` date
);

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `livres_nodisponible_a_louer`
-- (Voir ci-dessous la vue réelle)
--
CREATE TABLE `livres_nodisponible_a_louer` (
`idLivre` int(11)
,`titre` varchar(100)
,`auteur` varchar(100)
,`image` varchar(255)
,`disponibilite` tinyint(1)
,`prix` double
,`duree` varchar(50)
,`date_emprunt` date
,`date_retour` date
);

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `livres_nodisponible_a_vender`
-- (Voir ci-dessous la vue réelle)
--
CREATE TABLE `livres_nodisponible_a_vender` (
`idLivre` int(11)
,`titre` varchar(100)
,`auteur` varchar(100)
,`image` varchar(255)
,`disponibilite` tinyint(1)
,`prix` double
);

-- --------------------------------------------------------

--
-- Structure de la table `livre_de_location`
--

CREATE TABLE `livre_de_location` (
  `idLivre` int(11) NOT NULL,
  `prix` double NOT NULL,
  `duree` varchar(50) DEFAULT NULL,
  `date_emprunt` date NOT NULL,
  `date_retour` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `livre_de_location`
--

INSERT INTO `livre_de_location` (`idLivre`, `prix`, `duree`, `date_emprunt`, `date_retour`) VALUES
(1, 5, '7 jours', '2025-01-01', '2025-01-08'),
(2, 4, '3 jours', '2025-01-02', '2025-01-05'),
(3, 6, '10 jours', '2025-01-03', '2025-01-13'),
(4, 3.5, '5 jours', '2025-01-04', '2025-01-09'),
(5, 4.75, '7 jours', '2025-01-05', '2025-01-12'),
(6, 5.25, '14 jours', '2025-01-06', '2025-01-20'),
(7, 6, '7 jours', '2025-01-07', '2025-01-14'),
(8, 3, '2 jours', '2025-01-08', '2025-01-10'),
(9, 2.5, '1 jour', '2025-01-09', '2025-01-10'),
(10, 7, '30 jours', '2025-01-10', '2025-02-10');

-- --------------------------------------------------------

--
-- Structure de la table `livre_de_vente`
--

CREATE TABLE `livre_de_vente` (
  `idLivre` int(11) NOT NULL,
  `prix` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `livre_de_vente`
--

INSERT INTO `livre_de_vente` (`idLivre`, `prix`) VALUES
(1, 19.99),
(2, 24.5),
(3, 15),
(4, 30),
(5, 22),
(6, 18.75),
(7, 35),
(8, 40),
(9, 25),
(10, 20);

-- --------------------------------------------------------

--
-- Structure de la table `livre_pret`
--

CREATE TABLE `livre_pret` (
  `idLivre` int(11) NOT NULL,
  `duree` varchar(50) DEFAULT NULL,
  `date_emprunt` date NOT NULL,
  `date_retour` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `livre_pret`
--

INSERT INTO `livre_pret` (`idLivre`, `duree`, `date_emprunt`, `date_retour`) VALUES
(1, '7 jours', '2025-01-01', '2025-01-08'),
(2, '14 jours', '2025-01-02', '2025-01-16'),
(3, '21 jours', '2025-01-03', '2025-01-24'),
(4, '10 jours', '2025-01-04', '2025-01-14'),
(5, '5 jours', '2025-01-05', '2025-01-10'),
(6, '30 jours', '2025-01-06', '2025-02-05'),
(7, '7 jours', '2025-01-07', '2025-01-14'),
(8, '3 jours', '2025-01-08', '2025-01-11'),
(9, '15 jours', '2025-01-09', '2025-01-24'),
(10, '1 jour', '2025-01-10', '2025-01-11');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `adressemail` varchar(100) NOT NULL,
  `motdepasse` varchar(255) NOT NULL,
  `role` enum('ADMIN','UTILISATEUR') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `nom`, `prenom`, `adressemail`, `motdepasse`, `role`) VALUES
(1, 'Dupont', 'Jean', 'jean.dupont@gmail.com', 'password123', 'UTILISATEUR'),
(2, 'Martin', 'Claire', 'claire.martin@gmail.com', 'password123', 'ADMIN'),
(3, 'Durand', 'Paul', 'paul.durand@gmail.com', 'password123', 'UTILISATEUR'),
(4, 'Moreau', 'Julie', 'julie.moreau@gmail.com', 'password123', 'UTILISATEUR'),
(5, 'Blanc', 'Pierre', 'pierre.blanc@gmail.com', 'password123', 'UTILISATEUR'),
(6, 'Petit', 'Lucie', 'lucie.petit@gmail.com', 'password123', 'UTILISATEUR'),
(7, 'Garcia', 'Antoine', 'antoine.garcia@gmail.com', 'password123', 'UTILISATEUR'),
(8, 'Rousseau', 'Emma', 'emma.rousseau@gmail.com', 'password123', 'ADMIN'),
(9, 'Fournier', 'Lucas', 'lucas.fournier@gmail.com', 'password123', 'UTILISATEUR'),
(10, 'Bernard', 'Sophie', 'sophie.bernard@gmail.com', 'password123', 'UTILISATEUR'),
(11, 'yahya', 'kassani', 'yahya.kassani@gmail.com', 'yahya123', 'UTILISATEUR');

-- --------------------------------------------------------

--
-- Structure de la vue `livres_de_location_disponibles`
--
DROP TABLE IF EXISTS `livres_de_location_disponibles`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `livres_de_location_disponibles`  AS SELECT `l`.`idLivre` AS `idLivre`, `l`.`titre` AS `titre`, `l`.`auteur` AS `auteur`, `l`.`image` AS `image`, `l`.`disponibilite` AS `disponibilite`, `ll`.`prix` AS `prix`, `ll`.`duree` AS `duree`, `ll`.`date_emprunt` AS `date_emprunt`, `ll`.`date_retour` AS `date_retour` FROM (`livre` `l` join `livre_de_location` `ll` on(`l`.`idLivre` = `ll`.`idLivre`)) WHERE `l`.`disponibilite` = 1 AND `l`.`type` = 'location' ;

-- --------------------------------------------------------

--
-- Structure de la vue `livres_et_prets_disponibles`
--
DROP TABLE IF EXISTS `livres_et_prets_disponibles`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `livres_et_prets_disponibles`  AS SELECT `l`.`idLivre` AS `idLivre`, `l`.`titre` AS `titre`, `l`.`auteur` AS `auteur`, `l`.`image` AS `image`, `l`.`disponibilite` AS `disponibilite`, `lp`.`duree` AS `duree`, `lp`.`date_emprunt` AS `date_emprunt`, `lp`.`date_retour` AS `date_retour` FROM (`livre` `l` join `livre_pret` `lp` on(`l`.`idLivre` = `lp`.`idLivre`)) WHERE `l`.`disponibilite` = 1 AND `l`.`type` = 'pret' ;

-- --------------------------------------------------------

--
-- Structure de la vue `livres_et_vendres_disponibles`
--
DROP TABLE IF EXISTS `livres_et_vendres_disponibles`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `livres_et_vendres_disponibles`  AS SELECT `l`.`idLivre` AS `idLivre`, `l`.`titre` AS `titre`, `l`.`auteur` AS `auteur`, `l`.`image` AS `image`, `l`.`disponibilite` AS `disponibilite`, `lv`.`prix` AS `prix` FROM (`livre` `l` join `livre_de_vente` `lv` on(`l`.`idLivre` = `lv`.`idLivre`)) WHERE `l`.`disponibilite` = 1 AND `l`.`type` = 'vendre' ;

-- --------------------------------------------------------

--
-- Structure de la vue `livres_nodisponible_a_emprunter`
--
DROP TABLE IF EXISTS `livres_nodisponible_a_emprunter`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `livres_nodisponible_a_emprunter`  AS SELECT `l`.`idLivre` AS `idLivre`, `l`.`titre` AS `titre`, `l`.`auteur` AS `auteur`, `l`.`image` AS `image`, `l`.`disponibilite` AS `disponibilite`, `lp`.`duree` AS `duree`, `lp`.`date_emprunt` AS `date_emprunt`, `lp`.`date_retour` AS `date_retour` FROM (`livre` `l` join `livre_pret` `lp` on(`l`.`idLivre` = `lp`.`idLivre`)) WHERE `l`.`disponibilite` = 0 AND `l`.`type` = 'pret' ;

-- --------------------------------------------------------

--
-- Structure de la vue `livres_nodisponible_a_louer`
--
DROP TABLE IF EXISTS `livres_nodisponible_a_louer`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `livres_nodisponible_a_louer`  AS SELECT `l`.`idLivre` AS `idLivre`, `l`.`titre` AS `titre`, `l`.`auteur` AS `auteur`, `l`.`image` AS `image`, `l`.`disponibilite` AS `disponibilite`, `lp`.`prix` AS `prix`, `lp`.`duree` AS `duree`, `lp`.`date_emprunt` AS `date_emprunt`, `lp`.`date_retour` AS `date_retour` FROM (`livre` `l` join `livre_de_location` `lp` on(`l`.`idLivre` = `lp`.`idLivre`)) WHERE `l`.`disponibilite` = 0 AND `l`.`type` = 'location' ;

-- --------------------------------------------------------

--
-- Structure de la vue `livres_nodisponible_a_vender`
--
DROP TABLE IF EXISTS `livres_nodisponible_a_vender`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `livres_nodisponible_a_vender`  AS SELECT `l`.`idLivre` AS `idLivre`, `l`.`titre` AS `titre`, `l`.`auteur` AS `auteur`, `l`.`image` AS `image`, `l`.`disponibilite` AS `disponibilite`, `lp`.`prix` AS `prix` FROM (`livre` `l` join `livre_de_vente` `lp` on(`l`.`idLivre` = `lp`.`idLivre`)) WHERE `l`.`disponibilite` = 0 AND `l`.`type` = 'vendre' ;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `facture`
--
ALTER TABLE `facture`
  ADD PRIMARY KEY (`idFacture`),
  ADD KEY `idUtilisateur` (`idUtilisateur`),
  ADD KEY `idLivre` (`idLivre`);

--
-- Index pour la table `livre`
--
ALTER TABLE `livre`
  ADD PRIMARY KEY (`idLivre`),
  ADD KEY `idUtilisateur` (`idUtilisateur`);

--
-- Index pour la table `livre_de_location`
--
ALTER TABLE `livre_de_location`
  ADD PRIMARY KEY (`idLivre`);

--
-- Index pour la table `livre_de_vente`
--
ALTER TABLE `livre_de_vente`
  ADD PRIMARY KEY (`idLivre`);

--
-- Index pour la table `livre_pret`
--
ALTER TABLE `livre_pret`
  ADD PRIMARY KEY (`idLivre`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `adressemail` (`adressemail`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `facture`
--
ALTER TABLE `facture`
  MODIFY `idFacture` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `livre`
--
ALTER TABLE `livre`
  MODIFY `idLivre` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `facture`
--
ALTER TABLE `facture`
  ADD CONSTRAINT `facture_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `facture_ibfk_2` FOREIGN KEY (`idLivre`) REFERENCES `livre` (`idLivre`) ON DELETE CASCADE;

--
-- Contraintes pour la table `livre`
--
ALTER TABLE `livre`
  ADD CONSTRAINT `livre_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `livre_de_location`
--
ALTER TABLE `livre_de_location`
  ADD CONSTRAINT `livre_de_location_ibfk_1` FOREIGN KEY (`idLivre`) REFERENCES `livre` (`idLivre`) ON DELETE CASCADE;

--
-- Contraintes pour la table `livre_de_vente`
--
ALTER TABLE `livre_de_vente`
  ADD CONSTRAINT `livre_de_vente_ibfk_1` FOREIGN KEY (`idLivre`) REFERENCES `livre` (`idLivre`) ON DELETE CASCADE;

--
-- Contraintes pour la table `livre_pret`
--
ALTER TABLE `livre_pret`
  ADD CONSTRAINT `livre_pret_ibfk_1` FOREIGN KEY (`idLivre`) REFERENCES `livre` (`idLivre`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
