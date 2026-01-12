-- PostgreSQL Schema for SPK Kost
-- Adapted from MySQL dump

-- Enable UUID extension if needed usually not for this simple schema but good practice
-- CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- --------------------------------------------------------

--
-- Table structure for table calculation_history
--

CREATE TABLE calculation_history (
  id SERIAL PRIMARY KEY,
  user_id INTEGER DEFAULT NULL,
  calculation_type VARCHAR(20) NOT NULL CHECK (calculation_type IN ('ahp', 'topsis')),
  input_data JSONB DEFAULT NULL,
  result_data JSONB DEFAULT NULL,
  execution_time DOUBLE PRECISION DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_calc_user_id ON calculation_history(user_id);
CREATE INDEX idx_calc_created_at ON calculation_history(created_at);

-- --------------------------------------------------------

--
-- Table structure for table kampus
--

CREATE TABLE kampus (
  id SERIAL PRIMARY KEY,
  nama VARCHAR(150) NOT NULL,
  kode VARCHAR(20) DEFAULT NULL,
  alamat VARCHAR(255) DEFAULT NULL,
  kota VARCHAR(100) DEFAULT NULL,
  latitude DECIMAL(10,8) DEFAULT NULL,
  longitude DECIMAL(11,8) DEFAULT NULL,
  is_active SMALLINT NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_kampus_kota ON kampus(kota);

--
-- Dumping data for table kampus
--

INSERT INTO kampus (nama, kode, alamat, kota, latitude, longitude, is_active) VALUES
('Universitas Gunadarma Kampus J1', 'GD-J1', 'Jl. Margonda Raya No.100', 'Bekasi', -6.37020000, 106.82340000, 1),
('Universitas Indonesia', 'UI', 'Kampus UI Depok', 'Depok', -6.36080000, 106.82720000, 1),
('Institut Pertanian Bogor', 'IPB', 'Jl. Raya Dramaga', 'Bogor', -6.55890000, 106.72680000, 1),
('Universitas Pancasila', 'UP', 'Jl. Raya Lenteng Agung', 'Jakarta', -6.32980000, 106.83120000, 1),
('Universitas Mercu Buana', 'UMB', 'Jl. Meruya Selatan', 'Jakarta', -6.21560000, 106.73420000, 1);

-- --------------------------------------------------------

--
-- Table structure for table kost
--

CREATE TABLE kost (
  id SERIAL PRIMARY KEY,
  nama VARCHAR(191) NOT NULL,
  jarak_kampus DOUBLE PRECISION NOT NULL,
  jarak_market DOUBLE PRECISION NOT NULL,
  harga DOUBLE PRECISION NOT NULL,
  kebersihan INTEGER NOT NULL,
  keamanan INTEGER NOT NULL,
  fasilitas INTEGER NOT NULL,
  kampus_id INTEGER DEFAULT NULL,
  deskripsi TEXT DEFAULT NULL,
  alamat VARCHAR(255) DEFAULT NULL,
  latitude DECIMAL(10,8) DEFAULT NULL,
  longitude DECIMAL(11,8) DEFAULT NULL,
  foto_utama VARCHAR(255) DEFAULT NULL,
  is_active SMALLINT NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_kost_harga ON kost(harga);
CREATE INDEX idx_kost_jarak_kampus ON kost(jarak_kampus);
CREATE INDEX idx_kost_is_active ON kost(is_active);
CREATE INDEX idx_kost_kampus_id ON kost(kampus_id);

--
-- Dumping data for table kost
--

INSERT INTO kost (nama, jarak_kampus, jarak_market, harga, kebersihan, keamanan, fasilitas, kampus_id, is_active) VALUES
('Kost Papipul Pakuwon Mezanine', 1.2, 0.5, 2500000, 5, 4, 5, 1, 1),
('Kost Eleora Cikunir Tipe C', 2.5, 1, 1299000, 4, 5, 5, 1, 1),
('Kost De Jatti', 3.1, 0.8, 1400000, 4, 4, 5, 1, 1),
('Kost Delta Timur 102 Tipe A Pekayon', 1.8, 0.3, 1674000, 5, 5, 5, 1, 1),
('Kost Fans Rooms', 0.5, 0.2, 1500000, 3, 3, 4, 1, 1),
('Kost Krakatau 1B Tipe A', 2.2, 1.5, 1250500, 4, 3, 4, 1, 1),
('Kost CRV Cikas Tipe A Galaxy', 1.5, 0.5, 1325000, 5, 5, 5, 1, 1),
('Kost Eleora Cikunir Tipe A', 2.5, 1, 956000, 4, 4, 5, 1, 1),
('Kost Pink Moon Tipe B', 0.9, 0.4, 1350000, 4, 3, 4, 1, 1),
('Kost Ibu Datin Tipe C', 3.5, 1.2, 800000, 3, 3, 3, 1, 1),
('Kost Khazanah VIP Semi apartment', 1.1, 0.6, 1250000, 4, 4, 5, 1, 1),
('Kost Ezra Tipe A', 2, 1.1, 700000, 3, 2, 3, 1, 1),
('Kost De Miracle Inthecost Tipe B', 1.6, 0.7, 1500000, 4, 4, 5, 1, 1),
('Kost Khazanah Tipe Vvip Executive', 1.2, 0.6, 1250000, 5, 5, 5, 1, 1),
('Kost Kayuringin', 2.8, 1.3, 900000, 3, 3, 3, 1, 1),
('Kost Manohara', 0.8, 0.3, 1750000, 5, 4, 4, 1, 1),
('Rumah Kontrakan FHS Rent House', 3, 1.5, 1000000, 3, 3, 3, 1, 1),
('Kost Pink Moon Tipe C', 0.9, 0.4, 1550000, 4, 4, 4, 1, 1),
('Kost Galaxy Living 1 Executive', 1.4, 0.5, 1850000, 5, 5, 5, 1, 1),
('Kost Aa Kepin Vvip', 1.7, 0.8, 1250000, 4, 3, 4, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table kost_images
--

CREATE TABLE kost_images (
  id SERIAL PRIMARY KEY,
  kost_id INTEGER NOT NULL,
  image_url VARCHAR(255) NOT NULL,
  caption VARCHAR(191) DEFAULT NULL,
  urutan INTEGER NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_kost_images_kost_id ON kost_images(kost_id);

-- --------------------------------------------------------

--
-- Table structure for table temp_bobot
--

CREATE TABLE temp_bobot (
  id SERIAL PRIMARY KEY,
  kriteria VARCHAR(191) NOT NULL,
  jarak_kampus DOUBLE PRECISION NOT NULL,
  jarak_market DOUBLE PRECISION NOT NULL,
  harga DOUBLE PRECISION NOT NULL,
  kebersihan DOUBLE PRECISION NOT NULL,
  keamanan DOUBLE PRECISION NOT NULL,
  fasilitas DOUBLE PRECISION NOT NULL
);

INSERT INTO temp_bobot (kriteria, jarak_kampus, jarak_market, harga, kebersihan, keamanan, fasilitas) VALUES
('Jarak Kampus', 1, 2, 0.25, 1, 0.6667, 0.5),
('Jarak Market', 0.5, 1, 0.125, 0.5, 0.3333, 0.25),
('Harga', 4, 8, 1, 4, 2.6667, 2),
('Kebersihan', 1, 2, 0.25, 1, 0.6667, 0.5),
('Keamanan', 1.5, 3, 0.375, 1.5, 1, 0.75),
('Fasilitas', 2, 4, 0.5, 2, 1.3333, 1);

-- --------------------------------------------------------

--
-- Table structure for table temp_d_neg
--

CREATE TABLE temp_d_neg (
  id SERIAL PRIMARY KEY,
  nama VARCHAR(191) NOT NULL,
  dNegatif DOUBLE PRECISION NOT NULL
);

-- --------------------------------------------------------

--
-- Table structure for table temp_d_pos
--

CREATE TABLE temp_d_pos (
  id SERIAL PRIMARY KEY,
  nama VARCHAR(191) NOT NULL,
  dPositif DOUBLE PRECISION NOT NULL
);

-- --------------------------------------------------------

--
-- Table structure for table temp_nilai_pref
--

CREATE TABLE temp_nilai_pref (
  id SERIAL PRIMARY KEY,
  nama VARCHAR(191) NOT NULL,
  val DOUBLE PRECISION NOT NULL
);

-- --------------------------------------------------------

--
-- Table structure for table temp_normalisasi
--

CREATE TABLE temp_normalisasi (
  id SERIAL PRIMARY KEY,
  nama VARCHAR(191) NOT NULL,
  jarak_kampus DOUBLE PRECISION NOT NULL,
  jarak_market DOUBLE PRECISION NOT NULL,
  harga DOUBLE PRECISION NOT NULL,
  kebersihan DOUBLE PRECISION NOT NULL,
  keamanan DOUBLE PRECISION NOT NULL,
  fasilitas DOUBLE PRECISION NOT NULL
);

-- --------------------------------------------------------

--
-- Table structure for table temp_normalisasi_kriteria
--

CREATE TABLE temp_normalisasi_kriteria (
  id SERIAL PRIMARY KEY,
  kriteria VARCHAR(191) NOT NULL,
  jarak_kampus DOUBLE PRECISION NOT NULL,
  jarak_market DOUBLE PRECISION NOT NULL,
  harga DOUBLE PRECISION NOT NULL,
  kebersihan DOUBLE PRECISION NOT NULL,
  keamanan DOUBLE PRECISION NOT NULL,
  fasilitas DOUBLE PRECISION NOT NULL,
  avg DOUBLE PRECISION DEFAULT NULL,
  matrix_aw DOUBLE PRECISION DEFAULT NULL
);

-- --------------------------------------------------------

--
-- Table structure for table users
--

CREATE TABLE users (
  id SERIAL PRIMARY KEY,
  email VARCHAR(191) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  nama VARCHAR(191) NOT NULL,
  telepon VARCHAR(20) DEFAULT NULL,
  role VARCHAR(10) NOT NULL DEFAULT 'user' CHECK (role IN ('admin', 'user')),
  foto_profil VARCHAR(255) DEFAULT NULL,
  is_active SMALLINT NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_users_role ON users(role);

--
-- Dumping data for table users
--

INSERT INTO users (email, password, nama, role, is_active) VALUES
('admin@spkkost.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin', 1);

-- --------------------------------------------------------

--
-- Table structure for table user_preferences
--

CREATE TABLE user_preferences (
  id SERIAL PRIMARY KEY,
  user_id INTEGER NOT NULL,
  max_harga DOUBLE PRECISION DEFAULT NULL,
  max_jarak_kampus DOUBLE PRECISION DEFAULT NULL,
  min_kebersihan INTEGER DEFAULT NULL,
  min_keamanan INTEGER DEFAULT NULL,
  min_fasilitas INTEGER DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_prefs_user_id ON user_preferences(user_id);
