# Guia de Integração de Bibliotecas OSS

Este documento explica como integrar e usar as bibliotecas open source no sistema.

---

## 1. Instalação

### 1.1 Instalar Dependências NPM

```bash
npm install
```

Isso instalará todas as dependências listadas no `package.json`, incluindo:
- FullCalendar (já estava instalado)
- Tiptap (novo)
- Chart.js (novo)
- date-fns (novo)

### 1.2 Compilar Assets

```bash
# Desenvolvimento (watch mode)
npm run dev

# Produção
npm run build
```

---

## 2. FullCalendar - Agenda Visual

### 2.1 Configuração Básica

**Arquivo:** `resources/js/agenda.js`

```javascript
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';

document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    
    if (!calendarEl) return;
    
    const calendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
        initialView: 'timeGridWeek',
        locale: 'pt-br',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: function(fetchInfo, successCallback, failureCallback) {
            // Buscar eventos via Livewire
            Livewire.emit('buscar-eventos', {
                start: fetchInfo.startStr,
                end: fetchInfo.endStr
            });
            
            // Escutar resposta
            Livewire.on('eventos-recebidos', (eventos) => {
                successCallback(eventos);
            });
        },
        editable: true,
        selectable: true,
        selectMirror: true,
        dayMaxEvents: true,
        dateClick: function(info) {
            // Abrir modal de criação via Livewire
            Livewire.emit('abrir-modal-criar', {
                date: info.dateStr,
                time: info.dateStr
            });
        },
        eventClick: function(info) {
            // Abrir modal de edição
            Livewire.emit('abrir-modal-editar', info.event.id);
        },
        eventDrop: function(info) {
            // Atualizar horário
            Livewire.emit('atualizar-horario', {
                id: info.event.id,
                start: info.event.startStr,
                end: info.event.endStr
            });
        },
        eventResize: function(info) {
            // Atualizar duração
            Livewire.emit('atualizar-duracao', {
                id: info.event.id,
                start: info.event.startStr,
                end: info.event.endStr
            });
        }
    });
    
    calendar.render();
    
    // Expor para Livewire
    window.calendar = calendar;
});
```

### 2.2 No Componente Livewire

```php
// app/Livewire/AgendaView.php
public $eventos = [];

#[On('buscar-eventos')]
public function buscarEventos($periodo)
{
    $this->eventos = Atendimento::whereBetween('data_hora_inicio', [
        $periodo['start'],
        $periodo['end']
    ])->get()->map(function ($atendimento) {
        return [
            'id' => $atendimento->id,
            'title' => $atendimento->paciente->nome_completo,
            'start' => $atendimento->data_hora_inicio->toIso8601String(),
            'end' => $atendimento->data_hora_fim->toIso8601String(),
            'color' => $this->getColorByStatus($atendimento->status),
            'extendedProps' => [
                'profissional' => $atendimento->profissional->name,
                'sala' => $atendimento->sala->nome ?? 'Sem sala',
            ]
        ];
    })->toArray();
    
    $this->dispatch('eventos-recebidos', $this->eventos);
}

#[On('atualizar-horario')]
public function atualizarHorario($dados)
{
    $atendimento = Atendimento::findOrFail($dados['id']);
    $atendimento->update([
        'data_hora_inicio' => $dados['start'],
        'data_hora_fim' => $dados['end'],
    ]);
    
    $this->dispatch('app:toast', message: 'Horário atualizado', type: 'success');
}

protected function getColorByStatus($status)
{
    return match($status) {
        'Agendado' => '#3b82f6', // blue
        'Confirmado' => '#f59e0b', // yellow
        'Check-in' => '#10b981', // green
        'Concluído' => '#059669', // emerald
        'Cancelado' => '#ef4444', // red
        default => '#6b7280', // gray
    };
}
```

### 2.3 No Blade

```blade
<!-- resources/views/livewire/agenda-view.blade.php -->
<div>
    <div id="calendar" wire:ignore></div>
</div>

@push('scripts')
<script type="module" src="{{ asset('js/agenda.js') }}"></script>
@endpush
```

---

## 3. Tiptap - Editor de Texto Rico

### 3.1 Componente Alpine.js

**Arquivo:** `resources/js/components/tiptap-editor.js`

