-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Dec 07, 2025 at 04:23 PM
-- Server version: 8.0.40
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sistema_financiero`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int NOT NULL,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_class` tinyint NOT NULL,
  `account_type` enum('debit','credit') COLLATE utf8mb4_unicode_ci NOT NULL,
  `balance` decimal(15,2) DEFAULT '0.00',
  `is_active` tinyint(1) DEFAULT '1',
  `created_by` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `code`, `name`, `account_class`, `account_type`, `balance`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
(1, '1.1.01', 'Caja General', 1, 'credit', 50.00, 1, 1, '2025-11-28 22:48:49', '2025-12-05 01:41:59'),
(2, '1.1.02', 'Bancos', 1, 'debit', 50.00, 1, 1, '2025-11-28 22:48:49', '2025-12-01 12:51:23'),
(3, '1.2.01', 'Cuentas por Cobrar', 1, 'debit', 0.00, 1, 1, '2025-11-28 22:48:49', '2025-11-28 22:48:49'),
(4, '1.3.01', 'Inventarios', 1, 'debit', 0.00, 1, 1, '2025-11-28 22:48:49', '2025-11-28 22:48:49'),
(5, '1.4.01', 'Equipos de Oficina', 1, 'debit', 0.00, 1, 1, '2025-11-28 22:48:49', '2025-11-28 22:48:49'),
(6, '2.1.01', 'Cuentas por Pagar', 2, 'credit', 0.00, 1, 1, '2025-11-28 22:48:49', '2025-11-28 22:48:49'),
(7, '2.2.01', 'Préstamos Bancarios', 2, 'credit', 0.00, 1, 1, '2025-11-28 22:48:49', '2025-11-28 22:48:49'),
(8, '3.1.01', 'Capital Social', 3, 'credit', 0.00, 1, 1, '2025-11-28 22:48:49', '2025-11-28 22:48:49'),
(9, '3.2.01', 'Utilidades Retenidas', 3, 'credit', 0.00, 1, 1, '2025-11-28 22:48:49', '2025-11-28 22:48:49'),
(10, '3.3.01', 'Utilidad del Ejercicio', 3, 'credit', 0.00, 1, 1, '2025-11-28 22:48:49', '2025-11-28 22:48:49'),
(11, '4.1.01', 'Ventas', 4, 'credit', -100.00, 1, 1, '2025-11-28 22:48:49', '2025-12-05 01:41:59'),
(12, '4.2.01', 'Intereses Ganados', 4, 'credit', 0.00, 1, 1, '2025-11-28 22:48:49', '2025-11-28 22:48:49'),
(13, '5.1.01', 'Sueldos y Salarios', 5, 'debit', 0.00, 1, 1, '2025-11-28 22:48:49', '2025-11-28 22:48:49'),
(14, '5.2.01', 'Alquiler', 5, 'debit', -500.00, 1, 1, '2025-11-28 22:48:49', '2025-12-05 12:21:56'),
(15, '5.3.01', 'Servicios Públicos', 5, 'debit', 0.00, 1, 1, '2025-11-28 22:48:49', '2025-11-28 22:48:49'),
(16, '456', 'Prueba', 2, 'debit', 0.00, 1, 1, '2025-11-30 23:49:37', '2025-12-06 22:25:02'),
(17, '5.4.01', 'Publicidad', 5, 'debit', 0.00, 1, 4, '2025-12-05 01:36:19', '2025-12-05 01:36:19'),
(18, '405', 'Pagos', 5, 'debit', 500.00, 1, 1, '2025-12-05 12:20:33', '2025-12-05 12:21:56');

