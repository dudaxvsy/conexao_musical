<?php
session_start();

if (!isset($_SESSION["id_usuario"])) {
    header("Location: ../usuario/login.php");
    exit;
}

if (!in_array($_SESSION["tipo_usuario"], ["cantor", "musico"], true)) {
    header("Location: ../../public/index.php");
    exit;
}

require_once __DIR__ . "/../../controllers/ShowController.php";
require_once __DIR__ . "/../../controllers/ParticipacaoController.php";

$tipoUsuario = $_SESSION["tipo_usuario"];
$mensagem = "";
$tipoMensagem = "";

try {
    $showController = new ShowController();
    $participacaoController = new ParticipacaoController();

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $idShow = (int)($_POST["id_show"] ?? 0);

        if ($idShow <= 0) {
            $mensagem = "Show inválido.";
            $tipoMensagem = "erro";
        } elseif ($participacaoController->existeParticipacao($idShow, $_SESSION["id_usuario"])) {
            $mensagem = "Você já possui uma participação ou solicitação para este show.";
            $tipoMensagem = "aviso";
        } elseif ($participacaoController->cadastrarOferta($idShow, $_SESSION["id_usuario"])) {
            $mensagem = "Sua oferta de participação foi enviada.";
            $tipoMensagem = "sucesso";
        } else {
            $mensagem = "Não foi possível enviar sua oferta.";
            $tipoMensagem = "erro";
        }
    }

    $shows = $showController->listarDisponiveisParaMusico($_SESSION["id_usuario"]);

} catch (Exception $e) {
    $shows = [];
    $mensagem = "Não foi possível carregar os shows no momento.";
    $tipoMensagem = "erro";
}

function formatarData($data)
{
    return date("d/m/Y", strtotime($data));
}

function formatarHora($hora)
{
    return date("H:i", strtotime($hora));
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar shows - Conexão Musical</title>
    <link rel="stylesheet" href="../../public/css/buscar_shows.css">
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
            <span class="nome-usuario"><?= htmlspecialchars($_SESSION["nome_usuario"]) ?></span>
            <span class="tipo-usuario"><?= $tipoUsuario === "musico" ? "Músico" : "Cantor" ?></span>
        </div>
        <a href="../usuario/logout.php" class="botao-sair">SAIR</a>
    </div>
</header>

<main class="conteudo">
    <a href="../../public/index.php" class="voltar">← Voltar</a>

    <div class="cabecalho-pagina">
        <h1>Buscar shows</h1>
        <p>Encontre apresentações em que você pode se oferecer para participar.</p>
    </div>

    <?php if ($mensagem): ?>
        <div class="mensagem <?= $tipoMensagem ?>">
            <?= htmlspecialchars($mensagem) ?>
        </div>
    <?php endif; ?>

    <?php if (empty($shows)): ?>
        <section class="vazio">
            <div class="icone-vazio">♪</div>
            <h2>Nenhum show disponível</h2>
            <p>No momento, não há shows futuros cadastrados por outros cantores.</p>
        </section>
    <?php else: ?>
        <section class="lista-shows">
            <?php foreach ($shows as $show): ?>
                <article class="card-show">
                    <div class="topo-card">
                        <div>
                            <span class="data-show"><?= formatarData($show->getData()) ?></span>
                            <h2><?= htmlspecialchars($show->getTitulo()) ?></h2>
                        </div>
                        <span class="organizador">Por <?= htmlspecialchars($show->getNomeCantor()) ?></span>
                    </div>

                    <div class="detalhes">
                        <div>
                            <strong>Horário</strong>
                            <span><?= formatarHora($show->getHoraInicio()) ?> às <?= formatarHora($show->getHoraFim()) ?></span>
                        </div>
                        <div>
                            <strong>Local</strong>
                            <span><?= htmlspecialchars($show->getLocal()) ?></span>
                        </div>
                        <?php if ($show->getEndereco()): ?>
                            <div>
                                <strong>Endereço</strong>
                                <span><?= htmlspecialchars($show->getEndereco()) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($show->getDescricao()): ?>
                        <p class="descricao"><?= nl2br(htmlspecialchars($show->getDescricao())) ?></p>
                    <?php endif; ?>

                    <form method="POST">
                        <input type="hidden" name="id_show" value="<?= (int) $show->getIdShow() ?>">
                        <button type="submit" class="botao-oferecer">QUERO ME OFERECER</button>
                    </form>
                </article>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>
</main>

<footer>© 2026 CONEXÃO MUSICAL</footer>
</body>
</html>
