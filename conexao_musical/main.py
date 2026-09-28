# main.py

import tkinter as tk

from views.login import TelaLogin
from views.cadastro import TelaCadastro
from views.disponibilidade import TelaDisponibilidade

COR_FUNDO = "#F4F6FB"

COR_PRIMARIA = "#6C4DF6"
COR_SECUNDARIA = "#4C8DFF"

COR_TEXTO = "#1F2333"
COR_TEXTO_SECUNDARIO = "#70758F"

COR_BORDA = "#D9DDF0"


class App(tk.Tk):

    def __init__(self):
        super().__init__()

        self.title("🎵 Conexão Musical")
        self.geometry("1100x700")
        self.minsize(900, 650)

        self.configure(bg=COR_FUNDO)

        self.container = tk.Frame(
            self,
            bg=COR_FUNDO
        )

        self.container.pack(
            fill="both",
            expand=True
        )

        self.tela_atual = None
        self.usuario_logado = None

        self.mostrar_login()

    def trocar_tela(self, tela):

        if self.tela_atual:
            self.tela_atual.destroy()

        self.tela_atual = tela

        self.tela_atual.pack(
            fill="both",
            expand=True
        )

    def mostrar_login(self):

        tela = TelaLogin(
            self.container,
            ir_para_cadastro=self.mostrar_cadastro,
            ao_logar=self.mostrar_boas_vindas
        )

        self.trocar_tela(tela)

    def mostrar_cadastro(self):

        tela = TelaCadastro(
            self.container,
            ir_para_login=self.mostrar_login
        )

        self.trocar_tela(tela)

    def mostrar_disponibilidade(self):
    
        tela = TelaDisponibilidade(
            self.container,
            self.usuario_logado,
            self.mostrar_boas_vindas
    )

        self.trocar_tela(tela)

    def mostrar_boas_vindas(self, usuario):

        self.usuario_logado = usuario

        tela = tk.Frame(
            self.container,
            bg=COR_FUNDO
        )

        # ==========================
        # CABEÇALHO
        # ==========================

        header = tk.Frame(
            tela,
            bg=COR_PRIMARIA,
            height=90
        )

        header.pack(fill="x")
        header.pack_propagate(False)

        tk.Label(
            header,
            text="🎵 Conexão Musical",
            font=("Segoe UI", 24, "bold"),
            fg="white",
            bg=COR_PRIMARIA
        ).pack(
            side="left",
            padx=25,
            pady=20
        )

        tk.Label(
            header,
            text=f"Olá, {usuario.nome.split()[0]}",
            font=("Segoe UI", 12),
            fg="white",
            bg=COR_PRIMARIA
        ).pack(
            side="right",
            padx=25
        )

        # ==========================
        # CONTEÚDO
        # ==========================

        conteudo = tk.Frame(
            tela,
            bg=COR_FUNDO
        )

        conteudo.pack(
            expand=True,
            fill="both",
            padx=30,
            pady=30
        )

        tk.Label(
            conteudo,
            text="Dashboard",
            font=("Segoe UI", 28, "bold"),
            bg=COR_FUNDO,
            fg=COR_TEXTO
        ).pack(anchor="w")

        tk.Label(
            conteudo,
            text="Gerencie sua carreira musical",
            font=("Segoe UI", 11),
            bg=COR_FUNDO,
            fg=COR_TEXTO_SECUNDARIO
        ).pack(anchor="w", pady=(0, 25))

        cards = tk.Frame(
            conteudo,
            bg=COR_FUNDO
        )

        cards.pack()

        # ==========================
        # CARD DISPONIBILIDADE
        # ==========================

        card_disp = tk.Frame(
            cards,
            bg="white",
            width=240,
            height=180,
            highlightbackground=COR_BORDA,
            highlightthickness=1,
            cursor="hand2"
        )

        card_disp.grid(
            row=0,
            column=0,
            padx=15
        )

        card_disp.pack_propagate(False)

        lbl_disp_icon = tk.Label(
            card_disp,
            text="📅",
            font=("Segoe UI", 42),
            bg="white",
            cursor="hand2"
        )

        lbl_disp_icon.pack(
            pady=(20, 5)
        )

        lbl_disp_titulo = tk.Label(
            card_disp,
            text="Disponibilidade",
            font=("Segoe UI", 13, "bold"),
            bg="white",
            cursor="hand2"
        )

        lbl_disp_titulo.pack()

        lbl_disp_desc = tk.Label(
            card_disp,
            text="Gerencie seus horários",
            bg="white",
            fg=COR_TEXTO_SECUNDARIO,
            cursor="hand2"
        )

        lbl_disp_desc.pack()

        # Card inteiro clicável

        card_disp.bind(
            "<Button-1>",
            lambda e: self.mostrar_disponibilidade()
        )

        lbl_disp_icon.bind(
            "<Button-1>",
            lambda e: self.mostrar_disponibilidade()
        )

        lbl_disp_titulo.bind(
            "<Button-1>",
            lambda e: self.mostrar_disponibilidade()
        )

        lbl_disp_desc.bind(
            "<Button-1>",
            lambda e: self.mostrar_disponibilidade()
        )

        # ==========================
        # CARD SHOWS
        # ==========================

        card_show = tk.Frame(
            cards,
            bg="white",
            width=240,
            height=180,
            highlightbackground=COR_BORDA,
            highlightthickness=1
        )

        card_show.grid(
            row=0,
            column=1,
            padx=15
        )

        card_show.pack_propagate(False)

        tk.Label(
            card_show,
            text="🎤",
            font=("Segoe UI", 42),
            bg="white"
        ).pack(
            pady=(20, 5)
        )

        tk.Label(
            card_show,
            text="Shows",
            font=("Segoe UI", 13, "bold"),
            bg="white"
        ).pack()

        tk.Label(
            card_show,
            text="Em desenvolvimento",
            fg=COR_TEXTO_SECUNDARIO,
            bg="white"
        ).pack()

        # ==========================
        # CARD PARTICIPAÇÕES
        # ==========================

        card_part = tk.Frame(
            cards,
            bg="white",
            width=240,
            height=180,
            highlightbackground=COR_BORDA,
            highlightthickness=1
        )

        card_part.grid(
            row=0,
            column=2,
            padx=15
        )

        card_part.pack_propagate(False)

        tk.Label(
            card_part,
            text="🎼",
            font=("Segoe UI", 42),
            bg="white"
        ).pack(
            pady=(20, 5)
        )

        tk.Label(
            card_part,
            text="Participações",
            font=("Segoe UI", 13, "bold"),
            bg="white"
        ).pack()

        tk.Label(
            card_part,
            text="Em desenvolvimento",
            fg=COR_TEXTO_SECUNDARIO,
            bg="white"
        ).pack()

        # ==========================
        # SAIR
        # ==========================

        tk.Button(
            conteudo,
            text="Sair",
            bg="#E74C3C",
            fg="white",
            relief="flat",
            cursor="hand2",
            font=("Segoe UI", 11, "bold"),
            width=18,
            pady=8,
            command=self.mostrar_login
        ).pack(
            pady=40
        )

        self.trocar_tela(tela)


if __name__ == "__main__":
    app = App()
    app.mainloop()