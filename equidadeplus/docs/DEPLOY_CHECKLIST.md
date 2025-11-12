# Checklist de Deploy Rápido

1. **Atualizar código**
   - `git pull`
   - Verificar `.env` (APP_KEY, banco, fila, mail).

2. **Instalar dependências**
   - `composer install --no-dev --optimize-autoloader`
   - `npm install`
   - `npm run build`

3. **Banco de dados**
   - `php artisan migrate --force`
   - Opcional: `php artisan db:seed --class=CoreDemoSeeder` (para ambiente de demonstração).

4. **Cache & otimização**
   - `php artisan config:cache`
  - `php artisan route:cache`
   - `php artisan view:cache`

5. **Serviços auxiliares**
   - Garantir queue worker ativo (caso use notificações assíncronas).
   - Verificar storage/link: `php artisan storage:link`.

6. **Smoke test**
   - Login com usuário Admin (`admin@equidade.test / Admin123!`).
   - Verificar `/dashboard` e `/app/agenda`.

7. **Monitoramento**
   - Configurar logs (`storage/logs/laravel.log`) e ferramenta APM caso disponível.