--
-- Triggers `accounts`
--
DELIMITER $$
CREATE TRIGGER `audit_account_changes` AFTER UPDATE ON `accounts` FOR EACH ROW BEGIN
    INSERT INTO audit_logs (user_id, action, table_name, record_id, old_values, new_values)
    VALUES (
        NEW.created_by,
        'UPDATE',
        'accounts',
        NEW.id,
        JSON_OBJECT('balance', OLD.balance, 'name', OLD.name),
        JSON_OBJECT('balance', NEW.balance, 'name', NEW.name)
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `action` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `table_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `record_id` int DEFAULT NULL,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `table_name`, `record_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'UPDATE', 'accounts', 1, '{\"name\": \"Caja General\", \"balance\": 0.00}', '{\"name\": \"Caja General\", \"balance\": 0.00}', NULL, NULL, '2025-12-01 00:34:14'),
(2, 1, 'UPDATE', 'accounts', 15, '{\"name\": \"Servicios Públicos\", \"balance\": 0.00}', '{\"name\": \"Servicios Públicos\", \"balance\": 0.00}', NULL, NULL, '2025-12-01 12:50:52'),
(3, 1, 'UPDATE', 'accounts', 2, '{\"name\": \"Bancos\", \"balance\": 0.00}', '{\"name\": \"Bancos\", \"balance\": 50.00}', NULL, NULL, '2025-12-01 12:51:23'),
(4, 1, 'UPDATE', 'accounts', 1, '{\"name\": \"Caja General\", \"balance\": 0.00}', '{\"name\": \"Caja General\", \"balance\": -50.00}', NULL, NULL, '2025-12-01 12:51:23'),
(5, 1, 'UPDATE', 'accounts', 16, '{\"name\": \"Prueba\", \"balance\": 0.00}', '{\"name\": \"Prueba\", \"balance\": 0.00}', NULL, NULL, '2025-12-05 01:36:24'),
(6, 1, 'UPDATE', 'accounts', 1, '{\"name\": \"Caja General\", \"balance\": -50.00}', '{\"name\": \"Caja General\", \"balance\": 50.00}', NULL, NULL, '2025-12-05 01:41:59'),
(7, 1, 'UPDATE', 'accounts', 11, '{\"name\": \"Ventas\", \"balance\": 0.00}', '{\"name\": \"Ventas\", \"balance\": -100.00}', NULL, NULL, '2025-12-05 01:41:59'),
(8, 1, 'UPDATE', 'accounts', 18, '{\"name\": \"Pagos\", \"balance\": 0.00}', '{\"name\": \"Pagos\", \"balance\": 500.00}', NULL, NULL, '2025-12-05 12:21:56'),
(9, 1, 'UPDATE', 'accounts', 14, '{\"name\": \"Alquiler\", \"balance\": 0.00}', '{\"name\": \"Alquiler\", \"balance\": -500.00}', NULL, NULL, '2025-12-05 12:21:56'),
(10, 1, 'UPDATE', 'accounts', 16, '{\"name\": \"Prueba\", \"balance\": 0.00}', '{\"name\": \"Prueba\", \"balance\": 0.00}', NULL, NULL, '2025-12-06 22:25:02');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int NOT NULL,
  `report_type` enum('income_statement','balance_sheet') COLLATE utf8mb4_unicode_ci NOT NULL,
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `report_data` json NOT NULL,
  `hash` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_signed` tinyint(1) DEFAULT '0',
  `is_modified` tinyint(1) DEFAULT '0',
  `generated_by` int NOT NULL,
  `generated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `report_type`, `period_start`, `period_end`, `report_data`, `hash`, `is_signed`, `is_modified`, `generated_by`, `generated_at`) VALUES
