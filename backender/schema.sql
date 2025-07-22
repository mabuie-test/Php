-- schema.sql

-- Criação da base de dados
CREATE DATABASE IF NOT EXISTS philaded_Philaseanproviderwebsite
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE philaded_Philaseanproviderwebsite;

-- Tabela de usuários (clientes e admins)
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('client','admin') NOT NULL DEFAULT 'client',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabela de pedidos de serviço
CREATE TABLE IF NOT EXISTS requests (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  company VARCHAR(100),
  vessel VARCHAR(100) NOT NULL,
  port VARCHAR(50) NOT NULL,
  date_estimated DATE NOT NULL,
  services TEXT NOT NULL,
  notes TEXT,
  status ENUM('pending','in_progress','completed') NOT NULL DEFAULT 'pending',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabela de logs de auditoria
CREATE TABLE IF NOT EXISTS audit_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  action VARCHAR(255) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;
