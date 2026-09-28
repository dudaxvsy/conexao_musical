# controllers/usuario_controller.py
# Contém as regras de negócio relacionadas ao cadastro e login de usuários.
# As views (telas) não acessam o banco diretamente: elas sempre passam
# pelo controller, que por sua vez usa database/conexao.py.

import bcrypt
from datetime import datetime

from database.conexao import obter_conexao
from models.usuario import Usuario


class UsuarioController:

    # ---------- Cadastro ----------

    @staticmethod
    def email_ja_cadastrado(email: str) -> bool:
        """Verifica se já existe um usuário cadastrado com o e-mail informado."""
        conexao = obter_conexao()
        if conexao is None:
            raise ConnectionError("Não foi possível conectar ao banco de dados.")

        try:
            cursor = conexao.cursor()
            cursor.execute("SELECT id_usuario FROM usuarios WHERE email = %s", (email,))
            resultado = cursor.fetchone()
            return resultado is not None
        finally:
            cursor.close()
            conexao.close()

    @staticmethod
    def cadastrar_usuario(nome, email, senha, data_nascimento, telefone, tipo_usuario):
        """
        Cadastra um novo usuário no banco de dados.
        A senha é transformada em hash (bcrypt) antes de ser armazenada.

        Retorna (True, "mensagem de sucesso") ou (False, "mensagem de erro").
        """
        if UsuarioController.email_ja_cadastrado(email):
            return False, "Este e-mail já está cadastrado."

        senha_hash = bcrypt.hashpw(senha.encode("utf-8"), bcrypt.gensalt())

        conexao = obter_conexao()
        if conexao is None:
            return False, "Não foi possível conectar ao banco de dados."

        try:
            cursor = conexao.cursor()
            sql = """
                INSERT INTO usuarios
                    (nome, email, senha, data_nascimento, telefone, tipo_usuario, data_cadastro)
                VALUES (%s, %s, %s, %s, %s, %s, %s)
            """
            valores = (
                nome,
                email,
                senha_hash.decode("utf-8"),
                data_nascimento,
                telefone,
                tipo_usuario,
                datetime.now(),
            )
            cursor.execute(sql, valores)
            conexao.commit()
            return True, "Cadastro realizado com sucesso!"
        except Exception as erro:
            conexao.rollback()
            return False, f"Erro ao cadastrar usuário: {erro}"
        finally:
            cursor.close()
            conexao.close()

    # ---------- Login ----------

    @staticmethod
    def validar_login(email: str, senha: str):
        """
        Verifica as credenciais informadas.

        Retorna um objeto Usuario em caso de sucesso, ou None caso o
        e-mail não exista ou a senha esteja incorreta.
        """
        conexao = obter_conexao()
        if conexao is None:
            raise ConnectionError("Não foi possível conectar ao banco de dados.")

        try:
            cursor = conexao.cursor(dictionary=True)
            cursor.execute("SELECT * FROM usuarios WHERE email = %s", (email,))
            registro = cursor.fetchone()

            if registro is None:
                return None

            senha_confere = bcrypt.checkpw(
                senha.encode("utf-8"), registro["senha"].encode("utf-8")
            )
            if not senha_confere:
                return None

            return Usuario(
                id_usuario=registro["id_usuario"],
                nome=registro["nome"],
                email=registro["email"],
                senha=registro["senha"],
                data_nascimento=registro["data_nascimento"],
                telefone=registro["telefone"],
                tipo_usuario=registro["tipo_usuario"],
                data_cadastro=registro["data_cadastro"],
            )
        finally:
            cursor.close()
            conexao.close()
