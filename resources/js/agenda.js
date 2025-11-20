/**
 * Integração FullCalendar com Livewire
 * Usado no componente Agenda.php
 */

import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';

let calendarInstance = null;

export function initAgendaCalendar(events = []) {
    const calendarEl = document.getElementById('calendar');
    
    if (!calendarEl) {
        console.warn('Elemento #calendar não encontrado');
        return null;
    }

    // Destruir instância anterior se existir
    if (calendarInstance) {
        calendarInstance.destroy();
        calendarInstance = null;
    }

    // Garantir que events é um array
    if (!Array.isArray(events)) {
        console.warn('Events não é um array:', events);
        events = [];
    }

    console.log('Inicializando calendário com', events.length, 'eventos');

    calendarInstance = new Calendar(calendarEl, {
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
        initialView: 'timeGridWeek',
        locale: 'pt-br',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: events,
        editable: true,
        selectable: true,
        selectMirror: true,
        dayMaxEvents: true,
        height: 'auto',
        slotMinTime: '06:00:00',
        slotMaxTime: '22:00:00',
        allDaySlot: false,
        dateClick: function(info) {
            // Callback será sobrescrito pela view
            console.log('dateClick', info);
        },
        eventClick: function(info) {
            // Callback será sobrescrito pela view
            console.log('eventClick', info);
        },
        eventDrop: function(info) {
            // Atualizar horário via Livewire
            if (window.Livewire && info.event.extendedProps?.atendimento_id) {
                window.Livewire.find(
                    document.querySelector('[wire\\:id]')?.getAttribute('wire:id')
                )?.call('atualizarHorario', {
                    id: info.event.extendedProps.atendimento_id,
                    start: info.event.startStr,
                    end: info.event.endStr
                });
            }
        },
        eventResize: function(info) {
            // Atualizar duração via Livewire
            if (window.Livewire && info.event.extendedProps?.atendimento_id) {
                window.Livewire.find(
                    document.querySelector('[wire\\:id]')?.getAttribute('wire:id')
                )?.call('atualizarDuracao', {
                    id: info.event.extendedProps.atendimento_id,
                    start: info.event.startStr,
                    end: info.event.endStr
                });
            }
        }
    });

    calendarInstance.render();
    return calendarInstance;
}

// Escutar eventos do Livewire
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar quando o componente Livewire carregar
    if (window.Livewire) {
        Livewire.hook('morph.updated', ({ el, component }) => {
            const calendarEl = document.getElementById('calendar');
            if (calendarEl && !calendarInstance) {
                // Aguardar um pouco para garantir que os eventos foram carregados
                setTimeout(() => {
                    const eventos = component.get('eventos') || [];
                    initAgendaCalendar(eventos);
                }, 100);
            }
        });

        // Escutar atualizações de eventos
        Livewire.on('calendar-update', (data) => {
            if (calendarInstance && data.eventos) {
                calendarInstance.removeAllEvents();
                calendarInstance.addEventSource(data.eventos);
            }
        });
    }
});

// Exportar para uso global
window.initAgendaCalendar = initAgendaCalendar;

