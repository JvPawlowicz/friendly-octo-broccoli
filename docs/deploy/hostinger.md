# 🚀 Guia de Deploy - Hostinger

Este guia detalha o processo completo de deploy do sistema Equidade+ na Hostinger.

## 📋 Pré-requisitos

- Conta na Hostinger com acesso SSH
- Domínio configurado
- Banco de dados MySQL criado
- PHP 8.2+ instalado
- Composer instalado
- Node.js 18+ instalado (para build de assets)

## 🔧 Passo 1: Preparar o Ambiente

### 1.1 Acessar via SSH

```bash
ssh usuario@seu-dominio.com
```

### 1.2 Criar Diretório do Projeto

```bash
cd ~/domains/seu-dominio.com/public_html
# OU
cd ~/public_html
```

### 1.3 Clonar o Repositório

```bash
git clone https://github.com/seu-usuario/equidade-vps.git .
# OU fazer upload via FTP/SFTP
```

## 📦 Passo 2: Instalar Dependências

### 2.1 Instalar Dependências PHP

```bash
composer install --no-dev --optimize-autoloader
```

### 2.2 Instalar Dependências Node

```bash
npm install
npm run build
```

## ⚙️ Passo 3: Configurar Ambiente

### 3.1 Criar Arquivo .env

```bash
cp .env.example .env
nano .env
```

### 3.2 Configurar Variáveis de Ambiente

```env
APP_NAME="Equidade+"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://seu-dominio.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=nome_do_banco
DB_USERNAME=usuario_banco
DB_PASSWORD=senha_banco

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Sentry (Opcional - para monitoramento)
SENTRY_LARAVEL_DSN=
SENTRY_TRACES_SAMPLE_RATE=0.1

# Mail (Configurar SMTP da Hostinger)
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=seu-email@seu-dominio.com
MAIL_PASSWORD=sua-senha
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=seu-email@seu-dominio.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 3.3 Gerar Chave da Aplicação

```bash
php artisan key:generate
```

## 🗄️ Passo 4: Configurar Banco de Dados

### 4.1 Executar Migrations

```bash
php artisan migrate --force
```

### 4.2 Executar Seeders (Opcional - apenas na primeira instalação)

```bash
php artisan db:seed --force
```

## 📁 Passo 5: Configurar Permissões

```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## 🔒 Passo 6: Configurar SSL/HTTPS

Na Hostinger, ative o SSL gratuito via painel de controle:
1. Acesse o painel hPanel
2. Vá em **SSL**
3. Ative o certificado SSL gratuito
4. Force HTTPS (redirecionamento)

## 🚀 Passo 7: Otimizar para Produção

```bash
# Cache de configuração
php artisan config:cache

# Cache de rotas
php artisan route:cache

# Cache de views
php artisan view:cache

# Otimizar autoloader
composer dump-autoload --optimize
```

## 📝 Passo 8: Configurar Cron Jobs

Adicione ao crontab (via hPanel ou SSH):

```bash
* * * * * cd /caminho/para/projeto && php artisan schedule:run >> /dev/null 2>&1
```

Ou configure via hPanel:
1. Acesse **Cron Jobs**
2. Adicione novo cron:
   - **Comando**: `php /caminho/completo/para/artisan schedule:run`
   - **Frequência**: A cada minuto (`* * * * *`)

## 🔄 Passo 9: Configurar Backup Automatizado

### 9.1 Adicionar ao Kernel.php (app/Console/Kernel.php)

```php
protected function schedule(Schedule $schedule)
{
    // Backup diário às 2h da manhã
    $schedule->command('backup:database --compress')
        ->dailyAt('02:00')
        ->timezone('America/Sao_Paulo');
}
```

### 9.2 Criar Diretório de Backups

```bash
mkdir -p storage/app/backups
chmod 755 storage/app/backups
```

## 🔍 Passo 10: Configurar Monitoramento (Sentry)

### 10.1 Obter DSN do Sentry

1. Crie conta em [sentry.io](https://sentry.io)
2. Crie novo projeto Laravel
3. Copie o DSN

### 10.2 Adicionar ao .env

```env
SENTRY_LARAVEL_DSN=https://seu-dsn@sentry.io/projeto-id
SENTRY_TRACES_SAMPLE_RATE=0.1
```

## 🧪 Passo 11: Testar Aplicação

### 11.1 Verificar Health Check

Acesse: `https://seu-dominio.com/up`

Deve retornar: `{"status":"ok"}`

### 11.2 Testar Login

Acesse: `https://seu-dominio.com/login`

Use as credenciais padrão do seeder:
- **Admin**: admin@equidade.test / Admin123!
- **Coordenador**: coordenacao@equidade.test / Coordenador123!

⚠️ **IMPORTANTE**: Altere as senhas padrão após o primeiro login!

## 🔐 Passo 12: Segurança Adicional

### 12.1 Proteger Arquivos Sensíveis

Crie/edite `.htaccess` na raiz:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>

# Proteger arquivos sensíveis
<FilesMatch "^(\.env|\.git|composer\.(json|lock)|package\.(json|lock))$">
    Order allow,deny
    Deny from all
</FilesMatch>
```

### 12.2 Configurar PHP.ini

Ajuste via hPanel ou crie `php.ini`:

```ini
upload_max_filesize = 10M
post_max_size = 10M
memory_limit = 256M
max_execution_time = 300
```

## 📊 Passo 13: Monitoramento e Logs

### 13.1 Verificar Logs

```bash
tail -f storage/logs/laravel.log
```

### 13.2 Configurar Alertas Sentry

No painel do Sentry, configure alertas para:
- Erros críticos
- Taxa de erro > 5%
- Performance degradada

## 🔄 Passo 14: Atualizações Futuras

### 14.1 Processo de Atualização

```bash
# 1. Fazer backup
php artisan backup:database --compress

# 2. Atualizar código
git pull origin main
# OU fazer upload dos arquivos atualizados

# 3. Atualizar dependências
composer install --no-dev --optimize-autoloader
npm install && npm run build

# 4. Executar migrations
php artisan migrate --force

# 5. Limpar e recriar caches
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🆘 Troubleshooting

### Erro 500

1. Verificar logs: `storage/logs/laravel.log`
2. Verificar permissões: `chmod -R 755 storage bootstrap/cache`
3. Verificar .env configurado corretamente
4. Limpar cache: `php artisan optimize:clear`

### Erro de Permissão

```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Banco de Dados não Conecta

1. Verificar credenciais no .env
2. Verificar se banco existe
3. Verificar se usuário tem permissões
4. Testar conexão: `php artisan tinker` → `DB::connection()->getPdo();`

### Assets não Carregam

1. Verificar se `npm run build` foi executado
2. Verificar link simbólico: `php artisan storage:link`
3. Verificar permissões da pasta `public`

## 📞 Suporte

Para problemas específicos da Hostinger:
- **Suporte Hostinger**: https://www.hostinger.com.br/contato
- **Documentação Hostinger**: https://support.hostinger.com

## ✅ Checklist Final

- [ ] Código enviado para servidor
- [ ] Dependências instaladas
- [ ] .env configurado
- [ ] Banco de dados criado e migrado
- [ ] Permissões configuradas
- [ ] SSL/HTTPS ativado
- [ ] Caches otimizados
- [ ] Cron jobs configurados
- [ ] Backup automatizado configurado
- [ ] Sentry configurado (opcional)
- [ ] Testes realizados
- [ ] Senhas padrão alteradas
- [ ] Logs monitorados

---

**Sistema pronto para produção! 🎉**