(1, 'income_statement', '2025-12-01', '2025-12-01', '{\"type\": \"income_statement\", \"lines\": [], \"title\": \"Estado de Resultados\", \"net_income\": 0, \"period_end\": \"2025-12-01\", \"period_start\": \"2025-12-01\", \"total_income\": 0, \"total_expense\": 0}', '958ae279e63b98ff60a26dfd5fbb14cc07a9758accc9a866c4c12fbbadf10adb', 0, 0, 1, '2025-12-01 00:17:45'),
(2, 'balance_sheet', '2025-12-01', '2025-12-01', '{\"type\": \"balance_sheet\", \"lines\": [{\"code\": \"1.1.01\", \"name\": \"Caja General\", \"amount\": \"0.00\", \"account_class\": 1}, {\"code\": \"1.1.02\", \"name\": \"Bancos\", \"amount\": \"0.00\", \"account_class\": 1}, {\"code\": \"1.2.01\", \"name\": \"Cuentas por Cobrar\", \"amount\": \"0.00\", \"account_class\": 1}, {\"code\": \"1.3.01\", \"name\": \"Inventarios\", \"amount\": \"0.00\", \"account_class\": 1}, {\"code\": \"1.4.01\", \"name\": \"Equipos de Oficina\", \"amount\": \"0.00\", \"account_class\": 1}, {\"code\": \"2.1.01\", \"name\": \"Cuentas por Pagar\", \"amount\": \"0.00\", \"account_class\": 2}, {\"code\": \"2.2.01\", \"name\": \"Préstamos Bancarios\", \"amount\": \"0.00\", \"account_class\": 2}, {\"code\": \"3.1.01\", \"name\": \"Capital Social\", \"amount\": \"0.00\", \"account_class\": 3}, {\"code\": \"3.2.01\", \"name\": \"Utilidades Retenidas\", \"amount\": \"0.00\", \"account_class\": 3}, {\"code\": \"3.3.01\", \"name\": \"Utilidad del Ejercicio\", \"amount\": \"0.00\", \"account_class\": 3}, {\"code\": \"456\", \"name\": \"Prueba\", \"amount\": \"0.00\", \"account_class\": 2}], \"title\": \"Balance General\", \"period_end\": \"2025-12-01\", \"equation_ok\": true, \"period_start\": \"2025-12-01\", \"total_assets\": 0, \"total_equity\": 0, \"total_liabilities\": 0}', '3e9d3845370b1163baeab211a61bce176dcfd5c034464a9a1665e5821e5497a2', 1, 0, 1, '2025-12-01 00:17:53'),
(3, 'balance_sheet', '2025-12-01', '2025-12-01', '{\"type\": \"balance_sheet\", \"lines\": [{\"code\": \"1.1.01\", \"name\": \"Caja General\", \"amount\": \"0.00\", \"account_class\": 1}, {\"code\": \"1.1.02\", \"name\": \"Bancos\", \"amount\": \"0.00\", \"account_class\": 1}, {\"code\": \"1.2.01\", \"name\": \"Cuentas por Cobrar\", \"amount\": \"0.00\", \"account_class\": 1}, {\"code\": \"1.3.01\", \"name\": \"Inventarios\", \"amount\": \"0.00\", \"account_class\": 1}, {\"code\": \"1.4.01\", \"name\": \"Equipos de Oficina\", \"amount\": \"0.00\", \"account_class\": 1}, {\"code\": \"2.1.01\", \"name\": \"Cuentas por Pagar\", \"amount\": \"0.00\", \"account_class\": 2}, {\"code\": \"2.2.01\", \"name\": \"Préstamos Bancarios\", \"amount\": \"0.00\", \"account_class\": 2}, {\"code\": \"3.1.01\", \"name\": \"Capital Social\", \"amount\": \"0.00\", \"account_class\": 3}, {\"code\": \"3.2.01\", \"name\": \"Utilidades Retenidas\", \"amount\": \"0.00\", \"account_class\": 3}, {\"code\": \"3.3.01\", \"name\": \"Utilidad del Ejercicio\", \"amount\": \"0.00\", \"account_class\": 3}, {\"code\": \"456\", \"name\": \"Prueba\", \"amount\": \"0.00\", \"account_class\": 2}], \"title\": \"Balance General\", \"period_end\": \"2025-12-01\", \"equation_ok\": true, \"period_start\": \"2025-12-01\", \"total_assets\": 0, \"total_equity\": 0, \"total_liabilities\": 0}', '3e9d3845370b1163baeab211a61bce176dcfd5c034464a9a1665e5821e5497a2', 0, 0, 1, '2025-12-01 00:37:46'),
(4, 'balance_sheet', '2025-12-26', '2025-12-26', '{\"type\": \"balance_sheet\", \"lines\": [{\"code\": \"1.1.01\", \"name\": \"Caja General\", \"amount\": \"0.00\", \"account_class\": 1}, {\"code\": \"1.1.02\", \"name\": \"Bancos\", \"amount\": \"0.00\", \"account_class\": 1}, {\"code\": \"1.2.01\", \"name\": \"Cuentas por Cobrar\", \"amount\": \"0.00\", \"account_class\": 1}, {\"code\": \"1.3.01\", \"name\": \"Inventarios\", \"amount\": \"0.00\", \"account_class\": 1}, {\"code\": \"1.4.01\", \"name\": \"Equipos de Oficina\", \"amount\": \"0.00\", \"account_class\": 1}, {\"code\": \"2.1.01\", \"name\": \"Cuentas por Pagar\", \"amount\": \"0.00\", \"account_class\": 2}, {\"code\": \"2.2.01\", \"name\": \"Préstamos Bancarios\", \"amount\": \"0.00\", \"account_class\": 2}, {\"code\": \"3.1.01\", \"name\": \"Capital Social\", \"amount\": \"0.00\", \"account_class\": 3}, {\"code\": \"3.2.01\", \"name\": \"Utilidades Retenidas\", \"amount\": \"0.00\", \"account_class\": 3}, {\"code\": \"3.3.01\", \"name\": \"Utilidad del Ejercicio\", \"amount\": \"0.00\", \"account_class\": 3}, {\"code\": \"456\", \"name\": \"Prueba\", \"amount\": \"0.00\", \"account_class\": 2}], \"title\": \"Balance General\", \"period_end\": \"2025-12-26\", \"equation_ok\": true, \"period_start\": \"2025-12-26\", \"total_assets\": 0, \"total_equity\": 0, \"total_liabilities\": 0}', '44a647bf4843627f77cd9a0e263078f5e89d7ec70c07e4145ba62e2347956447', 1, 0, 1, '2025-12-01 00:38:29'),
(5, 'income_statement', '2025-01-01', '2025-12-31', '{\"type\": \"income_statement\", \"lines\": [{\"code\": \"4.1.01\", \"name\": \"Ventas\", \"amount\": \"100.00\", \"account_type\": \"credit\", \"account_class\": 4}], \"title\": \"Estado de Resultados\", \"net_income\": 100, \"period_end\": \"2025-12-31\", \"period_start\": \"2025-01-01\", \"total_income\": 100, \"total_expense\": 0}', '958228381142a528544144559e054e903963ddb0d9a06d65a9c28561e762376a', 1, 0, 5, '2025-12-05 01:43:54'),
(6, 'income_statement', '2025-01-01', '2025-12-31', '{\"type\": \"income_statement\", \"lines\": [{\"code\": \"4.1.01\", \"name\": \"Ventas\", \"amount\": \"100.00\", \"account_type\": \"credit\", \"account_class\": 4}], \"title\": \"Estado de Resultados\", \"net_income\": 100, \"period_end\": \"2025-12-31\", \"period_start\": \"2025-01-01\", \"total_income\": 100, \"total_expense\": 0}', '958228381142a528544144559e054e903963ddb0d9a06d65a9c28561e762376a', 1, 0, 5, '2025-12-05 01:43:55'),
(7, 'balance_sheet', '2025-12-05', '2025-12-05', '{\"type\": \"balance_sheet\", \"lines\": [{\"code\": \"1.1.01\", \"name\": \"Caja General\", \"amount\": \"50.00\", \"account_class\": 1}, {\"code\": \"1.1.02\", \"name\": \"Bancos\", \"amount\": \"50.00\", \"account_class\": 1}, {\"code\": \"1.2.01\", \"name\": \"Cuentas por Cobrar\", \"amount\": \"0.00\", \"account_class\": 1}, {\"code\": \"1.3.01\", \"name\": \"Inventarios\", \"amount\": \"0.00\", \"account_class\": 1}, {\"code\": \"1.4.01\", \"name\": \"Equipos de Oficina\", \"amount\": \"0.00\", \"account_class\": 1}, {\"code\": \"2.1.01\", \"name\": \"Cuentas por Pagar\", \"amount\": \"0.00\", \"account_class\": 2}, {\"code\": \"2.2.01\", \"name\": \"Préstamos Bancarios\", \"amount\": \"0.00\", \"account_class\": 2}, {\"code\": \"3.1.01\", \"name\": \"Capital Social\", \"amount\": \"0.00\", \"account_class\": 3}, {\"code\": \"3.2.01\", \"name\": \"Utilidades Retenidas\", \"amount\": \"0.00\", \"account_class\": 3}, {\"code\": \"3.3.01\", \"name\": \"Utilidad del Ejercicio\", \"amount\": \"0.00\", \"account_class\": 3}], \"title\": \"Balance General\", \"period_end\": \"2025-12-05\", \"equation_ok\": false, \"period_start\": \"2025-12-05\", \"total_assets\": 100, \"total_equity\": 0, \"total_liabilities\": 0}', '51a78cceb7fa40eccb85e06aae49349aabfd8a731b2cab794c247c87514b659a', 1, 0, 5, '2025-12-05 01:44:21');

