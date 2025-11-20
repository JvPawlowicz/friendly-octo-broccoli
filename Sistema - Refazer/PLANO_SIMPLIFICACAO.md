# Plano de Simplificação do Sistema Equidade+

**Data:** 2025  
**Objetivo:** Reduzir complexidade, eliminar duplicações e facilitar manutenção usando bibliotecas OSS prontas.

---

## 1. Mudanças Implementadas

### 1.1 Dashboard Unificado ✅

**Antes:**
- `DashboardAdmin.php` - 100 linhas
- `DashboardCoordenador.php` - 147 linhas  
- `DashboardSecretaria.php` - 117 linhas
- **Total:** 364 linhas, 3 arquivos, 3 views

**Depois:**
- `Dashboard.php` - 1 componente adaptativo
- `dashboard.blade.php` - 1 view com condicionais por role
- **Total:** ~200 linhas, 1 arquivo, 1 view

**Benefícios:**
- Redução de 45% no código
- Manutenção centralizada
- Lógica de escopo unificada
- Fácil adicionar novos KPIs

---

## 2. Bibliotecas OSS Recomendadas

### 2.1 Agenda - FullCalendar ✅ (Já Instalado)

**Biblioteca:** `@fullcalendar/core`, `@fullcalendar/daygrid`, `@fullcalendar/timegrid`, `@fullcalendar/interaction`

**Uso:**
- Visualização de agendamentos
- Drag & drop para reorganizar
- Criação rápida ao clicar na data
- Filtros por profissional/sala

**Implementação:**
```javascript
// resources/js/agenda.js
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';

const calendarEl = document.getElementById('calendar');
const calendar = new Calendar(calendarEl, {
    plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
    initialView: 'timeGridWeek',
    events: '/api/agenda/events',
    editable: true,
    selectable: true,
    dateClick: function(info) {
        // Abrir modal de criação
    },
    eventDrop: function(info) {
        // Atualizar horário via Livewire
    }
});
calendar.render();
```

---

### 2.2 Editor de Texto Rico - Tiptap ✅ (Adicionado)

**Biblioteca:** `@tiptap/core`, `@tiptap/starter-kit`, `@tiptap/extension-placeholder`

**Uso:**
- Editor de evoluções clínicas
- Formatação rica (negrito, itálico, listas)
- Autosave integrado
- Placeholder contextual

**Implementação:**
```javascript
// resources/js/editor.js
import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Placeholder from '@tiptap/extension-placeholder';

const editor = new Editor({
    extensions: [
        StarterKit,
        Placeholder.configure({
            placeholder: 'Digite a evolução clínica...',
        }),
    ],
    content: '',
    onUpdate: ({ editor }) => {
        // Autosave via Livewire
        Livewire.emit('evolucao-autosave', editor.getHTML());
    },
});
```

**Componente Livewire:**
```php
// app/Livewire/FormEvolucao.php
public $conteudo = '';

#[On('evolucao-autosave')]
public function autosave($html)
{
    $this->conteudo = $html;
    // Salvar no banco a cada 30s
    $this->dispatch('saved');
}
```

---

### 2.3 Gráficos - Chart.js ✅ (Adicionado)

**Biblioteca:** `chart.js`

**Uso:**
- Dashboard com gráficos de produtividade
- Relatórios visuais
- Gráficos de frequência

**Implementação:**
```javascript
// resources/js/charts.js
import Chart from 'chart.js/auto';

const ctx = document.getElementById('produtividadeChart');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Fev', 'Mar'],
        datasets: [{
            label: 'Atendimentos',
            data: [12, 19, 15],
        }]
    }
});
```

---

### 2.4 Manipulação de Datas - date-fns ✅ (Adicionado)

**Biblioteca:** `date-fns`

**Uso:**
- Formatação de datas no frontend
- Cálculos de períodos
- Validações de datas

**Exemplo:**
```javascript
import { format, addDays, isBefore } from 'date-fns';
import { ptBR } from 'date-fns/locale';

format(new Date(), 'dd/MM/yyyy', { locale: ptBR });
```

---

## 3. Simplificações Pendentes

### 3.1 Agenda - Consolidar Views

