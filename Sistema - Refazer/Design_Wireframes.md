# Design & Wireframes (Textual) – Equidade+ Nova Stack

Referência rápida para desenhar telas no Figma ou implementar diretamente com base nas estruturas planejadas.

## 1. Layout Base
- **Grid**: 12 colunas, gutter 24px, max-width 1280px.
- **Sidebar**: largura 280px (desktop), 72px (compacto), 100% altura.
- **Header**: altura 64px, contém busca global, avatar, badges.
- **Cards**: radius 12px, padding 20px, sombra suave (`0 10px 30px rgba(0,0,0,0.05)`).
- **Tipografia**: Inter 16px (body), 24px (h2), 18px (h3).

## 2. Telas Principais (Blueprint textual)

### 2.1 Login
```
 -------------------------------------------------
|                     Logo                        |
|               "Bem-vindo(a) de volta"           |
|                                                 |
|  [Email input________________________]          |
|  [Senha input________________________]          |
|  [Entrar]                                       |
|  Esqueci minha senha                             |
|                                                 |
|  Status do sistema: Online • Versão 1.0          |
 -------------------------------------------------
```

### 2.2 Dashboard
```
 -------------------------------------------------------------
| Sidebar | Header (busca, avatar, unidade)                   |
|         |---------------------------------------------------|
|         | KPIs (4 cards):                                   |
|         |  [Atendimentos Hoje] [Evoluções Pendentes] ...    |
|         |---------------------------------------------------|
|         | Linha superior: filtros (unidade, período)        |
|         |---------------------------------------------------|
|         | Área central: gráfico produtividade (70%)         |
|         | Área lateral: notificações recentes (30%)         |
|         |---------------------------------------------------|
|         | Bottom: lista de atalhos / pendências             |
 -------------------------------------------------------------
```

### 2.3 Agenda (Semana)
```
 -------------------------------------------------------------
| Sidebar | Header                                           |
|         |--------------------------------------------------|
|         | Toolbar: [Dia][Semana][Mês][Lista] [Filtros] ... |
|         |--------------------------------------------------|
|         | Calendário 7 colunas (dias) x 12 linhas (horas)  |
|         | - Cada célula com gradiente cor profissional     |
|         | - Tooltip ao hover (paciente, sala, status)      |
|         | Sidebar direita: filtros persistentes            |
 -------------------------------------------------------------
```

### 2.4 Paciente (Perfil)
```
 -------------------------------------------------------------
| Header: Nome / Status / Alertas (chips vermelhos)          |
| Tabs: [Dados Gerais][Documentos][Timeline][Avaliações]     |
|                                                             |
| Dados Gerais: dois cards                                   |
| - Info Pessoal (nome, idade, contatos)                      |
| - Info Clínica (diagnóstico, alergias, medicações)          |
|                                                             |
| Documentos: grade/lista com preview + categoria + ações     |
| Timeline: linha vertical com ícones (evolução, doc, notif.) |
 -------------------------------------------------------------
```

### 2.5 Evolução (Editor)
```
 -------------------------------------------------------------
| Breadcrumb: Agenda > Paciente > Evolução #123              |
| Header: Status (badge), paciente, profissional, datas      |
|------------------------------------------------------------|
| Coluna esquerda (70%):                                     |
| - Editor (tabs: Relato, Conduta, Objetivos)                |
| - Autosave info (ex.: "Rascunho salvo às 14:32")           |
| Coluna direita (30%):                                      |
| - Resumo do atendimento (data, sala, categoria)            |
| - Histórico / Addendums                                   |
|------------------------------------------------------------|
| Bottom sticky: [Salvar Rascunho] [Finalizar Evolução]      |
 -------------------------------------------------------------
```

### 2.6 Avaliação (Builder/Admin)
```
 -------------------------------------------------------------
| Header: Template "Anamnese" (versão)                       |
| Layout duas colunas:                                       |
| - Esquerda: toolbox (campos: texto, número, select, etc.)   |
| - Centro: canvas com drag & drop                           |
| - Direita: propriedades do campo selecionado               |
| Bottom: [Salvar] [Preview] [Publicar]                      |
 -------------------------------------------------------------
```

### 2.7 Relatórios
```
 -------------------------------------------------------------
| Sidebar filtros (25%): unidade, profissional, período, etc.|
| Área principal (75%):                                      |
| - Top: métricas chave (cards)                              |
| - Gráfico (barras/pizza)                                   |
| - Tabela detalhada (paginada)                              |
| Bottom: [Salvar filtro] [Exportar CSV] [Exportar PDF]      |
 -------------------------------------------------------------
```

### 2.8 Chat
```
 -------------------------------------------------------------
| Coluna 1 (20%):            | Coluna 2 (80%):                |
| - Busca contatos           | - Header thread (nome)         |
| - Lista conversas          | - Histórico mensagens           |
|   (badge não lidas)        |   (bubbles esquerda/direita)    |
|                            | - Composer (input + anexar)     |
 -------------------------------------------------------------
```

### 2.9 Painel Admin (Refine)
```
 -------------------------------------------------------------
| Sidebar recursos (Users, Units, Templates, Backups, Logs)  |
| Conteúdo: tabela (lista) com actions (edit, view, delete)  |
| Filtros no topo, bulk actions, breadcrumbs automáticos     |
 -------------------------------------------------------------
```

## 3. Componentes-Chave
- **Card KPI**: ícone 40px, título, valor (grande), variação (%).
- **Timeline item**: bullet colorido, título (ex.: Evolução finalizada), subtítulo (autor, data), descrição (resumo).
- **Modal**: header + subheader + formulário + footer com ações.
- **Form**: labels acima dos inputs, ícones para ajuda, feedback inline.
- **Toast**: canto inferior direito, cor context (verde/laranja/vermelho).

## 4. Estado Responsivo
- Mobile:
  - Sidebar vira drawer.
  - Dashboard cards empilham (1 coluna).
  - Agenda vira lista (cards por atendimento).
  - Bottom nav (agenda, pacientes, notificações).
- Tablet:
  - Sidebar colapsável.
  - Agenda exibe 2-3 colunas (profissionais).
- Desktop:
  - Layout completo.

## 5. Paleta (referência do design system)
- Primary: `#004684`
- Secondary: `#277200`
- Accent/Warning: `#B70D04`
- Success: `#01873B`
- Background: `#F5F5F5`
- Text: `#333333`

## 6. Microinterações
- Hover nos cards: elevação + sombra.
- Botões com leve animação (escurecer cor).
- Agenda drag & drop com highlight do slot destino.
- Autosave: ícone spinner → check.
- Notificações: badge com contagem e highlight ao abrir.

Use este blueprint como base para montar wireframes no Figma ou guias visuais. Ajuste conforme validação com stakeholders de UX.*** End Patch

