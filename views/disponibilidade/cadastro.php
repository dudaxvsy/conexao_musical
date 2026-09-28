<?php

session_start();

if (!isset($_SESSION["id_usuario"])) {
    header("Location: ../usuario/login.php");
    exit;
}

if ($_SESSION["tipo_usuario"] !== "musico") {
    header("Location: ../../public/index.php");
    exit;
}

$mensagem = "";
$tipoMensagem = "";

$data = "";
$hora_inicio = "";
$hora_fim = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    require_once __DIR__ . "/../../controllers/DisponibilidadeController.php";

    $id_usuario = $_SESSION["id_usuario"];

    $data = $_POST["data"] ?? "";
    $hora_inicio = $_POST["hora_inicio"] ?? "";
    $hora_fim = $_POST["hora_fim"] ?? "";

    if (
        empty($data) ||
        empty($hora_inicio) ||
        empty($hora_fim)
    ) {

        $mensagem = "Preencha todos os campos.";
        $tipoMensagem = "erro";

    } elseif ($hora_inicio >= $hora_fim) {

        $mensagem = "A hora de início deve ser anterior à hora de término.";
        $tipoMensagem = "erro";

    } else {

        try {

            $controller = new DisponibilidadeController();

            $resultado = $controller->cadastrar(
                $id_usuario,
                $data,
                $hora_inicio,
                $hora_fim
            );

            if ($resultado) {

                $mensagem = "Disponibilidade cadastrada com sucesso.";
                $tipoMensagem = "sucesso";

                $data = "";
                $hora_inicio = "";
                $hora_fim = "";

            } else {

                $mensagem = "Não foi possível cadastrar a disponibilidade.";
                $tipoMensagem = "erro";
            }

        } catch (Exception $e) {

            $mensagem = "Erro ao cadastrar a disponibilidade.";
            $tipoMensagem = "erro";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cadastrar disponibilidade - Conexão Musical</title>

    <link
        rel="stylesheet"
        href="../../public/css/disponibilidade.css"
    >

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

                <span class="tipo-usuario">

                    <?= $_SESSION["tipo_usuario"] === "musico" ? "Músico" : "Cantor" ?>

                </span>

            </div>


            <a
                href="../usuario/logout.php"
                class="botao-sair"
            >
                SAIR
            </a>

        </div>


    </header>


    <main class="conteudo">


        <a
            href="../../public/index.php"
            class="voltar"
        >
            ← Voltar
        </a>


        <div class="cabecalho-pagina">

            <h1>
                Cadastrar disponibilidade
            </h1>

            <p class="subtitulo">
                Informe a data e o horário em que você estará disponível para participar de shows.
            </p>

        </div>


        <?php if (!empty($mensagem)): ?>

            <div class="mensagem <?= $tipoMensagem ?>">

                <?= htmlspecialchars($mensagem) ?>

            </div>

        <?php endif; ?>


        <form
            method="POST"
            class="formulario"
        >


            <div class="campo">

                <label for="data">
                    Data*
                </label>

                <input
                    type="date"
                    id="data"
                    name="data"
                    value="<?= htmlspecialchars($data) ?>"
                    min="<?= date("Y-m-d") ?>"
                    required
                >

            </div>


            <div class="linha-campos">


                <div class="campo">

                    <label for="hora_inicio">
                        Horário de início*
                    </label>

                    <input
                        type="time"
                        id="hora_inicio"
                        name="hora_inicio"
                        value="<?= htmlspecialchars($hora_inicio) ?>"
                        required
                    >

                </div>


                <div class="campo">

                    <label for="hora_fim">
                        Horário de término*
                    </label>

                    <input
                        type="time"
                        id="hora_fim"
                        name="hora_fim"
                        value="<?= htmlspecialchars($hora_fim) ?>"
                        required
                    >

                </div>


            </div>


            <div class="botoes">


                <button
                    type="reset"
                    class="botao cancelar"
                >
                    CANCELAR
                </button>


                <button
                    type="submit"
                    class="botao cadastrar"
                >
                    CADASTRAR
                </button>


            </div>


        </form>


    </main>


    <footer>

        © 2026 CONEXÃO MUSICAL

    </footer>


</body>

</html>