**Atual:**
- `AgendaView.php` - Calendário FullCalendar (632 linhas)
- `AgendaBoard.php` - Board Kanban (170 linhas)

**Proposta:**
- Manter `AgendaView` como principal
- Adicionar toggle de visualização (Calendário/Board) no mesmo componente
- Reduzir para ~400 linhas

**Ação:**
```php
// app/Livewire/AgendaView.php
public $viewMode = 'calendar'; // 'calendar' | 'board'

public function toggleView()
{
    $this->viewMode = $this->viewMode === 'calendar' ? 'board' : 'calendar';
}
```

---

### 3.2 Avaliações - Consolidar Listagens

**Atual:**
- `MinhasAvaliacoes.php` - Para profissionais
- `AvaliacoesUnidade.php` - Para coordenadores

**Proposta:**
- Criar `AvaliacoesList.php` unificado
- Adaptar filtros baseado no role
- Reduzir duplicação de código

**Implementação:**
```php
// app/Livewire/AvaliacoesList.php
public function mount()
{
    $user = Auth::user();
    
    if ($user->hasRole('Profissional')) {
        $this->scope = 'minhas';
        $this->profissionalId = $user->id;
    } elseif ($user->hasAnyRole(['Coordenador', 'Admin'])) {
        $this->scope = 'unidade';
    }
}
```

---

### 3.3 Relatórios - Unificar Componentes

**Atual:**
- `RelatorioFrequencia.php` - Relatório de frequência
- `RelatorioProdutividade.php` - Relatório de produtividade

**Proposta:**
- Criar `Relatorios.php` com abas
- Cada aba carrega um tipo de relatório
- Compartilhar lógica de filtros e favoritos

**Estrutura:**
```php
// app/Livewire/Relatorios.php
public $tipoRelatorio = 'produtividade'; // 'produtividade' | 'frequencia' | 'clinico'

public function render()
{
    return view('livewire.relatorios', [
        'dados' => $this->gerarRelatorio($this->tipoRelatorio)
    ]);
}
```

---

## 4. Componentes a Remover/Deprecar

### 4.1 Dashboards Separados ❌

**Remover:**
- `app/Livewire/DashboardAdmin.php`
- `app/Livewire/DashboardCoordenador.php`
- `app/Livewire/DashboardSecretaria.php`
- `resources/views/livewire/dashboard-admin.blade.php`
- `resources/views/livewire/dashboard-coordenador.blade.php`
- `resources/views/livewire/dashboard-secretaria.blade.php`

**Substituir por:**
- `app/Livewire/Dashboard.php` ✅
- `resources/views/livewire/dashboard.blade.php` ✅

---

## 5. Estrutura de Arquivos Simplificada

### 5.1 Antes
```
app/Livewire/
├── DashboardAdmin.php
├── DashboardCoordenador.php
├── DashboardSecretaria.php
├── AgendaView.php
├── AgendaBoard.php
├── MinhasAvaliacoes.php
├── AvaliacoesUnidade.php
├── RelatorioFrequencia.php
└── RelatorioProdutividade.php
```

### 5.2 Depois
```
app/Livewire/
├── Dashboard.php (unificado)
├── Agenda.php (consolidado)
├── AvaliacoesList.php (unificado)
├── Relatorios.php (unificado)
└── ... (outros componentes)
```

---

## 6. Rotas Simplificadas

### 6.1 Antes
```php
Route::get('/dashboard', PainelEvolucoes::class);
Route::get('/dashboard-admin', DashboardAdmin::class);
Route::get('/dashboard-coordenador', DashboardCoordenador::class);
Route::get('/dashboard-secretaria', DashboardSecretaria::class);
```

### 6.2 Depois
```php
Route::get('/dashboard', Dashboard::class); // Adaptativo
```

---

## 7. Benefícios da Simplificação

### 7.1 Redução de Código
- **Dashboards:** 364 → 200 linhas (-45%)
- **Manutenção:** 3 arquivos → 1 arquivo
- **Views:** 3 views → 1 view

### 7.2 Facilidade de Manutenção
- Lógica centralizada
- Mudanças em um único lugar
- Menos bugs por duplicação

