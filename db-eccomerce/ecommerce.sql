-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 01-Abr-2025 às 15:35
-- Versão do servidor: 9.1.0
-- versão do PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `ecommerce`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `addresses`
--

DROP TABLE IF EXISTS `addresses`;
CREATE TABLE IF NOT EXISTS `addresses` (
  `id_address` int NOT NULL AUTO_INCREMENT,
  `id_user` int NOT NULL,
  `public_area` varchar(100) COLLATE latin1_general_ci NOT NULL COMMENT 'logradouro',
  `number` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `complement` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `district` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `city` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `state` char(2) COLLATE latin1_general_ci NOT NULL,
  `zip_code` varchar(8) COLLATE latin1_general_ci NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_address`),
  KEY `fk_id_user_address` (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Extraindo dados da tabela `addresses`
--

INSERT INTO `addresses` (`id_address`, `id_user`, `public_area`, `number`, `complement`, `district`, `city`, `state`, `zip_code`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 1, 'Rua Beijamin Constant', '796', NULL, 'Centro', 'Centro', 'SP', '06598745', 1, '2025-03-31 13:42:16', '2025-03-31 13:42:16');

-- --------------------------------------------------------

--
-- Estrutura da tabela `admins`
--

DROP TABLE IF EXISTS `admins`;
CREATE TABLE IF NOT EXISTS `admins` (
  `id_admin` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `email` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `password` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `status` enum('ACTIVE','INACTIVE') CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'INACTIVE',
  `permission` enum('SUPER','FINANCE','COMMON') COLLATE latin1_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_admin`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Extraindo dados da tabela `admins`
--

INSERT INTO `admins` (`id_admin`, `name`, `email`, `password`, `status`, `permission`, `created_at`, `updated_at`) VALUES
(1, 'Escala Web', 'teste@escalaweb.com.br', '$2y$10$tExDkovY5YwjiWtigsVcw.PRA2GSaDG0yzQWwHx3E5H4e100gX7aq', 'ACTIVE', 'SUPER', '2025-03-28 17:48:15', '2025-03-31 14:13:09'),
(2, 'Pedro Machado', 'suporte5@escalaweb.com.br', '$2y$10$fcEUCk1q4n41kVcul..teuIX0/oFIgjjBtPayiqFe6emDTaW3ijU2', 'ACTIVE', 'SUPER', '2025-03-31 14:20:31', '2025-03-31 16:29:15');

-- --------------------------------------------------------

--
-- Estrutura da tabela `brands`
--

DROP TABLE IF EXISTS `brands`;
CREATE TABLE IF NOT EXISTS `brands` (
  `id_brand` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_brand`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Extraindo dados da tabela `brands`
--

INSERT INTO `brands` (`id_brand`, `name`, `created_at`, `updated_at`) VALUES
(1, 'adidas', '2025-03-31 13:38:46', '2025-03-31 13:38:46');

-- --------------------------------------------------------

--
-- Estrutura da tabela `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id_category` int NOT NULL AUTO_INCREMENT,
  `parent_category_id` int DEFAULT NULL,
  `name` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_category`),
  UNIQUE KEY `name` (`name`),
  KEY `fk_parent_category` (`parent_category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Extraindo dados da tabela `categories`
--

INSERT INTO `categories` (`id_category`, `parent_category_id`, `name`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Celulares', '2025-03-31 13:39:24', '2025-03-31 13:39:24');

-- --------------------------------------------------------

--
-- Estrutura da tabela `coupon`
--

DROP TABLE IF EXISTS `coupon`;
CREATE TABLE IF NOT EXISTS `coupon` (
  `id_coupon` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `discount` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_coupon`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `folders`
--

DROP TABLE IF EXISTS `folders`;
CREATE TABLE IF NOT EXISTS `folders` (
  `id_folder` int NOT NULL AUTO_INCREMENT,
  `parent_id` int DEFAULT NULL,
  `folder_name` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `is_trash` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_folder`),
  UNIQUE KEY `folder_name` (`folder_name`),
  KEY `fk_parent_folder` (`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Extraindo dados da tabela `folders`
--

INSERT INTO `folders` (`id_folder`, `parent_id`, `folder_name`, `is_trash`, `created_at`, `updated_at`) VALUES
(1, NULL, 'uploads', 0, '2025-03-31 12:36:23', '2025-03-31 12:36:23'),
(2, 1, 'fotos', 0, '2025-03-31 12:36:37', '2025-03-31 12:36:37'),
(5, 1, 'camisas', 0, '2025-03-31 19:55:36', '2025-03-31 19:55:36'),
(6, 1, 'mouses', 0, '2025-03-31 19:56:36', '2025-03-31 19:56:36'),
(7, 5, 'iphone14', 0, '2025-03-31 20:15:39', '2025-03-31 20:15:39'),
(9, 1, 'camisas13', 0, '2025-03-31 20:27:24', '2025-03-31 20:27:24'),
(10, 2, 'ja', 0, '2025-03-31 20:48:18', '2025-03-31 20:48:18');

-- --------------------------------------------------------

--
-- Estrutura da tabela `legal_people`
--

DROP TABLE IF EXISTS `legal_people`;
CREATE TABLE IF NOT EXISTS `legal_people` (
  `id_legal_person` int NOT NULL AUTO_INCREMENT,
  `cnpj` varchar(14) COLLATE latin1_general_ci NOT NULL,
  `corporate_name` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `trade_name` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `state_registration` varchar(20) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'ISENTO',
  PRIMARY KEY (`id_legal_person`),
  UNIQUE KEY `cnpj` (`cnpj`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `media`
--

DROP TABLE IF EXISTS `media`;
CREATE TABLE IF NOT EXISTS `media` (
  `id_media` int NOT NULL AUTO_INCREMENT,
  `id_folder` int NOT NULL,
  `file_name` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `alias` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `file_type` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `file_size` int NOT NULL,
  `is_trash` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_media`),
  KEY `fk_id_folder` (`id_folder`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Extraindo dados da tabela `media`
--

INSERT INTO `media` (`id_media`, `id_folder`, `file_name`, `alias`, `file_type`, `file_size`, `is_trash`, `created_at`, `updated_at`) VALUES
(3, 1, '17434245837905492', 'LogoEscala.png', 'image/png', 1213525, 0, '2025-03-31 12:36:23', '2025-03-31 12:36:23'),
(4, 1, '17434245838055252', 'informacoes-sobre-cotacao.pdf', 'application/pdf', 660926, 0, '2025-03-31 12:36:23', '2025-03-31 12:36:23'),
(5, 2, '17434246260261202', 'LogoEscala.png', 'image/png', 1213525, 0, '2025-03-31 12:37:06', '2025-03-31 12:37:06');

-- --------------------------------------------------------

--
-- Estrutura da tabela `natural_people`
--

DROP TABLE IF EXISTS `natural_people`;
CREATE TABLE IF NOT EXISTS `natural_people` (
  `id_natural_person` int NOT NULL AUTO_INCREMENT,
  `cpf` varchar(11) COLLATE latin1_general_ci NOT NULL,
  `dt_birth` date NOT NULL,
  `gender` enum('N/E','M','F','O') CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'N/E',
  PRIMARY KEY (`id_natural_person`),
  UNIQUE KEY `cpf` (`cpf`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Extraindo dados da tabela `natural_people`
--

INSERT INTO `natural_people` (`id_natural_person`, `cpf`, `dt_birth`, `gender`) VALUES
(1, '84338578009', '2000-02-01', '');

-- --------------------------------------------------------

--
-- Estrutura da tabela `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id_order` int NOT NULL AUTO_INCREMENT,
  `id_coupon` int DEFAULT NULL,
  `id_user` int NOT NULL,
  `id_address` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_order`),
  KEY `fk_id_address_order` (`id_address`),
  KEY `fk_order_coupon` (`id_coupon`),
  KEY `fk_id_user_order` (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Extraindo dados da tabela `orders`
--

INSERT INTO `orders` (`id_order`, `id_coupon`, `id_user`, `id_address`, `created_at`, `updated_at`) VALUES
(1, NULL, 1, 1, '2025-03-31 13:45:16', '2025-03-31 13:45:16'),
(2, NULL, 1, 1, '2025-03-31 13:46:23', '2025-03-31 13:46:23');

-- --------------------------------------------------------

--
-- Estrutura da tabela `order_item`
--

DROP TABLE IF EXISTS `order_item`;
CREATE TABLE IF NOT EXISTS `order_item` (
  `id_order_item` int NOT NULL AUTO_INCREMENT,
  `id_product_variant` int NOT NULL,
  `id_order` int NOT NULL,
  `quantity` int NOT NULL,
  PRIMARY KEY (`id_order_item`),
  KEY `fk_id_order_item_order` (`id_order`),
  KEY `fk_id_order_product_variant` (`id_product_variant`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Extraindo dados da tabela `order_item`
--

INSERT INTO `order_item` (`id_order_item`, `id_product_variant`, `id_order`, `quantity`) VALUES
(1, 1, 1, 1),
(2, 1, 2, 1),
(3, 2, 2, 2);

-- --------------------------------------------------------

--
-- Estrutura da tabela `order_status`
--

DROP TABLE IF EXISTS `order_status`;
CREATE TABLE IF NOT EXISTS `order_status` (
  `id_order_status` int NOT NULL AUTO_INCREMENT,
  `id_order` int NOT NULL,
  `status` enum('PENDING','PROCESSING','SHIPPED','DELIVERED','CANCELLED','REFUNDED','RETURNED') CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'PENDING',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_order_status`),
  KEY `fk_order_status_id_order` (`id_order`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Extraindo dados da tabela `order_status`
--

INSERT INTO `order_status` (`id_order_status`, `id_order`, `status`, `created_at`) VALUES
(1, 1, 'PENDING', '2025-03-31 13:45:16'),
(2, 2, 'PENDING', '2025-03-31 13:46:23');

-- --------------------------------------------------------

--
-- Estrutura da tabela `payments`
--

DROP TABLE IF EXISTS `payments`;
CREATE TABLE IF NOT EXISTS `payments` (
  `id_payment` int NOT NULL AUTO_INCREMENT,
  `id_order` int NOT NULL,
  `id_transaction` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `status` enum('PENDING','AWAITING','REVIEW','PAID','DECLINED','REFUNDED') COLLATE latin1_general_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('CREDIT_CARD','DEBIT_CARD','BANK_SLIP','PIX') COLLATE latin1_general_ci NOT NULL,
  `payment_date` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_payment`),
  KEY `fk_id_order_payment` (`id_order`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `phones`
--

DROP TABLE IF EXISTS `phones`;
CREATE TABLE IF NOT EXISTS `phones` (
  `id_phone` int NOT NULL AUTO_INCREMENT,
  `id_user` int NOT NULL,
  `type` enum('WHATSAPP','CELLPHONE','PHONE','BUSINESS') COLLATE latin1_general_ci NOT NULL,
  `number` varchar(15) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id_phone`),
  KEY `fk_id_user_phone` (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Extraindo dados da tabela `phones`
--

INSERT INTO `phones` (`id_phone`, `id_user`, `type`, `number`) VALUES
(1, 1, 'WHATSAPP', '11987456321');

-- --------------------------------------------------------

--
-- Estrutura da tabela `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id_product` int NOT NULL AUTO_INCREMENT,
  `id_brand` int NOT NULL,
  `name` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `description` text COLLATE latin1_general_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_product`),
  KEY `fk_id_branch` (`id_brand`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Extraindo dados da tabela `products`
--

INSERT INTO `products` (`id_product`, `id_brand`, `name`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Tester with Variation', 'description', 1, '2025-03-31 13:40:11', '2025-03-31 13:40:11'),
(2, 1, 'Tester not Variation', 'description', 1, '2025-03-31 13:40:35', '2025-03-31 13:40:35');

-- --------------------------------------------------------

--
-- Estrutura da tabela `product_categories`
--

DROP TABLE IF EXISTS `product_categories`;
CREATE TABLE IF NOT EXISTS `product_categories` (
  `id_product_category` int NOT NULL AUTO_INCREMENT,
  `id_product` int NOT NULL,
  `id_category` int NOT NULL,
  PRIMARY KEY (`id_product_category`),
  KEY `fk_product_categories` (`id_category`),
  KEY `fk_categories_product` (`id_product`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Extraindo dados da tabela `product_categories`
--

INSERT INTO `product_categories` (`id_product_category`, `id_product`, `id_category`) VALUES
(1, 1, 1),
(2, 2, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `product_pictures`
--

DROP TABLE IF EXISTS `product_pictures`;
CREATE TABLE IF NOT EXISTS `product_pictures` (
  `id_product_picture` int NOT NULL AUTO_INCREMENT,
  `id_product_variant` int NOT NULL,
  `id_variant_attribute_value` int DEFAULT NULL,
  `id_media` int NOT NULL,
  `type` enum('PHOTO','VIDEO') COLLATE latin1_general_ci NOT NULL DEFAULT 'PHOTO',
  `is_main` tinyint(1) DEFAULT '0',
  `position` smallint DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_product_picture`),
  KEY `fk_product_variant_picture` (`id_product_variant`),
  KEY `fk_variant_attribute_value_picture` (`id_variant_attribute_value`),
  KEY `fk_id_media` (`id_media`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Extraindo dados da tabela `product_pictures`
--

INSERT INTO `product_pictures` (`id_product_picture`, `id_product_variant`, `id_variant_attribute_value`, `id_media`, `type`, `is_main`, `position`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 3, 'PHOTO', 1, 1, '2025-03-31 13:40:11', '2025-03-31 13:40:11'),
(2, 1, NULL, 5, 'PHOTO', 0, 2, '2025-03-31 13:40:11', '2025-03-31 13:40:11'),
(3, 2, NULL, 3, 'PHOTO', 1, 1, '2025-03-31 13:40:35', '2025-03-31 13:40:35');

-- --------------------------------------------------------

--
-- Estrutura da tabela `product_shipping_details`
--

DROP TABLE IF EXISTS `product_shipping_details`;
CREATE TABLE IF NOT EXISTS `product_shipping_details` (
  `id_shipping_details` int NOT NULL AUTO_INCREMENT,
  `id_product` int NOT NULL,
  `is_send` tinyint(1) NOT NULL,
  `package_weight` decimal(10,2) NOT NULL,
  `package_length` decimal(10,2) NOT NULL,
  `package_height` decimal(10,2) NOT NULL,
  `package_width` decimal(10,2) NOT NULL,
  `package_diameter` decimal(10,2) DEFAULT NULL,
  `package_format` enum('1','2','3') COLLATE latin1_general_ci NOT NULL DEFAULT '1',
  `hand_delivery` enum('S','N') COLLATE latin1_general_ci DEFAULT NULL,
  `receipet_notice` enum('S','N') COLLATE latin1_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_shipping_details`),
  KEY `fk_product_shipping_details` (`id_product`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `product_variants`
--

DROP TABLE IF EXISTS `product_variants`;
CREATE TABLE IF NOT EXISTS `product_variants` (
  `id_product_variant` int NOT NULL AUTO_INCREMENT,
  `id_product` int NOT NULL,
  `sku` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `qtd_stock` smallint NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `discount` decimal(10,2) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_product_variant`),
  KEY `fk_id_product_to_variant` (`id_product`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Extraindo dados da tabela `product_variants`
--

INSERT INTO `product_variants` (`id_product_variant`, `id_product`, `sku`, `price`, `qtd_stock`, `is_default`, `discount`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'sku', 121.00, 0, 1, 12.79, 1, '2025-03-31 13:40:11', '2025-03-31 14:07:51'),
(2, 2, 'sku', 121.00, 0, 1, 12.00, 1, '2025-03-31 13:40:35', '2025-03-31 13:46:23');

-- --------------------------------------------------------

--
-- Estrutura da tabela `product_variants_attributes`
--

DROP TABLE IF EXISTS `product_variants_attributes`;
CREATE TABLE IF NOT EXISTS `product_variants_attributes` (
  `id_product_variant_attribute` int NOT NULL AUTO_INCREMENT,
  `id_variant_attribute_value` int NOT NULL,
  `id_product_variant` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_product_variant_attribute`),
  KEY `fk_variant_attribute_value_product` (`id_variant_attribute_value`),
  KEY `fk_product_variant_re` (`id_product_variant`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `stock_movements`
--

DROP TABLE IF EXISTS `stock_movements`;
CREATE TABLE IF NOT EXISTS `stock_movements` (
  `id_stock_movements` int NOT NULL AUTO_INCREMENT,
  `id_product_variant` int NOT NULL,
  `id_admin` int NOT NULL,
  `movement_type` enum('SALE','RETURN','CANCELLATION','PURCHASE') COLLATE latin1_general_ci NOT NULL DEFAULT 'SALE',
  `quantity` tinyint NOT NULL,
  `reason` text COLLATE latin1_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_stock_movements`),
  KEY `fk_stock_admin` (`id_admin`),
  KEY `fk_stock_product_variant` (`id_product_variant`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `stores`
--

DROP TABLE IF EXISTS `stores`;
CREATE TABLE IF NOT EXISTS `stores` (
  `id_store` int NOT NULL AUTO_INCREMENT,
  `name` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `domain` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `account_id` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_store`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tokens_admins`
--

DROP TABLE IF EXISTS `tokens_admins`;
CREATE TABLE IF NOT EXISTS `tokens_admins` (
  `id_token` int NOT NULL AUTO_INCREMENT,
  `id_admin` int NOT NULL,
  `type` enum('FORGET','ACTIVE') COLLATE latin1_general_ci NOT NULL,
  `token` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `status` enum('ACTIVE','INACTIVE') CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'ACTIVE',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_token`),
  KEY `fk_tokens_id_admin` (`id_admin`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Extraindo dados da tabela `tokens_admins`
--

INSERT INTO `tokens_admins` (`id_token`, `id_admin`, `type`, `token`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'ACTIVE', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NDMxODQxNjgsImV4cCI6MTc0MzE4NTk2OCwiaWRfdXNlciI6MSwibmFtZSI6IkVzY2FsYSBXZWIiLCJydWxlIjoiU1VQRVIiLCJzdGF0dXMiOiJJTkFDVElWRSJ9.r3GLpX_ryz7Ec_qeuoIrDxyUOd7HiuCxpsQIQWPLXTc', 'INACTIVE', '2025-03-28 17:49:28', '2025-03-28 17:52:28'),
(2, 1, 'FORGET', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NDM0MjcxMzMsImV4cCI6MTc0MzQyODAzMywiaWRfdXNlciI6MSwibmFtZSI6IkVzY2FsYSBXZWIiLCJydWxlIjoiYWRtaW4iLCJzdGF0dXMiOiJBQ1RJVkUifQ.Kvi88DMZuT6LiNKTWdvEp8WGU2TvJOyb3_iRQ8NNKHA', 'INACTIVE', '2025-03-31 13:18:53', '2025-03-31 13:20:17'),
(3, 1, 'FORGET', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NDM0MjcyMTcsImV4cCI6MTc0MzQyODExNywiaWRfdXNlciI6MSwibmFtZSI6IkVzY2FsYSBXZWIiLCJydWxlIjoiYWRtaW4iLCJzdGF0dXMiOiJBQ1RJVkUifQ.dfNk16wj-9R7rptTNfjnGEUD6TcyIrfg1y8cCYBgoBI', 'INACTIVE', '2025-03-31 13:20:17', '2025-03-31 13:29:12'),
(4, 1, 'FORGET', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NDM0Mjc3NTIsImV4cCI6MTc0MzQyODY1MiwiaWRfdXNlciI6MSwibmFtZSI6IkVzY2FsYSBXZWIiLCJydWxlIjoiYWRtaW4iLCJzdGF0dXMiOiJBQ1RJVkUifQ.8MNw7jGyHvtD9qiSifcQE5OtM16to29HSHRNgBNUHC0', 'INACTIVE', '2025-03-31 13:29:12', '2025-03-31 13:30:15'),
(5, 1, 'FORGET', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NDM0Mjc4MTUsImV4cCI6MTc0MzQyODcxNSwiaWRfdXNlciI6MSwibmFtZSI6IkVzY2FsYSBXZWIiLCJydWxlIjoiYWRtaW4iLCJzdGF0dXMiOiJBQ1RJVkUifQ.KbcFw8T_VtGQmLdreEyEKvQUzlEZtMFdNEdjYIOWuAM', 'INACTIVE', '2025-03-31 13:30:15', '2025-03-31 13:31:34'),
(6, 1, 'FORGET', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NDM0Mjc4OTQsImV4cCI6MTc0MzQyODc5NCwiaWRfdXNlciI6MSwibmFtZSI6IkVzY2FsYSBXZWIiLCJydWxlIjoiYWRtaW4iLCJzdGF0dXMiOiJBQ1RJVkUifQ.XIrGuqIF-nZkzCLMWvrfAjvyodor-bInVhzHKBdXNuc', 'INACTIVE', '2025-03-31 13:31:34', '2025-03-31 13:31:50'),
(7, 1, 'FORGET', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NDM0Mjc5MTAsImV4cCI6MTc0MzQyODgxMCwiaWRfdXNlciI6MSwibmFtZSI6IkVzY2FsYSBXZWIiLCJydWxlIjoiYWRtaW4iLCJzdGF0dXMiOiJBQ1RJVkUifQ.abCigFwah0f1MDR-vSh23s4619TDIhzaktk_sD0m8g0', 'INACTIVE', '2025-03-31 13:31:50', '2025-03-31 13:31:56'),
(8, 1, 'FORGET', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NDM0Mjc5MTYsImV4cCI6MTc0MzQyODgxNiwiaWRfdXNlciI6MSwibmFtZSI6IkVzY2FsYSBXZWIiLCJydWxlIjoiYWRtaW4iLCJzdGF0dXMiOiJBQ1RJVkUifQ.xrdmNVUCSUe0_KJDfbKmj7ee3_pgplAKC_ZUzIPVrvo', 'INACTIVE', '2025-03-31 13:31:56', '2025-03-31 13:32:30'),
(9, 1, 'FORGET', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NDM0Mjc5NTAsImV4cCI6MTc0MzQyODg1MCwiaWRfdXNlciI6MSwibmFtZSI6IkVzY2FsYSBXZWIiLCJydWxlIjoiYWRtaW4iLCJzdGF0dXMiOiJBQ1RJVkUifQ.bdooTa10rq0HoGHYWqyECsFGEp5TqCU2xGBUErAmMJA', 'INACTIVE', '2025-03-31 13:32:30', '2025-03-31 13:34:33'),
(10, 1, 'FORGET', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NDM0MjgwNzMsImV4cCI6MTc0MzQyODk3MywiaWRfdXNlciI6MSwibmFtZSI6IkVzY2FsYSBXZWIiLCJydWxlIjoiYWRtaW4iLCJzdGF0dXMiOiJBQ1RJVkUifQ.pa5SVHN_ldxykioh7Gvn2Z0gyQPtxVr7vq97SCraA5g', 'INACTIVE', '2025-03-31 13:34:33', '2025-03-31 13:54:40'),
(11, 1, 'FORGET', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NDM0MjkyODAsImV4cCI6MTc0MzQzMDE4MCwiaWRfdXNlciI6MSwibmFtZSI6IkVzY2FsYSBXZWIiLCJydWxlIjoiYWRtaW4iLCJzdGF0dXMiOiJBQ1RJVkUifQ.jqIWMTy4mCs2K0p0iWoWhp5kqFvNSRGmVASYjtQmd20', 'INACTIVE', '2025-03-31 13:54:40', '2025-03-31 14:12:42'),
(12, 1, 'FORGET', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NDM0MzAzNjIsImV4cCI6MTc0MzQzMTI2MiwiaWRfdXNlciI6MSwibmFtZSI6IkVzY2FsYSBXZWIiLCJydWxlIjoiYWRtaW4iLCJzdGF0dXMiOiJBQ1RJVkUifQ.ZxluOAyaqCUEgEFyAmnhYZtREiJX0v-T2_8YREuFPQo', 'INACTIVE', '2025-03-31 14:12:42', '2025-03-31 14:13:09'),
(13, 2, 'ACTIVE', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NDM0MzA5MTAsImV4cCI6MTc0MzQzMjcxMCwiaWRfdXNlciI6MiwibmFtZSI6IlBlZHJvIE1hY2hhZG8iLCJydWxlIjoiU1VQRVIiLCJzdGF0dXMiOiJJTkFDVElWRSJ9.Mascq06SRLfMCfuXj2dxkLcYNr9ejaU-BDl7jm638ps', 'INACTIVE', '2025-03-31 14:21:50', '2025-03-31 16:25:52'),
(14, 1, 'FORGET', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NDM0Mzc4MzMsImV4cCI6MTc0MzQzODczMywiaWRfdXNlciI6MSwibmFtZSI6IkVzY2FsYSBXZWIiLCJydWxlIjoiYWRtaW4iLCJzdGF0dXMiOiJBQ1RJVkUifQ.tI4C4JJLV-AE4dIJ9WtlQsAHx-SrWsveSJZwHoH948U', 'ACTIVE', '2025-03-31 16:17:14', '2025-03-31 16:17:14'),
(15, 2, 'ACTIVE', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NDM0MzgzOTcsImV4cCI6MTc0MzQ0MDE5NywiaWRfdXNlciI6MiwibmFtZSI6IlBlZHJvIE1hY2hhZG8iLCJydWxlIjoiU1VQRVIiLCJzdGF0dXMiOiJJTkFDVElWRSJ9.SCk9dNupBG28Ve0PhoCqMj0RaPh5iVgEqX5PGr7WJDk', 'INACTIVE', '2025-03-31 16:26:37', '2025-03-31 16:27:17'),
(16, 2, 'ACTIVE', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NDM0Mzg1MzIsImV4cCI6MTc0MzQ0MDMzMiwiaWRfdXNlciI6MiwibmFtZSI6IlBlZHJvIE1hY2hhZG8iLCJydWxlIjoiU1VQRVIiLCJzdGF0dXMiOiJJTkFDVElWRSJ9.2vqkP2qfZC7EhkneHmHb1sjWhuF9pYy7hzXr1z6wOIQ', 'INACTIVE', '2025-03-31 16:28:52', '2025-03-31 16:29:15');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tokens_users`
--

DROP TABLE IF EXISTS `tokens_users`;
CREATE TABLE IF NOT EXISTS `tokens_users` (
  `id_token` int NOT NULL AUTO_INCREMENT,
  `id_user` int NOT NULL,
  `type` enum('FORGET','ACTIVE') COLLATE latin1_general_ci NOT NULL,
  `token` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `status` enum('ACTIVE','INACTIVE') COLLATE latin1_general_ci NOT NULL DEFAULT 'ACTIVE',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_token`),
  KEY `fk_id_user_token` (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Extraindo dados da tabela `tokens_users`
--

INSERT INTO `tokens_users` (`id_token`, `id_user`, `type`, `token`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'ACTIVE', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NDM0Mjg1NjQsImV4cCI6MTc0MzQzMDM2NCwiaWRfdXNlciI6MSwibmFtZSI6IkVzY2FsYSBXZWIiLCJydWxlIjoiVVNFUiIsInN0YXR1cyI6IklOQUNUSVZFIn0.HG73xFBUAW5K7BV_YtVhuOBaCBh6GPDlotiG7h7_gsI', 'INACTIVE', '2025-03-31 13:42:44', '2025-03-31 13:44:26');

-- --------------------------------------------------------

--
-- Estrutura da tabela `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id_user` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `email` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `password` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `status` enum('ACTIVE','INACTIVE','SUSPENSED') COLLATE latin1_general_ci NOT NULL DEFAULT 'INACTIVE',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `id_legal_person` int DEFAULT NULL,
  `id_natural_person` int DEFAULT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_natural_person` (`id_natural_person`),
  KEY `fk_legal_person` (`id_legal_person`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Extraindo dados da tabela `users`
--

INSERT INTO `users` (`id_user`, `username`, `email`, `password`, `status`, `created_at`, `updated_at`, `id_legal_person`, `id_natural_person`) VALUES
(1, 'Escala Web', 'teste@escalaweb.com.br', '$2y$10$A82fjgyFaEsFXzM7gMr3/OVN943oflmeg30OLKarf6bSMVIQrU9ce', 'ACTIVE', '2025-03-31 13:42:16', '2025-03-31 13:44:26', NULL, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `variant_attributes`
--

DROP TABLE IF EXISTS `variant_attributes`;
CREATE TABLE IF NOT EXISTS `variant_attributes` (
  `id_variant_attribute` int NOT NULL AUTO_INCREMENT,
  `name` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_variant_attribute`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Extraindo dados da tabela `variant_attributes`
--

INSERT INTO `variant_attributes` (`id_variant_attribute`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Cores', '2025-04-01 14:16:54', '2025-04-01 14:16:54'),
(2, 'Lista', '2025-04-01 14:31:49', '2025-04-01 14:31:49');

-- --------------------------------------------------------

--
-- Estrutura da tabela `variant_attributes_values`
--

DROP TABLE IF EXISTS `variant_attributes_values`;
CREATE TABLE IF NOT EXISTS `variant_attributes_values` (
  `id_variant_attribute_value` int NOT NULL AUTO_INCREMENT,
  `id_variant_attribute` int NOT NULL,
  `value` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `viewer` enum('LIST','COLOR') COLLATE latin1_general_ci NOT NULL DEFAULT 'LIST',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_variant_attribute_value`),
  KEY `fk_variant_attribute_value` (`id_variant_attribute`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `fk_id_user_address` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `fk_parent_category` FOREIGN KEY (`parent_category_id`) REFERENCES `categories` (`id_category`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Limitadores para a tabela `folders`
--
ALTER TABLE `folders`
  ADD CONSTRAINT `fk_parent_folder` FOREIGN KEY (`parent_id`) REFERENCES `folders` (`id_folder`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Limitadores para a tabela `media`
--
ALTER TABLE `media`
  ADD CONSTRAINT `fk_id_folder` FOREIGN KEY (`id_folder`) REFERENCES `folders` (`id_folder`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Limitadores para a tabela `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_id_address_order` FOREIGN KEY (`id_address`) REFERENCES `addresses` (`id_address`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_id_user_order` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_order_coupon` FOREIGN KEY (`id_coupon`) REFERENCES `orders` (`id_order`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Limitadores para a tabela `order_item`
--
ALTER TABLE `order_item`
  ADD CONSTRAINT `fk_id_order_item_order` FOREIGN KEY (`id_order`) REFERENCES `orders` (`id_order`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_id_order_product_variant` FOREIGN KEY (`id_product_variant`) REFERENCES `product_variants` (`id_product_variant`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Limitadores para a tabela `order_status`
--
ALTER TABLE `order_status`
  ADD CONSTRAINT `fk_order_status_id_order` FOREIGN KEY (`id_order`) REFERENCES `orders` (`id_order`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Limitadores para a tabela `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_id_order_payment` FOREIGN KEY (`id_order`) REFERENCES `orders` (`id_order`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Limitadores para a tabela `phones`
--
ALTER TABLE `phones`
  ADD CONSTRAINT `fk_id_user_phone` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Limitadores para a tabela `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_id_branch` FOREIGN KEY (`id_brand`) REFERENCES `brands` (`id_brand`);

--
-- Limitadores para a tabela `product_categories`
--
ALTER TABLE `product_categories`
  ADD CONSTRAINT `fk_categories_product` FOREIGN KEY (`id_product`) REFERENCES `products` (`id_product`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_product_categories` FOREIGN KEY (`id_category`) REFERENCES `categories` (`id_category`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Limitadores para a tabela `product_pictures`
--
ALTER TABLE `product_pictures`
  ADD CONSTRAINT `fk_id_media` FOREIGN KEY (`id_media`) REFERENCES `media` (`id_media`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_product_variant_picture` FOREIGN KEY (`id_product_variant`) REFERENCES `product_variants` (`id_product_variant`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_variant_attribute_value_picture` FOREIGN KEY (`id_variant_attribute_value`) REFERENCES `variant_attributes_values` (`id_variant_attribute_value`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Limitadores para a tabela `product_shipping_details`
--
ALTER TABLE `product_shipping_details`
  ADD CONSTRAINT `fk_product_shipping_details` FOREIGN KEY (`id_product`) REFERENCES `products` (`id_product`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Limitadores para a tabela `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `fk_id_product_to_variant` FOREIGN KEY (`id_product`) REFERENCES `products` (`id_product`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Limitadores para a tabela `product_variants_attributes`
--
ALTER TABLE `product_variants_attributes`
  ADD CONSTRAINT `fk_product_variant_re` FOREIGN KEY (`id_product_variant`) REFERENCES `product_variants` (`id_product_variant`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_variant_attribute_value_product` FOREIGN KEY (`id_variant_attribute_value`) REFERENCES `variant_attributes_values` (`id_variant_attribute_value`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Limitadores para a tabela `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD CONSTRAINT `fk_stock_admin` FOREIGN KEY (`id_admin`) REFERENCES `admins` (`id_admin`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_stock_product_variant` FOREIGN KEY (`id_product_variant`) REFERENCES `product_variants` (`id_product_variant`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Limitadores para a tabela `tokens_admins`
--
ALTER TABLE `tokens_admins`
  ADD CONSTRAINT `fk_tokens_id_admin` FOREIGN KEY (`id_admin`) REFERENCES `admins` (`id_admin`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Limitadores para a tabela `tokens_users`
--
ALTER TABLE `tokens_users`
  ADD CONSTRAINT `fk_id_user_token` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Limitadores para a tabela `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_legal_person` FOREIGN KEY (`id_legal_person`) REFERENCES `legal_people` (`id_legal_person`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_natural_person` FOREIGN KEY (`id_natural_person`) REFERENCES `natural_people` (`id_natural_person`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Limitadores para a tabela `variant_attributes_values`
--
ALTER TABLE `variant_attributes_values`
  ADD CONSTRAINT `fk_variant_attribute_value` FOREIGN KEY (`id_variant_attribute`) REFERENCES `variant_attributes` (`id_variant_attribute`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
