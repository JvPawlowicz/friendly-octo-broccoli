# 📦 Deploy via File Manager - Hostinger

Guia completo para fazer deploy do sistema Equidade usando o File Manager da Hostinger.

## 🎯 Quando Usar

Use este método se:
- ✅ Você prefere interface visual (sem Git/SSH)
- ✅ É o primeiro deploy
- ✅ Não tem experiência com Git
- ✅ Quer fazer upload manual dos arquivos

**Nota**: Mesmo usando File Manager, você precisará de acesso SSH para instalar dependências e configurar o sistema.

---

## 📋 Pré-requisitos

- Conta na Hostinger com acesso ao hPanel
- Acesso SSH habilitado
- PHP 8.2+ instalado

---

## 🚀 Passo a Passo

### 0. Criar Banco de Dados (OBRIGATÓRIO - Faça Primeiro!)

**SIM, você precisa criar o banco de dados antes de fazer o deploy!**

> 💡 **MySQL Local ou Remoto?** 
> - **Recomendado para começar**: MySQL Local (Hostinger) - Mais simples e rápido
> - **Para alto tráfego**: MySQL Remoto (AWS, Google Cloud, etc.) - Mais recursos
> - Consulte: `docs/deploy/MYSQL-REMOTO-VS-LOCAL.md` para comparar opções

#### No hPanel da Hostinger:

1. Acesse o **hPanel**
2. Vá em **MySQL Databases** (ou **Banco de Dados**)
3. Clique em **Create Database** (ou **Criar Banco de Dados**)
4. Configure:
   - **Nome do Banco**: `equidade_db` (ou o nome que preferir)
   - **Usuário**: Crie um novo usuário ou use existente
   - **Senha**: Crie uma senha forte
   - **Host**: Geralmente `localhost` (será informado)

5. **Anote as informações** (você precisará no `.env`):
   - Nome do banco (com prefixo, ex: `u123456789_equidade`)
   - Usuário (com prefixo, ex: `u123456789_admin`)
   - Senha
   - Host (geralmente `localhost`)

#### Exemplo de informações que você terá:

```
Database Name: u123456789_equidade
Database User: u123456789_admin
Database Password: SuaSenhaForte123!
Database Host: localhost
Database Port: 3306
```

⚠️ **IMPORTANTE**: 
- Guarde essas informações! Você precisará delas para configurar o `.env` no passo 3
- Use o nome COMPLETO do banco e usuário (com o prefixo `u123456789_`)
- Consulte o guia completo: `docs/deploy/CRIAR-BANCO-DADOS.md`

---

### 1. Preparar Arquivos Localmente

Os arquivos já estão prontos na pasta `deploy-filemanager/` ou no ZIP `equidade-filemanager-deploy.zip`.

**Opção A: Usar pasta completa**
- Faça upload de todos os arquivos da pasta `deploy-filemanager/`

**Opção B: Usar ZIP (mais rápido)**
- Faça upload do arquivo `equidade-filemanager-deploy.zip`
- Extraia no servidor via File Manager

### 2. Upload via File Manager

1. Acesse o **hPanel** da Hostinger
2. Vá em **File Manager**
3. Navegue até `public_html` (ou `domains/seu-dominio.com/public_html`)
4. **Faça upload de TODOS os arquivos**

   **Se usar ZIP:**
   - Faça upload do `equidade-filemanager-deploy.zip`
   - Clique com botão direito no arquivo
   - Selecione **Extract**
   - Aguarde a extração

   **Se usar pasta:**
   - Selecione todos os arquivos da pasta `deploy-filemanager/`
   - Faça upload para `public_html`

### 3. Configurar .env

1. No File Manager, localize o arquivo `.env.example`
2. Renomeie para `.env`
3. Edite o arquivo `.env` e configure com as informações do banco que você criou:

```env
APP_NAME="Equidade"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://sistemagrupoequidade.net

# ⚠️ CREDENCIAIS CONFIGURADAS PARA HOSTINGER
# Para MySQL LOCAL (Hostinger):
DB_CONNECTION=mysql
DB_HOST=localhost                    # Para MySQL local
DB_PORT=3306                         # Porta padrão MySQL
DB_DATABASE=u645639692_equidade_sis  # Nome do banco (Hostinger)
DB_USERNAME=u645639692_sistema       # Usuário do banco (Hostinger)
DB_PASSWORD=n&@=OC6R                 # Senha do banco (Hostinger)

# Para MySQL REMOTO (AWS, Google Cloud, etc.):
# DB_HOST=equidade-db.xxxxx.us-east-1.rds.amazonaws.com  # Host remoto
# DB_PORT=3306
# DB_DATABASE=equidade_db
# DB_USERNAME=admin
# DB_PASSWORD=sua-senha-forte
# DB_SSL_CA=/path/to/ca-cert.pem  # SSL recomendado para remoto

# Session e Cache (usar database em produção)
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

# Mail (configurar depois)
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=seu-email@seu-dominio.com
MAIL_PASSWORD=sua-senha-email
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=seu-email@seu-dominio.com
MAIL_FROM_NAME="${APP_NAME}"
```

**Credenciais já configuradas para Hostinger:**
- Banco: `u645639692_equidade_sis`
- Usuário: `u645639692_sistema`
- Senha: `n&@=OC6R`
- URL: `https://sistemagrupoequidade.net`

