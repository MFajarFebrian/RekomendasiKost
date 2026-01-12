-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 04, 2026 at 05:06 PM
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
-- Database: `spk_kost`
--

-- --------------------------------------------------------

--
-- Table structure for table `calculation_history`
--

CREATE TABLE `calculation_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `calculation_type` enum('ahp','topsis') NOT NULL,
  `input_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`input_data`)),
  `result_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`result_data`)),
  `execution_time` double DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kampus`
--

CREATE TABLE `kampus` (
  `id` int(11) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `kode` varchar(20) DEFAULT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `kota` varchar(100) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kampus`
--

INSERT INTO `kampus` (`id`, `nama`, `kode`, `alamat`, `kota`, `latitude`, `longitude`, `is_active`, `created_at`) VALUES
(1, 'Universitas Gunadarma Kampus J1', 'GD-J1', 'Jl. Margonda Raya No.100', 'Bekasi', -6.37020000, 106.82340000, 1, '2026-01-04 13:17:49'),
(7, 'Universitas Indonesia', 'UI', 'Kampus UI Depok', 'Depok', -6.36080000, 106.82720000, 1, '2026-01-04 13:17:49'),
(8, 'Institut Pertanian Bogor', 'IPB', 'Jl. Raya Dramaga', 'Bogor', -6.55890000, 106.72680000, 1, '2026-01-04 13:17:49'),
(9, 'Universitas Pancasila', 'UP', 'Jl. Raya Lenteng Agung', 'Jakarta', -6.32980000, 106.83120000, 1, '2026-01-04 13:17:49'),
(10, 'Universitas Mercu Buana', 'UMB', 'Jl. Meruya Selatan', 'Jakarta', -6.21560000, 106.73420000, 1, '2026-01-04 13:17:49');

-- --------------------------------------------------------

--
-- Table structure for table `kost`
--

CREATE TABLE `kost` (
  `id` int(11) NOT NULL,
  `nama` varchar(191) NOT NULL,
  `jarak_kampus` double NOT NULL,
  `jarak_market` double NOT NULL,
  `harga` double NOT NULL,
  `kebersihan` int(3) NOT NULL,
  `keamanan` int(3) NOT NULL,
  `fasilitas` int(3) NOT NULL,
  `kampus_id` int(11) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `foto_utama` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kost`
--

INSERT INTO `kost` (`id`, `nama`, `jarak_kampus`, `jarak_market`, `harga`, `kebersihan`, `keamanan`, `fasilitas`, `kampus_id`, `deskripsi`, `alamat`, `latitude`, `longitude`, `foto_utama`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Kost Papipul Pakuwon Mezanine', 1.2, 0.5, 2500000, 5, 4, 5, 1, NULL, NULL, NULL, NULL, NULL, 1, '2026-01-04 13:11:37', '2026-01-04 13:17:49'),
(2, 'Kost Eleora Cikunir Tipe C', 2.5, 1, 1299000, 4, 5, 5, 1, NULL, NULL, NULL, NULL, NULL, 1, '2026-01-04 13:11:37', '2026-01-04 13:17:49'),
(3, 'Kost De Jatti', 3.1, 0.8, 1400000, 4, 4, 5, 1, NULL, NULL, NULL, NULL, NULL, 1, '2026-01-04 13:11:37', '2026-01-04 13:17:49'),
(4, 'Kost Delta Timur 102 Tipe A Pekayon', 1.8, 0.3, 1674000, 5, 5, 5, 1, NULL, NULL, NULL, NULL, NULL, 1, '2026-01-04 13:11:37', '2026-01-04 13:17:49'),
(5, 'Kost Fans Rooms', 0.5, 0.2, 1500000, 3, 3, 4, 1, NULL, NULL, NULL, NULL, NULL, 1, '2026-01-04 13:11:37', '2026-01-04 13:17:49'),
(6, 'Kost Krakatau 1B Tipe A', 2.2, 1.5, 1250500, 4, 3, 4, 1, NULL, NULL, NULL, NULL, NULL, 1, '2026-01-04 13:11:37', '2026-01-04 13:17:49'),
(7, 'Kost CRV Cikas Tipe A Galaxy', 1.5, 0.5, 1325000, 5, 5, 5, 1, NULL, NULL, NULL, NULL, NULL, 1, '2026-01-04 13:11:37', '2026-01-04 13:17:49'),
(8, 'Kost Eleora Cikunir Tipe A', 2.5, 1, 956000, 4, 4, 5, 1, NULL, NULL, NULL, NULL, NULL, 1, '2026-01-04 13:11:37', '2026-01-04 13:17:49'),
(9, 'Kost Pink Moon Tipe B', 0.9, 0.4, 1350000, 4, 3, 4, 1, NULL, NULL, NULL, NULL, NULL, 1, '2026-01-04 13:11:37', '2026-01-04 13:17:49'),
(10, 'Kost Ibu Datin Tipe C', 3.5, 1.2, 800000, 3, 3, 3, 1, NULL, NULL, NULL, NULL, NULL, 1, '2026-01-04 13:11:37', '2026-01-04 13:17:49'),
(11, 'Kost Khazanah VIP Semi apartment', 1.1, 0.6, 1250000, 4, 4, 5, 1, NULL, NULL, NULL, NULL, NULL, 1, '2026-01-04 13:11:37', '2026-01-04 13:17:49'),
(12, 'Kost Ezra Tipe A', 2, 1.1, 700000, 3, 2, 3, 1, NULL, NULL, NULL, NULL, NULL, 1, '2026-01-04 13:11:37', '2026-01-04 13:17:49'),
(13, 'Kost De Miracle Inthecost Tipe B', 1.6, 0.7, 1500000, 4, 4, 5, 1, NULL, NULL, NULL, NULL, NULL, 1, '2026-01-04 13:11:37', '2026-01-04 13:17:49'),
(14, 'Kost Khazanah Tipe Vvip Executive', 1.2, 0.6, 1250000, 5, 5, 5, 1, NULL, NULL, NULL, NULL, NULL, 1, '2026-01-04 13:11:37', '2026-01-04 13:17:49'),
(15, 'Kost Kayuringin', 2.8, 1.3, 900000, 3, 3, 3, 1, NULL, NULL, NULL, NULL, NULL, 1, '2026-01-04 13:11:37', '2026-01-04 13:17:49'),
(16, 'Kost Manohara', 0.8, 0.3, 1750000, 5, 4, 4, 1, NULL, NULL, NULL, NULL, NULL, 1, '2026-01-04 13:11:37', '2026-01-04 13:17:49'),
(17, 'Rumah Kontrakan FHS Rent House', 3, 1.5, 1000000, 3, 3, 3, 1, NULL, NULL, NULL, NULL, NULL, 1, '2026-01-04 13:11:37', '2026-01-04 13:17:49'),
(18, 'Kost Pink Moon Tipe C', 0.9, 0.4, 1550000, 4, 4, 4, 1, NULL, NULL, NULL, NULL, NULL, 1, '2026-01-04 13:11:37', '2026-01-04 13:17:49'),
(19, 'Kost Galaxy Living 1 Executive', 1.4, 0.5, 1850000, 5, 5, 5, 1, NULL, NULL, NULL, NULL, NULL, 1, '2026-01-04 13:11:37', '2026-01-04 13:17:49'),
(20, 'Kost Aa Kepin Vvip', 1.7, 0.8, 1250000, 4, 3, 4, 1, NULL, NULL, NULL, NULL, NULL, 1, '2026-01-04 13:11:37', '2026-01-04 13:17:49');

-- --------------------------------------------------------

--
-- Table structure for table `kost_images`
--

CREATE TABLE `kost_images` (
  `id` int(11) NOT NULL,
  `kost_id` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `caption` varchar(191) DEFAULT NULL,
  `urutan` int(3) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `temp_bobot`
--

CREATE TABLE `temp_bobot` (
  `id` int(11) NOT NULL,
  `kriteria` varchar(191) NOT NULL,
  `jarak_kampus` double NOT NULL,
  `jarak_market` double NOT NULL,
  `harga` double NOT NULL,
  `kebersihan` double NOT NULL,
  `keamanan` double NOT NULL,
  `fasilitas` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `temp_bobot`
--

INSERT INTO `temp_bobot` (`id`, `kriteria`, `jarak_kampus`, `jarak_market`, `harga`, `kebersihan`, `keamanan`, `fasilitas`) VALUES
(1, 'Jarak Kampus', 1, 2, 0.25, 1, 0.6667, 0.5),
(2, 'Jarak Market', 0.5, 1, 0.125, 0.5, 0.3333, 0.25),
(3, 'Harga', 4, 8, 1, 4, 2.6667, 2),
(4, 'Kebersihan', 1, 2, 0.25, 1, 0.6667, 0.5),
(5, 'Keamanan', 1.5, 3, 0.375, 1.5, 1, 0.75),
(6, 'Fasilitas', 2, 4, 0.5, 2, 1.3333, 1);

-- --------------------------------------------------------

--
-- Table structure for table `temp_d_neg`
--

CREATE TABLE `temp_d_neg` (
  `id` int(11) NOT NULL,
  `nama` varchar(191) NOT NULL,
  `dNegatif` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `temp_d_neg`
--

INSERT INTO `temp_d_neg` (`id`, `nama`, `dNegatif`) VALUES
(1, 'Kost Papipul Pakuwon Mezanine', 0.066895705783652),
(2, 'Kost Eleora Cikunir Tipe C', 0.083918219466638),
(3, 'Kost De Jatti', 0.076744728128438),
(4, 'Kost Delta Timur 102 Tipe A Pekayon', 0.082792374150733),
(5, 'Kost Fans Rooms', 0.10578013375918),
(6, 'Kost Krakatau 1B Tipe A', 0.084852017817928),
(7, 'Kost CRV Cikas Tipe A Galaxy', 0.097699277204841),
(8, 'Kost Eleora Cikunir Tipe A', 0.10327472059625),
(9, 'Kost Pink Moon Tipe B', 0.10320459465308),
(10, 'Kost Ibu Datin Tipe C', 0.10861912954146),
(11, 'Kost Khazanah VIP Semi apartment', 0.10303668979335),
(12, 'Kost Ezra Tipe A', 0.12005221835734),
(13, 'Kost De Miracle Inthecost Tipe B', 0.083730102115755),
(14, 'Kost Khazanah Tipe Vvip Executive', 0.10311726159271),
(15, 'Kost Kayuringin', 0.10315020707276),
(16, 'Kost Manohara', 0.091658738369308),
(17, 'Rumah Kontrakan FHS Rent House', 0.095971640590969),
(18, 'Kost Pink Moon Tipe C', 0.09518496438998),
(19, 'Kost Galaxy Living 1 Executive', 0.076790086183327),
(20, 'Kost Aa Kepin Vvip', 0.093479830794272);

-- --------------------------------------------------------

--
-- Table structure for table `temp_d_pos`
--

CREATE TABLE `temp_d_pos` (
  `id` int(11) NOT NULL,
  `nama` varchar(191) NOT NULL,
  `dPositif` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `temp_d_pos`
--

INSERT INTO `temp_d_pos` (`id`, `nama`, `dPositif`) VALUES
(1, 'Kost Papipul Pakuwon Mezanine', 0.11598539333868),
(2, 'Kost Eleora Cikunir Tipe C', 0.066854346008886),
(3, 'Kost De Jatti', 0.077377562658457),
(4, 'Kost Delta Timur 102 Tipe A Pekayon', 0.068424261825882),
(5, 'Kost Fans Rooms', 0.053206118724152),
(6, 'Kost Krakatau 1B Tipe A', 0.07376357141301),
(7, 'Kost CRV Cikas Tipe A Galaxy', 0.047041657702904),
(8, 'Kost Eleora Cikunir Tipe A', 0.057641727056915),
(9, 'Kost Pink Moon Tipe B', 0.044826297795511),
(10, 'Kost Ibu Datin Tipe C', 0.079881125581476),
(11, 'Kost Khazanah VIP Semi apartment', 0.041311682051952),
(12, 'Kost Ezra Tipe A', 0.053087766294386),
(13, 'Kost De Miracle Inthecost Tipe B', 0.060252905090474),
(14, 'Kost Khazanah Tipe Vvip Executive', 0.041347765641408),
(15, 'Kost Kayuringin', 0.070421205696556),
(16, 'Kost Manohara', 0.06734641087912),
(17, 'Rumah Kontrakan FHS Rent House', 0.079886109211976),
(18, 'Kost Pink Moon Tipe C', 0.055835811543737),
(19, 'Kost Galaxy Living 1 Executive', 0.07659229205683),
(20, 'Kost Aa Kepin Vvip', 0.051602367016719);

-- --------------------------------------------------------

--
-- Table structure for table `temp_nilai_pref`
--

CREATE TABLE `temp_nilai_pref` (
  `id` int(11) NOT NULL,
  `nama` varchar(191) NOT NULL,
  `val` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `temp_nilai_pref`
--

INSERT INTO `temp_nilai_pref` (`id`, `nama`, `val`) VALUES
(1, 'Kost Khazanah VIP Semi apartment', 0.71380569435015),
(2, 'Kost Khazanah Tipe Vvip Executive', 0.71378702213928),
(3, 'Kost Pink Moon Tipe B', 0.69718281735632),
(4, 'Kost Ezra Tipe A', 0.69338240152225),
(5, 'Kost CRV Cikas Tipe A Galaxy', 0.67499410078505),
(6, 'Kost Fans Rooms', 0.66534138711314),
(7, 'Kost Aa Kepin Vvip', 0.64432323334428),
(8, 'Kost Eleora Cikunir Tipe A', 0.64179095488638),
(9, 'Kost Pink Moon Tipe C', 0.63027728338356),
(10, 'Kost Kayuringin', 0.59428108250667),
(11, 'Kost De Miracle Inthecost Tipe B', 0.58152766594066),
(12, 'Kost Manohara', 0.57645138413789),
(13, 'Kost Ibu Datin Tipe C', 0.57622802404496),
(14, 'Kost Eleora Cikunir Tipe C', 0.55658812464965),
(15, 'Kost Delta Timur 102 Tipe A Pekayon', 0.54750837178745),
(16, 'Rumah Kontrakan FHS Rent House', 0.54573449676519),
(17, 'Kost Krakatau 1B Tipe A', 0.53495383542904),
(18, 'Kost Galaxy Living 1 Executive', 0.5006447746109),
(19, 'Kost De Jatti', 0.49794697273578),
(20, 'Kost Papipul Pakuwon Mezanine', 0.36578796882068);

-- --------------------------------------------------------

--
-- Table structure for table `temp_normalisasi`
--

CREATE TABLE `temp_normalisasi` (
  `id` int(11) NOT NULL,
  `nama` varchar(191) NOT NULL,
  `jarak_kampus` double NOT NULL,
  `jarak_market` double NOT NULL,
  `harga` double NOT NULL,
  `kebersihan` double NOT NULL,
  `keamanan` double NOT NULL,
  `fasilitas` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `temp_normalisasi`
--

INSERT INTO `temp_normalisasi` (`id`, `nama`, `jarak_kampus`, `jarak_market`, `harga`, `kebersihan`, `keamanan`, `fasilitas`) VALUES
(1, 'Kost Papipul Pakuwon Mezanine', 0.13455147181194, 0.13076644524149, 0.39642364216251, 0.27156272329678, 0.22941573387056, 0.25582225504833),
(2, 'Kost Eleora Cikunir Tipe C', 0.28031556627487, 0.26153289048297, 0.20598172446764, 0.21725017863743, 0.2867696673382, 0.25582225504833),
(3, 'Kost De Jatti', 0.34759130218084, 0.20922631238638, 0.221997239611, 0.21725017863743, 0.22941573387056, 0.25582225504833),
(4, 'Kost Delta Timur 102 Tipe A Pekayon', 0.20182720771791, 0.078459867144891, 0.26544527079202, 0.27156272329678, 0.2867696673382, 0.25582225504833),
(5, 'Kost Fans Rooms', 0.056063113254974, 0.052306578096594, 0.2378541852975, 0.16293763397807, 0.17206180040292, 0.20465780403866),
(6, 'Kost Krakatau 1B Tipe A', 0.24667769832189, 0.39229933572446, 0.19829110580969, 0.21725017863743, 0.17206180040292, 0.20465780403866),
(7, 'Kost CRV Cikas Tipe A Galaxy', 0.16818933976492, 0.13076644524149, 0.21010453034613, 0.27156272329678, 0.2867696673382, 0.25582225504833),
(8, 'Kost Eleora Cikunir Tipe A', 0.28031556627487, 0.26153289048297, 0.15159240076294, 0.21725017863743, 0.22941573387056, 0.25582225504833),
(9, 'Kost Pink Moon Tipe B', 0.10091360385895, 0.10461315619319, 0.21406876676775, 0.21725017863743, 0.17206180040292, 0.20465780403866),
(10, 'Kost Ibu Datin Tipe C', 0.39244179278482, 0.31383946857956, 0.126855565492, 0.16293763397807, 0.17206180040292, 0.153493353029),
(11, 'Kost Khazanah VIP Semi apartment', 0.12333884916094, 0.15691973428978, 0.19821182108125, 0.21725017863743, 0.22941573387056, 0.25582225504833),
(12, 'Kost Ezra Tipe A', 0.2242524530199, 0.28768617953127, 0.1109986198055, 0.16293763397807, 0.11470786693528, 0.153493353029),
(13, 'Kost De Miracle Inthecost Tipe B', 0.17940196241592, 0.18307302333808, 0.2378541852975, 0.21725017863743, 0.22941573387056, 0.25582225504833),
(14, 'Kost Khazanah Tipe Vvip Executive', 0.13455147181194, 0.15691973428978, 0.19821182108125, 0.27156272329678, 0.2867696673382, 0.25582225504833),
(15, 'Kost Kayuringin', 0.31395343422786, 0.33999275762786, 0.1427125111785, 0.16293763397807, 0.17206180040292, 0.153493353029),
(16, 'Kost Manohara', 0.089700981207959, 0.078459867144891, 0.27749654951376, 0.27156272329678, 0.22941573387056, 0.20465780403866),
(17, 'Rumah Kontrakan FHS Rent House', 0.33637867952985, 0.39229933572446, 0.158569456865, 0.16293763397807, 0.17206180040292, 0.153493353029),
(18, 'Kost Pink Moon Tipe C', 0.10091360385895, 0.10461315619319, 0.24578265814075, 0.21725017863743, 0.22941573387056, 0.20465780403866),
(19, 'Kost Galaxy Living 1 Executive', 0.15697671711393, 0.13076644524149, 0.29335349520026, 0.27156272329678, 0.2867696673382, 0.25582225504833),
(20, 'Kost Aa Kepin Vvip', 0.19061458506691, 0.20922631238638, 0.19821182108125, 0.21725017863743, 0.17206180040292, 0.20465780403866);

-- --------------------------------------------------------

--
-- Table structure for table `temp_normalisasi_kriteria`
--

CREATE TABLE `temp_normalisasi_kriteria` (
  `id` int(11) NOT NULL,
  `kriteria` varchar(191) NOT NULL,
  `jarak_kampus` double NOT NULL,
  `jarak_market` double NOT NULL,
  `harga` double NOT NULL,
  `kebersihan` double NOT NULL,
  `keamanan` double NOT NULL,
  `fasilitas` double NOT NULL,
  `avg` double DEFAULT NULL,
  `matrix_aw` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `temp_normalisasi_kriteria`
--

INSERT INTO `temp_normalisasi_kriteria` (`id`, `kriteria`, `jarak_kampus`, `jarak_market`, `harga`, `kebersihan`, `keamanan`, `fasilitas`, `avg`, `matrix_aw`) VALUES
(1, 'Jarak Kampus', 0.1, 0.1, 0.1, 0.1, 0.1000044999775, 0.1, 0.10000074999625, 0.60000429166604),
(2, 'Jarak Market', 0.05, 0.05, 0.05, 0.05, 0.04999475002625, 0.05, 0.049999125004375, 0.29999464583927),
(3, 'Harga', 0.4, 0.4, 0.4, 0.4, 0.400002999985, 0.4, 0.4000004999975, 2.4000021666767),
(4, 'Kebersihan', 0.1, 0.1, 0.1, 0.1, 0.1000044999775, 0.1, 0.10000074999625, 0.60000429166604),
(5, 'Keamanan', 0.15, 0.15, 0.15, 0.15, 0.14999925000375, 0.15, 0.14999987500063, 0.89999893750531),
(6, 'Fasilitas', 0.2, 0.2, 0.2, 0.2, 0.19999400003, 0.2, 0.199999000005, 1.1999935833446);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(191) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(191) NOT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `foto_profil` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `nama`, `telepon`, `role`, `foto_profil`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'admin@spkkost.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', NULL, 'admin', NULL, 1, '2026-01-04 13:11:37', '2026-01-04 13:11:37');

-- --------------------------------------------------------

--
-- Table structure for table `user_preferences`
--

CREATE TABLE `user_preferences` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `max_harga` double DEFAULT NULL,
  `max_jarak_kampus` double DEFAULT NULL,
  `min_kebersihan` int(3) DEFAULT NULL,
  `min_keamanan` int(3) DEFAULT NULL,
  `min_fasilitas` int(3) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `calculation_history`
--
ALTER TABLE `calculation_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id_idx` (`user_id`),
  ADD KEY `created_at_idx` (`created_at`);

--
-- Indexes for table `kampus`
--
ALTER TABLE `kampus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kota_idx` (`kota`);

--
-- Indexes for table `kost`
--
ALTER TABLE `kost`
  ADD PRIMARY KEY (`id`),
  ADD KEY `harga_idx` (`harga`),
  ADD KEY `jarak_kampus_idx` (`jarak_kampus`),
  ADD KEY `is_active_idx` (`is_active`),
  ADD KEY `kampus_id_idx` (`kampus_id`);

--
-- Indexes for table `kost_images`
--
ALTER TABLE `kost_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kost_id_idx` (`kost_id`);

--
-- Indexes for table `temp_bobot`
--
ALTER TABLE `temp_bobot`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `temp_d_neg`
--
ALTER TABLE `temp_d_neg`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `temp_d_pos`
--
ALTER TABLE `temp_d_pos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `temp_nilai_pref`
--
ALTER TABLE `temp_nilai_pref`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `temp_normalisasi`
--
ALTER TABLE `temp_normalisasi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `temp_normalisasi_kriteria`
--
ALTER TABLE `temp_normalisasi_kriteria`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_unique` (`email`),
  ADD KEY `role_idx` (`role`);

--
-- Indexes for table `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id_idx` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `calculation_history`
--
ALTER TABLE `calculation_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kampus`
--
ALTER TABLE `kampus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `kost`
--
ALTER TABLE `kost`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `kost_images`
--
ALTER TABLE `kost_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `temp_bobot`
--
ALTER TABLE `temp_bobot`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `temp_d_neg`
--
ALTER TABLE `temp_d_neg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `temp_d_pos`
--
ALTER TABLE `temp_d_pos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `temp_nilai_pref`
--
ALTER TABLE `temp_nilai_pref`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `temp_normalisasi`
--
ALTER TABLE `temp_normalisasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `temp_normalisasi_kriteria`
--
ALTER TABLE `temp_normalisasi_kriteria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_preferences`
--
ALTER TABLE `user_preferences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
