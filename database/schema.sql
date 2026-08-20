-- Misma tabla que usa la versión Laravel (../laravel-productos-crud), para
-- que el CRUD sea comparable entre los dos frameworks.

CREATE DATABASE IF NOT EXISTS productos_crud;
USE productos_crud;

CREATE TABLE IF NOT EXISTS productos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
);
