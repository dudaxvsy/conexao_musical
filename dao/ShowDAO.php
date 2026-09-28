<?php

require_once __DIR__ . "/../config/Conexao.php";
require_once __DIR__ . "/../models/Show.php";

class ShowDAO
{
    private $conexao;

    public function __construct()
    {
        $this->conexao = Conexao::conectar();
    }

    public function cadastrar(Show $show)
    {
        $sql = "INSERT INTO shows
                (id_usuario, titulo, data, hora_inicio, hora_fim, local, endereco, descricao)
                VALUES
                (:id_usuario, :titulo, :data, :hora_inicio, :hora_fim, :local, :endereco, :descricao)";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(":id_usuario", $show->getIdUsuario());
        $stmt->bindValue(":titulo", $show->getTitulo());
        $stmt->bindValue(":data", $show->getData());
        $stmt->bindValue(":hora_inicio", $show->getHoraInicio());
        $stmt->bindValue(":hora_fim", $show->getHoraFim());
        $stmt->bindValue(":local", $show->getLocal());
        $stmt->bindValue(":endereco", $show->getEndereco());
        $stmt->bindValue(":descricao", $show->getDescricao());

        return $stmt->execute();
    }

    public function listarPorUsuario($id_usuario)
    {
        $sql = "SELECT *
                FROM shows
                WHERE id_usuario = :id_usuario
                ORDER BY data ASC, hora_inicio ASC";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(":id_usuario", $id_usuario);
        $stmt->execute();

        $resultados = $stmt->fetchAll();
        $shows = [];

        foreach ($resultados as $dados) {
            $shows[] = $this->mapear($dados);
        }

        return $shows;
    }

    public function listarDisponiveisParaMusico($id_usuario)
    {
        $sql = "SELECT
                    s.*,
                    u.nome AS nome_cantor
                FROM shows s
                INNER JOIN usuarios u ON u.id_usuario = s.id_usuario
                WHERE s.data >= CURDATE()
                  AND s.id_usuario <> :id_usuario
                  AND u.tipo = 'cantor'
                ORDER BY s.data ASC, s.hora_inicio ASC";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(":id_usuario", $id_usuario, PDO::PARAM_INT);
        $stmt->execute();

        $resultados = $stmt->fetchAll();
        $shows = [];

        foreach ($resultados as $dados) {
            $show = $this->mapear($dados);
            $show->setNomeCantor($dados["nome_cantor"]);
            $shows[] = $show;
        }

        return $shows;
    }

    public function listarParticipantesConfirmados($id_show, $id_cantor)
    {
        $sql = "SELECT
                    u.id_usuario,
                    u.nome,
                    u.tipo,
                    p.origem,
                    p.status
                FROM participacoes p
                INNER JOIN shows s ON s.id_show = p.id_show
                INNER JOIN usuarios u ON u.id_usuario = p.id_usuario
                WHERE p.id_show = :id_show
                  AND s.id_usuario = :id_cantor
                  AND p.status = 'confirmada'
                  AND p.id_usuario <> :id_cantor_usuario
                ORDER BY u.nome ASC";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(":id_show", $id_show, PDO::PARAM_INT);
        $stmt->bindValue(":id_cantor", $id_cantor, PDO::PARAM_INT);
        $stmt->bindValue(":id_cantor_usuario", $id_cantor, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    private function mapear($dados)
    {
        $show = new Show();

        $show->setIdShow($dados["id_show"]);
        $show->setIdUsuario($dados["id_usuario"]);
        $show->setTitulo($dados["titulo"]);
        $show->setData($dados["data"]);
        $show->setHoraInicio($dados["hora_inicio"]);
        $show->setHoraFim($dados["hora_fim"]);
        $show->setLocal($dados["local"]);
        $show->setEndereco($dados["endereco"] ?? "");
        $show->setDescricao($dados["descricao"] ?? "");
        $show->setDataCadastro($dados["data_cadastro"] ?? null);

        return $show;
    }
}
