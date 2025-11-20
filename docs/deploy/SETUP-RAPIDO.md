# ⚡ Setup Rápido - Deploy Automático

Guia rápido para configurar deploy automático em 5 minutos.

## 🚀 Passo a Passo

### 1. Execute o Script de Setup

```bash
./scripts/setup-deploy.sh
```

Este script irá:
- ✅ Gerar token de deploy seguro
- ✅ Adicionar token ao `.env`
- ✅ Configurar permissões
- ✅ Verificar todas as configurações

### 2. Configure o Webhook no GitHub

1. Acesse: **Settings** > **Webhooks** > **Add webhook**
2. Configure:
   - **Payload URL**: `https://seu-dominio.com/deploy`
   - **Content type**: `application/json`
   - **Events**: "Just the push event"
   - **Secret**: (deixe vazio)
3. Adicione header customizado:
   - **Name**: `X-Deploy-Token`
   - **Value**: (o token gerado pelo script)

### 3. Teste o Deploy

```bash
curl -X POST https://seu-dominio.com/deploy \
  -H "X-Deploy-Token: SEU_TOKEN_AQUI" \
  -H "Content-Type: application/json"
```

### 4. Verifique os Logs

```bash
tail -f storage/logs/deploy.log
```

## ✅ Pronto!

Agora, cada `git push` na branch `main` irá automaticamente:
- Atualizar o código
- Instalar dependências
- Compilar assets
- Executar migrations
- Limpar e cachear
- Reiniciar serviços

## 🔍 Monitoramento

- **Logs de deploy**: `storage/logs/deploy.log`
- **Logs da aplicação**: `storage/logs/laravel.log`
- **Status do deploy**: `https://seu-dominio.com/deploy/status` (opcional)

## 🆘 Problemas?

Consulte a [documentação completa](automatico-git.md) para troubleshooting.

