# Equidade - Sistema de Gestão Clínica

Sistema clínico completo construído em Laravel 12 + Livewire 3 para gestão de atendimentos, evoluções, avaliações e prontuário multiprofissional.

**Desenvolvido para o Grupo Equidade**

> 📚 **Documentação Completa**: Consulte a [documentação organizada](docs/README.md) para guias detalhados sobre deploy, produção, configuração e desenvolvimento.

## Requisitos

- PHP 8.2+
- Composer
- Node 18+
- MySQL ou Postgres (configurar `.env`)

## Como rodar localmente

```bash
cp .env.example .env
composer install
php artisan key:generate
npm install
```

### Banco de dados

```bash
php artisan migrate:fresh --seed
```

Esse comando executa as migrations, cria papéis/permissões e popula dados demo (unidade, sala, plano, usuários e um paciente).

### Servidores

- Backend: `php artisan serve`
- Frontend (Vite): `npm run dev`

Acesse `http://127.0.0.1:8000`.

## Usuários padrão

| Perfil        | Email                      | Senha             |
|---------------|----------------------------|-------------------|
| Admin         | admin@equidade.test        | Admin123!         |
| Coordenador   | coordenacao@equidade.test  | Coordenador123!   |
| Profissional  | profissional@equidade.test | Profissional123!  |
| Secretaria    | secretaria@equidade.test   | Secretaria123!    |

> Após fazer login, selecione a unidade “Clínica Equidade+ Central” caso solicitado.

## Fluxo clínico

1. Secretaria agenda o paciente a partir da agenda (`/app/agenda`).
2. Profissional conclui o atendimento, gera evolução pendente e finaliza pelo painel (`/dashboard`).
3. Prontuário do paciente consolida evoluções, avaliações e documentos (`/app/pacientes/{id}/prontuario`).

## Comandos úteis

- `php artisan tinker` – executar scripts rápidos.
- `php artisan migrate:fresh --seed` – resetar banco e repovoar dados demo.
- `php artisan test` – executar a suíte de testes (middleware e policies principais).

## Deploy rápido

1. `composer install --no-dev --optimize-autoloader`
2. `npm install && npm run build`
3. `php artisan migrate --force`
4. `php artisan config:cache && php artisan route:cache`

Consulte `docs/blueprint 2.0 md` para roadmap completo de módulos e evolução.