-- --------------------------------------------------------

--
-- Table structure for table `report_signatures`
--

CREATE TABLE `report_signatures` (
  `id` int NOT NULL,
  `report_id` int NOT NULL,
  `user_id` int NOT NULL,
  `signature_hash` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_snapshot` json NOT NULL,
  `signed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `report_signatures`
--

INSERT INTO `report_signatures` (`id`, `report_id`, `user_id`, `signature_hash`, `original_snapshot`, `signed_at`) VALUES
(1, 2, 2, 'd2e901f945a42a06f11b628e6e6117bf95a4185b5a7d3d70c6c0436c11d2badf', '{\"type\": \"balance_sheet\", \"lines\": [{\"code\": \"1.1.01\", \"name\": \"Caja General\", \"amount\": \"0.00\", \"account_class\": 1}, {\"code\": \"1.1.02\", \"name\": \"Bancos\", \"amount\": \"0.00\", \"account_class\": 1}, {\"code\": \"1.2.01\", \"name\": \"Cuentas por Cobrar\", \"amount\": \"0.00\", \"account_class\": 1}, {\"code\": \"1.3.01\", \"name\": \"Inventarios\", \"amount\": \"0.00\", \"account_class\": 1}, {\"code\": \"1.4.01\", \"name\": \"Equipos de Oficina\", \"amount\": \"0.00\", \"account_class\": 1}, {\"code\": \"2.1.01\", \"name\": \"Cuentas por Pagar\", \"amount\": \"0.00\", \"account_class\": 2}, {\"code\": \"2.2.01\", \"name\": \"Préstamos Bancarios\", \"amount\": \"0.00\", \"account_class\": 2}, {\"code\": \"3.1.01\", \"name\": \"Capital Social\", \"amount\": \"0.00\", \"account_class\": 3}, {\"code\": \"3.2.01\", \"name\": \"Utilidades Retenidas\", \"amount\": \"0.00\", \"account_class\": 3}, {\"code\": \"3.3.01\", \"name\": \"Utilidad del Ejercicio\", \"amount\": \"0.00\", \"account_class\": 3}, {\"code\": \"456\", \"name\": \"Prueba\", \"amount\": \"0.00\", \"account_class\": 2}], \"title\": \"Balance General\", \"period_end\": \"2025-12-01\", \"equation_ok\": true, \"period_start\": \"2025-12-01\", \"total_assets\": 0, \"total_equity\": 0, \"total_liabilities\": 0}', '2025-12-01 00:31:54'),
(2, 7, 5, '53d52a40bd706a55cbd0ea2b11ad090fed2846d2d36f94dd270bf2d8fd0f9548', '{\"type\": \"balance_sheet\", \"lines\": [{\"code\": \"1.1.01\", \"name\": \"Caja General\", \"amount\": \"50.00\", \"account_class\": 1}, {\"code\": \"1.1.02\", \"name\": \"Bancos\", \"amount\": \"50.00\", \"account_class\": 1}, {\"code\": \"1.2.01\", \"name\": \"Cuentas por Cobrar\", \"amount\": \"0.00\", \"account_class\": 1}, {\"code\": \"1.3.01\", \"name\": \"Inventarios\", \"amount\": \"0.00\", \"account_class\": 1}, {\"code\": \"1.4.01\", \"name\": \"Equipos de Oficina\", \"amount\": \"0.00\", \"account_class\": 1}, {\"code\": \"2.1.01\", \"name\": \"Cuentas por Pagar\", \"amount\": \"0.00\", \"account_class\": 2}, {\"code\": \"2.2.01\", \"name\": \"Préstamos Bancarios\", \"amount\": \"0.00\", \"account_class\": 2}, {\"code\": \"3.1.01\", \"name\": \"Capital Social\", \"amount\": \"0.00\", \"account_class\": 3}, {\"code\": \"3.2.01\", \"name\": \"Utilidades Retenidas\", \"amount\": \"0.00\", \"account_class\": 3}, {\"code\": \"3.3.01\", \"name\": \"Utilidad del Ejercicio\", \"amount\": \"0.00\", \"account_class\": 3}], \"title\": \"Balance General\", \"period_end\": \"2025-12-05\", \"equation_ok\": false, \"period_start\": \"2025-12-05\", \"total_assets\": 100, \"total_equity\": 0, \"total_liabilities\": 0}', '2025-12-05 01:45:38'),
(3, 6, 5, 'e1c69e2de01a168eeb789e3be12eafe65e475a82377f9892a7f9e1fa16bed7ce', '{\"type\": \"income_statement\", \"lines\": [{\"code\": \"4.1.01\", \"name\": \"Ventas\", \"amount\": \"100.00\", \"account_type\": \"credit\", \"account_class\": 4}], \"title\": \"Estado de Resultados\", \"net_income\": 100, \"period_end\": \"2025-12-31\", \"period_start\": \"2025-01-01\", \"total_income\": 100, \"total_expense\": 0}', '2025-12-05 01:45:41'),
(4, 4, 5, '74761ce125852f7ab5e8871539dd1f10c8695a937ab051563a1daa3ba26e8e49', '{\"type\": \"balance_sheet\", \"lines\": [{\"code\": \"1.1.01\", \"name\": \"Caja General\", \"amount\": \"0.00\", \"account_class\": 1}, {\"code\": \"1.1.02\", \"name\": \"Bancos\", \"amount\": \"0.00\", \"account_class\": 1}, {\"code\": \"1.2.01\", \"name\": \"Cuentas por Cobrar\", \"amount\": \"0.00\", \"account_class\": 1}, {\"code\": \"1.3.01\", \"name\": \"Inventarios\", \"amount\": \"0.00\", \"account_class\": 1}, {\"code\": \"1.4.01\", \"name\": \"Equipos de Oficina\", \"amount\": \"0.00\", \"account_class\": 1}, {\"code\": \"2.1.01\", \"name\": \"Cuentas por Pagar\", \"amount\": \"0.00\", \"account_class\": 2}, {\"code\": \"2.2.01\", \"name\": \"Préstamos Bancarios\", \"amount\": \"0.00\", \"account_class\": 2}, {\"code\": \"3.1.01\", \"name\": \"Capital Social\", \"amount\": \"0.00\", \"account_class\": 3}, {\"code\": \"3.2.01\", \"name\": \"Utilidades Retenidas\", \"amount\": \"0.00\", \"account_class\": 3}, {\"code\": \"3.3.01\", \"name\": \"Utilidad del Ejercicio\", \"amount\": \"0.00\", \"account_class\": 3}, {\"code\": \"456\", \"name\": \"Prueba\", \"amount\": \"0.00\", \"account_class\": 2}], \"title\": \"Balance General\", \"period_end\": \"2025-12-26\", \"equation_ok\": true, \"period_start\": \"2025-12-26\", \"total_assets\": 0, \"total_equity\": 0, \"total_liabilities\": 0}', '2025-12-05 01:45:46'),
(5, 5, 5, '4eccc936fdf1b7151840df8e42d9648786a29978bf6918cd5ac75b80d5b601e3', '{\"type\": \"income_statement\", \"lines\": [{\"code\": \"4.1.01\", \"name\": \"Ventas\", \"amount\": \"100.00\", \"account_type\": \"credit\", \"account_class\": 4}], \"title\": \"Estado de Resultados\", \"net_income\": 100, \"period_end\": \"2025-12-31\", \"period_start\": \"2025-01-01\", \"total_income\": 100, \"total_expense\": 0}', '2025-12-05 12:24:18');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Administrador', 'Control total del sistema, gestión de usuarios y roles', '2025-11-28 22:48:49'),
(2, 'Contador', 'Registro de transacciones, gestión del catálogo de cuentas', '2025-11-28 22:48:49'),
(3, 'Gerente Financiero', 'Generación y firma de reportes financieros', '2025-11-28 22:48:49'),
(4, 'Auditor', 'Consulta de reportes y logs, sin capacidad de modificación', '2025-11-28 22:48:49');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id` int NOT NULL,
  `role_id` int NOT NULL,
  `module` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `can_create` tinyint(1) DEFAULT '0',
  `can_read` tinyint(1) DEFAULT '0',
  `can_update` tinyint(1) DEFAULT '0',
  `can_delete` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`id`, `role_id`, `module`, `can_create`, `can_read`, `can_update`, `can_delete`) VALUES
(1, 1, 'users', 1, 1, 1, 0),
(2, 1, 'roles', 1, 1, 1, 1),
(3, 1, 'accounts', 1, 1, 1, 1),
(4, 1, 'transactions', 1, 1, 1, 1),
(5, 1, 'reports', 1, 1, 0, 0),
(6, 2, 'accounts', 1, 1, 1, 0),
(7, 2, 'transactions', 1, 1, 1, 0),
(8, 2, 'reports', 0, 1, 0, 0),
(9, 3, 'reports', 1, 1, 0, 0),
(10, 3, 'signatures', 1, 1, 0, 0),
(11, 3, 'transactions', 0, 1, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int NOT NULL,
  `transaction_date` date NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_posted` tinyint(1) DEFAULT '0',
  `created_by` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `transaction_date`, `description`, `is_posted`, `created_by`, `created_at`, `updated_at`) VALUES
