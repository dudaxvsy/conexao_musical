<?php

session_start();

if (!isset($_SESSION["id_usuario"])) {
    header("Location: ../usuario/login.php");
    exit;
}

require_once __DIR__ . "/../../controllers/ParticipacaoController.php";

$tipoUsuario = $_SESSION["tipo_usuario"];
$nomeUsuario = $_SESSION["nome_usuario"];
$participacoes = [];
$solicitacoes = [];
$convites = [];
$minhasOfertas = [];
$erro = "";
$mensagem = "";
$tipoMensagem = "";
$controller = new ParticipacaoController();

try {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if ($tipoUsuario === "cantor" && isset($_POST["acao_solicitacao"])) {
            $idParticipacao = (int) ($_POST["id_participacao"] ?? 0);
            $status = $_POST["status"] ?? "";

            if ($idParticipacao > 0 && in_array($status, ["pendente", "confirmada", "cancelada"], true)) {
                if ($controller->atualizarStatus($idParticipacao, $_SESSION["id_usuario"], $status)) {
                    $mensagem = $status === "confirmada"
                        ? "Solicitação confirmada com sucesso."
                        : ($status === "cancelada" ? "Solicitação cancelada com sucesso." : "Solicitação revertida com sucesso.");
                    $tipoMensagem = "sucesso";
                } else {
                    $mensagem = "Não foi possível atualizar a solicitação.";
                    $tipoMensagem = "erro";
                }
            } else {
                $mensagem = "Dados da solicitação inválidos.";
                $tipoMensagem = "erro";
            }
        }

        if ($tipoUsuario === "cantor" && isset($_POST["acao_convite"])) {
            $idParticipacao = (int) ($_POST["id_participacao"] ?? 0);
            $status = $_POST["status"] ?? "";

            if ($idParticipacao > 0 && in_array($status, ["confirmada", "cancelada"], true)) {
                if ($controller->responderConvite($idParticipacao, $_SESSION["id_usuario"], $status)) {
                    $mensagem = $status === "confirmada"
                        ? "Convite aceito com sucesso."
                        : "Convite recusado com sucesso.";
                    $tipoMensagem = "sucesso";
                } else {
                    $mensagem = "Não foi possível responder ao convite.";
                    $tipoMensagem = "erro";
                }
            } else {
                $mensagem = "Dados do convite inválidos.";
                $tipoMensagem = "erro";
            }
        }
    }

    if ($tipoUsuario === "musico") {
        $participacoes = $controller->listarPorUsuario(
            $_SESSION["id_usuario"],
            $tipoUsuario
        );
    } else {
        $solicitacoes = $controller->listarPorUsuario(
            $_SESSION["id_usuario"],
            $tipoUsuario
        );
        $convites = $controller->listarConvitesRecebidos($_SESSION["id_usuario"]);
        $minhasOfertas = $controller->listarOfertasDoParticipante($_SESSION["id_usuario"]);
    }
} catch (Exception $e) {
    $erro = "Não foi possível carregar as participações no momento.";
}

function formatarDataParticipacao($data)
{
    return date("d/m/Y", strtotime($data));
}

function formatarHoraParticipacao($hora)
{
    return date("H:i", strtotime($hora));
}

function textoStatus($status)
{
    switch ($status) {
        case "confirmada":
            return "Confirmada";
        case "cancelada":
            return "Cancelada";
        default:
            return "Pendente";
    }
}

function textoTipoParticipante($tipo)
{
    return $tipo === "musico" ? "Músico" : "Cantor";
}

