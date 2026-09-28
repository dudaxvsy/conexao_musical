<?php

session_start();

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION["tipo_usuario"] !== "cantor") {
    header("Location: ../../public/index.php");
    exit;
}

require_once __DIR__ . "/../../controllers/UsuarioController.php";

$data = $_GET["data"] ?? "";
$horaInicio = $_GET["hora_inicio"] ?? "";
$horaFim = $_GET["hora_fim"] ?? "";

$musicos = [];
$mensagem = "";
$tipoMensagem = "";

if (!empty($horaInicio) xor !empty($horaFim)) {
    $mensagem = "Informe o horário de início e o horário de término para filtrar por horário.";
    $tipoMensagem = "erro";
} elseif (!empty($horaInicio) && !empty($horaFim) && $horaInicio >= $horaFim) {
    $mensagem = "O horário de início deve ser anterior ao horário de término.";
    $tipoMensagem = "erro";
} else {
    try {
        $controller = new UsuarioController();
        $musicos = $controller->buscarMusicosDisponiveis(
            $data,
            $horaInicio,
            $horaFim
        );
    } catch (Exception $e) {
        $mensagem = "Não foi possível buscar os músicos no momento.";
        $tipoMensagem = "erro";
    }
}

$musicosAgrupados = [];

foreach ($musicos as $musico) {
    $id = $musico["id_usuario"];

    if (!isset($musicosAgrupados[$id])) {
        $musicosAgrupados[$id] = [
            "nome" => $musico["nome"],
            "email" => $musico["email"],
            "whatsapp" => $musico["whatsapp"] ?? "",
            "disponibilidades" => []
        ];
    }

    $musicosAgrupados[$id]["disponibilidades"][] = [
        "data" => $musico["data"],
        "hora_inicio" => $musico["hora_inicio"],
        "hora_fim" => $musico["hora_fim"]
    ];
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
    <title>Buscar músicos - Conexão Musical</title>
    <link rel="stylesheet" href="../../public/css/buscar_musicos.css">
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
            <span class="nome-usuario">
                <?= htmlspecialchars($_SESSION["nome_usuario"]) ?>
            </span>
            <span class="tipo-usuario">Cantor</span>
        </div>

        <a href="logout.php" class="botao-sair">SAIR</a>
    </div>

</header>

<main class="conteudo">

    <a href="../../public/index.php" class="voltar">← Voltar</a>

    <div class="cabecalho-pagina">
        <h1>Buscar músicos</h1>
        <p>Encontre músicos disponíveis para o seu show e entre em contato com eles.</p>
    </div>

    <section class="filtro">

        <div class="titulo-filtro">
            <h2>Consultar disponibilidade</h2>
            <p>Você pode buscar por uma data e, se quiser, por um horário específico.</p>
        </div>

        <form method="GET" class="formulario-filtro">

            <div class="campo">
                <label for="data">Data</label>
                <input
                    type="date"
                    id="data"
                    name="data"
                    value="<?= htmlspecialchars($data) ?>"
                    min="<?= date("Y-m-d") ?>"
                >
            </div>

            <div class="campo">
                <label for="hora_inicio">Horário de início</label>
                <input
                    type="time"
                    id="hora_inicio"
                    name="hora_inicio"
                    value="<?= htmlspecialchars($horaInicio) ?>"
                >
            </div>

            <div class="campo">
                <label for="hora_fim">Horário de término</label>
                <input
                    type="time"
                    id="hora_fim"
                    name="hora_fim"
                    value="<?= htmlspecialchars($horaFim) ?>"
                >
            </div>

            <button type="submit" class="botao-buscar">BUSCAR</button>

        </form>

        <?php if (!empty($data) || !empty($horaInicio) || !empty($horaFim)): ?>
            <a href="buscar_musicos.php" class="limpar-filtro">Limpar filtros</a>
        <?php endif; ?>

    </section>

    <?php if (!empty($mensagem)): ?>
        <div class="mensagem <?= $tipoMensagem ?>">
            <?= htmlspecialchars($mensagem) ?>
        </div>
    <?php endif; ?>

    <?php if (empty($mensagem) && empty($musicosAgrupados)): ?>

        <section class="estado-vazio">
            <div class="icone-vazio">♫</div>
            <h2>Nenhum músico encontrado</h2>
            <p>Não encontramos músicos com disponibilidade para os filtros informados.</p>
        </section>

    <?php elseif (!empty($musicosAgrupados)): ?>

        <section class="resultado-cabecalho">
            <h2>Músicos disponíveis</h2>
            <span><?= count($musicosAgrupados) ?> encontrado<?= count($musicosAgrupados) === 1 ? "" : "s" ?></span>
        </section>

        <section class="lista-musicos">

            <?php foreach ($musicosAgrupados as $musico): ?>

                <article class="musico-card">

                    <div class="musico-topo">
                        <div class="icone-musico">♫</div>

                        <div class="dados-musico">
                            <h2><?= htmlspecialchars($musico["nome"]) ?></h2>
                            <p><?= htmlspecialchars($musico["email"]) ?></p>
                        </div>
                    </div>

                    <div class="disponibilidades">
                        <span class="rotulo">Disponibilidade</span>

                        <?php foreach ($musico["disponibilidades"] as $disponibilidade): ?>
                            <div class="horario">
                                <span>
                                    <?= formatarData($disponibilidade["data"]) ?>
                                </span>
                                <strong>
                                    <?= formatarHora($disponibilidade["hora_inicio"]) ?> às
                                    <?= formatarHora($disponibilidade["hora_fim"]) ?>
                                </strong>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php
                        $whatsapp = preg_replace('/\D/', '', $musico["whatsapp"]);
                        if (strlen($whatsapp) === 10 || strlen($whatsapp) === 11) {
                            $whatsapp = "55" . $whatsapp;
                        }
                        $mensagemWhatsApp = rawurlencode("Olá! Encontrei seu contato pelo Conexão Musical e gostaria de conversar sobre uma oportunidade de show.");
                    ?>

                    <?php if (!empty($whatsapp)): ?>
                        <a
                            href="https://wa.me/<?= htmlspecialchars($whatsapp) ?>?text=<?= $mensagemWhatsApp ?>"
                            class="botao-contato"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            ENTRAR EM CONTATO PELO WHATSAPP
                        </a>
                    <?php else: ?>
                        <span class="contato-indisponivel">WhatsApp não informado</span>
                    <?php endif; ?>

                </article>

            <?php endforeach; ?>

        </section>

    <?php endif; ?>

</main>

<footer>
    © 2026 CONEXÃO MUSICAL
</footer>

</body>
</html>