(1, '2025-12-01', 'Prueba', 1, 1, '2025-12-01 12:51:23', '2025-12-01 12:51:23'),
(2, '2025-12-04', 'Venta contado', 1, 4, '2025-12-05 01:41:59', '2025-12-05 01:41:59'),
(3, '2025-12-05', 'Cuenta Pagos Transaccion', 1, 1, '2025-12-05 12:21:56', '2025-12-05 12:21:56');

--
-- Triggers `transactions`
--
DELIMITER $$
CREATE TRIGGER `check_balanced_transaction` BEFORE UPDATE ON `transactions` FOR EACH ROW BEGIN
    DECLARE total_debit DECIMAL(15,2);
    DECLARE total_credit DECIMAL(15,2);
    
    IF NEW.is_posted = TRUE AND OLD.is_posted = FALSE THEN
        SELECT COALESCE(SUM(debit), 0), COALESCE(SUM(credit), 0)
        INTO total_debit, total_credit
        FROM transaction_lines
        WHERE transaction_id = NEW.id;
        
        IF total_debit != total_credit THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Error: La transacción no está balanceada. Débitos deben igualar créditos.';
        END IF;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_lines`
--

CREATE TABLE `transaction_lines` (
  `id` int NOT NULL,
  `transaction_id` int NOT NULL,
  `account_id` int NOT NULL,
  `debit` decimal(15,2) DEFAULT '0.00',
  `credit` decimal(15,2) DEFAULT '0.00',
  `memo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ;

