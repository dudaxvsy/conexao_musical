<?php

require_once __DIR__ . "/../config/Conexao.php";
require_once __DIR__ . "/../models/Participacao.php";

class ParticipacaoDAO
{
    private $conexao;

    public function __construct()
    {
        $this->conexao = Conexao::conectar();
        $this->garantirColunaOrigem();
    }

    private function garantirColunaOrigem()
    {
        $stmt = $this->conexao->query("SHOW COLUMNS FROM participacoes LIKE 'origem'");

        if (!$stmt->fetch()) {
            $this->conexao->exec(
                "ALTER TABLE participacoes ADD COLUMN origem ENUM('oferta','convite') NOT NULL DEFAULT 'oferta' AFTER status"
            );
        }
    }

    public function listarPorMusico($id_usuario)
    {
        return $this->listarPorParticipante($id_usuario);
    }

    public function listarPorParticipante($id_usuario)
    {
        $sql = "SELECT
                    p.*,
                    s.titulo AS titulo_show,
                    s.data AS data_show,
                    s.hora_inicio,
                    s.hora_fim,
                    s.local AS local_show,
                    u.nome AS nome_usuario
                FROM participacoes p
                INNER JOIN shows s ON s.id_show = p.id_show
                INNER JOIN usuarios u ON u.id_usuario = s.id_usuario
                WHERE p.id_usuario = :id_usuario
                ORDER BY s.data ASC, s.hora_inicio ASC";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(":id_usuario", $id_usuario, PDO::PARAM_INT);
        $stmt->execute();

        return $this->mapear($stmt->fetchAll());
    }

    public function listarPorShowsDoCantor($id_usuario)
    {
        $sql = "SELECT
                    p.*,
                    s.titulo AS titulo_show,
                    s.data AS data_show,
                    s.hora_inicio,
                    s.hora_fim,
                    s.local AS local_show,
                    u.nome AS nome_usuario,
                    u.email AS email_usuario
                FROM participacoes p
                INNER JOIN shows s ON s.id_show = p.id_show
                INNER JOIN usuarios u ON u.id_usuario = p.id_usuario
                WHERE s.id_usuario = :id_usuario
                  AND p.origem = 'oferta'
                ORDER BY
                    CASE p.status
                        WHEN 'pendente' THEN 1
                        WHEN 'confirmada' THEN 2
                        ELSE 3
                    END,
                    u.nome ASC";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(":id_usuario", $id_usuario, PDO::PARAM_INT);
        $stmt->execute();

        return $this->mapear($stmt->fetchAll());
    }

    public function listarOfertasDoParticipante($id_usuario)
    {
        $sql = "SELECT
                    p.*,
                    s.titulo AS titulo_show,
                    s.data AS data_show,
                    s.hora_inicio,
                    s.hora_fim,
                    s.local AS local_show,
                    u.nome AS nome_usuario,
                    u.email AS email_usuario
                FROM participacoes p
                INNER JOIN shows s ON s.id_show = p.id_show
                INNER JOIN usuarios u ON u.id_usuario = s.id_usuario
                WHERE p.id_usuario = :id_usuario
                  AND p.origem = 'oferta'
                ORDER BY s.data ASC, s.hora_inicio ASC";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(":id_usuario", $id_usuario, PDO::PARAM_INT);
        $stmt->execute();

        return $this->mapear($stmt->fetchAll());
    }

    public function listarConvitesRecebidos($id_usuario)
    {
        $sql = "SELECT
                    p.*,
                    s.titulo AS titulo_show,
                    s.data AS data_show,
                    s.hora_inicio,
                    s.hora_fim,
                    s.local AS local_show,
                    u.nome AS nome_usuario,
                    u.email AS email_usuario
                FROM participacoes p
                INNER JOIN shows s ON s.id_show = p.id_show
                INNER JOIN usuarios u ON u.id_usuario = s.id_usuario
                WHERE p.id_usuario = :id_usuario
                  AND p.origem = 'convite'
                  AND u.tipo = 'cantor'
                ORDER BY
                    CASE p.status
                        WHEN 'pendente' THEN 1
                        WHEN 'confirmada' THEN 2
                        ELSE 3
                    END,
                    s.data ASC,
                    s.hora_inicio ASC";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(":id_usuario", $id_usuario, PDO::PARAM_INT);
        $stmt->execute();

        return $this->mapear($stmt->fetchAll());
    }

