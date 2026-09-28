-- database.sql
-- Script de criação do banco de dados do projeto Conexão Musical.
-- Não contém os dados pessoais cadastrados no banco local.

CREATE DATABASE IF NOT EXISTS conexao_musical
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE conexao_musical;

CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario      INT AUTO_INCREMENT PRIMARY KEY,
    nome            VARCHAR(150) NOT NULL,
    email           VARCHAR(150) NOT NULL UNIQUE,
    whatsapp        VARCHAR(20) NOT NULL,
    senha           VARCHAR(255) NOT NULL,
    data_nascimento DATE NOT NULL,
    telefone        VARCHAR(20) NOT NULL,
    tipo_usuario    VARCHAR(30) NOT NULL,
    data_cadastro   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS disponibilidades (
    id_disponibilidade INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario         INT NOT NULL,
    data               DATE NOT NULL,
    hora_inicio        TIME NOT NULL,
    hora_fim           TIME NOT NULL,
    CONSTRAINT uq_disponibilidade
        UNIQUE (id_usuario, data, hora_inicio, hora_fim),
    CONSTRAINT fk_disponibilidade_usuario
        FOREIGN KEY (id_usuario)
        REFERENCES usuarios (id_usuario)
        ON DELETE CASCADE
) ENGINE=InnoDB;
