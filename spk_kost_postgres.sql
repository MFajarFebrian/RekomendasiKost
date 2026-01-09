-- PostgreSQL Database Schema for Sistem Rekomendasi Kost
-- Converted from MySQL schema

-- Create ENUM for calculation types if it doesn't exist
DO $$ BEGIN
    CREATE TYPE calculation_type_enum AS ENUM ('ahp', 'topsis');
EXCEPTION
    WHEN duplicate_object THEN null;
END $$;

-- Create ENUM for user roles if it doesn't exist
DO $$ BEGIN
    CREATE TYPE user_role_enum AS ENUM ('admin', 'user');
EXCEPTION
    WHEN duplicate_object THEN null;
END $$;

-- ---------------------------------------------------------
-- Function to handle ON UPDATE CURRENT_TIMESTAMP behavior
-- ---------------------------------------------------------
CREATE OR REPLACE FUNCTION update_timestamp_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ language 'plpgsql';

-- --------------------------------------------------------
-- Table structure for table calculation_history
-- --------------------------------------------------------
CREATE TABLE calculation_history (
  id SERIAL PRIMARY KEY,
  user_id INTEGER DEFAULT NULL,
  calculation_type calculation_type_enum NOT NULL,
  input_data JSONB DEFAULT NULL,
  result_data JSONB DEFAULT NULL,
  execution_time DOUBLE PRECISION DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX calculation_history_user_id_idx ON calculation_history (user_id);
CREATE INDEX calculation_history_created_at_idx ON calculation_history (created_at);

-- --------------------------------------------------------
-- Table structure for table kampus
-- --------------------------------------------------------
CREATE TABLE kampus (
  id SERIAL PRIMARY KEY,
  nama VARCHAR(150) NOT NULL,
  kode VARCHAR(20) DEFAULT NULL,
  alamat VARCHAR(255) DEFAULT NULL,
  kota VARCHAR(100) DEFAULT NULL,
  latitude DECIMAL(10,8) DEFAULT NULL,
  longitude DECIMAL(11,8) DEFAULT NULL,
  is_active BOOLEAN NOT NULL DEFAULT TRUE,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX kampus_kota_idx ON kampus (kota);

-- Dumping data for table kampus
INSERT INTO kampus (id, nama, kode, alamat, kota, latitude, longitude, is_active, created_at) VALUES
(1, 'Universitas Gunadarma Kampus J1', 'GD-J1', 'Jl. Margonda Raya No.100', 'Bekasi', -6.37020000, 106.82340000, TRUE, '2026-01-04 13:17:49'),
(7, 'Universitas Indonesia', 'UI', 'Kampus UI Depok', 'Depok', -6.36080000, 106.82720000, TRUE, '2026-01-04 13:17:49'),
(8, 'Institut Pertanian Bogor', 'IPB', 'Jl. Raya Dramaga', 'Bogor', -6.55890000, 106.72680000, TRUE, '2026-01-04 13:17:49'),
(9, 'Universitas Pancasila', 'UP', 'Jl. Raya Lenteng Agung', 'Jakarta', -6.32980000, 106.83120000, TRUE, '2026-01-04 13:17:49'),
(10, 'Universitas Mercu Buana', 'UMB', 'Jl. Meruya Selatan', 'Jakarta', -6.21560000, 106.73420000, TRUE, '2026-01-04 13:17:49');

-- Reset serial for kampus
SELECT setval(pg_get_serial_sequence('kampus', 'id'), (SELECT MAX(id) FROM kampus));

-- --------------------------------------------------------
-- Table structure for table kost
-- --------------------------------------------------------
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
  is_active BOOLEAN NOT NULL DEFAULT TRUE,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX kost_harga_idx ON kost (harga);
CREATE INDEX kost_jarak_kampus_idx ON kost (jarak_kampus);
CREATE INDEX kost_is_active_idx ON kost (is_active);
CREATE INDEX kost_kampus_id_idx ON kost (kampus_id);

-- Trigger for updated_at in kost
CREATE TRIGGER update_kost_timestamp BEFORE UPDATE ON kost FOR EACH ROW EXECUTE PROCEDURE update_timestamp_column();

-- Dumping data for table kost
INSERT INTO kost (id, nama, jarak_kampus, jarak_market, harga, kebersihan, keamanan, fasilitas, kampus_id, is_active, created_at, updated_at) VALUES
(1, 'Kost Papipul Pakuwon Mezanine', 1.2, 0.5, 2500000, 5, 4, 5, 1, TRUE, '2026-01-04 13:11:37', '2026-01-04 13:17:49'),
(2, 'Kost Eleora Cikunir Tipe C', 2.5, 1, 1299000, 4, 5, 5, 1, TRUE, '2026-01-04 13:11:37', '2026-01-04 13:17:49'),
(3, 'Kost De Jatti', 3.1, 0.8, 1400000, 4, 4, 5, 1, TRUE, '2026-01-04 13:11:37', '2026-01-04 13:17:49'),
(4, 'Kost Delta Timur 102 Tipe A Pekayon', 1.8, 0.3, 1674000, 5, 5, 5, 1, TRUE, '2026-01-04 13:11:37', '2026-01-04 13:17:49'),
(5, 'Kost Fans Rooms', 0.5, 0.2, 1500000, 3, 3, 4, 1, TRUE, '2026-01-04 13:11:37', '2026-01-04 13:17:49'),
(6, 'Kost Krakatau 1B Tipe A', 2.2, 1.5, 1250500, 4, 3, 4, 1, TRUE, '2026-01-04 13:11:37', '2026-01-04 13:17:49'),
(7, 'Kost CRV Cikas Tipe A Galaxy', 1.5, 0.5, 1325000, 5, 5, 5, 1, TRUE, '2026-01-04 13:11:37', '2026-01-04 13:17:49'),
(8, 'Kost Eleora Cikunir Tipe A', 2.5, 1, 956000, 4, 4, 5, 1, TRUE, '2026-01-04 13:11:37', '2026-01-04 13:17:49'),
(9, 'Kost Pink Moon Tipe B', 0.9, 0.4, 1350000, 4, 3, 4, 1, TRUE, '2026-01-04 13:11:37', '2026-01-04 13:17:49'),
(10, 'Kost Ibu Datin Tipe C', 3.5, 1.2, 800000, 3, 3, 3, 1, TRUE, '2026-01-04 13:11:37', '2026-01-04 13:17:49'),
(11, 'Kost Khazanah VIP Semi apartment', 1.1, 0.6, 1250000, 4, 4, 5, 1, TRUE, '2026-01-04 13:11:37', '2026-01-04 13:17:49'),
(12, 'Kost Ezra Tipe A', 2, 1.1, 700000, 3, 2, 3, 1, TRUE, '2026-01-04 13:11:37', '2026-01-04 13:17:49'),
(13, 'Kost De Miracle Inthecost Tipe B', 1.6, 0.7, 1500000, 4, 4, 5, 1, TRUE, '2026-01-04 13:11:37', '2026-01-04 13:17:49'),
(14, 'Kost Khazanah Tipe Vvip Executive', 1.2, 0.6, 1250000, 5, 5, 5, 1, TRUE, '2026-01-04 13:11:37', '2026-01-04 13:17:49'),
(15, 'Kost Kayuringin', 2.8, 1.3, 900000, 3, 3, 3, 1, TRUE, '2026-01-04 13:11:37', '2026-01-04 13:17:49'),
(16, 'Kost Manohara', 0.8, 0.3, 1750000, 5, 4, 4, 1, TRUE, '2026-01-04 13:11:37', '2026-01-04 13:17:49'),
(17, 'Rumah Kontrakan FHS Rent House', 3, 1.5, 1000000, 3, 3, 3, 1, TRUE, '2026-01-04 13:11:37', '2026-01-04 13:17:49'),
(18, 'Kost Pink Moon Tipe C', 0.9, 0.4, 1550000, 4, 4, 4, 1, TRUE, '2026-01-04 13:11:37', '2026-01-04 13:17:49'),
(19, 'Kost Galaxy Living 1 Executive', 1.4, 0.5, 1850000, 5, 5, 5, 1, TRUE, '2026-01-04 13:11:37', '2026-01-04 13:17:49'),
(20, 'Kost Aa Kepin Vvip', 1.7, 0.8, 1250000, 4, 3, 4, 1, TRUE, '2026-01-04 13:11:37', '2026-01-04 13:17:49');

-- Reset serial for kost
SELECT setval(pg_get_serial_sequence('kost', 'id'), (SELECT MAX(id) FROM kost));

-- --------------------------------------------------------
-- Table structure for table kost_images
-- --------------------------------------------------------
CREATE TABLE kost_images (
  id SERIAL PRIMARY KEY,
  kost_id INTEGER NOT NULL,
  image_url VARCHAR(255) NOT NULL,
  caption VARCHAR(191) DEFAULT NULL,
  urutan INTEGER NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX kost_images_kost_id_idx ON kost_images (kost_id);

-- --------------------------------------------------------
-- Table structure for table temp_bobot
-- --------------------------------------------------------
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

-- Dumping data for table temp_bobot
INSERT INTO temp_bobot (id, kriteria, jarak_kampus, jarak_market, harga, kebersihan, keamanan, fasilitas) VALUES
(1, 'Jarak Kampus', 1, 2, 0.25, 1, 0.6667, 0.5),
(2, 'Jarak Market', 0.5, 1, 0.125, 0.5, 0.3333, 0.25),
(3, 'Harga', 4, 8, 1, 4, 2.6667, 2),
(4, 'Kebersihan', 1, 2, 0.25, 1, 0.6667, 0.5),
(5, 'Keamanan', 1.5, 3, 0.375, 1.5, 1, 0.75),
(6, 'Fasilitas', 2, 4, 0.5, 2, 1.3333, 1);

-- Reset serial for temp_bobot
SELECT setval(pg_get_serial_sequence('temp_bobot', 'id'), (SELECT MAX(id) FROM temp_bobot));

-- --------------------------------------------------------
-- Table structure for table temp_d_neg
-- --------------------------------------------------------
CREATE TABLE temp_d_neg (
  id SERIAL PRIMARY KEY,
  nama VARCHAR(191) NOT NULL,
  dNegatif DOUBLE PRECISION NOT NULL
);

-- --------------------------------------------------------
-- Table structure for table temp_d_pos
-- --------------------------------------------------------
CREATE TABLE temp_d_pos (
  id SERIAL PRIMARY KEY,
  nama VARCHAR(191) NOT NULL,
  dPositif DOUBLE PRECISION NOT NULL
);

-- --------------------------------------------------------
-- Table structure for table temp_nilai_pref
-- --------------------------------------------------------
CREATE TABLE temp_nilai_pref (
  id SERIAL PRIMARY KEY,
  nama VARCHAR(191) NOT NULL,
  val DOUBLE PRECISION NOT NULL
);

-- --------------------------------------------------------
-- Table structure for table temp_normalisasi
-- --------------------------------------------------------
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
-- Table structure for table temp_normalisasi_kriteria
-- --------------------------------------------------------
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

-- Reset serials for remaining tables
SELECT setval(pg_get_serial_sequence('temp_normalisasi_kriteria', 'id'), 1, false);

-- --------------------------------------------------------
-- Table structure for table users
-- --------------------------------------------------------
CREATE TABLE users (
  id SERIAL PRIMARY KEY,
  email VARCHAR(191) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  nama VARCHAR(191) NOT NULL,
  telepon VARCHAR(20) DEFAULT NULL,
  role user_role_enum NOT NULL DEFAULT 'user',
  foto_profil VARCHAR(255) DEFAULT NULL,
  is_active BOOLEAN NOT NULL DEFAULT TRUE,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX users_role_idx ON users (role);

-- Trigger for updated_at in users
CREATE TRIGGER update_users_timestamp BEFORE UPDATE ON users FOR EACH ROW EXECUTE PROCEDURE update_timestamp_column();

-- Dumping data for table users (using a dummy bcrypt hash for 'password')
INSERT INTO users (id, email, password, nama, telepon, role, foto_profil, is_active, created_at, updated_at) VALUES
(1, 'admin@spkkost.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', NULL, 'admin', NULL, TRUE, '2026-01-04 13:11:37', '2026-01-04 13:11:37');

-- Reset serial for users
SELECT setval(pg_get_serial_sequence('users', 'id'), (SELECT MAX(id) FROM users));

-- --------------------------------------------------------
-- Table structure for table user_preferences
-- --------------------------------------------------------
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

CREATE INDEX user_preferences_user_id_idx ON user_preferences (user_id);

-- Trigger for updated_at in user_preferences
CREATE TRIGGER update_user_preferences_timestamp BEFORE UPDATE ON user_preferences FOR EACH ROW EXECUTE PROCEDURE update_timestamp_column();
