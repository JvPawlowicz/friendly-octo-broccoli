# 📋 Resumo: O que falta para produção

## ✅ O que foi criado/ajustado

1. **CHECKLIST_PRODUCAO.md** - Checklist completo com todos os itens necessários
2. **deploy.sh** - Script automatizado de deploy
3. **env.production.example** - Template de variáveis de ambiente para produção
4. **supervisor-example.conf** - Configuração do Supervisor para Queue e Reverb
5. **nginx-example.conf** - Configuração do Nginx com SSL e WebSocket
6. **config/app.php** - Ajustado para usar timezone do Brasil por padrão

## 🔴 PRIORIDADE MÁXIMA - Fazer ANTES do deploy

### 1. Configurar Banco de Dados
- **Atual:** SQLite (desenvolvimento)
- **Necessário:** MySQL ou PostgreSQL em produção
- **Ação:** Criar banco de dados e configurar no `.env`

### 2. Configurar Variáveis de Ambiente
- Copiar `env.production.example` para `.env` no servidor
- Preencher TODAS as variáveis, especialmente:
  - `APP_KEY` (gerar com `php artisan key:generate`)
  - `APP_DEBUG=false` (CRÍTICO!)
  - `APP_URL` com HTTPS
  - Credenciais do banco de dados
  - Credenciais do email

### 3. Segurança Básica
- Configurar HTTPS/SSL (Let's Encrypt recomendado)
- `SESSION_SECURE_COOKIE=true` no `.env`
- Verificar permissões de arquivos

## 🟡 IMPORTANTE - Fazer durante o deploy

### 4. Otimizações
- Executar `composer install --optimize-autoloader --no-dev`
- Executar `npm run build`
- Cachear configurações: `php artisan config:cache`

### 5. Configurar Queue Worker
- Instalar Supervisor
- Configurar `supervisor-example.conf` com caminhos corretos
- Iniciar workers: `sudo supervisorctl start laravel-worker:*`

### 6. Configurar Laravel Reverb (WebSockets)
- Gerar chaves: `php artisan reverb:install`
- Configurar Supervisor para manter Reverb rodando
- Configurar proxy no Nginx para WebSocket

### 7. Configurar Servidor Web
- Instalar e configurar Nginx ou Apache
- Usar `nginx-example.conf` como base
- Configurar SSL/TLS

## 📝 Passos Rápidos para Deploy

1. **No servidor de produção:**
   ```bash
   # Clonar/copiar projeto
   git clone [seu-repositorio] /var/www/equidadeplus
   cd /var/www/equidadeplus
   
   # Copiar e configurar .env
   cp env.production.example .env
   nano .env  # Editar com suas configurações
   
   # Gerar APP_KEY
   php artisan key:generate
   ```

2. **Instalar dependências:**
   ```bash
   composer install --optimize-autoloader --no-dev
   npm ci
   npm run build
   ```

3. **Configurar banco de dados:**
   ```bash
   php artisan migrate --force
   php artisan db:seed  # Se necessário
   ```

4. **Otimizar:**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan storage:link
   ```

5. **Configurar Supervisor:**
   ```bash
   sudo cp supervisor-example.conf /etc/supervisor/conf.d/laravel-worker.conf
   # Editar caminhos no arquivo
   sudo supervisorctl reread
   sudo supervisorctl update
   sudo supervisorctl start laravel-worker:*
   ```

6. **Configurar Nginx:**
   ```bash
   sudo cp nginx-example.conf /etc/nginx/sites-available/equidadeplus
   # Editar domínio e caminhos
   sudo ln -s /etc/nginx/sites-available/equidadeplus /etc/nginx/sites-enabled/
   sudo nginx -t
   sudo systemctl reload nginx
   ```

7. **Configurar SSL (Let's Encrypt):**
   ```bash
   sudo certbot --nginx -d seudominio.com.br -d www.seudominio.com.br
   ```

## ⚠️ Checklist Rápido

- [ ] `.env` configurado com todas as variáveis
- [ ] `APP_DEBUG=false`
- [ ] `APP_KEY` gerado
- [ ] Banco de dados MySQL/PostgreSQL configurado
- [ ] Migrations executadas
- [ ] Assets compilados (`npm run build`)
- [ ] Configurações cacheadas
- [ ] HTTPS/SSL configurado
- [ ] Supervisor configurado para Queue
- [ ] Supervisor configurado para Reverb
- [ ] Nginx/Apache configurado
- [ ] Permissões de arquivos corretas
- [ ] Testes básicos realizados

## 🚀 Usando o Script de Deploy

Após configurar o `.env` e o banco de dados, você pode usar o script:

```bash
./deploy.sh
```

O script irá:
- Atualizar código (se usar Git)
- Instalar dependências
- Compilar assets
- Executar migrations
- Limpar e cachear configurações
- Verificar permissões
- Reiniciar serviços

## 📞 Problemas Comuns

### Erro de permissões
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Queue não processa
```bash
php artisan queue:work --tries=3
# Ou verificar Supervisor
sudo supervisorctl status
```

### Reverb não conecta
- Verificar se está rodando: `php artisan reverb:start`
- Verificar configuração do Nginx para WebSocket
- Verificar firewall (porta 8080)

### Erros 500
- Verificar logs: `tail -f storage/logs/laravel.log`
- Verificar permissões
- Verificar se `.env` está configurado
- Limpar cache: `php artisan config:clear`

## 📚 Documentação Adicional

- **CHECKLIST_PRODUCAO.md** - Checklist detalhado
- **REVERB_SETUP.md** - Configuração do Reverb
- Arquivos de exemplo: `*-example.conf` e `env.production.example`

