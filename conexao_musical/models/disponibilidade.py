from dataclasses import dataclass
from datetime import date
from typing import Optional


@dataclass
class Disponibilidade:
    id_disponibilidade: Optional[int]
    id_usuario: int
    data: date
    hora_inicio: str
    hora_fim: str