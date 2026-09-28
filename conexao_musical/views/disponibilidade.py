import tkinter as tk
from tkinter import ttk, messagebox

from tkcalendar import Calendar

from controllers.disponibilidade_controller import DisponibilidadeController


COR_FUNDO = "#F4F6FB"

COR_PRIMARIA = "#6C4DF6"
COR_SECUNDARIA = "#4C8DFF"

COR_TEXTO = "#1F2333"
COR_TEXTO_SECUNDARIO = "#70758F"

COR_BORDA = "#D9DDF0"


class TelaDisponibilidade(tk.Frame):

    def __init__(self, master, usuario, voltar_callback=None):
        super().__init__(master, bg=COR_FUNDO)

        self.usuario = usuario
        self.voltar_callback = voltar_callback

        self.pack(fill="both", expand=True)

        self.montar_tela()

        self.carregar_disponibilidades()

    def montar_tela(self):

        # HEADER FIXO -----------------------------------------------------

        header = tk.Frame(
            self,
            bg=COR_PRIMARIA,
            height=90
        )

        header.pack(fill="x")
        header.pack_propagate(False)

        tk.Button(
            header,
            text="← Voltar",
            bg="white",
            fg=COR_PRIMARIA,
            relief="flat",
            cursor="hand2",
            font=("Segoe UI", 10, "bold"),
            command=lambda: self.voltar_callback(self.usuario)
        ).pack(
            side="left",
            padx=20,
            pady=25
        )

        tk.Label(
            header,
            text="📅 Minha Agenda",
            font=("Segoe UI", 22, "bold"),
            fg="white",
            bg=COR_PRIMARIA
        ).pack(
            side="left",
            padx=20
        )

        # ÁREA ROLÁVEL (Canvas + Scrollbar) --------------------------------
        #
        # Estrutura:
        #   area_rolavel
        #     ├── self.canvas
        #     │     └── conteudo  (item do canvas via create_window,
        #     │                    NUNCA recebe pack/grid/place)
        #     │           └── miolo  (Frame comum, gerenciado por pack,
        #     │                       é aqui que os cards entram)
        #     └── scrollbar

        area_rolavel = tk.Frame(
            self,
            bg=COR_FUNDO
        )

        area_rolavel.pack(
            fill="both",
            expand=True
        )

        self.canvas = tk.Canvas(
            area_rolavel,
            bg=COR_FUNDO,
            highlightthickness=0
        )

        scrollbar = ttk.Scrollbar(
            area_rolavel,
            orient="vertical",
            command=self.canvas.yview
        )

        self.canvas.configure(
            yscrollcommand=scrollbar.set
        )

        self.canvas.pack(
            side="left",
            fill="both",
            expand=True
        )

        scrollbar.pack(
            side="right",
            fill="y"
        )

        # "conteudo" é o Frame que vira item do Canvas. Ele NÃO leva
        # pack/grid/place — quem o posiciona é o create_window abaixo.
        conteudo = tk.Frame(
            self.canvas,
            bg=COR_FUNDO
        )

        self.janela_conteudo = self.canvas.create_window(
            (0, 0),
            window=conteudo,
            anchor="nw"
        )

        # "miolo" é um Frame comum, filho de "conteudo", gerenciado por
        # pack normalmente — é nele que entram os cards e o padding
        # visual (padx/pady) que a tela já usava.
        miolo = tk.Frame(
            conteudo,
            bg=COR_FUNDO
        )

        miolo.pack(
            fill="both",
            expand=True,
            padx=30,
            pady=20
        )

        def atualizar_scrollregion(event=None):
            self.canvas.configure(
                scrollregion=self.canvas.bbox("all")
            )

        # Qualquer mudança de tamanho do conteúdo real (miolo crescendo
        # ou encolhendo ao adicionar/editar/excluir cards) precisa
        # recalcular a scrollregion do canvas.
        miolo.bind(
            "<Configure>",
            atualizar_scrollregion
        )

        def ajustar_largura_conteudo(event):
            # Faz o item do canvas acompanhar a largura do canvas,
            # evitando rolagem horizontal desnecessária.
            self.canvas.itemconfig(
                self.janela_conteudo,
                width=event.width
            )

        self.canvas.bind(
            "<Configure>",
            ajustar_largura_conteudo
        )

        # Rolagem com a roda do mouse (Windows/Mac usam <MouseWheel>,
        # Linux usa <Button-4>/<Button-5>). Só fica ativa enquanto o
        # mouse está sobre esta tela, para não afetar outras telas.
        def rolar_mouse(event):
            if event.num == 4:
                self.canvas.yview_scroll(-1, "units")
            elif event.num == 5:
                self.canvas.yview_scroll(1, "units")
            else:
                self.canvas.yview_scroll(
                    int(-1 * (event.delta / 120)),
                    "units"
                )

        def ativar_scroll_mouse(event):
            self.canvas.bind_all("<MouseWheel>", rolar_mouse)
            self.canvas.bind_all("<Button-4>", rolar_mouse)
            self.canvas.bind_all("<Button-5>", rolar_mouse)

        def desativar_scroll_mouse(event):
            self.canvas.unbind_all("<MouseWheel>")
            self.canvas.unbind_all("<Button-4>")
            self.canvas.unbind_all("<Button-5>")

        self.canvas.bind("<Enter>", ativar_scroll_mouse)
        self.canvas.bind("<Leave>", desativar_scroll_mouse)

        # A partir daqui, todo o conteúdo antigo da tela é criado dentro
        # de "miolo" (antes era criado direto em "self").

        # CARD CALENDÁRIO

        card_calendario = tk.Frame(
            miolo,
            bg="white",
            highlightbackground=COR_BORDA,
            highlightthickness=1
        )

        card_calendario.pack(
            fill="x",
            pady=(0, 20)
        )

        tk.Label(
            card_calendario,
            text="Selecione uma data",
            font=("Segoe UI", 14, "bold"),
            bg="white",
            fg=COR_TEXTO
        ).pack(pady=(15, 10))

        self.calendario = Calendar(
            card_calendario,
            selectmode="day",
            date_pattern="yyyy-mm-dd",
            background=COR_PRIMARIA,
            foreground="white",
            bordercolor=COR_PRIMARIA,
            headersbackground=COR_PRIMARIA,
            headersforeground="white",
            selectbackground=COR_SECUNDARIA,
            selectforeground="white",
        )

        self.calendario.pack(
            pady=(0, 20)
        )

        # CARD HORÁRIOS

        card_horario = tk.Frame(
            miolo,
            bg="white",
            highlightbackground=COR_BORDA,
            highlightthickness=1
        )

        card_horario.pack(
            fill="x",
            pady=(0, 20)
        )

        frame_horarios = tk.Frame(
            card_horario,
            bg="white"
        )

        frame_horarios.pack(
            pady=20
        )

        tk.Label(
            frame_horarios,
            text="Hora Inicial",
            bg="white",
            font=("Segoe UI", 10, "bold")
        ).grid(row=0, column=0)

        tk.Label(
            frame_horarios,
            text="Hora Final",
            bg="white",
            font=("Segoe UI", 10, "bold")
        ).grid(row=0, column=1)

        horarios = [
            f"{h:02d}:{m:02d}"
            for h in range(24)
            for m in range(60)
        ]

        self.entry_inicio = ttk.Combobox(
            frame_horarios,
            values=horarios,
            state="readonly",
            width=10
        )

        self.entry_inicio.grid(
            row=1,
            column=0,
            padx=15,
            pady=5
        )

        self.entry_inicio.set("18:00")

        self.entry_fim = ttk.Combobox(
            frame_horarios,
            values=horarios,
            state="readonly",
            width=10
        )

        self.entry_fim.grid(
            row=1,
            column=1,
            padx=15,
            pady=5
        )

        self.entry_fim.set("23:00")

        tk.Button(
            card_horario,
            text="+ Adicionar Horário",
            bg=COR_PRIMARIA,
            fg="white",
            relief="flat",
            cursor="hand2",
            font=("Segoe UI", 10, "bold"),
            command=self.adicionar
        ).pack(
            pady=(0, 20)
        )

        # CARD HORÁRIOS CADASTRADOS

        card_tabela = tk.Frame(
            miolo,
            bg="white",
            highlightbackground=COR_BORDA,
            highlightthickness=1
        )

        card_tabela.pack(
            fill="both",
            expand=True
        )

        tk.Label(
            card_tabela,
            text="📋 Horários Cadastrados",
            font=("Segoe UI", 14, "bold"),
            bg="white",
            fg=COR_TEXTO
        ).pack(
            pady=(15, 10)
        )

        self.frame_horarios = tk.Frame(
            card_tabela,
            bg="white"
        )

        self.frame_horarios.pack(
            fill="both",
            expand=True,
            padx=20,
            pady=(0, 15)
        )

    def adicionar(self):

        data = self.calendario.get_date()

        hora_inicio = self.entry_inicio.get().strip()

        hora_fim = self.entry_fim.get().strip()

        sucesso, mensagem = (
            DisponibilidadeController.cadastrar_disponibilidade(
                self.usuario.id_usuario,
                data,
                hora_inicio,
                hora_fim
            )
        )

        if sucesso:

            messagebox.showinfo(
                "Sucesso",
                mensagem
            )

            self.carregar_disponibilidades()

        else:

            messagebox.showerror(
                "Erro",
                mensagem
            )

    def carregar_disponibilidades(self):

        for widget in self.frame_horarios.winfo_children():
            widget.destroy()

        disponibilidades = (
            DisponibilidadeController.listar_disponibilidades(
                self.usuario.id_usuario
            )
        )

        self.calendario.calevent_remove("all")

        for disponibilidade in disponibilidades:

            data_formatada = disponibilidade["data"].strftime(
                "%d/%m/%Y"
            )

            hora_inicio = str(
                disponibilidade["hora_inicio"]
            )[:5]

            hora_fim = str(
                disponibilidade["hora_fim"]
            )[:5]

            card = tk.Frame(
                self.frame_horarios,
                bg="white",
                highlightbackground=COR_BORDA,
                highlightthickness=1
            )

            card.pack(
                fill="x",
                pady=6
            )

            info = tk.Frame(
                card,
                bg="white"
            )

            info.pack(
                side="left",
                padx=15,
                pady=12
            )

            tk.Label(
                info,
                text=f"📅 {data_formatada}",
                bg="white",
                fg=COR_TEXTO,
                font=("Segoe UI", 11, "bold")
            ).pack(
                anchor="w"
            )

            tk.Label(
                info,
                text=f"🕒 {hora_inicio} às {hora_fim}",
                bg="white",
                fg=COR_TEXTO_SECUNDARIO,
                font=("Segoe UI", 10)
            ).pack(
                anchor="w",
                pady=(4, 0)
            )

            # Botões da direita para a esquerda: Excluir (borda) e Editar
            tk.Button(
                card,
                text="🗑 Excluir",
                bg="#E74C3C",
                fg="white",
                relief="flat",
                cursor="hand2",
                font=("Segoe UI", 9, "bold"),
                command=lambda i=disponibilidade["id_disponibilidade"]: self.excluir_card(i)
            ).pack(
                side="right",
                padx=(0, 15),
                pady=12
            )

            tk.Button(
                card,
                text="✏️ Editar",
                bg=COR_SECUNDARIA,
                fg="white",
                relief="flat",
                cursor="hand2",
                font=("Segoe UI", 9, "bold"),
                command=lambda d=disponibilidade: self.abrir_editor(d)
            ).pack(
                side="right",
                padx=(0, 10),
                pady=12
            )

            self.calendario.calevent_create(
                disponibilidade["data"],
                "Disponível",
                "disp"
            )

        self.calendario.tag_config(
            "disp",
            background=COR_PRIMARIA,
            foreground="white"
        )

    def excluir_card(self, id_disponibilidade):

        if DisponibilidadeController.excluir_disponibilidade(
            id_disponibilidade
        ):

            self.carregar_disponibilidades()

            messagebox.showinfo(
                "Sucesso",
                "Horário removido."
            )

    def abrir_editor(self, disponibilidade):
        """
        Abre uma janela modal para editar data, hora inicial e hora final
        de uma disponibilidade já cadastrada (US07).
        """

        janela = tk.Toplevel(self)
        janela.title("Editar Disponibilidade")
        janela.configure(bg="white")
        janela.resizable(False, False)
        janela.transient(self.winfo_toplevel())
        janela.grab_set()

        tk.Label(
            janela,
            text="✏️ Editar Disponibilidade",
            font=("Segoe UI", 14, "bold"),
            bg="white",
            fg=COR_TEXTO
        ).pack(
            padx=25,
            pady=(20, 10)
        )

        tk.Label(
            janela,
            text="Selecione a nova data",
            font=("Segoe UI", 10, "bold"),
            bg="white",
            fg=COR_TEXTO_SECUNDARIO
        ).pack(pady=(0, 5))

        calendario_edicao = Calendar(
            janela,
            selectmode="day",
            date_pattern="yyyy-mm-dd",
            background=COR_PRIMARIA,
            foreground="white",
            bordercolor=COR_PRIMARIA,
            headersbackground=COR_PRIMARIA,
            headersforeground="white",
            selectbackground=COR_SECUNDARIA,
            selectforeground="white",
        )

        calendario_edicao.selection_set(disponibilidade["data"])

        calendario_edicao.pack(
            padx=25,
            pady=(0, 15)
        )

        frame_horarios_edicao = tk.Frame(
            janela,
            bg="white"
        )

        frame_horarios_edicao.pack(pady=(0, 20))

        tk.Label(
            frame_horarios_edicao,
            text="Hora Inicial",
            bg="white",
            font=("Segoe UI", 10, "bold")
        ).grid(row=0, column=0)

        tk.Label(
            frame_horarios_edicao,
            text="Hora Final",
            bg="white",
            font=("Segoe UI", 10, "bold")
        ).grid(row=0, column=1)

        horarios = [
            f"{h:02d}:{m:02d}"
            for h in range(24)
            for m in range(60)
        ]

        entry_inicio_edicao = ttk.Combobox(
            frame_horarios_edicao,
            values=horarios,
            state="readonly",
            width=10
        )

        entry_inicio_edicao.grid(
            row=1,
            column=0,
            padx=15,
            pady=5
        )

        entry_inicio_edicao.set(
            str(disponibilidade["hora_inicio"])[:5]
        )

        entry_fim_edicao = ttk.Combobox(
            frame_horarios_edicao,
            values=horarios,
            state="readonly",
            width=10
        )

        entry_fim_edicao.grid(
            row=1,
            column=1,
            padx=15,
            pady=5
        )

        entry_fim_edicao.set(
            str(disponibilidade["hora_fim"])[:5]
        )

        def salvar_edicao():

            nova_data = calendario_edicao.get_date()
            novo_inicio = entry_inicio_edicao.get().strip()
            novo_fim = entry_fim_edicao.get().strip()

            sucesso, mensagem = (
                DisponibilidadeController.atualizar_disponibilidade(
                    disponibilidade["id_disponibilidade"],
                    self.usuario.id_usuario,
                    nova_data,
                    novo_inicio,
                    novo_fim
                )
            )

            if sucesso:

                janela.destroy()

                self.carregar_disponibilidades()

                messagebox.showinfo(
                    "Sucesso",
                    mensagem
                )

            else:

                messagebox.showerror(
                    "Erro",
                    mensagem
                )

        botoes = tk.Frame(
            janela,
            bg="white"
        )

        botoes.pack(pady=(0, 20))

        tk.Button(
            botoes,
            text="💾 Salvar Alterações",
            bg=COR_PRIMARIA,
            fg="white",
            relief="flat",
            cursor="hand2",
            font=("Segoe UI", 10, "bold"),
            command=salvar_edicao
        ).pack(
            side="left",
            padx=(0, 10)
        )

        tk.Button(
            botoes,
            text="Cancelar",
            bg="white",
            fg=COR_TEXTO_SECUNDARIO,
            relief="flat",
            cursor="hand2",
            font=("Segoe UI", 9, "bold"),
            command=janela.destroy
        ).pack(side="left")