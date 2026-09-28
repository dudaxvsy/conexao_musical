<?php

session_start();

if (!isset($_SESSION["id_usuario"])) {
    header("Location: ../views/usuario/login.php");
    exit;
}

$nomeUsuario = $_SESSION["nome_usuario"];
$tipoUsuario = $_SESSION["tipo_usuario"];

require_once __DIR__ . "/../controllers/PerfilController.php";

$perfilController = new PerfilController();
$perfilUsuario = $perfilController->buscarPorUsuario((int) $_SESSION["id_usuario"]);
$fotoPerfil = $perfilUsuario ? $perfilUsuario->getFoto() : "";

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Início - Conexão Musical</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            min-height: 100vh;
            background: #FAF7FC;
            color: #5D5270;
            display: flex;
            flex-direction: column;
        }

        .topo {
            width: 100%;
            height: 88px;
            background: #CBBBF7;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 55px;
            flex-shrink: 0;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            color: white;
            font-weight: bold;
            font-size: 27px;
            line-height: 25px;
        }

        .logo > div {
            display: flex;
            flex-direction: column;
        }

        .icone-musical {
            font-size: 50px;
            line-height: 1;
        }

        .usuario {
            display: flex;
            align-items: center;
            gap: 15px;
            color: white;
        }

        .foto-usuario {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, 0.9);
            background: #E9E1F7;
            flex-shrink: 0;
        }

        .foto-usuario-placeholder {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.9);
            background: #E9E1F7;
            color: #A996E8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: bold;
            flex-shrink: 0;
        }

        .informacoes-usuario {
            display: flex;
            flex-direction: column;
            text-align: right;
        }

        .nome-usuario {
            font-size: 16px;
            font-weight: bold;
        }

        .tipo-usuario {
            font-size: 13px;
            margin-top: 4px;
            opacity: 0.85;
        }

        .botao-conta {
            color: white;
            text-decoration: none;
            font-size: 13px;
            font-weight: bold;
            border: 1px solid rgba(255, 255, 255, 0.85);
            padding: 9px 18px;
            border-radius: 22px;
            transition: 0.2s;
        }

        .botao-conta:hover {
            background: white;
            color: #A996E8;
        }

        .botao-sair {
            color: white;
            text-decoration: none;
            font-size: 13px;
            font-weight: bold;
            border: 1px solid rgba(255, 255, 255, 0.85);
            padding: 9px 21px;
            border-radius: 22px;
            transition: 0.2s;
        }

        .botao-sair:hover {
            background: white;
            color: #A996E8;
        }

        .conteudo {
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
            padding: 85px 35px 85px;
            flex: 1;
        }

        .cabecalho {
            margin-bottom: 48px;
        }

        .cabecalho h1 {
            color: #5E4A85;
            font-size: 32px;
            margin-bottom: 9px;
        }

        .cabecalho p {
            color: #9A8EAA;
            font-size: 16px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
        }

        .card {
            width: 100%;
            min-height: 178px;
            background: white;
            border: 1px solid #DDD5E8;
            border-radius: 14px;
            padding: 32px 34px;
            transition: 0.2s;
        }

        .card.clicavel {
            cursor: pointer;
        }

        .card.clicavel:hover {
            border-color: #A996E8;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(94, 74, 133, 0.06);
        }

        .card-link {
            display: block;
            color: inherit;
            text-decoration: none;
        }

        .icone-card {
            font-size: 29px;
            color: #A996E8;
            margin-bottom: 17px;
            line-height: 1;
        }

        .card h2 {
            color: #5E4A85;
            font-size: 20px;
            margin-bottom: 10px;
        }

        .card p {
            color: #9A8EAA;
            font-size: 15px;
            line-height: 1.5;
            max-width: 470px;
        }

        footer {
            width: 100%;
            height: 40px;
            background: #CBBBF7;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
            font-weight: bold;
            flex-shrink: 0;
        }

        @media (max-width: 750px) {
            .topo {
                padding: 0 25px;
            }

            .conteudo {
                padding: 60px 25px 70px;
            }

            .cards {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 500px) {
            .topo {
                height: 80px;
            }

            .logo {
                font-size: 20px;
                line-height: 19px;
            }

            .icone-musical {
                font-size: 38px;
            }

            .usuario {
                gap: 8px;
            }

            .foto-usuario,
            .foto-usuario-placeholder {
                width: 38px;
                height: 38px;
            }

            .foto-usuario-placeholder {
                font-size: 18px;
            }

            .nome-usuario {
                font-size: 12px;
            }

            .tipo-usuario {
                font-size: 10px;
            }

            .botao-conta,
            .botao-sair {
                padding: 7px 12px;
                font-size: 10px;
            }

            .conteudo {
                padding: 50px 20px 60px;
            }

            .cabecalho h1 {
                font-size: 27px;
            }

            .cabecalho p {
                font-size: 14px;
            }

            .card {
                padding: 27px;
            }
        }
    </style>
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
        <?php if (!empty($fotoPerfil)): ?>
            <img
                src="../public/uploads/perfis/<?= htmlspecialchars($fotoPerfil) ?>"
                alt="Foto de perfil de <?= htmlspecialchars($nomeUsuario) ?>"
                class="foto-usuario"
            >
        <?php else: ?>
            <div class="foto-usuario-placeholder" aria-label="Sem foto de perfil">♫</div>
        <?php endif; ?>

        <div class="informacoes-usuario">
            <span class="nome-usuario">
                <?= htmlspecialchars($nomeUsuario) ?>
            </span>
            <span class="tipo-usuario">
                <?= $tipoUsuario === "musico" ? "Músico" : "Cantor" ?>
            </span>
        </div>

        <a href="../views/usuario/dados_conta.php" class="botao-conta">CONTA</a>
        <a href="../views/usuario/logout.php" class="botao-sair">SAIR</a>
    </div>
</header>

<main class="conteudo">
    <div class="cabecalho">
        <h1>Olá, <?= htmlspecialchars($nomeUsuario) ?>!</h1>
        <p>Bem-vindo ao Conexão Musical.</p>
    </div>

    <section class="cards">

        <?php if ($tipoUsuario === "musico"): ?>

            <a href="../views/disponibilidade/cadastro.php" class="card-link">
                <div class="card clicavel">
                    <div class="icone-card">♫</div>
                    <h2>Minha disponibilidade</h2>
                    <p>
                        Cadastre e consulte os horários em que você está disponível para participar de shows.
                    </p>
                </div>
            </a>

            <a href="../views/participacao/minhas.php" class="card-link">
                <div class="card clicavel">
                    <div class="icone-card">♬</div>
                    <h2>Minhas participações</h2>
                    <p>
                        Consulte os shows dos quais você participa e acompanhe seus compromissos.
                    </p>
                </div>
            </a>

            <a href="../views/show/buscar.php" class="card-link">
                <div class="card clicavel">
                    <div class="icone-card">♪</div>
                    <h2>Buscar shows</h2>
                    <p>
                        Encontre shows em que você pode se oferecer para participar.
                    </p>
                </div>
            </a>

            <a href="../views/perfil/index.php" class="card-link">
                <div class="card clicavel">
                    <div class="icone-card">♩</div>
                    <h2>Meu perfil</h2>
                    <p>
                        Consulte e mantenha atualizadas suas informações para facilitar o contato com cantores.
                    </p>
                </div>
            </a>

        <?php else: ?>

            <a href="../views/show/meus.php" class="card-link">
                <div class="card clicavel">
                    <div class="icone-card">♪</div>
                    <h2>Meus shows</h2>
                    <p>
                        Cadastre e consulte seus shows para organizar suas apresentações.
                    </p>
                </div>
            </a>

            <a href="../views/participacao/minhas.php" class="card-link">
                <div class="card clicavel">
                    <div class="icone-card">♬</div>
                    <h2>Solicitações</h2>
                    <p>
                        Veja os músicos que se ofereceram para participar dos seus shows.
                    </p>
                </div>
            </a>

            <a href="../views/usuario/buscar_musicos.php" class="card-link">
                <div class="card clicavel">
                    <div class="icone-card">⌕</div>
                    <h2>Buscar músicos</h2>
                    <p>
                        Encontre músicos disponíveis para seus shows e entre em contato com eles.
                    </p>
                </div>
            </a>

            <a href="../views/show/buscar.php" class="card-link">
                <div class="card clicavel">
                    <div class="icone-card">♪</div>
                    <h2>Buscar shows</h2>
                    <p>
                        Encontre shows de outros cantores em que você pode se oferecer para participar.
                    </p>
                </div>
            </a>

            <a href="../views/perfil/index.php" class="card-link">
                <div class="card clicavel">
                    <div class="icone-card">♩</div>
                    <h2>Meu perfil</h2>
                    <p>
                        Consulte e mantenha atualizadas suas informações para facilitar o contato com outros profissionais.
                    </p>
                </div>
            </a>

        <?php endif; ?>

    </section>
</main>

<footer>
    © 2026 CONEXÃO MUSICAL
</footer>

</body>
</html>
