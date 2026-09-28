# database/conexao.py
# Responsável por abrir e fechar conexões com o banco de dados MySQL.

import mysql.connector
from mysql.connector import Error

from config import DB_CONFIG


def obter_conexao():
    """
    Cria e retorna uma nova conexão com o banco de dados MySQL,
    de acordo com as configurações definidas em config.py.

    Retorna None caso a conexão não possa ser estabelecida, para que
    as camadas superiores (controllers/views) possam tratar o erro
    e exibir uma mensagem adequada ao usuário.
    """
    try:
        conexao = mysql.connector.connect(
            host=DB_CONFIG["host"],
            user=DB_CONFIG["user"],
            password=DB_CONFIG["password"],
            database=DB_CONFIG["database"],
        )
        return conexao
    except Error as erro:
        print(f"[ERRO] Não foi possível conectar ao banco de dados: {erro}")
        return None
