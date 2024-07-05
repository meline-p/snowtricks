-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 05 juil. 2024 à 16:53
-- Version du serveur : 10.4.27-MariaDB
-- Version de PHP : 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `snowtricks`
--

-- --------------------------------------------------------

--
-- Structure de la table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `category`
--

INSERT INTO `category` (`id`, `name`, `slug`) VALUES
(18, 'Freestyle', 'freestyle'),
(19, 'Freeride', 'freeride'),
(20, 'Jibbing', 'jibbing'),
(21, 'Big Air', 'big-air'),
(22, 'Pipe', 'pipe'),
(23, 'Flatland', 'flatland');

-- --------------------------------------------------------

--
-- Structure de la table `comment`
--

CREATE TABLE `comment` (
  `id` int(11) NOT NULL,
  `trick_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` longtext NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp() COMMENT '(DC2Type:datetime_immutable)',
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `comment`
--

INSERT INTO `comment` (`id`, `trick_id`, `user_id`, `content`, `created_at`, `deleted_at`) VALUES
(21, 29, 13, 'Ce Powder Turn était incroyable ! Tellement fluide dans cette poudreuse.', '2024-07-05 13:35:16', NULL),
(22, 29, 7, 'Vraiment stylé ! Tu fais ça tellement naturellement. Tu peux m\'apprendre?', '2024-07-05 13:35:30', NULL),
(23, 29, 6, 'La neige avait l\'air parfaite pour ça ! Bravo pour cette descente.', '2024-07-05 13:35:40', NULL),
(24, 32, 8, 'Je suis content d\'avoir enfin réussi ce Boardslide clean sur le rail.', '2024-07-05 13:38:03', NULL),
(25, 32, 13, 'Super technique ! Tu fais ça avec tant de fluidité.', '2024-07-05 13:38:11', NULL),
(26, 32, 9, 'C\'est fou comment tu maintiens l\'équilibre là-dessus. Respect !', '2024-07-05 13:38:18', NULL),
(27, 33, 10, 'Boom ! Double Cork réussi !', '2024-07-05 13:39:12', NULL),
(28, 33, 6, 'Wow, ça demande tellement de technique et de courage. Tu es un pro !', '2024-07-05 13:39:19', NULL),
(29, 33, 10, 'C\'était impressionnant à voir ! Combien de temps ça t\'a pris pour maîtriser ça ?', '2024-07-05 13:39:27', NULL),
(30, 35, 8, 'Ce Backside Air était à la hauteur de mes attentes !', '2024-07-05 13:40:31', NULL),
(31, 35, 13, 'Ça montre vraiment ton niveau de maîtrise dans le half-pipe.', '2024-07-05 13:40:39', NULL),
(32, 35, 9, 'Tu as volé haut là-dedans ! Comment tu fais pour atterrir aussi proprement ?', '2024-07-05 13:40:47', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20240517100922', '2024-05-17 10:09:34', 7500),
('DoctrineMigrations\\Version20240517122720', '2024-05-17 12:27:30', 1082),
('DoctrineMigrations\\Version20240705145241', '2024-07-05 14:53:29', 466);

-- --------------------------------------------------------

--
-- Structure de la table `image`
--

CREATE TABLE `image` (
  `id` int(11) NOT NULL,
  `trick_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `extension` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp() COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `image`
--

INSERT INTO `image` (`id`, `trick_id`, `name`, `extension`, `created_at`, `updated_at`, `deleted_at`) VALUES
(107, 27, '6687fa8ab1945.avif', 'avif', '2024-07-05 13:52:10', NULL, NULL),
(108, 28, '6687fa9b2925e.avif', 'avif', '2024-07-05 13:52:27', NULL, NULL),
(109, 29, '6687faaa0a348.avif', 'avif', '2024-07-05 13:52:42', NULL, NULL),
(110, 30, '6687fab882066.avif', 'avif', '2024-07-05 13:52:56', NULL, NULL),
(111, 31, '6687fac45fa9c.avif', 'avif', '2024-07-05 13:53:08', NULL, NULL),
(112, 32, '6687fad057f25.avif', 'avif', '2024-07-05 13:53:20', NULL, NULL),
(113, 33, '6687fadc4375e.avif', 'avif', '2024-07-05 13:53:32', NULL, NULL),
(115, 34, '6687faf8bdc64.avif', 'avif', '2024-07-05 13:54:00', NULL, NULL),
(116, 35, '6687fb9829702.avif', 'avif', '2024-07-05 13:56:40', NULL, NULL),
(117, 36, '6687fbadcac52.avif', 'avif', '2024-07-05 13:57:01', NULL, NULL),
(118, 37, '6687fbbc192d2.avif', 'avif', '2024-07-05 13:57:16', NULL, NULL),
(119, 38, '6687fbccaa3e9.avif', 'avif', '2024-07-05 13:57:32', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

CREATE TABLE `messenger_messages` (
  `id` bigint(20) NOT NULL,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `available_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `delivered_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `trick`
--

CREATE TABLE `trick` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `promote_image_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` longtext DEFAULT NULL,
  `slug` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `trick`
--

INSERT INTO `trick` (`id`, `category_id`, `promote_image_id`, `name`, `description`, `slug`) VALUES
(27, 18, NULL, '360° (trois-six)', 'Rotation complète de 360 degrés autour de l\'axe longitudinal du snowboard.\r\n\r\nPour exécuter un 360° sur un snowboard, commencez par prendre de la vitesse et trouvez une section de la piste ou un kick dans le snowpark. Approchez l\'obstacle ou le saut avec une légère flexion des genoux pour préparer le pop. Juste avant de décoller, engagez vos épaules dans la direction de la rotation tout en gardant le regard fixé sur votre point de réception. Lorsque vous êtes en l\'air, pliez vos jambes pour absorber le terrain et effectuez une rotation complète en faisant glisser votre bras avant pour aider à maintenir l\'équilibre. Une fois que vous avez atteint 360 degrés, préparez-vous à atterrir en alignant doucement la planche avec la pente et en absorbant l\'impact avec les jambes pour une réception en douceur.', '3600-trois-six'),
(28, 18, NULL, 'Tail Grab', 'Saisie de la partie arrière (tail) du snowboard avec la main pendant un saut.\r\n\r\n Pour réaliser un Tail Grab, commencez par choisir une petite bosse ou un kick dans le snowpark où vous pouvez obtenir un peu d\'air. Prenez de la vitesse et approchez l\'obstacle avec confiance. Au moment de décoller, fléchissez vos genoux et tirez légèrement votre planche vers votre corps en pliant votre jambe arrière pour atteindre la partie arrière (tail) du snowboard. Attrapez fermement le tail avec votre main dominante tout en gardant l\'équilibre avec l\'autre main. Maintenez la prise pendant que vous êtes en l\'air, en gardant votre regard sur votre point de réception pour une orientation précise. Pour atterrir, relâchez la prise juste avant l\'impact, alignez la planche avec la pente et fléchissez les genoux pour amortir la réception et maintenir le contrôle.', 'tail-grab'),
(29, 19, NULL, 'Powder Turn', 'Virage fluide et large effectué dans la neige profonde (powder).\r\n\r\nPour exécuter un Powder Turn dans la neige profonde, choisissez une pente vierge avec une bonne couche de poudreuse. Prenez de la vitesse et fléchissez légèrement vos genoux pour maintenir une bonne stabilité. Inclinez votre corps légèrement en amont dans la direction que vous souhaitez tourner, en utilisant le poids de votre corps pour amorcer le virage. Utilisez vos pieds et vos jambes pour glisser en douceur à travers la poudreuse, en ajustant votre angle de virage pour contrôler votre vitesse. Gardez un centre de gravité bas et ajustez votre posture pour naviguer à travers les variations du terrain tout en profitant de la sensation de flottaison offerte par la neige profonde.', 'powder-turn'),
(30, 19, NULL, 'Drop Cliffs', 'Sauts ou descentes techniques depuis des rochers ou des falaises en terrain hors-piste.\r\n\r\nPour réaliser un Drop Cliffs, repérez un cliff ou une falaise avec une bonne réception et un bon dégagement en dessous. Approchez l\'obstacle avec vitesse et confiance. Juste avant le saut, fléchissez vos genoux et ajustez votre position pour absorber l\'impact à l\'atterrissage. Au moment du décollage, engagez-vous légèrement vers l\'avant tout en gardant votre regard sur le point de réception. Lorsque vous êtes en l\'air, gardez votre corps compact et contrôlé pour maintenir la stabilité. Anticipez l\'atterrissage en pliant les genoux pour amortir l\'impact, en visant à atterrir avec la planche bien alignée dans la direction de la pente pour une réception en douceur.', 'drop-cliffs'),
(31, 20, NULL, '50-50', 'Glisser droit sur un rail ou une box avec la planche perpendiculaire à l\'obstacle.\r\n\r\nPour exécuter un 50-50 sur un rail ou une box, approchez l\'obstacle avec vitesse et alignez-vous parfaitement avec lui. Fléchissez vos genoux et maintenez une posture stable au-dessus de l\'obstacle. Au moment de monter sur le rail ou la box, gardez votre poids centré entre vos pieds pour maintenir l\'équilibre. Glissez en ligne droite sur l\'obstacle en gardant vos épaules parallèles à la direction de glisse. Utilisez légèrement vos bras pour ajuster l\'équilibre tout en gardant vos genoux fléchis pour absorber les imperfections du rail ou de la box. Anticipez la fin de l\'obstacle et ajustez votre poids pour descendre proprement de l\'autre côté.', '50-50'),
(32, 20, NULL, 'Boardslide', 'Glisser sur un rail ou une box avec la planche parallèle à l\'obstacle.\r\n\r\nPour réaliser un Boardslide sur un rail ou une box, approchez l\'obstacle avec vitesse et alignez-vous correctement. Au moment de monter sur l\'obstacle, orientez votre planche pour qu\'elle soit parallèle à l\'obstacle, avec votre poids centré entre vos pieds. Engagez le Boardslide en faisant glisser votre planche le long du rail ou de la box tout en gardant vos épaules parallèles à la direction de glisse. Utilisez vos bras pour garder l\'équilibre et ajuster la position de votre corps pour maintenir la glisse. Anticipez la fin de l\'obstacle et ajustez votre poids pour descendre proprement de l\'autre côté.', 'boardslide'),
(33, 21, NULL, 'Double Cork', 'Rotation avec deux flips (corkscrew) et plusieurs rotations horizontales.\r\n\r\nPour réaliser un Double Cork, commencez par choisir un kick ou un saut avec suffisamment d\'élévation pour exécuter une double rotation. Approchez l\'obstacle avec vitesse et fléchissez légèrement vos genoux pour maintenir une bonne stabilité. Au moment de décoller, engagez-vous vers l\'avant tout en gardant votre regard sur le point de réception. Lorsque vous êtes en l\'air, initiez la première rotation en inclinant votre épaule et votre tête dans la direction de la rotation. Poussez vos pieds et utilisez vos bras pour générer la rotation tout en gardant votre corps compact pour contrôler la vitesse de rotation. Pour la deuxième rotation, répétez le processus en ajustant votre position pour maintenir l\'équilibre et terminer la figure avec une réception propre.', 'double-cork'),
(34, 21, NULL, 'Misty Flip', 'Backflip avec une rotation horizontale (spin) de 540 ou 720 degrés.\r\n\r\nPour réaliser un Misty Flip, trouvez un kick ou un saut avec suffisamment de hauteur pour exécuter une rotation horizontale avec une vrille. Approchez l\'obstacle avec vitesse et fléchissez légèrement vos genoux pour maintenir une bonne stabilité. Au moment de décoller, engagez-vous vers l\'avant tout en gardant votre regard sur le point de réception. Lorsque vous êtes en l\'air, inclinez votre épaule et votre tête dans la direction de la rotation horizontale tout en initié une vrille. Poussez vos pieds et utilisez vos bras pour générer la rotation tout en gardant votre corps compact pour contrôler la vitesse de rotation. Pour terminer la figure, relâchez progressivement la pression, en alignant la planche avec la pente pour une réception en douceur.', 'misty-flip'),
(35, 22, NULL, 'Backside Air', 'Saut dans le half-pipe où le rider tourne le dos à la paroi tout en effectuant une rotation.\r\n\r\nPour réaliser un Backside Air dans un half-pipe, prenez de la vitesse et approchez la paroi avec confiance. Juste avant de monter sur la lèvre, fléchissez vos genoux et ajustez votre position pour maximiser la hauteur. Lorsque vous montez sur la paroi, tournez votre épaule arrière vers la paroi tout en gardant votre regard fixé sur le point de réception. Initiez le saut en poussant sur vos pieds et en soulevant votre corps pour maximiser la hauteur de l\'air. Pendant le vol, maintenez une position compacte et contrôlée pour stabiliser votre rotation. Anticipez l\'atterrissage en pliant les genoux pour amortir l\'impact et alignez doucement la planche avec la pente pour une réception propre.', 'backside-air'),
(36, 22, NULL, 'McTwist', 'Rotation de 540 degrés avec une vrille (twist) sur l\'axe longitudinal du snowboard.\r\n\r\nPour réaliser un McTwist, prenez de la vitesse et approchez la paroi du half-pipe avec confiance. Juste avant de monter sur la lèvre, fléchissez vos genoux et ajustez votre position pour maximiser la hauteur. Lorsque vous montez sur la paroi, tournez votre épaule arrière vers la paroi tout en gardant votre regard fixé sur le point de réception. Initiez la rotation en inclinant votre tête et vos épaules dans la direction opposée à votre épaule arrière, tout en poussant vos pieds pour générer la rotation. Gardez votre corps compact et contrôlé pour stabiliser la rotation pendant le vol. Anticipez l\'atterrissage en pliant les genoux pour amortir l\'impact et alignez doucement la planche avec la pente pour une réception en douceur.', 'mctwist'),
(37, 23, NULL, 'Butter', 'Rotation du snowboard tout en gardant la planche en contact avec la neige, souvent en effectuant une rotation de 180 ou 360 degrés.\r\n\r\nPour réaliser un Butter, commencez par choisir une surface plane ou une petite inclinaison dans le snowpark. Prenez de la vitesse et approchez l\'obstacle avec confiance. Au moment du décollage, fléchissez vos genoux et transférez votre poids vers l\'avant tout en inclinant légèrement votre épaule du côté où vous souhaitez faire tourner la planche. Engagez le mouvement en appuyant sur le tail ou le nose de la planche avec votre pied arrière ou avant, respectivement, tout en gardant votre équilibre avec l\'autre main. Maintenez le mouvement de rotation fluide et contrôlé tout en gardant votre regard sur la direction de déplacement. Pour terminer le Butter, relâchez progressivement la pression et revenez à une position neutre pour maintenir votre équilibre et continuer votre trajet.', 'butter'),
(38, 23, NULL, 'Press', 'Maintien de la planche dans une position flexion prolongée sur un rail ou une surface plane.\r\n\r\nPour réaliser un Press, trouvez un rail ou une surface plane dans le snowpark. Approchez l\'obstacle avec vitesse et fléchissez légèrement vos genoux pour maintenir une bonne stabilité. Au moment de glisser sur l\'obstacle, transférez votre poids vers l\'avant ou l\'arrière de la planche, en fléchissant vos jambes pour maintenir une pression constante sur le rail ou la surface. Utilisez vos bras pour garder l\'équilibre tout en ajustant la position de votre corps pour maintenir le Press. Pour terminer, relâchez progressivement la pression et revenez à une position neutre pour terminer la glissade.', 'press');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` varchar(180) NOT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '(DC2Type:json)' CHECK (json_valid(`roles`)),
  `password` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `picture_slug` varchar(255) DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL,
  `reset_token` varchar(100) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp() COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `username`, `first_name`, `last_name`, `picture_slug`, `is_verified`, `reset_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(6, 'honore.marchand@orange.fr', '[]', '$2y$13$6KjYXzW/s24/.GMIMsJJAe.swCGrvEx9C3PatDGmirEByo8FUM6lu', 'anne-humbert', 'Marthe', 'LEGROS', NULL, 0, 'd2UxLPzQTR5RLEY5fyHB', '2024-05-17 10:17:51', NULL, NULL),
(7, 'eugene11@mercier.com', '[]', '$2y$13$VVoNd6duAYhM9SMGM2CWVOWlnWOzdaf.C42EmqrTzhgXShH2MZh5m', 'igros', 'Daniel', 'GODARD', NULL, 0, 'ryhYcYFUa0NUzSKlkt60', '2024-05-17 10:17:51', NULL, NULL),
(8, 'bernadette.picard@laposte.net', '[]', '$2y$13$YHHClNifMcRczozKRM4mju2a0ecx8E9okqzLEK0aHJFMgjGjgf6PC', 'dorothee-benoit', 'Alphonse', 'LENOIR', NULL, 0, '3hZed4FRDRl5NckvPhjm', '2024-05-17 10:17:52', NULL, NULL),
(9, 'hebert.timothee@wanadoo.fr', '[]', '$2y$13$241EHsnrc5Q.Z6r3ogbMde1fzD9RvoEgMctE7bL5r5XIF.8nOJZly', 'noel-bodin', 'Guy', 'OLIVIER', NULL, 0, 'KBLDIT1m1RSMGH6ZsxsZ', '2024-05-17 10:17:52', NULL, NULL),
(10, 'ldupuis@cohen.com', '[]', '$2y$13$joEh/WPBdgoleStTmY/d7ezvimAdZg4tSxkJ/VbnIxrD260kNFeR.', 'agathe-colin', 'Lucas', 'SAMSON', NULL, 0, 'cfc3Z8ZiwHicwUwLIG4j', '2024-05-17 10:17:52', NULL, NULL),
(13, 'meline.pischedda@gmail.com', '[]', '$2y$13$KRqma14HV/U2BfIpvvCN/.3q8AZ.ToiUbYu17fkIVcguTaCDAxnm.', 'melinep', 'Méline', 'PISCHEDDA', '665a150c529b8_13.avif', 1, '', '2024-05-17 12:58:32', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `user_trick`
--

CREATE TABLE `user_trick` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `trick_id` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `operation` enum('create','update','delete') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user_trick`
--

INSERT INTO `user_trick` (`id`, `user_id`, `trick_id`, `date`, `operation`) VALUES
(115, 13, 27, '2024-07-05 13:16:17', 'create'),
(116, 13, 28, '2024-07-05 13:16:45', 'create'),
(117, 13, 29, '2024-07-05 13:17:11', 'create'),
(118, 13, 30, '2024-07-05 13:17:37', 'create'),
(119, 13, 31, '2024-07-05 13:18:16', 'create'),
(120, 13, 32, '2024-07-05 13:18:48', 'create'),
(121, 13, 33, '2024-07-05 13:19:08', 'create'),
(122, 13, 34, '2024-07-05 13:19:27', 'create'),
(123, 13, 35, '2024-07-05 13:19:52', 'create'),
(124, 13, 36, '2024-07-05 13:20:18', 'create'),
(125, 13, 27, '2024-07-05 13:21:42', 'update'),
(126, 13, 28, '2024-07-05 13:22:07', 'update'),
(127, 13, 28, '2024-07-05 13:22:14', 'update'),
(128, 13, 29, '2024-07-05 13:24:34', 'update'),
(129, 13, 30, '2024-07-05 13:25:02', 'update'),
(130, 13, 37, '2024-07-05 13:26:04', 'create'),
(131, 13, 38, '2024-07-05 13:26:38', 'create'),
(132, 13, 37, '2024-07-05 13:27:17', 'update'),
(133, 13, 31, '2024-07-05 13:27:54', 'update'),
(134, 13, 32, '2024-07-05 13:28:08', 'update'),
(135, 13, 33, '2024-07-05 13:28:25', 'update'),
(136, 13, 34, '2024-07-05 13:28:38', 'update'),
(137, 13, 35, '2024-07-05 13:28:55', 'update'),
(138, 13, 36, '2024-07-05 13:29:09', 'update'),
(139, 13, 29, '2024-07-05 13:35:02', 'update'),
(142, 13, 27, '2024-07-05 13:51:57', 'update'),
(143, 13, 27, '2024-07-05 13:52:10', 'update'),
(144, 13, 28, '2024-07-05 13:52:27', 'update'),
(145, 13, 29, '2024-07-05 13:52:42', 'update'),
(146, 13, 30, '2024-07-05 13:52:56', 'update'),
(147, 13, 31, '2024-07-05 13:53:08', 'update'),
(148, 13, 32, '2024-07-05 13:53:20', 'update'),
(149, 13, 33, '2024-07-05 13:53:32', 'update'),
(150, 13, 34, '2024-07-05 13:53:44', 'update'),
(151, 13, 34, '2024-07-05 13:54:00', 'update'),
(152, 13, 35, '2024-07-05 13:56:40', 'update'),
(153, 13, 36, '2024-07-05 13:57:01', 'update'),
(154, 13, 37, '2024-07-05 13:57:16', 'update'),
(155, 13, 38, '2024-07-05 13:57:32', 'update'),
(156, 13, 33, '2024-07-05 14:31:28', 'update'),
(157, 13, 33, '2024-07-05 14:36:11', 'update'),
(158, 13, 27, '2024-07-05 14:36:40', 'update'),
(159, 13, 28, '2024-07-05 14:37:12', 'update'),
(160, 13, 29, '2024-07-05 14:37:45', 'update'),
(161, 13, 30, '2024-07-05 14:38:33', 'update'),
(162, 13, 31, '2024-07-05 14:39:04', 'update'),
(163, 13, 32, '2024-07-05 14:39:39', 'update'),
(164, 13, 34, '2024-07-05 14:40:28', 'update'),
(165, 13, 35, '2024-07-05 14:41:12', 'update'),
(166, 13, 36, '2024-07-05 14:41:42', 'update'),
(167, 13, 37, '2024-07-05 14:42:22', 'update'),
(168, 13, 38, '2024-07-05 14:43:21', 'update');

-- --------------------------------------------------------

--
-- Structure de la table `video`
--

CREATE TABLE `video` (
  `id` int(11) NOT NULL,
  `trick_id` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp() COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `video`
--

INSERT INTO `video` (`id`, `trick_id`, `url`, `created_at`, `updated_at`, `deleted_at`) VALUES
(19, 33, 'https://www.youtube.com/watch?v=_3C02T-4Uug', '2024-07-05 14:36:11', NULL, NULL),
(20, 27, 'https://www.youtube.com/watch?v=XKoj-e52w30', '2024-07-05 14:36:40', NULL, NULL),
(21, 28, 'https://www.youtube.com/watch?v=gbjwHZDaJLE', '2024-07-05 14:37:12', NULL, NULL),
(22, 29, 'https://www.youtube.com/watch?v=lD_kj-sD2dY', '2024-07-05 14:37:45', NULL, NULL),
(23, 30, 'https://www.youtube.com/watch?v=6iq3ZkdHxUM', '2024-07-05 14:38:33', NULL, NULL),
(24, 31, 'https://www.youtube.com/watch?v=e-7NgSu9SXg', '2024-07-05 14:39:04', NULL, NULL),
(25, 32, 'https://www.youtube.com/watch?v=gRZCF5_XRsA', '2024-07-05 14:39:39', NULL, NULL),
(26, 34, 'https://www.youtube.com/watch?v=hPuVJkw1MmI', '2024-07-05 14:40:28', NULL, NULL),
(27, 35, 'https://www.youtube.com/watch?v=_CN_yyEn78M', '2024-07-05 14:41:12', NULL, NULL),
(28, 36, 'https://www.youtube.com/watch?v=hgy-Ff2DS6Y', '2024-07-05 14:41:42', NULL, NULL),
(29, 37, 'https://www.youtube.com/watch?v=azUFH79x_lY', '2024-07-05 14:42:22', NULL, NULL),
(30, 38, 'https://www.youtube.com/watch?v=Z1gCwhmTV7A', '2024-07-05 14:43:21', NULL, NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_9474526CB281BE2E` (`trick_id`),
  ADD KEY `IDX_9474526CA76ED395` (`user_id`);

--
-- Index pour la table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `image`
--
ALTER TABLE `image`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_C53D045FB281BE2E` (`trick_id`);

--
-- Index pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  ADD KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  ADD KEY `IDX_75EA56E016BA31DB` (`delivered_at`);

--
-- Index pour la table `trick`
--
ALTER TABLE `trick`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_D8F0A91E5E237E06` (`name`),
  ADD UNIQUE KEY `UNIQ_D8F0A91ED80E7B11` (`promote_image_id`),
  ADD KEY `IDX_D8F0A91E12469DE2` (`category_id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`),
  ADD UNIQUE KEY `UNIQ_IDENTIFIER_USERNAME` (`username`);

--
-- Index pour la table `user_trick`
--
ALTER TABLE `user_trick`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_3A325246A76ED395` (`user_id`),
  ADD KEY `IDX_3A325246B281BE2E` (`trick_id`);

--
-- Index pour la table `video`
--
ALTER TABLE `video`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_7CC7DA2CB281BE2E` (`trick_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT pour la table `comment`
--
ALTER TABLE `comment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT pour la table `image`
--
ALTER TABLE `image`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;

--
-- AUTO_INCREMENT pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `trick`
--
ALTER TABLE `trick`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `user_trick`
--
ALTER TABLE `user_trick`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=169;

--
-- AUTO_INCREMENT pour la table `video`
--
ALTER TABLE `video`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `FK_9474526CA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_9474526CB281BE2E` FOREIGN KEY (`trick_id`) REFERENCES `trick` (`id`);

--
-- Contraintes pour la table `image`
--
ALTER TABLE `image`
  ADD CONSTRAINT `FK_C53D045FB281BE2E` FOREIGN KEY (`trick_id`) REFERENCES `trick` (`id`);

--
-- Contraintes pour la table `trick`
--
ALTER TABLE `trick`
  ADD CONSTRAINT `FK_D8F0A91E12469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`),
  ADD CONSTRAINT `FK_D8F0A91ED80E7B11` FOREIGN KEY (`promote_image_id`) REFERENCES `image` (`id`);

--
-- Contraintes pour la table `user_trick`
--
ALTER TABLE `user_trick`
  ADD CONSTRAINT `FK_3A325246A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_3A325246B281BE2E` FOREIGN KEY (`trick_id`) REFERENCES `trick` (`id`);

--
-- Contraintes pour la table `video`
--
ALTER TABLE `video`
  ADD CONSTRAINT `FK_7CC7DA2CB281BE2E` FOREIGN KEY (`trick_id`) REFERENCES `trick` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
