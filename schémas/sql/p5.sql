-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3307
-- Généré le : mer. 03 juil. 2024 à 13:01
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
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `comment`
--

INSERT INTO `comment` (`id`, `content`, `post_id`, `user_id`, `isConfirmed`) VALUES
(25, 'Très bon article !', 23, 8, 1),
(26, 'Super article !', 24, 8, 1);

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
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `post`
--

INSERT INTO `post` (`id`, `title`, `chapo`, `content`, `author`, `slug`, `updatedAt`, `user_id`) VALUES
(23, ' L\'avenir de l\'intelligence artificielle', ' L\'IA révolutionne chaque aspect de nos vies. Quel avenir pour cette technologie transformative ?', 'L\'intelligence artificielle (IA) connaît des avancées spectaculaires dans de nombreux domaines, de la santé à l\'éducation en passant par les transports. Les experts prédisent que dans les prochaines décennies, l\'IA deviendra encore plus intégrée à notre quotidien, améliorant la productivité et facilitant les tâches complexes. Cependant, il est également crucial de prendre en compte les implications éthiques et sociales de cette évolution rapide.', 'Warez', 'l-39-avenir-de-l-39-intelligence-artificielle', '2024-07-03 14:59:22', 8),
(24, 'Les énergies renouvelables : un espoir pour la planète', 'La transition vers les énergies renouvelables est essentielle pour lutter contre le changement climatique. Quelles sont les dernières avancées dans ce domaine ?', 'Face aux défis environnementaux, les énergies renouvelables représentent une solution durable pour réduire notre empreinte carbone. L&#39;énergie solaire, éolienne, hydroélectrique et géothermique sont de plus en plus adoptées à travers le monde. Les innovations technologiques et les politiques gouvernementales favorisent leur développement, rendant l&#39;énergie verte plus accessible et plus économique. Cette transition énergétique est essentielle pour garantir un avenir sain et prospère pour les générations futures.', 'Warez', 'les-nergies-renouvelables-un-espoir-pour-la-plan-te', '2024-07-03 14:53:09', 8),
(25, 'La révolution numérique dans l\'éducation', 'La technologie transforme la manière dont nous apprenons et enseignons. Quel impact a-t-elle sur le système éducatif ?', ' La révolution numérique a bouleversé le secteur de l\'éducation, offrant de nouvelles opportunités d\'apprentissage et d\'enseignement. Les plateformes en ligne, les applications éducatives et les cours virtuels permettent aux étudiants d\'accéder à des ressources pédagogiques de qualité partout dans le monde. Cette transformation favorise une éducation plus inclusive et personnalisée, adaptée aux besoins individuels des apprenants. Cependant, il est crucial de combler le fossé numérique pour que tous les étudiants puissent bénéficier de ces avancées.', 'Warez', 'la-r-volution-num-rique-dans-l-39-ducation', '2024-07-03 14:59:43', 8),
(26, 'La biodiversité en péril : comment agir ?', 'La perte de biodiversité est une menace majeure pour notre planète. Quelles actions pouvons-nous entreprendre pour la préserver ?', ' La biodiversité, essentielle à l&#39;équilibre de nos écosystèmes, est gravement menacée par les activités humaines. La déforestation, la pollution, le changement climatique et la surexploitation des ressources naturelles contribuent à l&#39;extinction rapide de nombreuses espèces. Pour inverser cette tendance, il est crucial de protéger les habitats naturels, de promouvoir des pratiques agricoles durables et de sensibiliser le public à l&#39;importance de la biodiversité. Chacun de nous peut jouer un rôle en adoptant des comportements respectueux de l&#39;environnement.', 'Warez', 'la-biodiversit-en-p-ril-comment-agir', '2024-07-03 14:53:37', 8),
(27, 'Le télétravail : une nouvelle réalité', 'La pandémie de COVID-19 a accéléré l&#39;adoption du télétravail. Quels sont les avantages et les défis de cette nouvelle manière de travailler ?', 'Le télétravail est devenu une norme pour de nombreux professionnels à travers le monde. Cette nouvelle réalité présente de nombreux avantages, tels qu&#39;une plus grande flexibilité, une réduction des trajets domicile-travail et une meilleure conciliation entre vie professionnelle et personnelle. Cependant, elle pose également des défis, comme l&#39;isolement social, la gestion du temps et l&#39;aménagement d&#39;un espace de travail à domicile. Les entreprises doivent trouver un équilibre pour soutenir leurs employés et garantir leur productivité dans ce contexte en constante ', 'Warez', 'le-t-l-travail-une-nouvelle-r-alit', '2024-07-03 14:53:49', 8);

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
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `username`, `email`, `password`, `isConfirmed`, `roles`, `createdAt`) VALUES
(8, 'Warez', 'thimote.cabotte6259@gmail.com', '$2y$10$mIgbk77P.cf3x922lwI7tuo.Q8AwsNMM9q9MvJatVDNK.mYacTCUW', 1, 'USER', '2024-06-07 10:27:57'),
(24, 'Admin', 'admin@gmail.com', '$2y$10$VhnyNTc0sns8qMHW45VVtu7c7lrB7heie3yX41MqWo7k/86bxp1Wi', 1, 'ADMIN', '2024-07-03 10:29:43');

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
