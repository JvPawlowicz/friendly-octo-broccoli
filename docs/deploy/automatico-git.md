# 🚀 Deploy Automático via Git - Hostinger

Este guia explica como configurar deploy automático via Git para a Hostinger, permitindo que cada push no repositório atualize automaticamente o sistema em produção.

## 📋 Pré-requisitos

- Conta na Hostinger com acesso SSH
- Repositório Git (GitHub, GitLab, Bitbucket, etc.)
- Acesso SSH ao servidor Hostinger
- Chave SSH configurada

---

## ⚡ Setup Rápido (Recomendado)

Execute o script de setup automático:

```bash
./scripts/setup-deploy.sh
```

Este script irá:
- ✅ Gerar um token de deploy seguro
- ✅ Adicionar o token ao `.env`
- ✅ Configurar permissões do `deploy.sh`
- ✅ Verificar todas as configurações
- ✅ Fornecer instruções para configurar o webhook

---

## 🔧 Método 1: Deploy via Webhook (Recomendado)

### Passo 1: Executar Setup Automático

```bash
./scripts/setup-deploy.sh
```

Ou configure manualmente:

### Passo 2: Configurar Token de Deploy

1. Gerar um token seguro:
```bash
php artisan tinker
>>> bin2hex(random_bytes(32))
```

2. Adicionar ao `.env`:
```env
DEPLOY_TOKEN=seu-token-gerado-aqui
```

### Passo 3: Verificar Configuração

O endpoint `/deploy` já está configurado. Verifique:

```bash
php artisan route:list | grep deploy
```

### Passo 3: Configurar Webhook no GitHub/GitLab

#### GitHub:
1. Vá em **Settings** > **Webhooks** > **Add webhook**
2. **Payload URL**: `https://seu-dominio.com/deploy`
3. **Content type**: `application/json`
4. **Events**: Selecione "Just the push event"
5. Adicione header customizado:
   - **Name**: `X-Deploy-Token`
   - **Value**: `seu-token-super-secreto-aqui`

#### GitLab:
1. Vá em **Settings** > **Webhooks**
2. **URL**: `https://seu-dominio.com/deploy`
3. **Trigger**: Push events
4. Adicione header customizado:
   - **Name**: `X-Deploy-Token`
   - **Value**: `seu-token-super-secreto-aqui`

---

## 🔧 Método 2: Deploy via SSH Hook

### Passo 1: Configurar Git no Servidor

No servidor Hostinger via SSH:

```bash
cd ~/domains/seu-dominio.com/public_html

# Inicializar Git (se ainda não foi feito)
git init

# Adicionar remote
git remote add origin https://github.com/seu-usuario/seu-repositorio.git

# Fazer pull inicial
git pull origin main
```

### Passo 2: Criar Git Hook

Crie o arquivo `.git/hooks/post-receive`:

```bash
#!/bin/bash

PROJECT_DIR="/home/usuario/domains/seu-dominio.com/public_html"
cd $PROJECT_DIR || exit

# Atualizar código
git fetch origin
git reset --hard origin/main

# Executar script de deploy
bash deploy.sh
```

Tornar executável:
```bash
chmod +x .git/hooks/post-receive
```

---

## 🔧 Método 3: Usando GitHub Actions

### Passo 1: Criar Workflow

Crie o arquivo `.github/workflows/deploy.yml`:

```yaml
name: Deploy to Hostinger

on:
  push:
    branches:
      - main

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
    - name: Deploy to server
      uses: appleboy/ssh-action@master
      with:
        host: ${{ secrets.HOSTINGER_HOST }}
        username: ${{ secrets.HOSTINGER_USER }}
        key: ${{ secrets.HOSTINGER_SSH_KEY }}
        script: |
          cd ~/domains/seu-dominio.com/public_html
          git fetch origin
          git reset --hard origin/main
          git clean -fd
          bash deploy.sh
```

### Passo 2: Configurar Secrets no GitHub