⚠️ **IMPORTANTE**: As credenciais acima já estão no `.env.example`. Apenas renomeie para `.env` e gere a `APP_KEY`!

### 4. Acessar via SSH (Obrigatório)

Você precisará acessar via SSH para executar comandos:

1. No hPanel, vá em **SSH Access**
2. Copie as credenciais SSH
3. Conecte via terminal:

```bash
ssh usuario@seu-dominio.com
```

### 5. Executar Script de Instalação

No servidor, via SSH:

```bash
cd ~/domains/sistemagrupoequidade.net/public_html
# OU
cd ~/public_html

# OPÇÃO A - Executar script pronto (RECOMENDADO)
bash COPIAR-COLE-SERVIDOR.sh

# OPÇÃO B - Copiar e colar conteúdo diretamente
# Abra o arquivo COPIAR-COLE-SERVIDOR.sh, copie TODO o conteúdo
# Cole no terminal SSH e pressione Enter
```

⚠️ **IMPORTANTE**: O script `COPIAR-COLE-SERVIDOR.sh` resolve automaticamente o problema do `APP_KEY` que bloqueava a instalação!

O script irá:
- ✅ Instalar dependências do Composer
- ✅ Instalar dependências do NPM e compilar assets
- ✅ Gerar chave da aplicação
- ✅ Executar migrations
- ✅ Executar seeders
- ✅ Criar link do storage
- ✅ Configurar permissões
- ✅ Cachear configurações

### 6. Configuração Manual (se necessário)

Se o script não funcionar, execute manualmente:

```bash
# Instalar dependências
composer install --no-dev --optimize-autoloader
npm install && npm run build

# Gerar chave
php artisan key:generate

# Migrations
php artisan migrate --force

# Seeders
php artisan db:seed --force

# Link do storage
php artisan storage:link

# Permissões
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Cachear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 7. Configurar Cron Job

No hPanel, vá em **Cron Jobs** e adicione:

```
* * * * * cd /caminho/para/public_html && php artisan schedule:run >> /dev/null 2>&1
```

**Encontrar o caminho completo:**
```bash
# Via SSH
pwd
# Copie o caminho completo e use no cron
```

### 8. Testar

1. Acesse: `https://seu-dominio.com`
2. Verifique health check: `https://seu-dominio.com/up`
3. Faça login com:
   - **Admin**: admin@equidade.test / Admin123!
   - **Coordenador**: coordenacao@equidade.test / Coordenador123!

⚠️ **IMPORTANTE**: Altere as senhas padrão após o primeiro login!

---

## 🔄 Atualizações Futuras

Para atualizar o sistema:

### Opção 1: File Manager (Manual)
1. Faça upload dos novos arquivos via File Manager
2. Via SSH, execute:
   ```bash
   composer install --no-dev --optimize-autoloader
   npm install && npm run build
   php artisan migrate --force
   php artisan optimize:clear
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

### Opção 2: Migrar para Git (Recomendado)
Após o primeiro deploy, considere migrar para Git para atualizações automáticas:
- Consulte: `docs/deploy/automatico-git.md`

---

## 🆘 Troubleshooting

### Erro 500

1. Verificar permissões:
   ```bash
   chmod -R 755 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

2. Verificar logs:
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. Limpar cache:
   ```bash
   php artisan optimize:clear
   ```

### Erro de Permissão

```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Banco de Dados não Conecta

1. **Verifique se o banco foi criado**:
   - Acesse hPanel → MySQL Databases
   - Confirme que o banco existe e está ativo

2. **Verifique credenciais no `.env`**:
   - Nome do banco está correto? (geralmente começa com `u` seguido de números)
   - Usuário está correto?
   - Senha está correta? (sem espaços extras)
   - Host está como `localhost`?

3. **Teste conexão via SSH**:
   ```bash
   php artisan tinker
   >>> DB::connection()->getPdo();
   ```
   Se der erro, verifique as credenciais novamente.

4. **Verifique formato do nome do banco**:
   - Na Hostinger, o nome geralmente é: `u123456789_nome`
   - Use o nome COMPLETO, incluindo o prefixo `u123456789_`

5. **Verifique se o usuário tem permissão**:
   - No hPanel, vá em MySQL Databases
   - Verifique se o usuário está associado ao banco
   - Se não estiver, adicione o usuário ao banco

### Composer não encontrado

```bash
# Instalar Composer globalmente
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
```

### NPM não encontrado

- Verifique se Node.js está instalado: `node --version`
- Se não estiver, instale via hPanel ou contate suporte

---

## ✅ Checklist de Deploy

Use o arquivo `CHECKLIST-DEPLOY.md` incluído no pacote para verificar todos os passos.

---

## 📚 Documentação Adicional

- **Como criar banco de dados**: `docs/deploy/CRIAR-BANCO-DADOS.md` ⭐ **LEIA PRIMEIRO!**
- **Instruções completas**: `INSTRUCOES-DEPLOY.md` (no pacote)
- **Deploy automático**: `docs/deploy/automatico-git.md`
- **Deploy completo**: `docs/deploy/hostinger.md`

---

## 🎉 Pronto!

Seu sistema está configurado e pronto para uso!

Para dúvidas, consulte a documentação completa em `docs/`.

