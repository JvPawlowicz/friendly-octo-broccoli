# Integração OSS Completa - Documentação

**Data:** 2025  
**Status:** ✅ Integração Completa

---

## 📦 Bibliotecas Integradas

### 1. FullCalendar ✅
- **Arquivo:** `resources/js/agenda.js`
- **Uso:** Visualização de agenda em calendário
- **Integração:** Componente `Agenda.php`
- **Funcionalidades:**
  - Visualização mensal, semanal e diária
  - Drag & drop para reagendar
  - Clique para criar/editar atendimentos
  - Atualização em tempo real via Livewire

### 2. Tiptap ✅
- **Arquivo:** `resources/js/tiptap-editor.js`
- **Uso:** Editor de texto rico em evoluções
- **Integração:** Componente `FormEvolucao.php`
- **Funcionalidades:**
  - Formatação rica (negrito, itálico, listas)
  - Autosave integrado
  - Placeholder contextual
  - Sincronização com Livewire

### 3. Chart.js ✅
- **Arquivo:** `resources/js/charts.js`
- **Uso:** Gráficos em relatórios
- **Integração:** Componente `Relatorios.php`
- **Funcionalidades:**
  - Gráficos de barras
  - Gráficos de pizza
  - Gráficos de linha
  - Helpers para criação rápida

### 4. date-fns ✅
- **Arquivo:** `resources/js/date-utils.js`
- **Uso:** Manipulação de datas
- **Integração:** Global (disponível em todo o app)
- **Funcionalidades:**
  - Formatação de datas
  - Cálculos de diferença
  - Comparações
  - Localização PT-BR

---

## 📁 Estrutura de Arquivos

```
resources/js/
├── app.js              # Entrada principal
├── bootstrap.js        # Bootstrap do Laravel
├── agenda.js           # Integração FullCalendar
├── tiptap-editor.js    # Integração Tiptap
├── charts.js           # Integração Chart.js
└── date-utils.js       # Utilitários date-fns
```

---

## 🔧 Como Usar

### FullCalendar

```javascript
// Inicializar calendário
window.initAgendaCalendar(eventos);

// Atualizar eventos
Livewire.on('calendar-update', (data) => {
    window.initAgendaCalendar(data.eventos);
});
```

### Tiptap

```javascript
// Inicializar editor
const editor = window.initTiptapEditor(
    'element-id',
    'conteudo-inicial',
    'placeholder-text',
    livewireComponent
);

// Ações do editor
window.editorActions.bold('element-id');
window.editorActions.italic('element-id');
```

### Chart.js

```javascript
// Gráfico de barras
window.chartHelpers.bar(
    'canvas-id',
    ['Label1', 'Label2'],
    [10, 20],
    'Título',
    'rgb(59, 130, 246)'
);

// Gráfico de pizza
window.chartHelpers.pie(
    'canvas-id',
    ['A', 'B'],
    [10, 20],
    ['rgb(59, 130, 246)', 'rgb(16, 185, 129)']
);
```

### date-fns

```javascript
// Formatar data
window.dateUtils.format(new Date(), 'dd/MM/yyyy');

// Adicionar dias
window.dateUtils.addDays(new Date(), 7);

// Diferença em dias
window.dateUtils.differenceInDays(date1, date2);
```

---

## 🎯 Componentes que Usam as Bibliotecas

### Agenda (`Agenda.php`)
- ✅ FullCalendar para visualização de calendário
- ✅ Toggle entre Calendário e Board

### FormEvolucao (`FormEvolucao.php`)
- ✅ Tiptap para editor de texto rico
- ✅ Autosave integrado
- ✅ Sincronização com Livewire

### Relatorios (`Relatorios.php`)
- ✅ Chart.js para gráficos
- ✅ Gráficos de produtividade
- ✅ Gráficos de absenteísmo

---

## 📝 Exemplos de Uso

### Exemplo 1: Inicializar Calendário

```blade
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.initAgendaCalendar) {
            window.initAgendaCalendar(@json($eventos));
        }
    });
</script>
@endpush
```

### Exemplo 2: Inicializar Editor Tiptap

```blade
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const wireId = @this.id;
        const component = window.Livewire.find(wireId);
        
        window.initTiptapEditor(
            'editor-id',
            @js($conteudo),
            'Digite aqui...',
            component
        );
    });
</script>
@endpush
```

### Exemplo 3: Criar Gráfico

```blade
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        window.chartHelpers.bar(
            'meuGrafico',
            @json($labels),
            @json($dados),
            'Título',
            'rgb(59, 130, 246)'
        );
    });
</script>
@endpush
```

---

## ⚙️ Configuração

### Compilar Assets

```bash
# Desenvolvimento
npm run dev

# Produção
npm run build

# Watch mode
npm run dev -- --watch
```

### Dependências

Todas as dependências já estão instaladas via `npm install`:
- `@fullcalendar/core`
- `@fullcalendar/daygrid`
- `@fullcalendar/timegrid`
- `@fullcalendar/interaction`
- `@tiptap/core`
- `@tiptap/starter-kit`
- `@tiptap/extension-placeholder`
- `chart.js`
- `date-fns`

---

## 🐛 Troubleshooting

### Calendário não aparece
- Verificar se `initAgendaCalendar` está disponível
- Verificar se o elemento `#calendar` existe
- Verificar console do navegador para erros

### Editor não funciona
- Verificar se Tiptap está importado
- Verificar se o elemento do editor existe
- Verificar se Livewire está disponível

### Gráficos não renderizam
- Verificar se Chart.js está importado
- Verificar se o canvas existe
- Verificar dados no console

---

## 📚 Referências

- [FullCalendar Docs](https://fullcalendar.io/docs)
- [Tiptap Docs](https://tiptap.dev/docs)
- [Chart.js Docs](https://www.chartjs.org/docs)
- [date-fns Docs](https://date-fns.org/docs)

---

**Última atualização:** 2025

