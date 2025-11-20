# 🗄️ Como Criar Banco de Dados na Hostinger

Guia passo a passo para criar o banco de dados MySQL na Hostinger antes do deploy.

> 💡 **Dúvida entre MySQL Local ou Remoto?** Consulte: `docs/deploy/MYSQL-REMOTO-VS-LOCAL.md`

## ⚠️ IMPORTANTE

**Você DEVE criar o banco de dados ANTES de fazer o deploy!** O sistema precisa do banco para funcionar.

---

## 📋 Passo a Passo

### 1. Acessar o hPanel

1. Acesse o painel da Hostinger: https://hpanel.hostinger.com
2. Faça login com suas credenciais

### 2. Localizar MySQL Databases

1. No menu principal, procure por **MySQL Databases**
2. Ou vá em **Advanced** → **MySQL Databases**
3. Ou **Databases** → **MySQL Databases**

### 3. Criar Novo Banco de Dados

1. Clique em **Create Database** (ou **Criar Banco de Dados**)
2. Preencha os campos:

   **Database Name:**
   - Digite um nome: `equidade_db` ou `equidadeplus`
   - ⚠️ **Na Hostinger, o nome será prefixado automaticamente**
   - Exemplo: Se você digitar `equidade`, o nome final será `u123456789_equidade`

   **Database User:**
   - Opção 1: Use um usuário existente (se já tiver)
   - Opção 2: Crie um novo usuário
     - Nome: `equidade_admin` ou similar
     - ⚠️ **Também será prefixado**: `u123456789_equidade_admin`

   **Password:**
   - Crie uma senha forte
   - Use letras, números e caracteres especiais
   - Exemplo: `Equidade@2025!`
   - ⚠️ **ANOTE ESTA SENHA!** Você precisará no `.env`

3. Clique em **Create** (ou **Criar**)

### 4. Anotar as Informações

Após criar, você verá algo como:

```
✅ Database created successfully!

Database Name: u123456789_equidade
Database User: u123456789_equidade_admin
Database Host: localhost
Database Port: 3306
```

**⚠️ IMPORTANTE**: Copie e salve essas informações! Você precisará delas para configurar o `.env`.

### 5. Verificar Permissões

1. Certifique-se de que o usuário está associado ao banco
2. Se necessário, vá em **Add User to Database**
3. Selecione o usuário e o banco
4. Marque todas as permissões (ou pelo menos SELECT, INSERT, UPDATE, DELETE, CREATE, DROP)

---

## 📝 Exemplo de Configuração no .env

Com base nas informações que você anotou, configure o `.env` assim:

```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u645639692_equidade_sis     # Nome COMPLETO do banco (Hostinger)
DB_USERNAME=u645639692_sistema          # Usuário COMPLETO (Hostinger)
DB_PASSWORD=n&@=OC6R                    # Senha do banco (Hostinger)
```

**✅ Credenciais já configuradas!** O arquivo `.env.example` já contém essas informações prontas para uso.

**⚠️ ATENÇÃO**: 
- Use o nome COMPLETO do banco (com o prefixo `u123456789_`)
- Use o nome COMPLETO do usuário (com o prefixo `u123456789_`)
- Não adicione espaços extras
- A senha é case-sensitive (maiúsculas/minúsculas importam)

---

## ✅ Verificar se Está Funcionando

Após configurar o `.env`, teste a conexão via SSH:

```bash
php artisan tinker
>>> DB::connection()->getPdo();
```

Se retornar algo como `PDO Object`, está funcionando! ✅

Se der erro, verifique:
- Nome do banco está correto?
- Usuário está correto?
- Senha está correta?
- Host está como `localhost`?

---

## 🆘 Problemas Comuns

### "Access denied for user"

**Causa**: Usuário ou senha incorretos

**Solução**:
1. Verifique se copiou o nome COMPLETO do usuário (com prefixo)
2. Verifique se a senha está correta (sem espaços)
3. Verifique se o usuário está associado ao banco no hPanel

### "Unknown database"

**Causa**: Nome do banco incorreto

**Solução**:
1. Verifique se copiou o nome COMPLETO do banco (com prefixo `u123456789_`)
2. Verifique se o banco existe no hPanel
3. Confirme que o banco está ativo

### "Can't connect to MySQL server"

**Causa**: Host ou porta incorretos

**Solução**:
- Host deve ser: `localhost`
- Porta deve ser: `3306`
- Verifique se o MySQL está ativo no hPanel

---

## 📚 Próximos Passos

Após criar o banco de dados:

1. ✅ Configure o `.env` com as informações do banco
2. ✅ Faça upload dos arquivos via File Manager
3. ✅ Execute `php artisan migrate` para criar as tabelas
4. ✅ Execute `php artisan db:seed` para popular dados iniciais

---

## 💡 Dica

**Guarde as informações do banco em local seguro!** Você precisará delas sempre que:
- Fazer deploy em outro ambiente
- Restaurar backup
- Configurar conexão de outro servidor

---

**Pronto!** Agora você pode continuar com o deploy seguindo o guia `filemanager.md`.