1. Vá em **Settings** > **Secrets and variables** > **Actions**
2. Adicione:
   - `HOSTINGER_HOST`: IP ou domínio do servidor
   - `HOSTINGER_USER`: Usuário SSH
   - `HOSTINGER_SSH_KEY`: Chave SSH privada

---

## 🔐 Segurança

### 1. Proteger Rota de Webhook

A rota `/deploy` já tem rate limiting (`throttle:10,1`) e validação de token.

### 2. Usar Token Forte

Gere um token seguro usando:
```bash
php artisan tinker
>>> Str::random(60)
```

### 3. Logs de Deploy

Os logs são salvos automaticamente em `storage/logs/deploy.log`.

---

## 📝 Script de Deploy

O script `deploy.sh` na raiz do projeto já está configurado e inclui:

- ✅ Atualização do código via Git
- ✅ Backup do banco de dados
- ✅ Instalação de dependências (Composer e NPM)
- ✅ Build de assets
- ✅ Execução de migrations
- ✅ Limpeza e otimização de caches
- ✅ Verificação de permissões
- ✅ Logs detalhados

---

## 🔄 Fluxo de Trabalho Recomendado

### 1. Estrutura de Branches

```
main (produção)
  └── develop (desenvolvimento)
      └── feature/* (features)
```

### 2. Processo de Deploy

1. **Desenvolvimento**: Trabalhe na branch `develop`
2. **Teste**: Teste localmente
3. **Merge**: Faça merge para `main`
4. **Push**: `git push origin main`
5. **Deploy Automático**: Webhook/GitHub Actions executa deploy

### 3. Rollback (Em caso de problema)

```bash
cd ~/domains/seu-dominio.com/public_html
git log --oneline -10  # Ver últimos commits
git reset --hard <commit-hash>  # Voltar para commit anterior
bash deploy.sh
```

---

## 🧪 Testar Deploy

### Teste Manual

```bash
# No servidor
cd ~/domains/seu-dominio.com/public_html
bash deploy.sh
```

### Teste via Webhook

```bash
# Localmente
curl -X POST https://seu-dominio.com/deploy \
  -H "X-Deploy-Token: seu-token-super-secreto-aqui" \
  -H "Content-Type: application/json"
```

### Teste via Git Push

```bash
# Fazer uma pequena alteração
echo "<!-- Deploy test -->" >> resources/views/welcome.blade.php
git add .
git commit -m "Test deploy"
git push origin main

# Verificar se deploy foi executado
ssh usuario@seu-dominio.com "tail -f ~/domains/seu-dominio.com/public_html/storage/logs/deploy.log"
```

---

## 🆘 Troubleshooting

### Erro: "Permission denied"

```bash
chmod +x deploy.sh
chmod -R 755 storage bootstrap/cache
```

### Erro: "Composer not found"

```bash
# Instalar Composer globalmente
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
```

### Erro: "npm not found"

```bash
# Instalar Node.js (via Hostinger ou manualmente)
# Verificar versão: node --version
```

### Deploy não executa automaticamente

1. Verificar se webhook está configurado corretamente
2. Verificar logs do servidor: `tail -f storage/logs/laravel.log`
3. Verificar se token está correto
4. Testar manualmente: `bash deploy.sh`

---

## ✅ Vantagens do Deploy Automático

- ✅ **Rapidez**: Deploy em segundos após push
- ✅ **Confiabilidade**: Processo padronizado
- ✅ **Rastreabilidade**: Logs de cada deploy
- ✅ **Segurança**: Token protegido
- ✅ **Rollback fácil**: Git permite voltar versões

---

## 📚 Recursos Adicionais

- [Documentação Hostinger SSH](https://support.hostinger.com/pt-br/articles/4428959-como-conectar-ao-servidor-via-ssh)
- [GitHub Actions](https://docs.github.com/en/actions)
- [GitLab CI/CD](https://docs.gitlab.com/ee/ci/)
- [Deploy na Hostinger](hostinger.md) - Guia completo de deploy

---

**Sistema configurado para deploy automático! 🎉**

