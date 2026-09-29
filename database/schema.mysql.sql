-- ------------------------------------------------------------------
-- HashTag Technologies — MySQL 5.7+/8.0 / MariaDB 10.3+ schema
-- utf8mb4, InnoDB, foreign keys enforced.
-- ------------------------------------------------------------------
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS case_study_images, case_study_metrics, testimonials, case_studies,
  case_study_categories, services, metrics, clients, team_members, timeline,
  contact_submissions, technology_stack, social_links, faqs, seo_meta, media,
  site_settings, login_attempts, users;

CREATE TABLE users (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(120) NOT NULL,
  email         VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role          VARCHAR(20)  NOT NULL DEFAULT 'admin',
  last_login_at DATETIME NULL,
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE login_attempts (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ip           VARCHAR(45)  NOT NULL,
  email        VARCHAR(190) NOT NULL,
  attempted_at DATETIME NOT NULL,
  INDEX idx_login_ip (ip, attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE site_settings (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  setting_key VARCHAR(100) NOT NULL UNIQUE,
  value       MEDIUMTEXT NULL,
  type        VARCHAR(20)  NOT NULL DEFAULT 'text',   -- text|textarea|list|email|url|image
  group_name  VARCHAR(50)  NOT NULL DEFAULT 'general',
  label       VARCHAR(150) NOT NULL,
  sort_order  INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE media (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  filename    VARCHAR(255) NOT NULL,
  path        VARCHAR(255) NOT NULL,
  webp_path   VARCHAR(255) NULL,
  mime        VARCHAR(60)  NOT NULL,
  size_bytes  INT UNSIGNED NOT NULL DEFAULT 0,
  width       INT UNSIGNED NULL,
  height      INT UNSIGNED NULL,
  alt         VARCHAR(255) NULL,
  uploaded_by INT UNSIGNED NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_media_user FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Services power four sections: core (home), agent_feature, automation, process
CREATE TABLE services (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  section     VARCHAR(30)  NOT NULL DEFAULT 'core',
  title       VARCHAR(150) NOT NULL,
  slug        VARCHAR(160) NOT NULL,
  icon        VARCHAR(40)  NULL,
  excerpt     TEXT NULL,
  features    TEXT NULL,          -- JSON array
  meta        VARCHAR(150) NULL,  -- e.g. timeline for process steps
  cta_label   VARCHAR(80)  NULL,
  cta_url     VARCHAR(255) NULL,
  sort_order  INT NOT NULL DEFAULT 0,
  is_active   TINYINT(1) NOT NULL DEFAULT 1,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME NULL,
  INDEX idx_services_section (section, is_active, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE metrics (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  label      VARCHAR(120) NOT NULL,
  value      DECIMAL(12,2) NOT NULL DEFAULT 0,
  prefix     VARCHAR(10) NULL,
  suffix     VARCHAR(10) NULL,
  decimals   TINYINT UNSIGNED NOT NULL DEFAULT 0,
  animate    TINYINT(1) NOT NULL DEFAULT 1,
  sort_order INT NOT NULL DEFAULT 0,
  is_active  TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE clients (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name       VARCHAR(120) NOT NULL,
  logo       VARCHAR(255) NULL,
  url        VARCHAR(255) NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active  TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE case_study_categories (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(80)  NOT NULL,
  slug        VARCHAR(90)  NOT NULL UNIQUE,
  description VARCHAR(255) NULL,
  sort_order  INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE case_studies (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category_id     INT UNSIGNED NULL,
  title           VARCHAR(200) NOT NULL,
  slug            VARCHAR(200) NOT NULL UNIQUE,
  tag_label       VARCHAR(120) NULL,
  industry        VARCHAR(150) NULL,
  excerpt         TEXT NULL,
  hero_image      VARCHAR(255) NULL,
  visual          VARCHAR(30)  NOT NULL DEFAULT 'network',
  challenge       TEXT NULL,
  solution        TEXT NULL,
  technology      TEXT NULL,
  tech_tags       TEXT NULL,       -- JSON array
  architecture    TEXT NULL,       -- one step per line "Title | description"
  results         TEXT NULL,
  seo_title       VARCHAR(200) NULL,
  seo_description VARCHAR(300) NULL,
  og_image        VARCHAR(255) NULL,
  is_published    TINYINT(1) NOT NULL DEFAULT 1,
  is_featured     TINYINT(1) NOT NULL DEFAULT 0,
  sort_order      INT NOT NULL DEFAULT 0,
  published_at    DATETIME NULL,
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME NULL,
  INDEX idx_cs_pub (is_published, sort_order),
  CONSTRAINT fk_cs_category FOREIGN KEY (category_id) REFERENCES case_study_categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE case_study_metrics (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  case_study_id INT UNSIGNED NOT NULL,
  label         VARCHAR(120) NOT NULL,
  value         VARCHAR(40)  NOT NULL,
  sort_order    INT NOT NULL DEFAULT 0,
  CONSTRAINT fk_csm_cs FOREIGN KEY (case_study_id) REFERENCES case_studies(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE case_study_images (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  case_study_id INT UNSIGNED NOT NULL,
  path          VARCHAR(255) NOT NULL,
  caption       VARCHAR(255) NULL,
  sort_order    INT NOT NULL DEFAULT 0,
  CONSTRAINT fk_csi_cs FOREIGN KEY (case_study_id) REFERENCES case_studies(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE testimonials (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  case_study_id INT UNSIGNED NULL,
  quote         TEXT NOT NULL,
  name          VARCHAR(120) NOT NULL,
  designation   VARCHAR(150) NULL,
  company       VARCHAR(150) NULL,
  sort_order    INT NOT NULL DEFAULT 0,
  is_active     TINYINT(1) NOT NULL DEFAULT 1,
  CONSTRAINT fk_t_cs FOREIGN KEY (case_study_id) REFERENCES case_studies(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE team_members (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name       VARCHAR(120) NOT NULL,
  role       VARCHAR(150) NOT NULL,
  bio        TEXT NULL,
  expertise  TEXT NULL,          -- JSON array
  photo      VARCHAR(255) NULL,
  linkedin   VARCHAR(255) NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active  TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE timeline (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  year        VARCHAR(10)  NOT NULL,
  title       VARCHAR(150) NOT NULL,
  description TEXT NULL,
  sort_order  INT NOT NULL DEFAULT 0,
  is_active   TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE contact_submissions (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name       VARCHAR(120) NOT NULL,
  email      VARCHAR(190) NOT NULL,
  phone      VARCHAR(40)  NULL,
  company    VARCHAR(150) NULL,
  budget     VARCHAR(60)  NULL,
  message    TEXT NOT NULL,
  status     VARCHAR(20)  NOT NULL DEFAULT 'new',   -- new|read|contacted
  notes      TEXT NULL,
  ip         VARCHAR(45)  NULL,
  user_agent VARCHAR(255) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL,
  INDEX idx_cs_status (status, created_at),
  INDEX idx_cs_ip (ip, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE technology_stack (
  id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name           VARCHAR(80) NOT NULL,
  category       VARCHAR(50) NOT NULL,
  show_in_hero   TINYINT(1) NOT NULL DEFAULT 0,
  show_in_ticker TINYINT(1) NOT NULL DEFAULT 0,
  sort_order     INT NOT NULL DEFAULT 0,
  is_active      TINYINT(1) NOT NULL DEFAULT 1,
  INDEX idx_tech_cat (category, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE social_links (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  platform   VARCHAR(50)  NOT NULL,
  url        VARCHAR(255) NOT NULL,
  icon       VARCHAR(40)  NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active  TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE faqs (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  question   VARCHAR(255) NOT NULL,
  answer     TEXT NOT NULL,
  page       VARCHAR(40) NOT NULL DEFAULT 'contact',
  sort_order INT NOT NULL DEFAULT 0,
  is_active  TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE seo_meta (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  page_key    VARCHAR(60)  NOT NULL UNIQUE,
  title       VARCHAR(200) NULL,
  description VARCHAR(300) NULL,
  og_image    VARCHAR(255) NULL,
  noindex     TINYINT(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
