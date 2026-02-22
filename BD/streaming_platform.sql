-- ============================================================
--  BASE DE DATOS: Plataforma de Streaming
-- ============================================================

CREATE DATABASE IF NOT EXISTS streaming_platform
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE streaming_platform;

-- ------------------------------------------------------------
-- ROLES
-- ------------------------------------------------------------
CREATE TABLE roles (
    id_rol      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(50)  NOT NULL UNIQUE,          -- admin, moderador, suscriptor, etc.
    descripcion VARCHAR(255),
    creado_en   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO roles (nombre, descripcion) VALUES
    ('admin',       'Acceso total al sistema'),
    ('moderador',   'Gestión de contenido'),
    ('suscriptor',  'Usuario con suscripción activa'),
    ('invitado',    'Acceso limitado sin suscripción');

-- ------------------------------------------------------------
-- PERSONAS  (datos personales, independientes del sistema)
-- ------------------------------------------------------------
CREATE TABLE personas (
    id_persona      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombres         VARCHAR(100) NOT NULL,
    apellidos       VARCHAR(100) NOT NULL,
    fecha_nacimiento DATE,
    genero          ENUM('M','F','Otro','Prefiero no decir'),
    telefono        VARCHAR(20),
    pais            VARCHAR(80),
    ciudad          VARCHAR(80),
    creado_en       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ------------------------------------------------------------
-- USUARIOS  (credenciales y estado en el sistema)
-- ------------------------------------------------------------
CREATE TABLE usuarios (
    id_usuario      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_persona      INT UNSIGNED NOT NULL,
    id_rol          INT UNSIGNED NOT NULL,
    email           VARCHAR(150) NOT NULL UNIQUE,
    password_hash   VARCHAR(255) NOT NULL,             -- bcrypt / argon2
    nombre_usuario  VARCHAR(60)  NOT NULL UNIQUE,
    avatar          MEDIUMBLOB,                        -- imagen de perfil almacenada en BD
    avatar_mime     VARCHAR(50),                       -- ej. image/jpeg
    activo          TINYINT(1)   NOT NULL DEFAULT 1,
    email_verificado TINYINT(1)  NOT NULL DEFAULT 0,
    ultimo_acceso   DATETIME,
    creado_en       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_usuario_persona FOREIGN KEY (id_persona) REFERENCES personas(id_persona)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_usuario_rol     FOREIGN KEY (id_rol)     REFERENCES roles(id_rol)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

-- ------------------------------------------------------------
-- SUSCRIPCIONES
-- ------------------------------------------------------------
CREATE TABLE planes_suscripcion (
    id_plan         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre          VARCHAR(80)    NOT NULL,           -- Básico, Estándar, Premium
    precio_mensual  DECIMAL(8,2)   NOT NULL,
    calidad_max     VARCHAR(20),                       -- 480p, 1080p, 4K
    pantallas_sim   TINYINT UNSIGNED NOT NULL DEFAULT 1,
    activo          TINYINT(1)     NOT NULL DEFAULT 1
);

CREATE TABLE suscripciones (
    id_suscripcion  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_usuario      INT UNSIGNED NOT NULL,
    id_plan         INT UNSIGNED NOT NULL,
    fecha_inicio    DATE         NOT NULL,
    fecha_fin       DATE,
    estado          ENUM('activa','cancelada','expirada','pausada') NOT NULL DEFAULT 'activa',
    creado_en       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_sus_usuario FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_sus_plan    FOREIGN KEY (id_plan)    REFERENCES planes_suscripcion(id_plan)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

-- ------------------------------------------------------------
-- GÉNEROS / CATEGORÍAS
-- ------------------------------------------------------------
CREATE TABLE generos (
    id_genero   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(80) NOT NULL UNIQUE,
    descripcion VARCHAR(255)
);

-- ------------------------------------------------------------
-- PERSONAS DEL ELENCO  (actores, directores, etc.)
-- ------------------------------------------------------------
CREATE TABLE elenco_personas (
    id_elenco   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(150) NOT NULL,
    biography   TEXT,
    foto        MEDIUMBLOB,                            -- foto almacenada en BD
    foto_mime   VARCHAR(50),
    creado_en   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ------------------------------------------------------------
-- PELÍCULAS
-- ------------------------------------------------------------
CREATE TABLE peliculas (
    id_pelicula     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titulo          VARCHAR(200) NOT NULL,
    titulo_original VARCHAR(200),
    sinopsis        TEXT,
    anio_estreno    YEAR,
    duracion_min    SMALLINT UNSIGNED,                 -- duración en minutos
    clasificacion   VARCHAR(10),                       -- G, PG, PG-13, R, etc.
    idioma_original VARCHAR(50),
    pais_produccion VARCHAR(80),

    -- Miniatura almacenada directamente en la BD
    miniatura       MEDIUMBLOB,                        -- imagen BLOB
    miniatura_mime  VARCHAR(50),                       -- ej. image/webp

    -- URLs de video (YouTube o servidor propio)
    url_trailer     VARCHAR(500),                      -- URL YouTube del tráiler
    url_video       VARCHAR(500),                      -- URL del video completo

    destacada       TINYINT(1)   NOT NULL DEFAULT 0,
    activa          TINYINT(1)   NOT NULL DEFAULT 1,
    creado_en       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Relación Película ↔ Géneros (muchos a muchos)
CREATE TABLE pelicula_generos (
    id_pelicula INT UNSIGNED NOT NULL,
    id_genero   INT UNSIGNED NOT NULL,
    PRIMARY KEY (id_pelicula, id_genero),
    CONSTRAINT fk_pg_pelicula FOREIGN KEY (id_pelicula) REFERENCES peliculas(id_pelicula)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_pg_genero   FOREIGN KEY (id_genero)   REFERENCES generos(id_genero)
        ON UPDATE CASCADE ON DELETE CASCADE
);

-- Relación Película ↔ Elenco
CREATE TABLE pelicula_elenco (
    id_pelicula     INT UNSIGNED NOT NULL,
    id_elenco       INT UNSIGNED NOT NULL,
    rol_en_pelicula VARCHAR(100),                      -- Actor, Director, Guionista…
    nombre_personaje VARCHAR(150),
    orden           TINYINT UNSIGNED DEFAULT 99,
    PRIMARY KEY (id_pelicula, id_elenco),
    CONSTRAINT fk_pe_pelicula FOREIGN KEY (id_pelicula) REFERENCES peliculas(id_pelicula)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_pe_elenco   FOREIGN KEY (id_elenco)   REFERENCES elenco_personas(id_elenco)
        ON UPDATE CASCADE ON DELETE CASCADE
);

-- ------------------------------------------------------------
-- SERIES  (estructura similar a películas pero con episodios)
-- ------------------------------------------------------------
CREATE TABLE series (
    id_serie        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titulo          VARCHAR(200) NOT NULL,
    sinopsis        TEXT,
    anio_inicio     YEAR,
    clasificacion   VARCHAR(10),
    miniatura       MEDIUMBLOB,
    miniatura_mime  VARCHAR(50),
    url_trailer     VARCHAR(500),
    activa          TINYINT(1)   NOT NULL DEFAULT 1,
    creado_en       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE temporadas (
    id_temporada    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_serie        INT UNSIGNED NOT NULL,
    numero          TINYINT UNSIGNED NOT NULL,
    titulo          VARCHAR(200),
    anio            YEAR,
    CONSTRAINT fk_temp_serie FOREIGN KEY (id_serie) REFERENCES series(id_serie)
        ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE episodios (
    id_episodio     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_temporada    INT UNSIGNED NOT NULL,
    numero          TINYINT UNSIGNED NOT NULL,
    titulo          VARCHAR(200) NOT NULL,
    descripcion     TEXT,
    duracion_min    SMALLINT UNSIGNED,
    miniatura       MEDIUMBLOB,
    miniatura_mime  VARCHAR(50),
    url_video       VARCHAR(500),
    creado_en       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_ep_temporada FOREIGN KEY (id_temporada) REFERENCES temporadas(id_temporada)
        ON UPDATE CASCADE ON DELETE CASCADE
);

-- ------------------------------------------------------------
-- HISTORIAL DE REPRODUCCIÓN
-- ------------------------------------------------------------
CREATE TABLE historial_reproduccion (
    id_historial    BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_usuario      INT UNSIGNED NOT NULL,
    id_pelicula     INT UNSIGNED,
    id_episodio     INT UNSIGNED,
    progreso_seg    INT UNSIGNED NOT NULL DEFAULT 0,   -- segundos vistos
    completado      TINYINT(1)  NOT NULL DEFAULT 0,
    fecha           DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_hr_usuario   FOREIGN KEY (id_usuario)  REFERENCES usuarios(id_usuario)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_hr_pelicula  FOREIGN KEY (id_pelicula) REFERENCES peliculas(id_pelicula)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_hr_episodio  FOREIGN KEY (id_episodio) REFERENCES episodios(id_episodio)
        ON UPDATE CASCADE ON DELETE SET NULL,

    -- Solo una de las dos referencias debe estar presente
    CONSTRAINT chk_contenido CHECK (
        (id_pelicula IS NOT NULL AND id_episodio IS NULL) OR
        (id_pelicula IS NULL AND id_episodio IS NOT NULL)
    )
);

-- ------------------------------------------------------------
-- LISTA DE FAVORITOS / MI LISTA
-- ------------------------------------------------------------
CREATE TABLE mi_lista (
    id_lista        BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_usuario      INT UNSIGNED NOT NULL,
    id_pelicula     INT UNSIGNED,
    id_serie        INT UNSIGNED,
    agregado_en     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_ml_usuario  FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_ml_pelicula FOREIGN KEY (id_pelicula) REFERENCES peliculas(id_pelicula)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_ml_serie    FOREIGN KEY (id_serie)    REFERENCES series(id_serie)
        ON UPDATE CASCADE ON DELETE CASCADE
);

-- ------------------------------------------------------------
-- VALORACIONES Y RESEÑAS
-- ------------------------------------------------------------
CREATE TABLE valoraciones (
    id_valoracion   BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_usuario      INT UNSIGNED NOT NULL,
    id_pelicula     INT UNSIGNED,
    id_serie        INT UNSIGNED,
    puntuacion      TINYINT UNSIGNED NOT NULL,          -- 1-10
    resena          TEXT,
    creado_en       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_val_usuario  FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_val_pelicula FOREIGN KEY (id_pelicula) REFERENCES peliculas(id_pelicula)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_val_serie    FOREIGN KEY (id_serie)    REFERENCES series(id_serie)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT chk_puntuacion  CHECK (puntuacion BETWEEN 1 AND 10)
);

-- ------------------------------------------------------------
-- ÍNDICES ADICIONALES
-- ------------------------------------------------------------
CREATE INDEX idx_usuarios_email       ON usuarios(email);
CREATE INDEX idx_peliculas_titulo     ON peliculas(titulo);
CREATE INDEX idx_peliculas_anio       ON peliculas(anio_estreno);
CREATE INDEX idx_historial_usuario    ON historial_reproduccion(id_usuario);
CREATE INDEX idx_historial_pelicula   ON historial_reproduccion(id_pelicula);