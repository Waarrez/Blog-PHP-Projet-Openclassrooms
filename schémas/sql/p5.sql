-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3307
-- Généré le : ven. 28 juin 2024 à 10:42
-- Version du serveur : 10.6.5-MariaDB
-- Version de PHP : 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `p5`
--

-- --------------------------------------------------------

--
-- Structure de la table `comment`
--

DROP TABLE IF EXISTS `comment`;
CREATE TABLE IF NOT EXISTS `comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `isConfirmed` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `postId` (`post_id`),
  KEY `userId` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `comment`
--

INSERT INTO `comment` (`id`, `content`, `post_id`, `user_id`, `isConfirmed`) VALUES
(17, 'Test', 15, 8, 1),
(18, 'Très bon article ! ', 18, 8, 1),
(19, 'Bon article !', 18, 8, 1);

-- --------------------------------------------------------

--
-- Structure de la table `post`
--

DROP TABLE IF EXISTS `post`;
CREATE TABLE IF NOT EXISTS `post` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `chapo` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `author` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `updatedAt` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `post`
--

INSERT INTO `post` (`id`, `title`, `chapo`, `content`, `author`, `slug`, `updatedAt`, `user_id`) VALUES
(15, 'Salut le monde 2', 'Monde', 'Ceci est un test', 'Warez', 'salut-le-monde', '2024-06-28 09:46:00', 8),
(18, 'Nouvel article sur le php', 'PHP', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce eu eros massa. Sed sed placerat massa. Vestibulum scelerisque euismod orci a suscipit. Suspendisse eu urna id quam iaculis malesuada. Duis est ligula, consectetur quis enim ut, sagittis laoreet nisi. Integer sed magna vehicula, molestie ligula quis, pellentesque diam. Sed mi libero, malesuada quis enim eget, lacinia sodales metus.&#13;&#10;&#13;&#10;In hac habitasse platea dictumst. Nunc id diam et magna pulvinar porta a vitae augue. Morbi ac maximus elit. Nulla non aliquam ante, in feugiat purus. Nunc in volutpat mauris. Morbi ac metus convallis, efficitur orci in, luctus neque. Vivamus pellentesque pellentesque efficitur. Vestibulum interdum lacus nec ligula condimentum vehicula. Aenean sodales eros sed eros dignissim, et tempus sem scelerisque. Donec mollis sem lacus, quis condimentum nunc luctus id.', 'Warez', 'nouvel-article-sur-le-php', '2024-06-28 10:07:54', 8);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `isConfirmed` tinyint(1) NOT NULL,
  `roles` varchar(255) NOT NULL,
  `createdAt` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `username`, `email`, `password`, `isConfirmed`, `roles`, `createdAt`) VALUES
(8, 'Warez', 'thimote.cabotte6259@gmail.com', '$2y$10$mIgbk77P.cf3x922lwI7tuo.Q8AwsNMM9q9MvJatVDNK.mYacTCUW', 1, 'USER', '2024-06-07 10:27:57'),
(10, 'John Doe', 'johndoe@gmail.com', '$2y$10$ZeCVHBg2.LRIulQrxcSCLeC9ZNeReKCGbpbfoal8NAbC.7LZ1gosC', 1, 'USER', '2024-06-07 15:36:46'),
(14, 'Administrateur', 'admin@gmail.com', '$2y$10$pNht4.cgrqze5lvBI7.hz.wN2d3Wsw9dw1INcKQpml/Ao3DvKTpMm', 1, 'ADMIN', '2024-06-14 12:54:09'),
(22, 'Toto', 'toto@gmail.com', '$2y$10$dytVPmItxfkopqVjHaHVTOvX5US27BnXUAxL94fFhmhFX9h9Hyzjm', 1, 'USER', '2024-06-25 19:58:04'),
(23, 'TEST', 'test@gmail.com', '$2y$10$zc126WIk9Bz49aQhzd5O6OHl8OuzMNzKywW/7Rz/EXOQDwi3PcEpm', 1, 'USER', '2024-06-26 09:49:32');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `comment_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `post_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