--
-- Dumping data for table `transaction_lines`
--

INSERT INTO `transaction_lines` (`id`, `transaction_id`, `account_id`, `debit`, `credit`, `memo`) VALUES
(1, 1, 2, 50.00, 0.00, 'Débito'),
(2, 1, 1, 0.00, 50.00, 'Crédito'),
(3, 2, 1, 100.00, 0.00, 'Débito'),
(4, 2, 11, 0.00, 100.00, 'Crédito'),
(5, 3, 18, 500.00, 0.00, 'Débito'),
(6, 3, 14, 0.00, 500.00, 'Crédito');

--
-- Triggers `transaction_lines`
--
DELIMITER $$
CREATE TRIGGER `update_account_balance` AFTER INSERT ON `transaction_lines` FOR EACH ROW BEGIN
    DECLARE tx_posted BOOLEAN;
    
    SELECT is_posted INTO tx_posted
    FROM transactions
    WHERE id = NEW.transaction_id;
    
    IF tx_posted = TRUE THEN
        UPDATE accounts
        SET balance = balance + NEW.debit - NEW.credit
        WHERE id = NEW.account_id;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `full_name`, `email`, `is_active`, `created_at`, `created_by`, `updated_at`, `last_login`) VALUES
(1, 'admin', '$2y$10$r2i/jsA7AOjWhbBisvjauuREGE33VOSs/qWfv2/992sqhUc5BLGJS', 'Administrador del Sistema', 'admin@sistemafinanciero.com', 1, '2025-11-28 22:48:49', NULL, '2025-12-07 16:14:29', '2025-12-07 16:14:29'),
(2, 'prueba', '$2y$10$aeeZ6.j8MEzl2RtVWyKrr.svkjzwkr8prSdhqImnPadzabah5Yede', 'Prueba Gonzalez', 'prueba1@gmail.com', 0, '2025-11-30 23:55:07', 1, '2025-12-05 01:33:27', '2025-12-01 00:31:49'),
(3, 'prueba2', '$2y$10$.xO1NIz/ZVjh0LpfM554FeVQ0Eh1wZbx7luwI.YcWAB6KEQHGHJg2', 'Prueba Prueba', 'prueba2@hotmail.com', 0, '2025-12-01 00:33:14', 1, '2025-12-05 01:46:53', '2025-12-01 13:36:48'),
(4, 'Contador', '$2y$10$kyGKT0DI084A801TIapTyeUjUNYL/MBfNoWwG/d0pUknPuN.y1Gj6', 'Pedro', 'pedro@hotmail.com', 1, '2025-12-05 01:32:56', 1, '2025-12-05 01:35:31', '2025-12-05 01:35:31'),
(5, 'Gerente', '$2y$10$hUBY60Ynx5JbAiaVrxxgGeYvTNTT5G6WWuvgLoryF0eOCHOUFbNJe', 'Pablo', 'pablo123@gmail.com', 1, '2025-12-05 01:33:18', 1, '2025-12-05 12:23:17', '2025-12-05 12:23:17'),
(6, 'Auditor', '$2y$10$XE/DsKcXzf6DLPD9UpO/iOEVnkwRtcDVGQo2It.GZlcQMeWeHv/RG', 'Auditor', 'auditor@gmail.com', 1, '2025-12-05 01:46:14', 5, '2025-12-07 16:14:49', '2025-12-07 16:14:49');

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `role_id` int NOT NULL,
  `assigned_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `assigned_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id`, `user_id`, `role_id`, `assigned_at`, `assigned_by`) VALUES
(1, 1, 1, '2025-11-28 22:48:49', NULL),
(4, 5, 3, '2025-12-05 01:34:16', 1),
(6, 6, 4, '2025-12-05 01:46:34', 1),
(7, 4, 2, '2025-12-05 12:19:34', 1);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_balance_sheet`
-- (See below for the actual view)
--
CREATE TABLE `view_balance_sheet` (
`code` varchar(20)
,`name` varchar(150)
,`account_class` tinyint
,`amount` decimal(15,2)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_income_statement`
-- (See below for the actual view)
--
CREATE TABLE `view_income_statement` (
`code` varchar(20)
,`name` varchar(150)
,`account_class` tinyint
,`amount` decimal(38,2)
);

-- --------------------------------------------------------

--
-- Structure for view `view_balance_sheet`
--
DROP TABLE IF EXISTS `view_balance_sheet`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_balance_sheet`  AS SELECT `a`.`code` AS `code`, `a`.`name` AS `name`, `a`.`account_class` AS `account_class`, `a`.`balance` AS `amount` FROM `accounts` AS `a` WHERE ((`a`.`account_class` in (1,2,3)) AND (`a`.`is_active` = true)) ORDER BY `a`.`code` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `view_income_statement`
--
DROP TABLE IF EXISTS `view_income_statement`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_income_statement`  AS SELECT `a`.`code` AS `code`, `a`.`name` AS `name`, `a`.`account_class` AS `account_class`, sum((case when (`a`.`account_type` = 'credit') then (`tl`.`credit` - `tl`.`debit`) else (`tl`.`debit` - `tl`.`credit`) end)) AS `amount` FROM ((`accounts` `a` join `transaction_lines` `tl` on((`a`.`id` = `tl`.`account_id`))) join `transactions` `t` on((`tl`.`transaction_id` = `t`.`id`))) WHERE ((`t`.`is_posted` = true) AND (`a`.`account_class` in (4,5,6,7))) GROUP BY `a`.`id`, `a`.`code`, `a`.`name`, `a`.`account_class` ORDER BY `a`.`code` ASC ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `fk_account_created_by` (`created_by`),
  ADD KEY `idx_code` (`code`),
  ADD KEY `idx_class` (`account_class`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_table_name` (`table_name`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_report_generated_by` (`generated_by`),
  ADD KEY `idx_type` (`report_type`),
  ADD KEY `idx_period` (`period_start`,`period_end`),
  ADD KEY `idx_signed` (`is_signed`);

--
-- Indexes for table `report_signatures`
--
ALTER TABLE `report_signatures`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `report_id` (`report_id`),
  ADD KEY `idx_report_id` (`report_id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `idx_name` (`name`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_role_module` (`role_id`,`module`),
  ADD KEY `idx_role_id` (`role_id`),
  ADD KEY `idx_module` (`module`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_date` (`transaction_date`),
  ADD KEY `idx_posted` (`is_posted`),
  ADD KEY `idx_created_by` (`created_by`);

--
-- Indexes for table `transaction_lines`
--
ALTER TABLE `transaction_lines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_transaction_id` (`transaction_id`),
  ADD KEY `idx_account_id` (`account_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_user_created_by` (`created_by`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_role` (`user_id`,`role_id`),
  ADD KEY `fk_user_role_assigned_by` (`assigned_by`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `report_signatures`
--
ALTER TABLE `report_signatures`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `transaction_lines`
--
ALTER TABLE `transaction_lines`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accounts`
--
ALTER TABLE `accounts`
  ADD CONSTRAINT `fk_account_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `fk_report_generated_by` FOREIGN KEY (`generated_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `report_signatures`
--
ALTER TABLE `report_signatures`
  ADD CONSTRAINT `fk_signature_report` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_signature_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `fk_role_permission_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `fk_transaction_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `transaction_lines`
--
ALTER TABLE `transaction_lines`
  ADD CONSTRAINT `fk_line_account` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `fk_line_transaction` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_user_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `fk_user_role_assigned_by` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_user_role_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user_role_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