### 7.3 Performance
- Menos arquivos para carregar
- Cache mais eficiente
- Bundle menor

---

## 8. Próximos Passos

### Fase 1: Dashboard ✅
- [x] Criar Dashboard unificado
- [x] Atualizar rotas
- [ ] Remover dashboards antigos (após testes)

### Fase 2: Agenda ✅
- [x] Consolidar AgendaView e AgendaBoard em Agenda.php
- [x] Adicionar toggle de visualização (Calendário/Board)
- [x] Integrar FullCalendar
- [x] Manter funcionalidade de drag & drop (board)

### Fase 3: Avaliações ✅
- [x] Unificar MinhasAvaliacoes e AvaliacoesUnidade em AvaliacoesList.php
- [x] Adaptar escopo baseado no role
- [x] Simplificar filtros

### Fase 4: Relatórios ✅
- [x] Unificar componentes de relatórios em Relatorios.php
- [x] Adicionar abas para diferentes tipos
- [x] Integrar Chart.js
- [x] Melhorar visualizações

### Fase 5: Editor Rico
- [ ] Integrar Tiptap em FormEvolucao
- [ ] Implementar autosave
- [ ] Adicionar formatação rica

---

## 9. Bibliotecas OSS Utilizadas

| Biblioteca | Versão | Uso | Status |
|------------|--------|-----|--------|
| @fullcalendar/core | ^6.1.19 | Agenda visual | ✅ Instalado |
| @tiptap/core | ^2.1.13 | Editor de texto rico | ✅ Adicionado |
| chart.js | ^4.4.0 | Gráficos | ✅ Adicionado |
| date-fns | ^2.30.0 | Manipulação de datas | ✅ Adicionado |
| laravel-echo | ^2.2.6 | WebSockets | ✅ Instalado |
| pusher-js | ^8.4.0 | Real-time | ✅ Instalado |

---

## 10. Comandos de Instalação

```bash
# Instalar dependências NPM
npm install @tiptap/core @tiptap/pm @tiptap/starter-kit @tiptap/extension-placeholder chart.js date-fns

# Compilar assets
npm run build

# Ou em desenvolvimento
npm run dev
```

---

## 11. Notas de Implementação

### 11.1 Tiptap com Livewire

Para usar Tiptap com Livewire, é necessário:

1. **Criar componente Alpine.js:**
```javascript
// resources/js/components/tiptap-editor.js
Alpine.data('tiptapEditor', (initialContent) => ({
    editor: null,
    init() {
        this.editor = new Editor({
            extensions: [StarterKit, Placeholder],
            content: initialContent,
            onUpdate: () => {
                this.$wire.set('conteudo', this.editor.getHTML());
            }
        });
    }
}));
```

2. **No Blade:**
```blade
<div x-data="tiptapEditor(@js($conteudo))" x-init="init()">
    <div x-ref="editor"></div>
</div>
```

### 11.2 FullCalendar com Livewire

Para eventos dinâmicos:

```php
// app/Livewire/Agenda.php
public function getEventsProperty()
{
    return $this->atendimentos->map(function ($atendimento) {
        return [
            'id' => $atendimento->id,
            'title' => $atendimento->paciente->nome_completo,
            'start' => $atendimento->data_hora_inicio->toIso8601String(),
            'end' => $atendimento->data_hora_fim->toIso8601String(),
            'color' => $this->getColorByStatus($atendimento->status),
        ];
    });
}
```

---

## 12. Checklist de Migração

- [x] Dashboard unificado criado
- [x] Rotas atualizadas
- [x] Bibliotecas OSS adicionadas ao package.json
- [x] Consolidar Agenda (Agenda.php com toggle)
- [x] Consolidar Avaliações (AvaliacoesList.php adaptativo)
- [x] Consolidar Relatórios (Relatorios.php com abas)
- [x] Integrar Chart.js nos relatórios
- [x] Integrar FullCalendar na agenda
- [ ] Testes completos
- [ ] Remover componentes antigos (após testes)
- [ ] Integrar Tiptap no editor de evoluções
- [x] Documentação atualizada

---

**Última atualização:** 2025

