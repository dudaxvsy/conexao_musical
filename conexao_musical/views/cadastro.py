# views/cadastro.py

import re
import tkinter as tk
from tkinter import ttk, messagebox
from datetime import datetime

from controllers.usuario_controller import UsuarioController

REGEX_EMAIL = re.compile(r"^[^@\s]+@[^@\s]+\.[^@\s]+$")

TIPOS_USUARIO = [
    "Cantor",
    "Músico",
    "Cantor e Músico"
]


class TelaCadastro(tk.Frame):

    COR_FUNDO = "#F4F6FB"
    COR_CARD = "#FFFFFF"

    COR_PRIMARIA = "#6C4DF6"
    COR_SECUNDARIA = "#4C8DFF"

    COR_TEXTO = "#1F2333"
    COR_TEXTO_SECUNDARIO = "#70758F"

    COR_BORDA = "#D9DDF0"

    COR_ERRO = "#E74C3C"
    COR_SUCESSO = "#2ECC71"

    def __init__(self, master, ir_para_login):
        super().__init__(master, bg=self.COR_FUNDO)

        self.ir_para_login = ir_para_login

        self._montar_interface()

    def _montar_interface(self):

        style = ttk.Style()

        try:
            style.theme_use("clam")
        except:
            pass

        container = tk.Frame(
            self,
            bg=self.COR_FUNDO
        )

        container.place(
            relx=0.5,
            rely=0.5,
            anchor="center"
        )

        tk.Label(
            container,
            text="🎵",
            font=("Segoe UI", 50),
            fg=self.COR_PRIMARIA,
            bg=self.COR_FUNDO
        ).pack(pady=(0, 8))

        tk.Label(
            container,
            text="Conexão Musical",
            font=("Segoe UI", 26, "bold"),
            fg=self.COR_PRIMARIA,
            bg=self.COR_FUNDO
        ).pack()

        tk.Label(
            container,
            text="Crie sua conta e participe da comunidade musical",
            font=("Segoe UI", 11),
            fg=self.COR_TEXTO_SECUNDARIO,
            bg=self.COR_FUNDO
        ).pack(pady=(4, 20))

        card = tk.Frame(
            container,
            bg=self.COR_CARD,
            padx=40,
            pady=30,
            highlightbackground=self.COR_BORDA,
            highlightthickness=1
        )

        card.pack()

        tk.Label(
            card,
            text="Cadastro",
            font=("Segoe UI", 18, "bold"),
            fg=self.COR_TEXTO,
            bg=self.COR_CARD
        ).grid(
            row=0,
            column=0,
            sticky="w",
            pady=(0, 20)
        )

        campos = [
            ("Nome Completo", "entry_nome", {}),
            ("E-mail", "entry_email", {}),
            ("Senha", "entry_senha", {"show": "•"}),
            ("Confirmar Senha", "entry_confirmar_senha", {"show": "•"}),
            ("Data de Nascimento (DD/MM/AAAA)", "entry_nascimento", {}),
            ("Telefone", "entry_telefone", {}),
        ]

        linha = 1

        for rotulo, atributo, opcoes in campos:

            tk.Label(
                card,
                text=rotulo,
                font=("Segoe UI", 10, "bold"),
                fg=self.COR_TEXTO,
                bg=self.COR_CARD
            ).grid(
                row=linha,
                column=0,
                sticky="w"
            )

            entry = ttk.Entry(
                card,
                width=42,
                font=("Segoe UI", 11),
                **opcoes
            )

            entry.grid(
                row=linha + 1,
                column=0,
                pady=(5, 12)
            )

            setattr(self, atributo, entry)

            linha += 2

        tk.Label(
            card,
            text="Tipo de Usuário",
            font=("Segoe UI", 10, "bold"),
            fg=self.COR_TEXTO,
            bg=self.COR_CARD
        ).grid(
            row=linha,
            column=0,
            sticky="w"
        )

        self.combo_tipo = ttk.Combobox(
            card,
            values=TIPOS_USUARIO,
            state="readonly",
            width=39,
            font=("Segoe UI", 11)
        )

        self.combo_tipo.grid(
            row=linha + 1,
            column=0,
            pady=(5, 10)
        )

        linha += 2

        self.label_mensagem = tk.Label(
            card,
            text="",
            font=("Segoe UI", 9),
            fg=self.COR_ERRO,
            bg=self.COR_CARD,
            wraplength=360,
            justify="left"
        )

        self.label_mensagem.grid(
            row=linha,
            column=0,
            sticky="w",
            pady=(0, 12)
        )

        linha += 1

        btn_cadastrar = tk.Button(
            card,
            text="Criar Conta",
            font=("Segoe UI", 11, "bold"),
            bg=self.COR_PRIMARIA,
            fg="white",
            activebackground=self.COR_SECUNDARIA,
            activeforeground="white",
            relief="flat",
            bd=0,
            cursor="hand2",
            width=32,
            pady=12,
            command=self._tentar_cadastrar
        )

        btn_cadastrar.grid(
            row=linha,
            column=0,
            pady=(5, 15)
        )

        linha += 1

        link_login = tk.Label(
            card,
            text="Já possui conta? Fazer login",
            font=("Segoe UI", 10, "underline"),
            fg=self.COR_SECUNDARIA,
            bg=self.COR_CARD,
            cursor="hand2"
        )

        link_login.grid(
            row=linha,
            column=0
        )

        link_login.bind(
            "<Button-1>",
            lambda e: self.ir_para_login()
        )

    # ==================================
    # VALIDAÇÃO E CADASTRO
    # ==================================

    def _tentar_cadastrar(self):

        nome = self.entry_nome.get().strip()
        email = self.entry_email.get().strip().lower()
        senha = self.entry_senha.get()
        confirmar_senha = self.entry_confirmar_senha.get()
        data_nascimento_texto = self.entry_nascimento.get().strip()
        telefone = self.entry_telefone.get().strip()
        tipo_usuario = self.combo_tipo.get()

        if not all([
            nome,
            email,
            senha,
            confirmar_senha,
            data_nascimento_texto,
            telefone,
            tipo_usuario
        ]):
            self._mostrar_mensagem(
                "Preencha todos os campos obrigatórios.",
                erro=True
            )
            return

        if not REGEX_EMAIL.match(email):
            self._mostrar_mensagem(
                "Informe um e-mail válido.",
                erro=True
            )
            return

        if len(senha) < 6:
            self._mostrar_mensagem(
                "A senha deve ter pelo menos 6 caracteres.",
                erro=True
            )
            return

        if senha != confirmar_senha:
            self._mostrar_mensagem(
                "As senhas informadas não coincidem.",
                erro=True
            )
            return

        try:
            data_nascimento = datetime.strptime(
                data_nascimento_texto,
                "%d/%m/%Y"
            ).date()

        except ValueError:
            self._mostrar_mensagem(
                "Data inválida. Use DD/MM/AAAA.",
                erro=True
            )
            return

        if data_nascimento >= datetime.now().date():
            self._mostrar_mensagem(
                "A data de nascimento deve ser anterior à data atual.",
                erro=True
            )
            return

        telefone_numeros = re.sub(r"\D", "", telefone)

        if len(telefone_numeros) < 8 or len(telefone_numeros) > 15:
            self._mostrar_mensagem(
                "Informe um telefone válido.",
                erro=True
            )
            return

        try:

            sucesso, mensagem = UsuarioController.cadastrar_usuario(
                nome=nome,
                email=email,
                senha=senha,
                data_nascimento=data_nascimento,
                telefone=telefone,
                tipo_usuario=tipo_usuario,
            )

        except ConnectionError as erro:
            messagebox.showerror(
                "Erro de conexão",
                str(erro)
            )
            return

        except Exception as erro:
            messagebox.showerror(
                "Erro",
                f"Ocorreu um erro inesperado: {erro}"
            )
            return

        if not sucesso:
            self._mostrar_mensagem(
                mensagem,
                erro=True
            )
            return

        self._mostrar_mensagem(
            mensagem,
            erro=False
        )

        messagebox.showinfo(
            "Sucesso",
            mensagem
        )

        self._limpar_campos()

        self.ir_para_login()

    def _mostrar_mensagem(self, texto, erro=True):

        self.label_mensagem.config(
            text=texto,
            fg=self.COR_ERRO if erro else self.COR_SUCESSO
        )

    def _limpar_campos(self):

        for atributo in [
            "entry_nome",
            "entry_email",
            "entry_senha",
            "entry_confirmar_senha",
            "entry_nascimento",
            "entry_telefone"
        ]:
            getattr(self, atributo).delete(0, tk.END)

        self.combo_tipo.set("")

        self._mostrar_mensagem("")