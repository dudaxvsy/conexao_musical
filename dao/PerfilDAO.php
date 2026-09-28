<?php

require_once __DIR__ . "/../config/Conexao.php";
require_once __DIR__ . "/../models/Perfil.php";

class PerfilDAO
{
    private $conexao;

    public function __construct()
    {
        $this->conexao = Conexao::conectar();
        $this->criarTabelaSeNecessario();
    }

    private function criarTabelaSeNecessario()
    {
        $sql = "CREATE TABLE IF NOT EXISTS perfis (
                    id_usuario INT PRIMARY KEY,
                    cidade VARCHAR(100) NULL,
                    instagram VARCHAR(150) NULL,
                    descricao TEXT NULL,
                    foto VARCHAR(255) NULL,
                    instrumentos VARCHAR(255) NULL,
                    CONSTRAINT fk_perfil_usuario
                        FOREIGN KEY (id_usuario)
                        REFERENCES usuarios(id_usuario)
                        ON DELETE CASCADE
                        ON UPDATE CASCADE
                ) ENGINE=InnoDB";

        $this->conexao->exec($sql);
    }

    public function buscarPorUsuario($idUsuario)
    {
        $sql = "SELECT * FROM perfis WHERE id_usuario = :id_usuario";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(":id_usuario", $idUsuario, PDO::PARAM_INT);
        $stmt->execute();

        $dados = $stmt->fetch();

        if (!$dados) {
            $perfil = new Perfil();
            $perfil->setIdUsuario($idUsuario);
            $perfil->setCidade("");
            $perfil->setInstagram("");
            $perfil->setDescricao("");
            $perfil->setFoto("");
            $perfil->setInstrumentos("");
            return $perfil;
        }

        return $this->mapear($dados);
    }

    public function salvar(Perfil $perfil)
    {
        $sql = "INSERT INTO perfis
                    (id_usuario, cidade, instagram, descricao, foto, instrumentos)
                VALUES
                    (:id_usuario, :cidade, :instagram, :descricao, :foto, :instrumentos)
                ON DUPLICATE KEY UPDATE
                    cidade = VALUES(cidade),
                    instagram = VALUES(instagram),
                    descricao = VALUES(descricao),
                    foto = VALUES(foto),
                    instrumentos = VALUES(instrumentos)";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(":id_usuario", $perfil->getIdUsuario(), PDO::PARAM_INT);
        $stmt->bindValue(":cidade", $perfil->getCidade());
        $stmt->bindValue(":instagram", $perfil->getInstagram());
        $stmt->bindValue(":descricao", $perfil->getDescricao());
        $stmt->bindValue(":foto", $perfil->getFoto());
        $stmt->bindValue(":instrumentos", $perfil->getInstrumentos());

        return $stmt->execute();
    }

    private function mapear($dados)
    {
        $perfil = new Perfil();
        $perfil->setIdUsuario($dados["id_usuario"]);
        $perfil->setCidade($dados["cidade"] ?? "");
        $perfil->setInstagram($dados["instagram"] ?? "");
        $perfil->setDescricao($dados["descricao"] ?? "");
        $perfil->setFoto($dados["foto"] ?? "");
        $perfil->setInstrumentos($dados["instrumentos"] ?? "");
        return $perfil;
    }
}