    public function listarOfertasPorShow($id_show, $id_cantor)
    {
        $sql = "SELECT
                    p.*,
                    s.titulo AS titulo_show,
                    s.data AS data_show,
                    s.hora_inicio,
                    s.hora_fim,
                    s.local AS local_show,
                    u.nome AS nome_usuario,
                    u.email AS email_usuario
                FROM participacoes p
                INNER JOIN shows s ON s.id_show = p.id_show
                INNER JOIN usuarios u ON u.id_usuario = p.id_usuario
                WHERE p.id_show = :id_show
                  AND s.id_usuario = :id_cantor
                  AND p.origem = 'oferta'
                ORDER BY
                    CASE p.status
                        WHEN 'pendente' THEN 1
                        WHEN 'confirmada' THEN 2
                        ELSE 3
                    END,
                    u.nome ASC";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(":id_show", $id_show, PDO::PARAM_INT);
        $stmt->bindValue(":id_cantor", $id_cantor, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function atualizarStatus($id_participacao, $id_cantor, $status)
    {
        if (!in_array($status, ["pendente", "confirmada", "cancelada"], true)) {
            return false;
        }

        $sql = "UPDATE participacoes p
                INNER JOIN shows s ON s.id_show = p.id_show
                INNER JOIN usuarios u ON u.id_usuario = p.id_usuario
                SET p.status = :status
                WHERE p.id_participacao = :id_participacao
                  AND s.id_usuario = :id_cantor
                  AND p.origem = 'oferta'
                  AND u.id_usuario <> :id_cantor_usuario";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(":status", $status);
        $stmt->bindValue(":id_participacao", $id_participacao, PDO::PARAM_INT);
        $stmt->bindValue(":id_cantor", $id_cantor, PDO::PARAM_INT);
        $stmt->bindValue(":id_cantor_usuario", $id_cantor, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function responderConvite($id_participacao, $id_usuario, $status)
    {
        if (!in_array($status, ["confirmada", "cancelada"], true)) {
            return false;
        }

        $sql = "UPDATE participacoes p
                INNER JOIN usuarios u ON u.id_usuario = p.id_usuario
                SET p.status = :status
                WHERE p.id_participacao = :id_participacao
                  AND p.id_usuario = :id_usuario
                  AND p.origem = 'convite'
                  AND u.tipo = 'cantor'";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(":status", $status);
        $stmt->bindValue(":id_participacao", $id_participacao, PDO::PARAM_INT);
        $stmt->bindValue(":id_usuario", $id_usuario, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function existeParticipacao($id_show, $id_usuario)
    {
        $sql = "SELECT id_participacao FROM participacoes
                WHERE id_show = :id_show AND id_usuario = :id_usuario";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(":id_show", $id_show, PDO::PARAM_INT);
        $stmt->bindValue(":id_usuario", $id_usuario, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch() !== false;
    }

    public function cadastrarOferta($id_show, $id_usuario)
    {
        $sql = "INSERT INTO participacoes
                (id_show, id_usuario, funcao, status, origem, observacao)
                SELECT
                    :id_show,
                    :id_usuario,
                    NULL,
                    'pendente',
                    'oferta',
                    NULL
                FROM shows s
                INNER JOIN usuarios u ON u.id_usuario = :id_usuario_usuario
                WHERE s.id_show = :id_show_show
                  AND s.id_usuario <> :id_usuario_dono";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(":id_show", $id_show, PDO::PARAM_INT);
        $stmt->bindValue(":id_usuario", $id_usuario, PDO::PARAM_INT);
        $stmt->bindValue(":id_usuario_usuario", $id_usuario, PDO::PARAM_INT);
        $stmt->bindValue(":id_show_show", $id_show, PDO::PARAM_INT);
        $stmt->bindValue(":id_usuario_dono", $id_usuario, PDO::PARAM_INT);

        return $stmt->execute() && $stmt->rowCount() > 0;
    }

    public function cadastrarConvite($id_show, $id_cantor, $id_convidado)
    {
        if ($id_cantor === $id_convidado) {
            return false;
        }

        $sql = "INSERT INTO participacoes
                (id_show, id_usuario, funcao, status, origem, observacao)
                SELECT
                    :id_show,
                    :id_convidado,
                    NULL,
                    'pendente',
                    'convite',
                    NULL
                FROM shows s
                INNER JOIN usuarios u ON u.id_usuario = :id_convidado_usuario
                WHERE s.id_show = :id_show_show
                  AND s.id_usuario = :id_cantor
                  AND u.tipo = 'cantor'";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(":id_show", $id_show, PDO::PARAM_INT);
        $stmt->bindValue(":id_convidado", $id_convidado, PDO::PARAM_INT);
        $stmt->bindValue(":id_convidado_usuario", $id_convidado, PDO::PARAM_INT);
        $stmt->bindValue(":id_show_show", $id_show, PDO::PARAM_INT);
        $stmt->bindValue(":id_cantor", $id_cantor, PDO::PARAM_INT);

        return $stmt->execute() && $stmt->rowCount() > 0;
    }

    private function mapear($resultados)
    {
        $participacoes = [];

        foreach ($resultados as $dados) {
            $participacao = new Participacao();

            $participacao->setIdParticipacao($dados["id_participacao"]);
            $participacao->setIdShow($dados["id_show"]);
            $participacao->setIdUsuario($dados["id_usuario"]);
            $participacao->setFuncao($dados["funcao"] ?? null);
            $participacao->setStatus($dados["status"]);
            $participacao->setOrigem($dados["origem"] ?? "oferta");
            $participacao->setObservacao($dados["observacao"] ?? null);
            $participacao->setDataCadastro($dados["data_cadastro"]);
            $participacao->setTituloShow($dados["titulo_show"]);
            $participacao->setDataShow($dados["data_show"]);
            $participacao->setHoraInicio($dados["hora_inicio"]);
            $participacao->setHoraFim($dados["hora_fim"]);
            $participacao->setLocalShow($dados["local_show"]);
            $participacao->setNomeUsuario($dados["nome_usuario"]);
            $participacao->setEmailUsuario($dados["email_usuario"] ?? null);

            $participacoes[] = $participacao;
        }

        return $participacoes;
    }
}
