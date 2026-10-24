CREATE DATABASE IF NOT EXISTS emo_loop
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE emo_loop;

ALTER TABLE users
  ADD COLUMN vai_tro VARCHAR(20) NOT NULL DEFAULT 'user' AFTER password,
  ADD COLUMN so_dien_thoai VARCHAR(20) NULL AFTER vai_tro;

CREATE TABLE loai_san_pham (
  id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  ten         VARCHAR(100) NOT NULL,
  mo_ta       TEXT NULL,
  created_at  DATETIME NULL,
  updated_at  DATETIME NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE chuong (
  id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  tieu_de      VARCHAR(255) NOT NULL,
  thu_tu       INT NOT NULL,
  noi_dung     JSON NOT NULL,
  da_xuat_ban  BOOLEAN NOT NULL DEFAULT FALSE,
  created_at   DATETIME NULL,
  updated_at   DATETIME NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE nhan_vat (
  id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  ten         VARCHAR(100) NOT NULL,
  thu_muc     VARCHAR(100) NOT NULL,
  mo_ta       TEXT NULL,
  anh         VARCHAR(255) NULL,
  created_at  DATETIME NULL,
  updated_at  DATETIME NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE vat_pham (
  id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  ten         VARCHAR(100) NOT NULL,
  mo_ta       TEXT NULL,
  anh         VARCHAR(255) NULL,
  chuong_id   BIGINT UNSIGNED NULL,
  created_at  DATETIME NULL,
  updated_at  DATETIME NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_vat_pham_chuong
    FOREIGN KEY (chuong_id) REFERENCES chuong (id)
) ENGINE=InnoDB;

CREATE TABLE san_pham (
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
    FOREIGN KEY (loai_san_pham_id) REFERENCES loai_san_pham (id),
  CONSTRAINT fk_san_pham_nhan_vat
    FOREIGN KEY (nhan_vat_id) REFERENCES nhan_vat (id),
  CONSTRAINT fk_san_pham_vat_pham
    FOREIGN KEY (vat_pham_id) REFERENCES vat_pham (id)
) ENGINE=InnoDB;

CREATE TABLE ma_giam (
  id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  ma              VARCHAR(50) NOT NULL,
  loai_giam       VARCHAR(20) NOT NULL,
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
    FOREIGN KEY (san_pham_id) REFERENCES san_pham (id),
  CONSTRAINT fk_ma_giam_nhan_vat
    FOREIGN KEY (nhan_vat_id) REFERENCES nhan_vat (id),
  CONSTRAINT fk_ma_giam_vat_pham
    FOREIGN KEY (vat_pham_id) REFERENCES vat_pham (id)
) ENGINE=InnoDB;

CREATE TABLE gio_hang (
  id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id      BIGINT UNSIGNED NOT NULL,
  san_pham_id  BIGINT UNSIGNED NOT NULL,
  so_luong     INT NOT NULL DEFAULT 1,
  created_at   DATETIME NULL,
  updated_at   DATETIME NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_gio_hang_users
    FOREIGN KEY (user_id) REFERENCES users (id),
  CONSTRAINT fk_gio_hang_san_pham
    FOREIGN KEY (san_pham_id) REFERENCES san_pham (id)
) ENGINE=InnoDB;

CREATE TABLE don_hang (
  id                      BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id                 BIGINT UNSIGNED NOT NULL,
  ma_giam_id              BIGINT UNSIGNED NULL,
  ten_nguoi_nhan          VARCHAR(255) NOT NULL,
  so_dien_thoai           VARCHAR(20) NOT NULL,
  dia_chi                 VARCHAR(500) NOT NULL,
  phuong_thuc_thanh_toan  VARCHAR(20) NOT NULL,
  the_4_so_cuoi           VARCHAR(4) NULL,
  trang_thai              VARCHAR(20) NOT NULL,
  tien_giam               DECIMAL(12,2) NOT NULL DEFAULT 0,
  tong_tien               DECIMAL(12,2) NOT NULL,
  created_at              DATETIME NULL,
  updated_at              DATETIME NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_don_hang_users
    FOREIGN KEY (user_id) REFERENCES users (id),
  CONSTRAINT fk_don_hang_ma_giam
    FOREIGN KEY (ma_giam_id) REFERENCES ma_giam (id)
) ENGINE=InnoDB;

CREATE TABLE don_hang_chi_tiet (
  id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  don_hang_id  BIGINT UNSIGNED NOT NULL,
  san_pham_id  BIGINT UNSIGNED NOT NULL,
  so_luong     INT NOT NULL,
  gia          DECIMAL(12,2) NOT NULL,
  created_at   DATETIME NULL,
  updated_at   DATETIME NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_ct_don_hang
    FOREIGN KEY (don_hang_id) REFERENCES don_hang (id),
  CONSTRAINT fk_ct_san_pham
    FOREIGN KEY (san_pham_id) REFERENCES san_pham (id)
) ENGINE=InnoDB;

CREATE TABLE tien_trinh (
  id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id       BIGINT UNSIGNED NOT NULL,
  chuong_id     BIGINT UNSIGNED NOT NULL,
  giai_doan     INT NOT NULL,
  cot_moc       VARCHAR(50) NOT NULL,
  diem_hy       INT NOT NULL DEFAULT 0,
  diem_no       INT NOT NULL DEFAULT 0,
  diem_ai       INT NOT NULL DEFAULT 0,
  diem_o        INT NOT NULL DEFAULT 0,
  anh_hien_tai  VARCHAR(255) NULL,
  created_at    DATETIME NULL,
  updated_at    DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_tien_trinh_users (user_id),
  CONSTRAINT fk_tien_trinh_users
    FOREIGN KEY (user_id) REFERENCES users (id),
  CONSTRAINT fk_tien_trinh_chuong
    FOREIGN KEY (chuong_id) REFERENCES chuong (id)
) ENGINE=InnoDB;

CREATE TABLE nguoi_dung_ma_giam (
  id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id       BIGINT UNSIGNED NOT NULL,
  ma_giam_id    BIGINT UNSIGNED NOT NULL,
  da_su_dung    BOOLEAN NOT NULL DEFAULT FALSE,
  ngay_nhan     DATETIME NOT NULL,
  ngay_su_dung  DATETIME NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_ndmg_users
    FOREIGN KEY (user_id) REFERENCES users (id),
  CONSTRAINT fk_ndmg_ma_giam
    FOREIGN KEY (ma_giam_id) REFERENCES ma_giam (id)
) ENGINE=InnoDB;

CREATE TABLE nguoi_dung_vat_pham (
  id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id      BIGINT UNSIGNED NOT NULL,
  vat_pham_id  BIGINT UNSIGNED NOT NULL,
  ngay_nhan    DATETIME NOT NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_ndvp_users
    FOREIGN KEY (user_id) REFERENCES users (id),
  CONSTRAINT fk_ndvp_vat_pham
    FOREIGN KEY (vat_pham_id) REFERENCES vat_pham (id)
) ENGINE=InnoDB;

CREATE TABLE nguoi_dung_nhan_vat (
  id               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id          BIGINT UNSIGNED NOT NULL,
  nhan_vat_id      BIGINT UNSIGNED NOT NULL,
  ngay_chinh_phuc  DATETIME NOT NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_ndnv_users
    FOREIGN KEY (user_id) REFERENCES users (id),
  CONSTRAINT fk_ndnv_nhan_vat
    FOREIGN KEY (nhan_vat_id) REFERENCES nhan_vat (id)
) ENGINE=InnoDB;
