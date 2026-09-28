# views/login.py

import tkinter as tk
from tkinter import ttk, messagebox

from controllers.usuario_controller import UsuarioController


class TelaLogin(tk.Frame):

    COR_FUNDO = "#F4F6FB"
    COR_CARD = "#FFFFFF"

    COR_PRIMARIA = "#6C4DF6"
    COR_SECUNDARIA = "#4C8DFF"

    COR_TEXTO = "#1F2333"
    COR_TEXTO_SECUNDARIO = "#70758F"

    COR_BORDA = "#D9DDF0"
    COR_ERRO = "#E74C3C"

    def __init__(self, master, ir_para_cadastro, ao_logar):
        super().__init__(master, bg=self.COR_FUNDO)

        self.ir_para_cadastro = ir_para_cadastro
        self.ao_logar = ao_logar

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

        # Ícone musical

        tk.Label(
            container,
            text="🎵",
            font=("Segoe UI", 54),
            fg=self.COR_PRIMARIA,
            bg=self.COR_FUNDO
        ).pack(pady=(0, 10))

        # Título

        tk.Label(
            container,
            text="Conexão Musical",
            font=("Segoe UI", 28, "bold"),
            fg=self.COR_PRIMARIA,
            bg=self.COR_FUNDO
        ).pack()

        # Subtítulo

        tk.Label(
            container,
            text="Conectando cantores e músicos",
            font=("Segoe UI", 11),
            fg=self.COR_TEXTO_SECUNDARIO,
            bg=self.COR_FUNDO
        ).pack(pady=(4, 25))

        # Card

        card = tk.Frame(
            container,
            bg=self.COR_CARD,
            padx=40,
            pady=35,
            highlightbackground=self.COR_BORDA,
            highlightthickness=1
        )
        card.pack()

        tk.Label(
            card,
            text="Entrar",
            font=("Segoe UI", 18, "bold"),
            fg=self.COR_TEXTO,
            bg=self.COR_CARD
        ).grid(
            row=0,
            column=0,
            sticky="w",
            pady=(0, 20)
        )

        # Email

        tk.Label(
            card,
            text="E-mail",
            font=("Segoe UI", 10, "bold"),
            fg=self.COR_TEXTO,
            bg=self.COR_CARD
        ).grid(row=1, column=0, sticky="w")

        self.entry_email = ttk.Entry(
            card,
            width=42,
            font=("Segoe UI", 11)
        )

        self.entry_email.grid(
            row=2,
            column=0,
            pady=(5, 15)
        )

        # Senha

        tk.Label(
            card,
            text="Senha",
            font=("Segoe UI", 10, "bold"),
            fg=self.COR_TEXTO,
            bg=self.COR_CARD
        ).grid(row=3, column=0, sticky="w")

        self.entry_senha = ttk.Entry(
            card,
            width=42,
            font=("Segoe UI", 11),
            show="•"
        )

        self.entry_senha.grid(
            row=4,
            column=0,
            pady=(5, 10)
        )

        # Mensagem erro

        self.label_erro = tk.Label(
            card,
            text="",
            font=("Segoe UI", 9),
            fg=self.COR_ERRO,
            bg=self.COR_CARD,
            wraplength=320,
            justify="left"
        )

        self.label_erro.grid(
            row=5,
            column=0,
            sticky="w",
            pady=(0, 15)
        )

        # Botão

        btn_entrar = tk.Button(
            card,
            text="Entrar na Plataforma",
            font=("Segoe UI", 11, "bold"),
            bg=self.COR_PRIMARIA,
            fg="white",
            activebackground=self.COR_SECUNDARIA,
            activeforeground="white",
            relief="flat",
            bd=0,
            cursor="hand2",
            width=30,
            pady=12,
            command=self._tentar_login
        )

        btn_entrar.grid(
            row=6,
            column=0,
            pady=(0, 15)
        )

        # Link cadastro

        link_cadastro = tk.Label(
            card,
            text="Ainda não possui uma conta? Cadastre-se",
            font=("Segoe UI", 10, "underline"),
            fg=self.COR_SECUNDARIA,
            bg=self.COR_CARD,
            cursor="hand2"
        )

        link_cadastro.grid(
            row=7,
            column=0
        )

        link_cadastro.bind(
            "<Button-1>",
            lambda e: self.ir_para_cadastro()
        )

        self.entry_senha.bind(
            "<Return>",
            lambda e: self._tentar_login()
        )

    # =====================================

    def _tentar_login(self):

        email = self.entry_email.get().strip()
        senha = self.entry_senha.get()

        if not email or not senha:
            self._mostrar_erro(
                "Preencha e-mail e senha."
            )
            return

        try:
            usuario = UsuarioController.validar_login(
                email,
                senha
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

        if usuario is None:
            self._mostrar_erro(
                "E-mail ou senha incorretos."
            )
            return

        self._mostrar_erro("")
        self.ao_logar(usuario)

    def _mostrar_erro(self, mensagem):
        self.label_erro.config(text=mensagem)

    def limpar_campos(self):
        self.entry_email.delete(0, tk.END)
        self.entry_senha.delete(0, tk.END)
        self._mostrar_erro("")