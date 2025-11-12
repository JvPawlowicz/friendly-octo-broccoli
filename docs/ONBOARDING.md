# Onboarding Rápido – Equidade+

## 1. Acesso
- URL: `http://127.0.0.1:8000` (ou domínio de produção).
- Credenciais iniciais no [README](../README.md).

## 2. Selecionar unidade
Ao entrar pela primeira vez, confirme a unidade “Clínica Equidade+ Central”; o middleware `scope.unit` garante que todas as telas respeitem essa seleção.

## 3. Fluxo diário
1. **Secretaria**
   - Navega em `/app/agenda`.
   - Cria/ajusta atendimentos (drag & drop, filtros).
2. **Profissional**
   - Painel em `/dashboard`.
   - Finaliza evoluções pendentes e cria adendos.
3. **Coordenador/Admin**
   - Analisa pendências e relatórios ( `/dashboard`, `/app/relatorios`).

## 4. Prontuário
- Acesse `Pacientes → Prontuário` para ver linha do tempo (evoluções, avaliações, documentos).
- Uploads ficam disponíveis em documentos do paciente.

## 5. Recomendações
- Troque senhas iniciais imediatamente.
- Configure e-mail SMTP no `.env` para notificações.
- Utilize `php artisan migrate:fresh --seed` em ambientes de teste sempre que precisar resetar dados.


