# models/usuario.py
# Representa a entidade Usuário do sistema Conexão Musical.

from dataclasses import dataclass
from datetime import date, datetime
from typing import Optional


@dataclass
class Usuario:
    id_usuario: Optional[int]
    nome: str
    email: str
    senha: str  # já vem como hash quando lido do banco
    data_nascimento: date
    telefone: str
    tipo_usuario: str
    data_cadastro: Optional[datetime] = None
