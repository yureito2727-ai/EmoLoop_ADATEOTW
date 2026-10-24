-- =====================================================
-- Emo Loop - Cơ sở dữ liệu (MySQL 8.x)
-- Các bảng được tạo theo thứ tự phụ thuộc khóa ngoại
-- =====================================================

CREATE DATABASE IF NOT EXISTS emo_loop
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE emo_loop;

-- -----------------------------------------------------
-- 1. Các bảng không phụ thuộc bảng nào
-- -----------------------------------------------------

CREATE TABLE Loai_San_Pham (
  id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  ten         VARCHAR(100) NOT NULL,
  mo_ta       TEXT NULL,
  created_at  DATETIME NULL,
  updated_at  DATETIME NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE Nguoi_Dung (
  id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  ten            VARCHAR(255) NOT NULL,
  email          VARCHAR(255) NOT NULL,
  mat_khau       VARCHAR(255) NOT NULL,
  vai_tro        VARCHAR(20) NOT NULL,
  so_dien_thoai  VARCHAR(20) NULL,
  created_at     DATETIME NULL,
  updated_at     DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_nguoi_dung_email (email)
) ENGINE=InnoDB;

CREATE TABLE Chuong (
  id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  tieu_de      VARCHAR(255) NOT NULL,
  thu_tu       INT NOT NULL,
  noi_dung     JSON NOT NULL,
  da_xuat_ban  BOOLEAN NOT NULL DEFAULT FALSE,
  created_at   DATETIME NULL,
  updated_at   DATETIME NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE Nhan_Vat (
  id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  ten         VARCHAR(100) NOT NULL,
  thu_muc     VARCHAR(100) NOT NULL,
  mo_ta       TEXT NULL,
  anh         VARCHAR(255) NULL,
  created_at  DATETIME NULL,
  updated_at  DATETIME NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- 2. Vật phẩm, sản phẩm, mã giảm giá
-- -----------------------------------------------------

CREATE TABLE Vat_Pham (
  id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  ten         VARCHAR(100) NOT NULL,
  mo_ta       TEXT NULL,
  anh         VARCHAR(255) NULL,
  chuong_id   BIGINT UNSIGNED NULL,
  created_at  DATETIME NULL,
  updated_at  DATETIME NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_vat_pham_chuong
    FOREIGN KEY (chuong_id) REFERENCES Chuong (id)
) ENGINE=InnoDB;

CREATE TABLE San_Pham (
  id                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  loai_san_pham_id  BIGINT UNSIGNED NOT NULL,
  ten               VARCHAR(255) NOT NULL,
  mo_ta             TEXT NULL,
  gia               DECIMAL(12,2) NOT NULL,
  ton_kho           INT NOT NULL DEFAULT 0,
  anh               VARCHAR(255) NULL,
  nhan_vat_id       BIGINT UNSIGNED NULL,
  vat_pham_id       BIGINT UNSIGNED NULL,
  created_at        DATETIME NULL,
  updated_at        DATETIME NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_san_pham_loai
    FOREIGN KEY (loai_san_pham_id) REFERENCES Loai_San_Pham (id),
  CONSTRAINT fk_san_pham_nhan_vat
    FOREIGN KEY (nhan_vat_id) REFERENCES Nhan_Vat (id),
  CONSTRAINT fk_san_pham_vat_pham
    FOREIGN KEY (vat_pham_id) REFERENCES Vat_Pham (id)
) ENGINE=InnoDB;

CREATE TABLE Ma_Giam (
  id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  ma              VARCHAR(50) NOT NULL,
  loai_giam       VARCHAR(20) NOT NULL,        -- ví dụ: 'phan_tram' hoặc 'so_tien'
  gia_tri         DECIMAL(12,2) NOT NULL,
  ngay_het_han    DATETIME NULL,
  dang_hoat_dong  BOOLEAN NOT NULL DEFAULT TRUE,
  san_pham_id     BIGINT UNSIGNED NULL,
  nhan_vat_id     BIGINT UNSIGNED NULL,
  vat_pham_id     BIGINT UNSIGNED NULL,
  created_at      DATETIME NULL,
  updated_at      DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_ma_giam_ma (ma),
  CONSTRAINT fk_ma_giam_san_pham
    FOREIGN KEY (san_pham_id) REFERENCES San_Pham (id),
  CONSTRAINT fk_ma_giam_nhan_vat
    FOREIGN KEY (nhan_vat_id) REFERENCES Nhan_Vat (id),
  CONSTRAINT fk_ma_giam_vat_pham
    FOREIGN KEY (vat_pham_id) REFERENCES Vat_Pham (id)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- 3. Giỏ hàng, đơn hàng
-- -----------------------------------------------------

CREATE TABLE Gio_Hang (
  id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nguoi_dung_id  BIGINT UNSIGNED NOT NULL,
  san_pham_id    BIGINT UNSIGNED NOT NULL,
  so_luong       INT NOT NULL DEFAULT 1,
  created_at     DATETIME NULL,
  updated_at     DATETIME NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_gio_hang_nguoi_dung
    FOREIGN KEY (nguoi_dung_id) REFERENCES Nguoi_Dung (id),
  CONSTRAINT fk_gio_hang_san_pham
    FOREIGN KEY (san_pham_id) REFERENCES San_Pham (id)
) ENGINE=InnoDB;

CREATE TABLE Don_Hang (
  id                      BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nguoi_dung_id           BIGINT UNSIGNED NOT NULL,
  ma_giam_id              BIGINT UNSIGNED NULL,
  ten_nguoi_nhan          VARCHAR(255) NOT NULL,
  so_dien_thoai           VARCHAR(20) NOT NULL,
  dia_chi                 VARCHAR(500) NOT NULL,
  phuong_thuc_thanh_toan  VARCHAR(20) NOT NULL,   -- 'COD' hoặc 'THE'
  the_4_so_cuoi           VARCHAR(4) NULL,
  trang_thai              VARCHAR(20) NOT NULL,
  tien_giam               DECIMAL(12,2) NOT NULL DEFAULT 0,
  tong_tien               DECIMAL(12,2) NOT NULL,
  created_at              DATETIME NULL,
  updated_at              DATETIME NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_don_hang_nguoi_dung
    FOREIGN KEY (nguoi_dung_id) REFERENCES Nguoi_Dung (id),
  CONSTRAINT fk_don_hang_ma_giam
    FOREIGN KEY (ma_giam_id) REFERENCES Ma_Giam (id)
) ENGINE=InnoDB;

CREATE TABLE Don_Hang_Chi_Tiet (
  id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  don_hang_id  BIGINT UNSIGNED NOT NULL,
  san_pham_id  BIGINT UNSIGNED NOT NULL,
  so_luong     INT NOT NULL,
  gia          DECIMAL(12,2) NOT NULL,           -- giá tại thời điểm đặt hàng
  created_at   DATETIME NULL,
  updated_at   DATETIME NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_ct_don_hang
    FOREIGN KEY (don_hang_id) REFERENCES Don_Hang (id),
  CONSTRAINT fk_ct_san_pham
    FOREIGN KEY (san_pham_id) REFERENCES San_Pham (id)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- 4. Tiến trình game
-- -----------------------------------------------------

CREATE TABLE Tien_Trinh (
  id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nguoi_dung_id  BIGINT UNSIGNED NOT NULL,
  chuong_id      BIGINT UNSIGNED NOT NULL,
  giai_doan      INT NOT NULL,
  cot_moc        VARCHAR(50) NOT NULL,
  diem_hy        INT NOT NULL DEFAULT 0,
  diem_no        INT NOT NULL DEFAULT 0,
  diem_ai        INT NOT NULL DEFAULT 0,
  diem_o         INT NOT NULL DEFAULT 0,
  anh_hien_tai   VARCHAR(255) NULL,
  created_at     DATETIME NULL,
  updated_at     DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_tien_trinh_nguoi_dung (nguoi_dung_id),   -- mỗi người chơi 1 tiến trình
  CONSTRAINT fk_tien_trinh_nguoi_dung
    FOREIGN KEY (nguoi_dung_id) REFERENCES Nguoi_Dung (id),
  CONSTRAINT fk_tien_trinh_chuong
    FOREIGN KEY (chuong_id) REFERENCES Chuong (id)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- 5. Bảng trung gian (nhiều-nhiều)
-- -----------------------------------------------------

CREATE TABLE Nguoi_Dung_Ma_Giam (
  id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nguoi_dung_id  BIGINT UNSIGNED NOT NULL,
  ma_giam_id     BIGINT UNSIGNED NOT NULL,
  da_su_dung     BOOLEAN NOT NULL DEFAULT FALSE,
  ngay_nhan      DATETIME NOT NULL,
  ngay_su_dung   DATETIME NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_ndmg_nguoi_dung
    FOREIGN KEY (nguoi_dung_id) REFERENCES Nguoi_Dung (id),
  CONSTRAINT fk_ndmg_ma_giam
    FOREIGN KEY (ma_giam_id) REFERENCES Ma_Giam (id)
) ENGINE=InnoDB;

CREATE TABLE Nguoi_Dung_Vat_Pham (
  id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nguoi_dung_id  BIGINT UNSIGNED NOT NULL,
  vat_pham_id    BIGINT UNSIGNED NOT NULL,
  ngay_nhan      DATETIME NOT NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_ndvp_nguoi_dung
    FOREIGN KEY (nguoi_dung_id) REFERENCES Nguoi_Dung (id),
  CONSTRAINT fk_ndvp_vat_pham
    FOREIGN KEY (vat_pham_id) REFERENCES Vat_Pham (id)
) ENGINE=InnoDB;

CREATE TABLE Nguoi_Dung_Nhan_Vat (
  id               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nguoi_dung_id    BIGINT UNSIGNED NOT NULL,
  nhan_vat_id      BIGINT UNSIGNED NOT NULL,
  ngay_chinh_phuc  DATETIME NOT NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_ndnv_nguoi_dung
    FOREIGN KEY (nguoi_dung_id) REFERENCES Nguoi_Dung (id),
  CONSTRAINT fk_ndnv_nhan_vat
    FOREIGN KEY (nhan_vat_id) REFERENCES Nhan_Vat (id)
) ENGINE=InnoDB;
