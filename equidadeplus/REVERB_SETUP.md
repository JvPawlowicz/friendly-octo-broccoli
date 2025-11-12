# Configuração do Laravel Reverb para Tempo Real

## Instalação

```bash
composer require laravel/reverb
php artisan reverb:install
```

## Configuração

1. Publique o arquivo de configuração:
```bash
php artisan vendor:publish --tag=reverb-config
```

2. Configure o `.env`:
```env
BROADCAST_DRIVER=reverb
REVERB_APP_ID=equidadeplus
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http
```

3. Gere as chaves:
```bash
php artisan reverb:install
```

## Iniciar o Servidor Reverb

```bash
php artisan reverb:start
```

Ou em produção, use um process manager como Supervisor.

## Frontend (JavaScript)

No arquivo `resources/js/app.js` ou na view do componente Livewire:

```javascript
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

// Escutar atualizações na agenda
Echo.private('agenda.' + unidadeId)
    .listen('AtendimentoAtualizado', (e) => {
        // Recarregar eventos do FullCalendar
        Livewire.emit('atualizar-agenda');
    });
```

## Variáveis de Ambiente no Frontend

No `.env`:
```env
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