```javascript
import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Placeholder from '@tiptap/extension-placeholder';

document.addEventListener('alpine:init', () => {
    Alpine.data('tiptapEditor', (initialContent = '', placeholder = 'Digite aqui...') => ({
        editor: null,
        content: initialContent,
        
        init() {
            this.editor = new Editor({
                element: this.$refs.editor,
                extensions: [
                    StarterKit.configure({
                        heading: {
                            levels: [1, 2, 3],
                        },
                    }),
                    Placeholder.configure({
                        placeholder: placeholder,
                    }),
                ],
                content: this.content,
                editorProps: {
                    attributes: {
                        class: 'prose prose-sm sm:prose lg:prose-lg xl:prose-2xl mx-auto focus:outline-none min-h-[200px] p-4',
                    },
                },
                onUpdate: ({ editor }) => {
                    this.content = editor.getHTML();
                    // Enviar para Livewire
                    this.$wire.set('conteudo', this.content);
                },
                onBlur: () => {
                    // Autosave ao perder foco
                    this.$wire.call('autosave');
                },
            });
        },
        
        // Métodos auxiliares
        bold() {
            this.editor.chain().focus().toggleBold().run();
        },
        
        italic() {
            this.editor.chain().focus().toggleItalic().run();
        },
        
        heading(level) {
            this.editor.chain().focus().toggleHeading({ level }).run();
        },
        
        bulletList() {
            this.editor.chain().focus().toggleBulletList().run();
        },
        
        orderedList() {
            this.editor.chain().focus().toggleOrderedList().run();
        },
        
        undo() {
            this.editor.chain().focus().undo().run();
        },
        
        redo() {
            this.editor.chain().focus().redo().run();
        },
    }));
});
```

### 3.2 No Componente Livewire

```php
// app/Livewire/FormEvolucao.php
public $conteudo = '';
public $autosaveAtivo = false;

public function autosave()
{
    if (empty($this->conteudo)) {
        return;
    }
    
    // Salvar no banco
    if ($this->evolucaoId) {
        Evolucao::where('id', $this->evolucaoId)->update([
            'conteudo' => $this->conteudo,
            'updated_at' => now(),
        ]);
        
        $this->autosaveAtivo = true;
        $this->dispatch('autosave-success');
    }
}

#[On('autosave-success')]
public function mostrarFeedback()
{
    $this->dispatch('app:toast', message: 'Salvo automaticamente', type: 'info');
}
```

### 3.3 No Blade

```blade
<!-- resources/views/livewire/form-evolucao.blade.php -->
<div x-data="tiptapEditor(@js($conteudo), 'Digite a evolução clínica...')">
    <!-- Toolbar -->
    <div class="border-b border-gray-200 p-2 flex gap-2">
        <button @click="bold()" class="px-3 py-1 hover:bg-gray-100 rounded">
            <strong>B</strong>
        </button>
        <button @click="italic()" class="px-3 py-1 hover:bg-gray-100 rounded italic">
            I
        </button>
        <button @click="heading(2)" class="px-3 py-1 hover:bg-gray-100 rounded">
            H2
        </button>
        <button @click="bulletList()" class="px-3 py-1 hover:bg-gray-100 rounded">
            •
        </button>
        <button @click="orderedList()" class="px-3 py-1 hover:bg-gray-100 rounded">
            1.
        </button>
        <button @click="undo()" class="px-3 py-1 hover:bg-gray-100 rounded">
            ↶
        </button>
        <button @click="redo()" class="px-3 py-1 hover:bg-gray-100 rounded">
            ↷
        </button>
    </div>
    
    <!-- Editor -->
    <div x-ref="editor" class="border border-gray-300 rounded-b"></div>
    
    <!-- Feedback de autosave -->
    <div x-show="$wire.autosaveAtivo" 
         x-transition
         class="text-sm text-gray-500 mt-2">
        ✓ Salvo automaticamente
    </div>
</div>

@push('scripts')
<script type="module" src="{{ asset('js/components/tiptap-editor.js') }}"></script>
@endpush
```

---

## 4. Chart.js - Gráficos

### 4.1 Componente de Gráfico

**Arquivo:** `resources/js/components/chart.js`

```javascript
import Chart from 'chart.js/auto';

document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de Produtividade
    const produtividadeCtx = document.getElementById('produtividadeChart');
    if (produtividadeCtx) {
        new Chart(produtividadeCtx, {
            type: 'line',
            data: {
                labels: window.chartLabels || [],
                datasets: [{
                    label: 'Atendimentos',
                    data: window.chartData || [],
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Produtividade (últimos 30 dias)'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
    
    // Gráfico de Pizza - Frequência
    const frequenciaCtx = document.getElementById('frequenciaChart');
    if (frequenciaCtx) {
        new Chart(frequenciaCtx, {
            type: 'pie',
            data: {
                labels: ['Presentes', 'Faltas', 'Cancelados'],
                datasets: [{
                    data: window.frequenciaData || [0, 0, 0],
                    backgroundColor: [
                        'rgb(16, 185, 129)',
                        'rgb(239, 68, 68)',
                        'rgb(156, 163, 175)'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    }
});
```

