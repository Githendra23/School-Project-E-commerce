-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 19, 2023 at 11:02 PM
-- Server version: 8.0.31
-- PHP Version: 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `site_e-commerce`
--

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
CREATE TABLE IF NOT EXISTS `clients` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `NOM` varchar(25) NOT NULL,
  `PRENOM` varchar(25) NOT NULL,
  `EMAIL` varchar(45) NOT NULL,
  `MDP` varchar(45) NOT NULL,
  `CIVILITE` varchar(10) NOT NULL,
  `PANIER_PANIER ID` int NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `fk_CLIENTS_PANIER_idx` (`PANIER_PANIER ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `panier`
--

DROP TABLE IF EXISTS `panier`;
CREATE TABLE IF NOT EXISTS `panier` (
  `PANIER_ID` int NOT NULL AUTO_INCREMENT,
  `NOM` varchar(25) NOT NULL,
  `HEURE_DE_RETRAIT` varchar(45) NOT NULL,
  `HEURE_DE_COMMANDE` varchar(45) NOT NULL,
  `MONTANT_TOTAL` float DEFAULT NULL,
  PRIMARY KEY (`PANIER_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `panier_elements`
--

DROP TABLE IF EXISTS `panier_elements`;
CREATE TABLE IF NOT EXISTS `panier_elements` (
  `QUANTITE_POIDS` int NOT NULL,
  `QUANTITE_UNITE` int NOT NULL,
  `PANIER_PANIER ID` int NOT NULL,
  `STOCK_PRODUIT ID` int NOT NULL,
  KEY `fk_PANIER_ELEMENTS_PANIER1_idx` (`PANIER_PANIER ID`),
  KEY `fk_PANIER_ELEMENTS_STOCK1_idx` (`STOCK_PRODUIT ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `produit`
--

DROP TABLE IF EXISTS `produit`;
CREATE TABLE IF NOT EXISTS `produit` (
  `PRODUIT_ID` int NOT NULL AUTO_INCREMENT,
  `NOM_PRODUIT` varchar(45) NOT NULL,
  `PRIX_UNITAIRE` float NOT NULL,
  `POIDS` int NOT NULL,
  `STOCK` int DEFAULT NULL,
  `TYPE` varchar(25) NOT NULL,
  `URL` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`PRODUIT_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `produit`
--

INSERT INTO `produit` (`PRODUIT_ID`, `NOM_PRODUIT`, `PRIX_UNITAIRE`, `POIDS`, `STOCK`, `TYPE`, `URL`) VALUES
(18, 'Concombre', 1, 1, 200, 'périssables', 'https://static.wikia.nocookie.net/house-party/images/5/59/Cucumber.png'),
(19, 'Tomate', 1, 1, 200, 'périssables', 'https://pngimg.com/d/tomato_PNG12511.png'),
(20, 'Oignon', 1, 1, 200, 'périssables', 'https://pngimg.com/d/onion_PNG99190.png'),
(21, 'Carrote', 1, 1, 200, 'périssables', 'https://www.transparentpng.com/thumb/carrot/AciY35-carrot-transparent-picture.png'),
(22, 'Pommes de terre', 1, 1, 200, 'périssables', 'https://www.lespommesdeterre.com/wp-content/themes/cnipt-theme/img/pages/agata.png'),
(23, 'Pomme', 1, 1, 200, 'périssables', 'https://www.pngall.com/wp-content/uploads/11/Red-Apple-PNG.png');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `clients`
--
ALTER TABLE `clients`
  ADD CONSTRAINT `fk_CLIENTS_PANIER` FOREIGN KEY (`PANIER_PANIER ID`) REFERENCES `panier` (`PANIER_ID`);

--
-- Constraints for table `panier_elements`
--
ALTER TABLE `panier_elements`
  ADD CONSTRAINT `fk_PANIER_ELEMENTS_PANIER1` FOREIGN KEY (`PANIER_PANIER ID`) REFERENCES `panier` (`PANIER_ID`),
  ADD CONSTRAINT `fk_PANIER_ELEMENTS_STOCK1` FOREIGN KEY (`STOCK_PRODUIT ID`) REFERENCES `produit` (`PRODUIT_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
