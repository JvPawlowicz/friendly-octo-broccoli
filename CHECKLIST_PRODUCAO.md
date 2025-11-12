# Checklist para Deploy em Produção - Equidade Plus

## 🔴 CRÍTICO - Deve ser feito antes do deploy

### 1. Arquivo `.env` de Produção
- [ ] Criar arquivo `.env` no servidor de produção
- [ ] Configurar todas as variáveis de ambiente necessárias
- [ ] **NUNCA** commitar o arquivo `.env` no Git (já está no `.gitignore`)

### 2. Configurações de Ambiente
- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false` (CRÍTICO: nunca deixar `true` em produção)
- [ ] `APP_URL=https://seudominio.com.br` (com HTTPS)
- [ ] `APP_KEY` gerado e configurado (executar `php artisan key:generate`)

### 3. Banco de Dados
- [ ] Migrar de SQLite para MySQL/PostgreSQL em produção
- [ ] Configurar conexão no `.env`:
  ```
  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=nome_do_banco
  DB_USERNAME=usuario
  DB_PASSWORD=senha_forte
  ```
- [ ] Executar migrations: `php artisan migrate --force`
- [ ] Executar seeders (se necessário): `php artisan db:seed`
- [ ] Criar backup inicial do banco de dados

### 4. Segurança
- [ ] Configurar HTTPS/SSL no servidor
- [ ] `SESSION_SECURE_COOKIE=true` (para cookies HTTPS)
- [ ] `SESSION_HTTP_ONLY=true` (já está configurado)
- [ ] Configurar firewall (permitir apenas portas necessárias)
- [ ] Desabilitar exibição de erros em produção
- [ ] Configurar rate limiting adequado
- [ ] Revisar permissões de arquivos (storage, bootstrap/cache devem ser graváveis)

### 5. Timezone
- [ ] Alterar timezone de `UTC` para `America/Sao_Paulo` em `config/app.php`
- [ ] Ou configurar via `.env`: `APP_TIMEZONE=America/Sao_Paulo`

## 🟡 IMPORTANTE - Recomendado antes do deploy

### 6. Otimizações de Performance
- [ ] Executar `php artisan config:cache`
- [ ] Executar `php artisan route:cache`
- [ ] Executar `php artisan view:cache`
- [ ] Executar `php artisan event:cache`
- [ ] Executar `composer install --optimize-autoloader --no-dev`
- [ ] Executar `npm run build` para compilar assets de produção

### 7. Storage e Arquivos
- [ ] Criar link simbólico: `php artisan storage:link`
- [ ] Verificar permissões da pasta `storage/` (755 ou 775)
- [ ] Verificar permissões da pasta `bootstrap/cache/` (755 ou 775)
- [ ] Configurar backup automático de arquivos importantes
- [ ] Se usar S3, configurar credenciais AWS no `.env`

### 8. Queue e Jobs
- [ ] Configurar driver de queue (database, redis, etc.)
- [ ] Configurar Supervisor para processar queues automaticamente
- [ ] Criar arquivo de configuração do Supervisor para `queue:work`
- [ ] Testar processamento de jobs

### 9. Laravel Reverb (WebSockets)
- [ ] Configurar Reverb para produção
- [ ] Configurar variáveis no `.env`:
  ```
  BROADCAST_DRIVER=reverb
  REVERB_APP_ID=equidadeplus
  REVERB_APP_KEY=chave_gerada
  REVERB_APP_SECRET=secret_gerado
  REVERB_HOST=seudominio.com.br
  REVERB_PORT=443
  REVERB_SCHEME=https
  ```
- [ ] Configurar Supervisor para manter Reverb rodando
- [ ] Configurar proxy reverso (Nginx) para WebSocket

### 10. Logs e Monitoramento
- [ ] Configurar rotação de logs (`LOG_CHANNEL=daily`)
- [ ] Configurar nível de log adequado (`LOG_LEVEL=error` ou `warning`)
- [ ] Configurar monitoramento de erros (Sentry, Bugsnag, etc.)
- [ ] Configurar alertas para erros críticos

