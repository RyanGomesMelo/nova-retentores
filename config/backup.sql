-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 07, 2025 at 05:14 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `teste_retentoresvan`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `name`, `email`, `password`) VALUES
(3, 'Ryan Gomes', 'ryangomesmelo123@gmail.com', '$2y$10$lV.ODDyfx9vs1DjVjJj4XuXBP5uvoCVGMyYThjZwpSOAzKFyNUogu'),
(7, 'Ivan Gomes', 'ivan', '$2y$10$4ByrYunAAjHSF/RBQalyJu7bcMDW.IhIkWFqS1Ck21v28HxC1zQby'),
(8, 'Ivan Gomes', 'ivandemolicao@gmail.com', '$2y$10$pMEb4LGWI75sx19XoCDcYuSpFTJdx7KEqJ4EAB7IjGW8VXYDcuIvi');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `cat_id` int(11) NOT NULL,
  `cat_name` varchar(200) NOT NULL,
  `parent_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`cat_id`, `cat_name`, `parent_id`) VALUES
(1, 'Amortecedores', 0),
(2, 'Hastes', 1),
(3, 'Haste 10', 2),
(4, 'Haste 12', 2),
(25, 'Batentes Internos', 0),
(28, 'Caixa De Direção', 0),
(35, 'Haste 20', 2),
(36, 'Haste 22', 2),
(37, 'Haste 25', 2),
(38, 'Haste 28', 2),
(39, 'Retentores Para Motos', 0),
(40, 'Roletes', 0),
(41, 'Haste 17 e 18', 2),
(42, 'Haste 14 à 15,8', 2),
(44, 'Buchas/Guias/Bronzinas', 0);

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `prod_name` varchar(100) NOT NULL,
  `prod_description` text NOT NULL,
  `prod_price` decimal(10,2) NOT NULL,
  `cat_id` int(11) NOT NULL,
  `prod_id` int(11) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`prod_name`, `prod_description`, `prod_price`, `cat_id`, `prod_id`, `image_path`) VALUES
('Rolete 0152', 'Rolete 25,00x52,00/55,00x2,00/15,00.', 0.00, 40, 1, '0152 ROLETE.jpeg'),
('Rolete 0176', 'Rolete de dimensões de 15,00x34,95x10 mm.', 2.00, 40, 2, '0176 ROLETE.jpeg'),
('Rolete 0186', 'O Rolete 0186 possui dimensões de 20,00x47,00x10,00mm.', 2.00, 40, 3, '0186 ROLETE.jpeg'),
('Rolete 0328', 'O Rolete 0328 possui as dimensões 20,00x47,00x3,00x2,00 mm.', 2.00, 40, 4, '0328 ROLETE.jpeg'),
('Rolete 0645', 'O Rolete 0645 apresenta as dimensões 30,00x72,00x9,50/12,50 mm.', 2.00, 40, 5, '0645.jpeg'),
('Retentor Para Moto 0307 | Honda', 'O Retentor de Moto 0307 possui as dimensões 27,00x39,00x10,50 mm.', 2.00, 39, 6, '0307 MOTO.jpg'),
('Retentor Para Moto 0366', 'O Retentor 0366 para motos apresenta as dimensões 22,00x35,00x7,00 mm.', 2.00, 39, 7, '0366 MOTO.jpg'),
('Retentor 0547', 'O Retentor 0547 possui dimensões 28,00x52,00x6,00/9,00 mm ', 2.00, 39, 8, '0547 MOTO.jpg'),
('Retentor 0739 | Honda ', 'O Retentor 0739 apresenta as dimensões 10,00x30,00x12,00 mm.', 2.00, 39, 9, '0739 MOTO.jpg'),
('Retentor 0740 | Honda', 'O Retentor 0740 possui dimensões 10,00x28,00x13,40 mm', 2.00, 39, 10, '0740 MOTO.jpg'),
('Retentor 0741 | Yamaha', 'O Retentor 0741 tem as dimensões 8,00x28,00x9,00 mm ', 2.00, 39, 11, '0741 MOTO.jpg'),
('Retentor 1078 | Harley Davidson', 'O Retentor 1078 possui as dimensões 36,00x66,00x10,50 mm.', 2.00, 39, 12, '1078 MOTO.jpg'),
('Retentor 1195 | Honda', 'O Retentor 1195 possui as dimensões 31,00x43,00x10,30 mm.', 2.00, 39, 13, '1195 moto.jpeg'),
('Retentor 1196 | Yamaha ', 'O Retentor 1196 possui as dimensões 33,00x45,00x8,00/10,00 mm.', 2.00, 39, 14, '1196 MOTO.jpeg'),
('Retentor 1197 | Honda & Suzuki', 'O Retentor 1197 possui as dimensões 37,00x50,00/54,50 x 5,50/14,80 mm.', 2.00, 39, 15, '1197 - 1198 MOTO.jpeg'),
('Retentor 1198 | Honda & Suzuki', 'O Retentor 1198 tem as dimensões 37,00x50,00x11,00 mm.', 2.00, 39, 16, '1198 MOTO.jpeg'),
('Retentor 1224 ', 'O Retentor 1224 possui as dimensões 12,70x24,00x5,00 mm.', 2.00, 39, 17, '1224 MOTO.jpeg'),
('Retentor 0632 | Renault', 'Dimensões:28x53x7/11\r\nAplicação:Renault Master / Ducato', 2.00, 38, 18, '0632.jpg'),
('Retentor 1176 | FIAT', 'Dimensões: 28x57x6,15/10,20\r\nAplicações: Diant, Fiat Ducato', 2.00, 38, 19, '1176.jpg'),
('Retentor 1183 | Land Rover', 'Dimensões: 28,00x 59,20 x 6,30/15,00', 2.00, 38, 20, '1183.jpeg'),
('Retentor 1082 | Sprinter', 'Dimensões: 28,00x48,00x5,00/10,50', 2.00, 38, 21, '1082.jpg'),
('Retentor 1165 | Honda', 'Dimensões: 25,00x 51,00 x 4,00/9,00\r\nAplicações: Honda Civic', 2.00, 37, 22, '1165.jpg'),
('Retentor 1181 | Volkswagen', 'Dimensões: 25x45,25x5,5/11\r\nAplicações: Diant, Jetta', 2.00, 37, 23, '1181.jpeg'),
('Retentor 1184 | Land Rover', 'Dimensões: 25x00x51,50x10,50\r\nAplicações: Range Rover', 2.00, 37, 24, '1184.jpeg'),
('Retentor 1191 | BRGO', 'Dimensões: 25,00x54,00x11,20\r\nAplicações: Dianteiro - BRGO', 2.00, 37, 25, '1191.jpg'),
('Retentor 1222 | Bmw', 'Dimensões: 25,00x49,50x5,20\r\nAplicações: BMW X1', 2.00, 37, 26, '1222.jpeg'),
('Retentor 1252 | Chevrolet', 'Dimensões: 25,00x52,60x 3,90/1310', 2.00, 37, 27, '1252.jpeg'),
('Retentor 023 ', 'Dimensões: 22x35x10\r\nAplicação: Ônibus E Caminhões ', 2.00, 36, 28, '023.jpg'),
('Retentor 0463 | U20', 'Dimensões: 22,00x41,00x5,50/14,00', 2.00, 36, 29, '0463.jpg'),
('Retentor 1055 ', 'Dimensões: 22,00x33,85x2,90/9,00\r\nAplicações: Santana, Monza, Omega, Marea, Brava, Punto, Stilo, Doblo, Palio, Gol G%.', 2.00, 36, 30, '1055.jpeg'),
('Retentor 1056 | Monza E Santana', 'Dimensões: 22,00x34,00x4,00/9,00\r\nAplicações: Monza, Santana (Cofap).', 2.00, 36, 31, '1056.jpg'),
('Retentor 1062 ', 'Dimensões: 22,00x35,00x3,50/9,50\r\nAplicação: Tempra, Monza, Kadet, Santana, Uno, Elba, Prêmio, Fiorino. (monroe)', 2.00, 36, 32, '1062.jpg'),
('Retentor 1063', 'Dimensões: 22,00x34,00x3,50/8,70\r\nAplicações: Tempra, Santana, Monza, Kadet. (cofap)', 2.00, 36, 33, '1063.jpeg'),
('Retentor 1088', 'Dimensões: 22,00x35,60x3,80/10,00', 2.00, 36, 34, '1088.jpg'),
('Retentor 1089 ', 'Dimensões: 22,00x35,60x3,5/12,00', 2.00, 36, 35, '1089.jpg'),
('Retentor 1180', 'Dimensões: 22x48x4,3/9,00', 2.00, 36, 36, '1180.jpg'),
('Retentor 1182 | Bmw', 'Dimensões: 22x48x7/10,50\r\nAplicação: Bmw 320/318/120/118.', 2.00, 36, 37, '1182.jpg'),
('Retentor 1218 | Toyota', 'Dimensões: 22,00x46,50x5,50/10', 2.00, 36, 38, '1218.jpeg'),
('Retentor 1219 | Ford', 'Dimensões: 22,00x40,80x2,50/9,70\r\nAplicações: Ford Focus', 2.00, 36, 39, '1219.jpeg'),
('Retentor 0035', 'Dimensões: 20x34,85x3/10\r\nAplicação: Ônibus E Caminhões.', 2.00, 35, 40, '0035.jpg'),
('Retentor 217 ', 'Dimensões: 20x35x10\r\nAplicação: Ônibus e Caminhões.', 2.00, 35, 41, '0217.jpeg'),
('Retentor 616', 'Dimensões: 20x34,90x4,70/9,00', 2.00, 35, 42, '616.jpg'),
('Retentor 1052', 'Dimensões: 20,00x34,80x3,600x/10,00', 2.00, 35, 43, '1052.jpg'),
('Retentor 1053', 'Dimensões: 20,00x32,20x3,00/9,30\r\nAplicação: Uno, Elba, Prêmio, Fiorino.', 2.00, 35, 44, '1053.jpg'),
('Retentor 1054', 'Dimensões: 20,00x32,25x3,50/10,50\r\nAplicações: Escort, Gol, Corsa, Vectra e Palio', 2.00, 35, 45, '1054.jpg'),
('Retentor 1057', 'Descrição: 20,00x31,70x3,30/10,20', 2.00, 35, 46, '1057.jpg'),
('Retentor 1059', 'Dimensões: 20,00x32,00x3,00/10,70\r\nAplicação: Uno/ Prêmio / Elba', 2.00, 35, 47, '1059.jpg'),
('Retentor 1107', 'Dimensões: 20,00x33,25x3,50/9,00\r\nAplicação: Gol e Escort', 2.00, 35, 48, '1107.jpg'),
('Retentor 1112', 'Dimensões: 20,00x32,50x3,20/8,50\r\nAplicação: Gol, Logus, Astra, Corsa, Meriva, Vectra, Zafira, Uno, Palio Fire, Escort.', 2.00, 35, 49, '1112.jpeg'),
('Retentor 1148', 'Dimensões: 20,00x32,25x3,50/10,50\r\nAplicação: Cabine', 2.00, 35, 50, '1148.jpeg'),
('Retentores 1179', 'Dimensões: 20x34,3x4/12\r\nAplicação: Fiesta, Ford K.', 2.00, 35, 51, '1179.jpg'),
('Retentor 1188', 'Dimensões:20,00x41,00x5,50/14,00\r\nAplicação: Sistema Gardinotec\r\nApl', 2.00, 35, 52, '1188.jpeg'),
('Retentor 1203', 'Dimensões: 20,00x45,75x10,30\r\nAplicação: Hyundai I30, Velloster.', 2.00, 35, 54, '1203.jpg'),
('Retentor 1215', 'Dimensões: 20,00x40,80x5,00/9,50\r\nAplicação: Fiesta ', 2.00, 35, 55, '1215.jpeg'),
('Retentor 1272', 'Dimensões: 20,00x41,30x6,50/12,70\r\nAplicação: Gol, Corsa, Celta, Prisma.', 2.00, 35, 56, '1272.jpeg'),
('Retentor 0092', 'Dimensões: 15,80x32,00x8,00', 2.00, 42, 57, '0092.jpeg'),
('Retentor 1051', 'Dimensões: 15,80x31,50x10,00', 2.00, 42, 58, '1051.jpg'),
('Retentor 1064', 'Dimensões: 14,20x27,10x10,00\r\nAplicação: Pampa', 2.00, 42, 59, '1064.jpg'),
('Retentor 1066', 'Dimensões: 15,80x35,80x9,20\r\nAplicação: Corcel, Pampa, Del Rey, Blazer, Marea.', 2.00, 42, 60, '1066.jpg'),
('Retentor 1129', 'Dimensões: 14,00x36,25x3,50/10,00\r\nAplicação: Peugeot 206/ 307, Renault / Citroen C3.', 2.00, 42, 61, '1129.jpg'),
('Retentor 1187', 'Dimensões: 15,80x35,80x6,50x12\r\nAplicação: Ford Ranger, Montana.', 2.00, 42, 62, '1187.jpg'),
('Retentor 1212 ', 'Dimensões: 15,8x31,20x3,00/12,00\r\nAplicação: Kwid', 2.00, 42, 63, '1212.jpeg'),
('Retentor 1213', 'Dimensão: 15,8x36,80x2,70/9,70\r\nAplicação: Amarok', 2.00, 42, 64, '1213.jpeg'),
('Retentor 1238', 'Dimensões: 15,80x33,50x10,00\r\nAplicação: Troller.', 2.00, 42, 65, '1238.jpeg'),
('Retentor 0141', 'Dimensões: 12,40x24,60x7,10\r\nAplicação: Corsa, Gol, Saveiro.', 2.00, 4, 66, '0141.jpg'),
('Retentor 0726', 'Dimensões: 12,50x42,9x5,50/9,70\r\nAplicação: Renault Press.', 2.00, 4, 67, '0726.jpeg'),
('Retentor 0907', 'Dimensões: 12,40x31,72x3,40/8,50\r\nAplicação: Golf Vectra 97.', 2.00, 4, 68, '0907.jpeg'),
('Retentor 1060', 'Dimensões: 12,40x24,50x8,00\r\nAplicação: Fusca, Gol, Kombi, Passat', 2.00, 4, 69, '1060.jpg'),
('Retentor 1061', 'Dimensões: 12,40x27,50x8,00/10,00\r\nAplicação: Opala, Corsa , Omega, Astra, Meriva, Logus, Escort 93, Clio, Ka , Fiesta', 2.00, 4, 70, '1061.jpg'),
('Retentor 1065', 'Dimensões: 12,40x26,30x7,50/9,60\r\nAplicações: Santana, Quantun, Vectra.', 2.00, 4, 71, '1065.jpg'),
('Retentor 1118', 'Dimensões: 12,40x27,60x6,20/10,80\r\nAplicação: Opala, Corsa, Omega, Astra, Meriva, Logus, Escort 93 , Clio, Ka, Fiesta.', 2.00, 4, 72, '1118.jpg'),
('Retentor 1119', 'Dimensões: 12,40x26,30x6,00/10,80\r\nAplicação: Santana, Quantun, Vectra.', 2.00, 4, 73, '1119.jpg'),
('Retentor 1171', 'Dimensões: 12,00x35,00x3,60/10,00\r\nAplicação: Gol G5, G6, Voyage.', 2.00, 4, 74, '1171.jpg'),
('Retentor 1172', 'Dimensões: 12,40x33,50x10,00\r\nAplicação: Ford Focus.', 2.00, 4, 75, '1172.jpg'),
('Retentor 1079', 'Dimensão: 10,80x 28,60x2,50/9,70\r\nAplicação: Fox/CrossFox', 2.00, 3, 76, '1079.jpg'),
('Retentor 1093', 'Dimensões: 10,80x31,50x2,50/9,70', 2.00, 3, 77, '1093.jpg'),
('Retentor 1174', 'Dimensões: 10,80x31,50x2,50/9,70\r\nAplicação: Bwm, Audi A3, Golf, EcoSport, Fiesta.', 2.00, 3, 78, '1174.jpg'),
('Retentor 1220', 'Dimensões: 10,80x32,00x2,50/9,70\r\nAplicação: Golf Moderno.', 2.00, 3, 79, '1220.jpeg'),
('Retentor 0032', 'Dimensões: 18x30x7\r\nAplicação: Ford F1000, Golf Apollo, Fox, Pointer, Polo, Santana, Gol, Saveiro.', 2.00, 28, 80, '0032.jpg'),
('Retentor 0113', 'Dimensão: 20,00x35,00x8,00\r\nAplicação: Opala, Caravan.', 2.00, 28, 81, '0113.jpeg'),
('Retentor 0149', 'Dimensões: 55,00x80,00x10,00\r\nAplicação: Scana 110.', 2.00, 28, 82, '0149.jpeg'),
('Retentor 0182', 'Dimensões: 23x34,50x8,00\r\nAplicação: VW Golf', 2.00, 28, 83, '0182 NY.jpeg'),
('Retentor 0192', 'Dimensões: 19x39,75x6,30\r\nAplicação: FND.', 2.00, 28, 84, '0192.jpeg'),
('Retentor 0247', 'Dimensões: 28,00x40,60/48,00x5,00\r\nAplicação: Ford Cargo.', 2.00, 28, 85, '0247..jpeg'),
('Retentor 0285', 'Dimensões: 25,00x35,00x7,00\r\nAplicação: Bomba Caixa de Direção Hidraulica', 2.00, 28, 86, '0285.jpeg'),
('Retentor 0309', 'Dimensões: 26,80x36,90x3,40/6,80\r\nAplicação: Trinther, FND, Escort.', 2.00, 28, 87, '0309.jpg'),
('Retentor 0327', 'Dimensões: 42,00x52,00x5,00\r\nAplicação: Caixa de Direção', 2.00, 28, 88, '0327..jpeg'),
('Retetentor 0342', 'Dimensões: 23x34,50x6,40\r\nAplicação: Fiat Palio.', 2.00, 28, 89, '0342NY.jpeg'),
('Retentor 0377', 'Dimensões: 30,00x44,50x7,00\r\nAplicação: Chrysler Dakota, GM D200, Jeep Cherokee, MBB Sprinter.', 2.00, 28, 90, '0377NY.jpeg'),
('Retentor 0380', 'Dimensões: 31,10x43,55x6,50\r\nAplicação: Ford Explorer e Ranger.', 2.00, 28, 91, '0380NY.jpeg'),
('Retentor 0381', 'Dimensões: 30,00x40/48x8,30\r\nAplicação: Ford Explorer e Ford Ranger.', 2.00, 28, 92, '0381NY.jpeg'),
('Retentor 0383', 'Dimensões: 25x39,25x10/12,50\r\nAplicação: Fiat Uno, Prêmio, Elba.', 2.00, 28, 93, '0383NY.jpeg'),
('Retentor 0386', 'Dimensões: 20,60x41,20x6,50\r\nAplicação: Fiat Palio , Palio Weekend.', 2.00, 28, 94, '0386.jpg'),
('Retentor 0407', 'Dimensões: 21,40x39,00x1000\r\nAplicação: Ford Escort, Verona.', 2.00, 28, 95, '0407NY.jpeg'),
('Retentor 0412', 'Dimensões: 25,00x40,00x8,00\r\nAplicação: Alfa 164.', 2.00, 28, 96, '0412.jpeg'),
('Retentor 0468', 'Dimensões: 32,00x42,00x6,00\r\nAplicação: Caixa de Direção, Diversos.', 2.00, 28, 97, '0471.jpg'),
('Retentor 0471', 'Dimensão: 20,00x42,00x7,00\r\nAplicação: Alfa 200', 2.00, 28, 98, '0471.jpg'),
('Retentor 0574', 'Dimensões: 27,00x38,40x5,70\r\nAplicação: Ford 1000.', 2.00, 28, 99, '0574.jpg'),
('Retentor 0575', 'Dimensões: 19,05x34,64x6,30/9,00\r\nAplicação: Ford Escort, VW Gol G2, G3, G4, Parati, Saveiro / Fiat Palio, Idea, Strada, Doblo, Siena.', 2.00, 28, 100, '0575.jpg'),
('Retentor 0576', 'Dimensões: 23,30x29,70x4,00\r\nAplicação: Ford Escort, VW Apollo, Logus, Gol, Parati, Passat, Santana, Saveiro e Voyage.', 2.00, 28, 101, '0576.jpeg'),
('Retentor 0580', 'Dimensão: 25x35x3,80/7,50\r\nAplicação: Kadett, Monza.', 2.00, 28, 102, '0580NY.jpeg'),
('Retentor 0586', 'Dimensões: 24,00x33,00x7,00\r\nAplicação: Ford Escort.', 2.00, 28, 103, '0586.jpeg'),
('Retentor 0604', 'Dimensões: 32,00x42,00x5,00\r\nAplicação: Toyota Hilux.', 2.00, 28, 104, '0604.jpg'),
('Retentor 0737', 'Dimensões: 25,4x41,8/37,45x7,5/3,2\r\nAplicação: VW Logus, Pointer, Verona, Ford Escort.', 2.00, 28, 105, '0737NY.jpeg'),
('Retentor 0755', 'Dimensões: 20,00x39,75x6,30\r\nAplicação: Celtar , Corsa, Astra...', 2.00, 28, 106, '0755.jpg'),
('Retentor 0805', 'Dimensões: 28,00x39,40x6,40\r\nAplicação: FND.', 2.00, 28, 107, '0805..jpeg'),
('Retentor 0852', 'Dimensões: 25,00x47,00x10,00\r\nAplicação: Fiat 70, 130.', 2.00, 28, 108, '0852.jpg'),
('Retentor 0992', 'Dimensões: 25,00x37,8x6,3\r\nAplicação: Fiat Palio.', 2.00, 28, 109, '0992NY.jpg'),
('Retentor 0994', 'Dimensões: 29,00x41,00x8,00\r\nAplicação: Ford Ka, Fiesta, Mondeo.', 2.00, 28, 110, '0994.jpg'),
('Retentor 1009', 'Dimensões: 21x35,50x6,00/7,20\r\nAplicação: Ford Escort, VW Logus, Pointer.', 2.00, 28, 111, '1009.jpeg'),
('Retentor 1207', 'Dimensão: 19,00x30,00x6,00\r\nAplicação: Gol G5, Fox.', 2.00, 28, 112, '1207.jpeg'),
('Retentor 1208', 'Dimensões: 24,00x35,00x8,00\r\nAplicação: Gol G5, Fox.', 2.00, 28, 113, '1208.jpeg'),
('Retentor 1209', 'Dimensões: 24,00x37,00x8,00\r\nAplicação: Fox, Gol G5, Audi A3.', 2.00, 28, 114, '1209.jpeg'),
('Retentor 1210', 'Dimensões: 24,00x38,00x7,00\r\nAplicação: Gol G5, Fox.', 2.00, 28, 115, '1210.jpeg'),
('Retentor 1221', 'Dimensões: 19,00x33,30x8,00\r\nAplicação: Peugeot', 2.00, 28, 116, '1221.jpeg'),
('Retentores 1223', 'Dimensões: 23,00x34,50x6,00\r\nAplicação: Palio, Gol.', 2.00, 28, 117, '1223NY.jpeg'),
('Retentores 1226', 'Dimensões: 22,90x30,00x5,00\r\nAplicação: Gol G2, G3, G4.', 2.00, 28, 118, '1226.jfif'),
('Retentor 1228', 'Dimensões: 12,00x26,80x10,00/10,50\r\nAplicação: Monza, Kadett, Ipanema.', 2.00, 28, 119, '1228BBRY.jpeg'),
('Retentores 1229', 'Dimensões: 21,00x35,00x8,00\r\nAplicação:  Gol G2, G3, G4.', 2.00, 28, 120, '1229NR.jfif'),
('Retentor 1230', 'Dimensões: 24,00x39,00x8,50\r\nAplicação: Audi A3.', 2.00, 28, 122, '1230NY.jpeg'),
('Retentor 1231', 'Dimensões: 28,00x37,00x5,00\r\nAplicação: Audi A3', 2.00, 28, 123, '1231.jpeg'),
('Retentor 1232', 'Dimensões: 24,00x33,00x5,00\r\nAplicação: Audi A3...', 2.00, 28, 124, '1232.jpeg'),
('Retentores 1233 e 1234', 'Dimensões: 24,00x37,00x17,50.', 2.00, 28, 125, '1233NY E 1234N.jpeg'),
('Retentor 1235', 'Dimensões: 22,00x32,00x4,00/6,00\r\nAplicação: Celta, Prisma, Corsa.', 2.00, 28, 126, '1235NY.jpeg'),
('Retentor 1236', 'Dimensões: 22,00x35,00x10,00/15,00\r\nAplicação: Corsa, Celta, Meriva, Montana.', 2.00, 28, 127, '1236NY.jpeg'),
('Retentor 1241', 'Dimensões: 24,00x37,00x8,50\r\nAplicação: Fox, Voyage, Logan.', 2.00, 28, 128, '1241NY.jpeg'),
('Retentor 1245', 'Dimensões: 22,00x34,50x7,30/12,00\r\nAplicação: Fiesta, EcoSport, Visteon.', 2.00, 28, 129, '1245NY.jpeg'),
('Retentor 1246', 'Dimensões: 24,00x42,00x8,00\r\nAplicação: Focus, Palio, Civic.', 2.00, 28, 130, '1246NY.jpeg'),
('Retentor 1253', 'Dimensões:22,00x35,30x7,50\r\nAplicação: Fiesta e EcoSport', 2.00, 28, 131, '1253NY.jpeg'),
('Retentor 1254', 'Dimensões: 14,50x40,00x3,00\r\nAplicação: Celta, Monza, Kadett.', 2.00, 28, 132, '1254 ARRUELA GUARDA PO.jpeg'),
('Retentor 1255', 'Dimensões: 28,50x37,50x5,00\r\nAplicação: Fiesta e EcoSport', 2.00, 28, 133, '1255N.jpeg'),
('Retentor 1256', 'Dimensões: 21,00x41,30x6,30/9,30\r\nAplicação: Fiesta e Ecosport', 2.00, 28, 134, '1256N.jpeg'),
('Retentor 1257', 'Dimensões: 31,00x47,70x7,50\r\nAplicação: Ranger 98.', 2.00, 28, 135, '1257NY.jpeg'),
('Retentor 1258', 'Dimensões: 31,00x43,70/48,00x6,50\r\nAplicação: Ranger 98', 2.00, 28, 136, '1258NY.jpeg'),
('Retentor 1259', 'Dimensões: 25,00x34,00x5,00\r\nAplicação: Ranger 98', 2.00, 28, 137, '1259N.jpeg'),
('Retentor 1260', 'Dimensões: 31,00x43,70/48,00x4,20/8,30\r\nAplicação: Ranger 98', 2.00, 28, 138, '1260NY.jpeg'),
('Retentor 1262', 'Dimensões: 23,00x32,70x5,50/7,50\r\nAplicação: Gol G5, Paraty, Saveiro, Voyage.', 2.00, 28, 139, '1262NY.jpeg'),
('Retentor 1263', 'Dimensão: 24,00x36,50x8,50\r\nAplicação: Renault Clio.', 2.00, 28, 140, '1263NY.jpeg'),
('Kit De Retentores 1233, 1266 1229', 'Kit Retentores.', 2.00, 28, 141, 'kit 1223 1226 1229.jfif'),
('Kit 1263, 1234', 'Kit Retentores.', 2.00, 28, 142, 'kit 1223 1226 1229.jfif'),
('Kit Corsa Bucha de Aluminio 0192, 0755, 0309, 1236, 1235', 'Kit Retentores', 2.00, 28, 143, 'kit corsa antigo bucha de aluminio   0192 0755 0309 1236 1235.jpeg'),
('KIT FIESTA ECO VISTEON 1253 - 45 - 55 - 56', 'Kit Retentores.', 2.00, 28, 144, 'KIT FIESTA ECO VISTEON 1253 - 45 - 55 - 56.jpeg'),
('KIT GOL G5 TRW', 'Kit Retentores.', 2.00, 28, 145, 'KIT GOL G5 TRW.jpeg'),
('KIT MONZA, KADETT 1228BBRY 1254A 0309 0755', 'Kit Retentores.', 2.00, 28, 146, 'KIT MONZA KADETT 1228BBRY 1254A 0309 0755.jpeg'),
('Retentor 0100', 'Dimensões: 24,00x37,00x7,00\r\nAplicação: Vw, Apollo, Logus, Brasilia, Fusca, Variant...', 2.00, 28, 147, '0100.jpg'),
('Retentor 0101', 'Dimensões: 16,30x24,00x7,00\r\nAplicação: VW, Apollo, Logus, Brasilia, Fusca, Variant, Parati, Gol, Passat, Quantum...', 2.00, 28, 148, '0101.jpg'),
('Retentor 0821', 'Dimensões: 45,00x62,00x10,00\r\nAplicação: Scania 75', 2.00, 28, 149, '0821..jpeg'),
('Retentor 0852', 'Dimensões: 25,00x47,00x10,00\r\nAplicação: Fiat 70, 130...', 2.00, 28, 150, '0852..jpeg'),
('kit ranger 98 visteon 1257 - 58 - 59 - 60', 'Kit Retentores', 2.00, 28, 151, 'kit ranger 98 visteon 1257 - 58 - 59 - 60.jpeg'),
('Retentor 1237', 'Dimensões: 12,40x39,40x4,50/11,50\r\nAplicação: Corsa.', 2.00, 4, 152, 'Retentor 1237.jpg'),
('Retentor 1295', 'Dimensões: 22,00x45,00x6,80/11,00\r\nAplicação: Fox, Polo, Jetta, Gol G5.', 2.00, 4, 153, 'Retentor 1295.jpg'),
('Bucha Haste 10x12x08', 'Bucha Haste 10x12x08', 2.00, 44, 154, '1 - BH 10X12.jpg'),
('Bucha Haste de 10', 'Bucha Haste de 10', 2.00, 44, 155, '2 - BH10.jpg'),
('Bucha Haste 12x14x08', 'Bucha Haste 12x14x08', 2.00, 44, 156, '3 - BH12x14.jpg'),
('Bucha Haste de 12', 'Bucha Haste de 12', 2.00, 44, 157, '4 - BH12.jpg'),
('Bucha Haste de 16', 'Bucha Haste de 16', 2.00, 44, 158, '5 - BH16.jpg'),
('Bucha Haste de 18x20x15', 'Bucha Haste de 18x20x15', 2.00, 44, 159, '6 - BH 18.jpg'),
('Bucha Haste de 20x13x08', 'Bucha Haste de 20x13x08', 2.00, 44, 160, '7 - BH20x13.jpg'),
('Bucha Haste de 20', 'Bucha Haste de 20', 2.00, 44, 161, '8- BH20.jpg'),
('Bucha Haste de 22', 'Bucha Haste de 22', 2.00, 44, 162, '9 - BH22.jpg'),
('Bucha Haste de 25', 'Bucha Haste de 25', 2.00, 44, 163, '10 - BH25.jpg'),
('Bucha Haste de 28', 'Bucha Haste de 28', 2.00, 44, 164, '11 - BH28.jpeg'),
('Batente Interno Amortecedor Haste De 10', 'Batente Interno Amortecedor Haste De 10', 2.00, 25, 165, 'H10.jpg'),
('Batente Interno Amortecedor Haste De 12', 'Batente Interno Amortecedor Haste De 12', 2.00, 25, 166, 'H12.jpeg'),
('Batente Interno Amortecedor Haste De 16', 'Batente Interno Amortecedor Haste De 16', 2.00, 25, 167, 'H16.jpeg'),
('Batente Interno Amortecedor Haste De 20', 'Batente Interno Amortecedor Haste De 20', 2.00, 25, 168, 'H20.jpg'),
('Batente Interno Amortecedor Haste De 22', 'Batente Interno Amortecedor Haste De 22', 2.00, 25, 169, 'H22.jpg'),
('Batente Interno Amortecedor Haste De 25', 'Batente Interno Amortecedor Haste De 25', 2.00, 25, 170, 'H25.jpg'),
('Batente Interno Amortecedor Haste De 28', 'Batente Interno Amortecedor Haste De 28', 2.00, 25, 171, 'H28.jpg'),
('Retentor 1206', 'Dimensões: 18,00x30,80x3,30/10,00\r\nAplicação: Ônibus.', 2.00, 41, 172, '1206.jpeg'),
('Retentor 1211', 'Dimensões: 18,00x31,80x3,00/12,00\r\nAplicação: Nissan Versa, Nissan March.', 2.00, 41, 173, '1211.jpeg'),
('Retentor 1251', 'Dimensões: 17,00x53,00x3,30/10,00\r\nAplicação: Ônibus.', 2.00, 41, 174, '1251.jpeg'),
('Mais Produtos Em Breve !', 'Mais Produtos Em Breve !', 2.00, 0, 99999, 'em_breve.webp'),
('Mais Produtos Em Breve !', 'Mais Produtos Em Breve !', 2.00, 44, 213123131, 'em_breve.webp'),
('Mais Produtos Em Breve !', 'Mais Produtos Em Breve !', 2.00, 41, 232323232, 'em_breve.webp'),
('Mais Produtos Em Breve !', 'Mais Produtos Em Breve !', 22.00, 25, 999099909, 'em_breve.webp');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` tinyint(4) DEFAULT 3,
  `name` varchar(191) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `last_name` varchar(255) DEFAULT NULL,
  `cpf` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(191) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `token_time` bigint(20) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `logged_in` tinyint(4) DEFAULT 0,
  `banned` tinyint(4) DEFAULT 0,
  `inviter` int(10) UNSIGNED DEFAULT NULL,
  `affiliate_revenue_share` decimal(20,2) DEFAULT 0.00,
  `affiliate_cpa` decimal(20,2) DEFAULT 0.00,
  `affiliate_baseline` decimal(20,2) DEFAULT 0.00,
  `is_demo_agent` tinyint(4) DEFAULT 0,
  `oauth_id` varchar(100) DEFAULT NULL,
  `oauth_type` varchar(50) DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `kscinus` tinyint(1) DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `name`, `is_admin`, `last_name`, `cpf`, `phone`, `email`, `email_verified_at`, `password`, `remember_token`, `token_time`, `token`, `logged_in`, `banned`, `inviter`, `affiliate_revenue_share`, `affiliate_cpa`, `affiliate_baseline`, `is_demo_agent`, `oauth_id`, `oauth_type`, `status`, `created_at`, `updated_at`, `kscinus`) VALUES
(1, 0, 'Admin', 1, NULL, NULL, NULL, 'admin@demo.com', NULL, '$2y$10$8IViREJTQIAXRY7n9D3UDuhes4bNjBTSaz0E/in5uNb1LX6ZWgNQq', '25F1JeK1ZziZ1KWnoYU1iCLFT6rgXJYZj5a8SiKLEE5A5uC5ZrFksg5qUgxE', 1696659991, 'ff8e95055e285d0e5d0cbd733a6ffb20b042c539d61ab8b2b28358a152cdc09e', 0, 0, 10, 40.00, 20.00, 0.00, 1, NULL, NULL, 'active', '2023-09-24 21:13:49', '2024-01-03 15:21:20', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`cat_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`prod_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `users_email_unique` (`email`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `cat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=136;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
