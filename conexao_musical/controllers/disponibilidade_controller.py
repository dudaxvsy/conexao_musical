from database.conexao import obter_conexao


class DisponibilidadeController:

    @staticmethod
    def cadastrar_disponibilidade(
        id_usuario,
        data,
        hora_inicio,
        hora_fim
    ):

        if not data or not hora_inicio or not hora_fim:
            return False, "Preencha todos os campos."

        if hora_fim <= hora_inicio:
            return False, "O horário final deve ser maior que o inicial."

        conexao = obter_conexao()

        if conexao is None:
            return False, "Não foi possível conectar ao banco."

        try:
            cursor = conexao.cursor()

            cursor.execute("""
                SELECT id_disponibilidade
                FROM disponibilidades
                WHERE id_usuario = %s
                AND data = %s
                AND hora_inicio = %s
                AND hora_fim = %s
            """, (
                id_usuario,
                data,
                hora_inicio,
                hora_fim
            ))

            if cursor.fetchone():
                return False, "Essa disponibilidade já existe."

            cursor.execute("""
                INSERT INTO disponibilidades
                (
                    id_usuario,
                    data,
                    hora_inicio,
                    hora_fim
                )
                VALUES (%s, %s, %s, %s)
            """, (
                id_usuario,
                data,
                hora_inicio,
                hora_fim
            ))

            conexao.commit()

            return True, "Disponibilidade cadastrada com sucesso."

        except Exception as erro:

            conexao.rollback()

            return False, str(erro)

        finally:
            cursor.close()
            conexao.close()

    @staticmethod
    def listar_disponibilidades(id_usuario):

        conexao = obter_conexao()

        if conexao is None:
            return []

        try:

            cursor = conexao.cursor(dictionary=True)

            cursor.execute("""
                SELECT *
                FROM disponibilidades
                WHERE id_usuario = %s
                ORDER BY data, hora_inicio
            """, (id_usuario,))

            return cursor.fetchall()

        finally:
            cursor.close()
            conexao.close()

    @staticmethod
    def atualizar_disponibilidade(
        id_disponibilidade,
        id_usuario,
        data,
        hora_inicio,
        hora_fim
    ):
        """
        Atualiza uma disponibilidade já cadastrada (US07).

        Só permite a alteração se a disponibilidade pertencer ao
        id_usuario informado, evitando que um usuário edite
        registros de outra pessoa.
        """

        if not data or not hora_inicio or not hora_fim:
            return False, "Preencha todos os campos."

        if hora_fim <= hora_inicio:
            return False, "O horário final deve ser maior que o inicial."

        conexao = obter_conexao()

        if conexao is None:
            return False, "Não foi possível conectar ao banco."

        try:
            cursor = conexao.cursor()

            # Garante que a disponibilidade existe e pertence ao usuário logado
            cursor.execute("""
                SELECT id_disponibilidade
                FROM disponibilidades
                WHERE id_disponibilidade = %s
                AND id_usuario = %s
            """, (
                id_disponibilidade,
                id_usuario
            ))

            if not cursor.fetchone():
                return False, (
                    "Disponibilidade não encontrada ou você não tem "
                    "permissão para editá-la."
                )

            # Impede que a edição gere uma duplicidade com OUTRO registro
            cursor.execute("""
                SELECT id_disponibilidade
                FROM disponibilidades
                WHERE id_usuario = %s
                AND data = %s
                AND hora_inicio = %s
                AND hora_fim = %s
                AND id_disponibilidade != %s
            """, (
                id_usuario,
                data,
                hora_inicio,
                hora_fim,
                id_disponibilidade
            ))

            if cursor.fetchone():
                return False, "Essa disponibilidade já existe."

            cursor.execute("""
                UPDATE disponibilidades
                SET data = %s,
                    hora_inicio = %s,
                    hora_fim = %s
                WHERE id_disponibilidade = %s
                AND id_usuario = %s
            """, (
                data,
                hora_inicio,
                hora_fim,
                id_disponibilidade,
                id_usuario
            ))

            conexao.commit()

            return True, "Disponibilidade atualizada com sucesso."

        except Exception as erro:

            conexao.rollback()

            return False, str(erro)

        finally:
            cursor.close()
            conexao.close()

    @staticmethod
    def excluir_disponibilidade(id_disponibilidade):

        conexao = obter_conexao()

        if conexao is None:
            return False

        try:

            cursor = conexao.cursor()

            cursor.execute("""
                DELETE FROM disponibilidades
                WHERE id_disponibilidade = %s
            """, (id_disponibilidade,))

            conexao.commit()

            return True

        except:

            conexao.rollback()

            return False

        finally:
            cursor.close()
            conexao.close()