### 11. Email
- [ ] Configurar driver de email (SMTP, Mailgun, SendGrid, etc.)
- [ ] Configurar variáveis no `.env`:
  ```
  MAIL_MAILER=smtp
  MAIL_HOST=smtp.exemplo.com
  MAIL_PORT=587
  MAIL_USERNAME=usuario
  MAIL_PASSWORD=senha
  MAIL_ENCRYPTION=tls
  MAIL_FROM_ADDRESS=noreply@seudominio.com.br
  MAIL_FROM_NAME="${APP_NAME}"
  ```
- [ ] Testar envio de emails

### 12. Cache
- [ ] Configurar driver de cache adequado (redis recomendado para produção)
- [ ] Se usar Redis, configurar no `.env`:
  ```
  CACHE_STORE=redis
  REDIS_HOST=127.0.0.1
  REDIS_PASSWORD=null
  REDIS_PORT=6379
  ```

## 🟢 RECOMENDADO - Melhorias e boas práticas

### 13. Backup Automatizado
- [ ] Configurar backup automático do banco de dados (cron job)
- [ ] Configurar backup de arquivos importantes
- [ ] Testar restauração de backup
- [ ] Documentar processo de backup e restore

### 14. Testes
- [ ] Executar testes: `php artisan test`
- [ ] Testar funcionalidades críticas manualmente
- [ ] Testar em ambiente de staging antes de produção

### 15. Documentação
- [ ] Criar documentação de instalação/deploy
- [ ] Documentar variáveis de ambiente necessárias
- [ ] Documentar comandos de manutenção
- [ ] Criar guia de troubleshooting

### 16. Servidor Web
- [ ] Configurar Nginx ou Apache
- [ ] Configurar PHP-FPM
- [ ] Configurar SSL/TLS (Let's Encrypt recomendado)
- [ ] Configurar headers de segurança (HSTS, CSP, etc.)
- [ ] Configurar compressão (gzip)

### 17. Process Manager (Supervisor)
- [ ] Instalar Supervisor
- [ ] Configurar Supervisor para Laravel Queue Worker
- [ ] Configurar Supervisor para Laravel Reverb
- [ ] Configurar auto-restart em caso de falha

### 18. Variáveis de Ambiente Adicionais
Verificar se todas estas variáveis estão configuradas:
- [ ] `APP_NAME` - Nome da aplicação
- [ ] `APP_LOCALE` - Idioma (pt_BR recomendado)
- [ ] `APP_FALLBACK_LOCALE` - Idioma fallback
- [ ] `SESSION_LIFETIME` - Tempo de sessão (padrão: 120 minutos)
- [ ] `QUEUE_CONNECTION` - Driver de queue
- [ ] `CACHE_STORE` - Driver de cache
- [ ] `SESSION_DRIVER` - Driver de sessão

## 📋 Script de Deploy Sugerido

Criar um script `deploy.sh` com os seguintes comandos:

```bash
#!/bin/bash

# Atualizar código
git pull origin main

# Instalar dependências
composer install --optimize-autoloader --no-dev
npm ci
npm run build

# Executar migrations
php artisan migrate --force

# Limpar e otimizar
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Cachear para produção
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Reiniciar serviços
sudo supervisorctl restart laravel-worker:*
sudo supervisorctl restart laravel-reverb:*
sudo systemctl reload php-fpm
```

## ⚠️ Checklist Pós-Deploy

- [ ] Testar login/logout
- [ ] Testar criação de pacientes
- [ ] Testar criação de atendimentos
- [ ] Testar criação de evoluções
- [ ] Testar upload de documentos
- [ ] Testar relatórios
- [ ] Verificar logs de erro
- [ ] Verificar performance
- [ ] Testar em diferentes navegadores
- [ ] Verificar responsividade mobile

## 🔧 Comandos Úteis de Manutenção

```bash
# Ver logs em tempo real
tail -f storage/logs/laravel.log

# Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Recachear para produção
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Verificar status da queue
php artisan queue:work --once

# Verificar status do Reverb
php artisan reverb:status

# Verificar rotas
php artisan route:list

# Verificar configuração
php artisan config:show
```

## 📝 Notas Importantes

1. **NUNCA** commitar arquivos `.env` ou credenciais
2. **SEMPRE** usar HTTPS em produção
3. **SEMPRE** manter `APP_DEBUG=false` em produção
4. Fazer backup antes de qualquer atualização
5. Testar em ambiente de staging antes de produção
6. Monitorar logs regularmente
7. Manter dependências atualizadas (com cuidado)
8. Documentar todas as mudanças de configuração

