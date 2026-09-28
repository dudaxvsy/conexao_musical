<?php

session_start();

$mensagem = "";
$tipoMensagem = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    require_once __DIR__ . "/../../controllers/UsuarioController.php";

    $email = trim($_POST["email"] ?? "");
    $senha = $_POST["senha"] ?? "";

    if (empty($email) || empty($senha)) {

        $mensagem = "Preencha todos os campos.";
        $tipoMensagem = "erro";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $mensagem = "Informe um e-mail válido.";
        $tipoMensagem = "erro";

    } else {

        try {

            $controller = new UsuarioController();

            $usuario = $controller->autenticar($email, $senha);

            if ($usuario !== null) {

                $_SESSION["id_usuario"] = $usuario->getIdUsuario();
                $_SESSION["nome_usuario"] = $usuario->getNome();
                $_SESSION["email_usuario"] = $usuario->getEmail();
                $_SESSION["tipo_usuario"] = $usuario->getTipo();

                header("Location: ../../public/index.php");
                exit;

            } else {

                $mensagem = "E-mail ou senha incorretos.";
                $tipoMensagem = "erro";
            }

        } catch (Exception $e) {

            $mensagem = "Erro ao realizar o login.";
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

    <title>Login - Conexão Musical</title>

    <link rel="stylesheet" href="../../public/css/login.css">

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

        <div class="acesso-cadastro">

            <span>Ainda não possui uma conta?</span>

            <a href="cadastro.php">
                Criar conta
            </a>

        </div>

    </header>


    <main class="conteudo">

        <div class="cabecalho-pagina">

            <h1>Entrar</h1>

            <p class="subtitulo">
                Entre na sua conta para acessar o Conexão Musical.
            </p>

        </div>


        <?php if (!empty($mensagem)): ?>

            <div class="mensagem <?= $tipoMensagem ?>">

                <?= htmlspecialchars($mensagem) ?>

            </div>

        <?php endif; ?>


        <form method="POST" class="formulario">

            <div class="campo">

                <label for="email">E-mail*</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Digite seu e-mail"
                    value="<?= htmlspecialchars($_POST["email"] ?? "") ?>"
                    required
                >

            </div>


            <div class="campo">

                <label for="senha">Senha*</label>

                <input
                    type="password"
                    id="senha"
                    name="senha"
                    placeholder="Digite sua senha"
                    required
                >

            </div>


            <div class="botoes">

                <button
                    type="submit"
                    class="botao entrar"
                >
                    ENTRAR
                </button>

            </div>

        </form>

    </main>


    <footer>

        © 2026 CONEXÃO MUSICAL

    </footer>

</body>

</html>