# Configuração OpenEMR no Railway

## 🚂 Visão Geral

Este guia detalha como configurar e fazer deploy do OpenEMR customizado na plataforma Railway, mantendo o armazenamento persistente.

## 📋 Pré-requisitos

- Conta no Railway
- Repositório Git configurado
- Conhecimento básico de Railway

## 🏗️ Estrutura de Serviços no Railway

### Serviços Necessários

1. **Serviço PHP** (OpenEMR Application)
2. **Serviço MySQL** (Banco de Dados)
3. **Volume de Armazenamento** (Documentos e arquivos)

---

## 📦 Passo 1: Criar Projeto no Railway

1. Acesse [Railway](https://railway.app/)
2. Clique em **New Project**
3. Selecione **Deploy from GitHub repo**
4. Conecte seu repositório
5. Selecione o repositório com o OpenEMR

---

## 🗄️ Passo 2: Criar Serviço MySQL

1. No projeto Railway, clique em **+ New**
2. Selecione **Database** > **MySQL**
3. Railway criará automaticamente:
   - Instância MySQL
   - Variáveis de ambiente com credenciais

### Variáveis de Ambiente Criadas Automaticamente

```
MYSQL_HOST=containers-us-west-xxx.railway.app
MYSQL_PORT=xxxx
MYSQL_DATABASE=railway
MYSQL_USER=root
MYSQL_PASSWORD=xxxxx
MYSQL_URL=mysql://root:xxxxx@containers-us-west-xxx.railway.app:xxxx/railway
```

---

## 🐘 Passo 3: Configurar Serviço PHP

### 3.1 Criar Serviço PHP

1. No projeto Railway, clique em **+ New**
2. Selecione **GitHub Repo**
3. Selecione o repositório do OpenEMR

### 3.2 Configurar Build

Railway detectará automaticamente que é PHP. Você pode usar:

**Opção 1: Nixpacks (Recomendado)**

Crie arquivo `nixpacks.toml` na raiz:

```toml
[phases.setup]
nixPkgs = [
  "php83",
  "php83Extensions.curl",
  "php83Extensions.pdo_mysql",
  "php83Extensions.gd",
  "php83Extensions.mbstring",
  "php83Extensions.xml",
  "php83Extensions.zip",
  "php83Packages.composer",
  "apacheHttpd"
]

[phases.install]
cmds = [
  "composer install --no-dev --optimize-autoloader"
]

[start]
cmd = "apache2-foreground"
```

**Opção 2: Dockerfile**

Crie arquivo `Dockerfile`:

```dockerfile
FROM php:8.3-apache

# Instalar dependências
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo_mysql mysqli zip

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Copiar arquivos
COPY . /var/www/html/

# Configurar permissões
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expor porta
EXPOSE 80
```

### 3.3 Configurar Variáveis de Ambiente

No serviço PHP, adicione as seguintes variáveis:

```env
# Banco de Dados (use as variáveis do serviço MySQL)
DB_HOST=${MYSQL_HOST}
DB_PORT=${MYSQL_PORT}
DB_NAME=${MYSQL_DATABASE}
DB_USER=${MYSQL_USER}
DB_PASS=${MYSQL_PASSWORD}

# OpenEMR Config
OE_SITE_ID=default
OE_SITE_DIR=sites/default

# PHP Config
PHP_INI_SCAN_DIR=/usr/local/etc/php/conf.d
PHP_UPLOAD_MAX_FILESIZE=50M
PHP_POST_MAX_SIZE=50M
PHP_MEMORY_LIMIT=512M

# Apache Config
APACHE_DOCUMENT_ROOT=/var/www/html
```

---

## 💾 Passo 4: Configurar Armazenamento Persistente

### 4.1 Criar Volume

1. No serviço PHP, vá em **Settings** > **Volumes**
2. Clique em **+ Add Volume**
3. Configure:
   - **Mount Path**: `/var/www/html/sites`
   - **Name**: `openemr-sites`

Isso garantirá que os documentos e configurações sejam persistidos.

### 4.2 Configurar Permissões

Crie script `setup-storage.sh`:

```bash
#!/bin/bash
# Criar diretórios necessários
mkdir -p /var/www/html/sites/default/documents
mkdir -p /var/www/html/sites/default/documents/cache
mkdir -p /var/www/html/sites/default/documents/temp

# Configurar permissões
chown -R www-data:www-data /var/www/html/sites
chmod -R 755 /var/www/html/sites
chmod -R 700 /var/www/html/sites/default/documents
```

---

## ⚙️ Passo 5: Configurar Apache

### 5.1 Criar .htaccess

Crie arquivo `.htaccess` na raiz:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    
    # Redirecionar para HTTPS
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
    
    # Proteger arquivos sensíveis
    <FilesMatch "\.(sql|log|ini)$">
        Order allow,deny
        Deny from all
    </FilesMatch>
</IfModule>

# Configurações PHP
php_value upload_max_filesize 50M
php_value post_max_size 50M
php_value memory_limit 512M
php_value max_execution_time 300
```

### 5.2 Configurar Virtual Host

Se usar Dockerfile, adicione configuração Apache:

```apache
<VirtualHost *:80>
    ServerName localhost
    DocumentRoot /var/www/html
    
    <Directory /var/www/html>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

---

## 🔐 Passo 6: Configurar Segurança

### 6.1 Variáveis de Ambiente Sensíveis

Configure no Railway (Settings > Variables):

```env
# OpenEMR Security
OE_SITE_ID=default
OE_SITE_DIR=sites/default

# Session Security
SESSION_TIMEOUT=3600
SESSION_COOKIE_SECURE=1
SESSION_COOKIE_HTTPONLY=1

# Encryption (gerar chaves únicas)
OE_ENCRYPTION_KEY=<gerar_chave_aleatoria>
```

### 6.2 Gerar Chaves de Criptografia

Execute no terminal:

```bash
# Gerar chave de criptografia
openssl rand -base64 32
```

Adicione a chave gerada em `OE_ENCRYPTION_KEY`.

---

## 🚀 Passo 7: Deploy

### 7.1 Primeiro Deploy

1. Faça commit e push do código
2. Railway detectará automaticamente e iniciará o build
3. Acompanhe os logs em **Deployments**

### 7.2 Instalação Inicial do OpenEMR

Após o deploy:

1. Acesse a URL fornecida pelo Railway
2. Siga o wizard de instalação do OpenEMR
3. Configure:
   - Banco de dados (use as variáveis do MySQL)
   - Site ID: `default`
   - Usuário administrador
   - Configurações iniciais

### 7.3 Configurar Domínio Customizado (Opcional)

1. No serviço PHP, vá em **Settings** > **Networking**
2. Clique em **Generate Domain** ou adicione domínio customizado
3. Configure DNS apontando para o domínio do Railway

---

## 📊 Passo 8: Monitoramento

### 8.1 Logs

Acesse logs em:
- **Deployments** > **View Logs**
- Ou via CLI: `railway logs`

### 8.2 Métricas

Monitore:
- Uso de CPU
- Uso de memória
- Uso de disco
- Tráfego de rede

---

## 🔄 Passo 9: Backup

### 9.1 Backup do Banco de Dados

Configure backup automático do MySQL no Railway:
1. Vá em **Settings** do serviço MySQL
2. Configure **Backup Schedule**
3. Ou use script manual:

```bash
# Backup manual
mysqldump -h $MYSQL_HOST -u $MYSQL_USER -p$MYSQL_PASSWORD $MYSQL_DATABASE > backup.sql
```

### 9.2 Backup de Arquivos

Os arquivos em `/var/www/html/sites` estão no volume persistente, mas faça backup periódico:

```bash
# Backup de documentos
tar -czf documents-backup.tar.gz /var/www/html/sites/default/documents
```

---

## 🛠️ Troubleshooting

### Problema: Erro de Conexão com Banco

**Solução**:
- Verifique variáveis de ambiente
- Confirme que o serviço MySQL está rodando
- Verifique firewall/network do Railway

### Problema: Permissões de Arquivo

**Solução**:
- Execute `setup-storage.sh` no primeiro deploy
- Verifique permissões do volume
- Ajuste via script de inicialização

### Problema: Timeout

**Solução**:
- Aumente `max_execution_time` no PHP
- Verifique queries lentas
- Otimize banco de dados

### Problema: Upload de Arquivos Falha

**Solução**:
- Verifique `upload_max_filesize` e `post_max_size`
- Verifique permissões do diretório `documents`
- Verifique espaço em disco

---

## 📝 Checklist de Deploy

- [ ] Serviço MySQL criado e configurado
- [ ] Serviço PHP criado e configurado
- [ ] Volume de armazenamento configurado
- [ ] Variáveis de ambiente configuradas
- [ ] Build configurado (nixpacks.toml ou Dockerfile)
- [ ] Permissões de arquivos configuradas
- [ ] Domínio configurado (opcional)
- [ ] Backup configurado
- [ ] Instalação inicial do OpenEMR concluída
- [ ] Testes funcionais realizados
- [ ] Monitoramento configurado

---

## 🔗 Recursos

- [Railway Documentation](https://docs.railway.app/)
- [Railway PHP Guide](https://docs.railway.app/guides/php)
- [Railway MySQL Guide](https://docs.railway.app/guides/postgresql)
- [OpenEMR Installation Guide](https://www.open-emr.org/wiki/index.php/OpenEMR_Installation_Guide)

---

## 💡 Dicas

1. **Use variáveis de ambiente** para todas as configurações sensíveis
2. **Monitore logs** regularmente
3. **Faça backups** periódicos
4. **Teste em staging** antes de produção
5. **Use volumes** para dados que precisam persistir
6. **Configure alertas** no Railway para monitoramento

