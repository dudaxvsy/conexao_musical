USE conexao_musical;

ALTER TABLE usuarios
ADD COLUMN whatsapp VARCHAR(20) NOT NULL AFTER email;