### 4.2 No Componente Livewire

```php
// app/Livewire/Relatorios.php
public function render()
{
    $dados = $this->gerarDados();
    
    return view('livewire.relatorios', [
        'chartLabels' => $dados['labels'],
        'chartData' => $dados['values'],
    ]);
}
```

### 4.3 No Blade

```blade
<div>
    <canvas id="produtividadeChart" 
            data-labels="{{ json_encode($chartLabels) }}"
            data-data="{{ json_encode($chartData) }}"></canvas>
</div>

@push('scripts')
<script>
    window.chartLabels = @json($chartLabels);
    window.chartData = @json($chartData);
</script>
<script type="module" src="{{ asset('js/components/chart.js') }}"></script>
@endpush
```

---

## 5. date-fns - Manipulação de Datas

### 5.1 Uso Básico

```javascript
import { format, addDays, differenceInDays, parseISO } from 'date-fns';
import { ptBR } from 'date-fns/locale';

// Formatar data
const dataFormatada = format(new Date(), 'dd/MM/yyyy', { locale: ptBR });
// Resultado: "15/01/2025"

// Adicionar dias
const amanha = addDays(new Date(), 1);

// Diferença em dias
const dias = differenceInDays(new Date('2025-01-20'), new Date('2025-01-15'));
// Resultado: 5

// Parse de string ISO
const data = parseISO('2025-01-15T10:30:00Z');
```

### 5.2 Helper Global

**Arquivo:** `resources/js/utils/date.js`

```javascript
import { format, addDays, differenceInDays, parseISO, isBefore, isAfter } from 'date-fns';
import { ptBR } from 'date-fns/locale';

window.dateUtils = {
    format: (date, pattern = 'dd/MM/yyyy') => {
        return format(date instanceof Date ? date : parseISO(date), pattern, { locale: ptBR });
    },
    
    formatDateTime: (date) => {
        return format(date instanceof Date ? date : parseISO(date), 'dd/MM/yyyy HH:mm', { locale: ptBR });
    },
    
    addDays: (date, days) => {
        return addDays(date instanceof Date ? date : parseISO(date), days);
    },
    
    differenceInDays: (date1, date2) => {
        return differenceInDays(
            date1 instanceof Date ? date1 : parseISO(date1),
            date2 instanceof Date ? date2 : parseISO(date2)
        );
    },
    
    isBefore: (date1, date2) => {
        return isBefore(
            date1 instanceof Date ? date1 : parseISO(date1),
            date2 instanceof Date ? date2 : parseISO(date2)
        );
    },
    
    isAfter: (date1, date2) => {
        return isAfter(
            date1 instanceof Date ? date1 : parseISO(date1),
            date2 instanceof Date ? date2 : parseISO(date2)
        );
    },
};
```

### 5.3 Uso no HTML

```html
<script>
    // Formatar data exibida
    const data = new Date('2025-01-15');
    document.getElementById('data').textContent = window.dateUtils.format(data);
    // Exibe: "15/01/2025"
</script>
```

---

## 6. Estrutura de Arquivos Recomendada

```
resources/
├── js/
│   ├── app.js (principal)
│   ├── agenda.js (FullCalendar)
│   ├── components/
│   │   ├── tiptap-editor.js
│   │   └── chart.js
│   └── utils/
│       └── date.js
└── css/
    └── app.css
```

---

## 7. Importação no app.js

```javascript
// resources/js/app.js
import './bootstrap';
import './agenda';
import './components/tiptap-editor';
import './components/chart';
import './utils/date';
```

---

## 8. Próximos Passos

1. **Instalar dependências:**
   ```bash
   npm install
   ```

2. **Criar arquivos JavaScript:**
   - `resources/js/agenda.js`
   - `resources/js/components/tiptap-editor.js`
   - `resources/js/components/chart.js`
   - `resources/js/utils/date.js`

3. **Integrar nos componentes Livewire:**
   - Atualizar `AgendaView.php` para usar FullCalendar
   - Atualizar `FormEvolucao.php` para usar Tiptap
   - Atualizar `Relatorios.php` para usar Chart.js

4. **Testar:**
   - Verificar se os eventos do FullCalendar funcionam
   - Testar autosave do Tiptap
   - Validar gráficos do Chart.js

---

**Última atualização:** 2025