function renderizarParticipacaoCard($participacao, $tipoUsuario, $modo = "geral")
{
    ?>
    <article class="participacao-card">
        <div class="participacao-principal">
            <div class="icone-participacao">♪</div>

            <div class="informacoes-show">
                <h2><?= htmlspecialchars($participacao->getTituloShow()) ?></h2>
                <div class="detalhes">
                    <span><?= formatarDataParticipacao($participacao->getDataShow()) ?></span>
                    <span><?= formatarHoraParticipacao($participacao->getHoraInicio()) ?> às <?= formatarHoraParticipacao($participacao->getHoraFim()) ?></span>
                    <span><?= htmlspecialchars($participacao->getLocalShow()) ?></span>
                </div>
            </div>
        </div>

        <div class="participacao-info">
            <div>
                <span class="rotulo">
                    <?php if ($modo === "convite"): ?>
                        Convidado por
                    <?php elseif ($modo === "oferta"): ?>
                        Organizador
                    <?php else: ?>
                        <?= $tipoUsuario === "musico" ? "Organizador" : "Participante" ?>
                    <?php endif; ?>
                </span>
                <strong><?= htmlspecialchars($participacao->getNomeUsuario()) ?></strong>
            </div>

            <?php if (!empty($participacao->getEmailUsuario())): ?>
                <div>
                    <span class="rotulo">E-mail</span>
                    <strong><?= htmlspecialchars($participacao->getEmailUsuario()) ?></strong>
                </div>
            <?php endif; ?>

            <?php if (!empty($participacao->getFuncao())): ?>
                <div>
                    <span class="rotulo">Função</span>
                    <strong><?= htmlspecialchars($participacao->getFuncao()) ?></strong>
                </div>
            <?php endif; ?>

            <span class="status <?= htmlspecialchars($participacao->getStatus()) ?>">
                <?= textoStatus($participacao->getStatus()) ?>
            </span>
        </div>

        <?php if ($modo === "convite" && $participacao->getStatus() === "pendente"): ?>
            <div class="acoes-solicitacao">
                <form method="POST">
                    <input type="hidden" name="acao_convite" value="1">
                    <input type="hidden" name="id_participacao" value="<?= (int) $participacao->getIdParticipacao() ?>">
                    <input type="hidden" name="status" value="confirmada">
                    <button type="submit" class="botao-solicitacao confirmar">ACEITAR</button>
                </form>

                <form method="POST">
                    <input type="hidden" name="acao_convite" value="1">
                    <input type="hidden" name="id_participacao" value="<?= (int) $participacao->getIdParticipacao() ?>">
                    <input type="hidden" name="status" value="cancelada">
                    <button type="submit" class="botao-solicitacao cancelar">RECUSAR</button>
                </form>
            </div>
        <?php endif; ?>

        <?php if ($modo === "solicitacao"): ?>
            <div class="acoes-solicitacao">
                <?php if ($participacao->getStatus() === "pendente"): ?>
                    <form method="POST">
                        <input type="hidden" name="acao_solicitacao" value="1">
                        <input type="hidden" name="id_participacao" value="<?= (int) $participacao->getIdParticipacao() ?>">
                        <input type="hidden" name="status" value="confirmada">
                        <button type="submit" class="botao-solicitacao confirmar">CONFIRMAR</button>
                    </form>

                    <form method="POST">
                        <input type="hidden" name="acao_solicitacao" value="1">
                        <input type="hidden" name="id_participacao" value="<?= (int) $participacao->getIdParticipacao() ?>">
                        <input type="hidden" name="status" value="cancelada">
                        <button type="submit" class="botao-solicitacao cancelar">CANCELAR</button>
                    </form>
                <?php else: ?>
                    <form method="POST">
                        <input type="hidden" name="acao_solicitacao" value="1">
                        <input type="hidden" name="id_participacao" value="<?= (int) $participacao->getIdParticipacao() ?>">
                        <input type="hidden" name="status" value="pendente">
                        <button type="submit" class="botao-solicitacao reverter">REVERTER</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </article>
    <?php
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $tipoUsuario === "musico" ? "Minhas participações" : "Solicitações" ?> - Conexão Musical</title>
    <link rel="stylesheet" href="../../public/css/participacao.css">
</head>
<body>

<header class="topo">
    <div class="logo">
        <span class="icone-musical">♫</span>
        <div>
            <span>Conexão</span>
            <span>Musical</span>
        </div>
    </div>

    <div class="usuario">
        <div class="informacoes-usuario">
            <span class="nome-usuario"><?= htmlspecialchars($nomeUsuario) ?></span>
            <span class="tipo-usuario"><?= $tipoUsuario === "musico" ? "Músico" : "Cantor" ?></span>
        </div>
        <a href="../usuario/logout.php" class="botao-sair">SAIR</a>
    </div>
</header>

<main class="conteudo">
    <a href="../../public/index.php" class="voltar">← Voltar</a>

    <div class="cabecalho-pagina">
        <h1><?= $tipoUsuario === "musico" ? "Minhas participações" : "Solicitações" ?></h1>
        <p>
            <?php if ($tipoUsuario === "musico"): ?>
                Acompanhe os shows em que você se ofereceu para participar.
            <?php else: ?>
                Gerencie solicitações dos seus shows e acompanhe convites para participar de outras apresentações.
            <?php endif; ?>
        </p>
    </div>

    <?php if ($mensagem): ?>
        <div class="mensagem sucesso"><?= htmlspecialchars($mensagem) ?></div>
    <?php endif; ?>

    <?php if ($erro): ?>
        <div class="mensagem erro"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <?php if ($tipoUsuario === "musico"): ?>

        <?php if (empty($participacoes) && empty($erro)): ?>
            <section class="estado-vazio">
                <div class="icone-vazio">♬</div>
                <h2>Nenhuma participação cadastrada</h2>
                <p>Quando você se oferecer para participar de um show, ele aparecerá aqui.</p>
            </section>
        <?php elseif (!empty($participacoes)): ?>
            <section class="lista-participacoes">
                <?php foreach ($participacoes as $participacao): ?>
                    <?php renderizarParticipacaoCard($participacao, $tipoUsuario); ?>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>

    <?php else: ?>

        <section class="bloco-secao">
            <div class="titulo-bloco">
                <h2>Solicitações nos meus shows</h2>
                <p>Participantes que se ofereceram para tocar ou cantar nas apresentações que você cadastrou.</p>
            </div>

            <?php if (empty($solicitacoes)): ?>
                <div class="estado-vazio pequeno">
                    <div class="icone-vazio">♫</div>
                    <h2>Nenhuma solicitação</h2>
                    <p>Quando alguém se oferecer para participar de um dos seus shows, aparecerá aqui.</p>
                </div>
            <?php else: ?>
                <section class="lista-participacoes">
                    <?php foreach ($solicitacoes as $participacao): ?>
                        <?php renderizarParticipacaoCard($participacao, $tipoUsuario, "solicitacao"); ?>
                    <?php endforeach; ?>
                </section>
            <?php endif; ?>
        </section>

        <section class="bloco-secao">
            <div class="titulo-bloco">
                <h2>Convites recebidos</h2>
                <p>Veja os convites enviados por outros cantores e responda quando quiser participar.</p>
            </div>

            <?php if (empty($convites)): ?>
                <div class="estado-vazio pequeno">
                    <div class="icone-vazio">♪</div>
                    <h2>Nenhum convite recebido</h2>
                    <p>Quando outro cantor convidar você para um show, o convite aparecerá aqui.</p>
                </div>
            <?php else: ?>
                <section class="lista-participacoes">
                    <?php foreach ($convites as $participacao): ?>
                        <?php renderizarParticipacaoCard($participacao, $tipoUsuario, "convite"); ?>
                    <?php endforeach; ?>
                </section>
            <?php endif; ?>
        </section>

        <section class="bloco-secao">
            <div class="titulo-bloco">
                <h2>Minhas ofertas em outros shows</h2>
                <p>Acompanhe os shows de outros cantores em que você se ofereceu para participar.</p>
            </div>

            <?php if (empty($minhasOfertas)): ?>
                <div class="estado-vazio pequeno">
                    <div class="icone-vazio">♪</div>
                    <h2>Nenhuma oferta enviada</h2>
                    <p>Quando você se oferecer para participar de outro show, ele aparecerá aqui.</p>
                </div>
            <?php else: ?>
                <section class="lista-participacoes">
                    <?php foreach ($minhasOfertas as $participacao): ?>
                        <?php renderizarParticipacaoCard($participacao, $tipoUsuario, "oferta"); ?>
                    <?php endforeach; ?>
                </section>
            <?php endif; ?>
        </section>

    <?php endif; ?>

</main>

<footer>© 2026 CONEXÃO MUSICAL</footer>
</body>
</html>
