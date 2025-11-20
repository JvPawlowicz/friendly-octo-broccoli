# Roteiros de QA Manual – Equidade+ Nova Stack

Checklist para validação manual após cada entrega ou antes do go-live. Complementa os testes automatizados (`Plano_Testes.md`).

## 1. Configurações Iniciais
- [ ] Conseguir login com cada papel (admin, coordenador, profissional, secretaria).
- [ ] Selecionar unidade e verificar persistência ao reentrar.
- [ ] Alterar senha via fluxo “esqueci minha senha”.

## 2. Agenda
- [ ] Criar agendamento via clique em slot vazio (dados corretos).
- [ ] Validar bloqueio de sobreposição (mesmo profissional).
- [ ] Drag & drop para outro horário (status mantido).
- [ ] Alterar status para confirmado, check-in, concluído.
- [ ] Concluir atendimento e verificar criação de evolução pendente.
- [ ] Cancelar atendimento e checar registro no log.

## 3. Pacientes
- [ ] Criar paciente com dados básicos.
- [ ] Editar e salvar informações clínicas.
- [ ] Anexar documento (PDF e imagem) e baixar.
- [ ] Ver timeline consolidada (evoluções, avaliações, docs).
- [ ] Testar restrição de acesso (profissional sem vínculo não vê).

## 4. Evoluções
- [ ] Abrir evolução pendente, preencher campos, salvar rascunho.
- [ ] Finalizar evolução (assinatura) e garantir bloqueio de edição.
- [ ] Coordenador revisa e marca como revisada.
- [ ] Gerar PDF e validar conteúdo.
- [ ] Criar addendum e verificar timeline.

## 5. Avaliações
- [ ] Admin cria template com campos diversos.
- [ ] Profissional inicia avaliação, preenche, finaliza.
- [ ] Coordenador revisa e adiciona comentário.
- [ ] Timeline do paciente atualiza.
- [ ] Exportar PDF.

## 6. Relatórios
- [ ] Gerar relatório produtividade com filtros (unidade, período).
- [ ] Salvar filtro favorito e recarregar.
- [ ] Exportar CSV e PDF (arquivo disponível).
- [ ] Validar dados com amostragem (cross-check com agenda).

## 7. Chat & Notificações
- [ ] Enviar mensagem 1:1 e grupo; confirmação de recebimento.
- [ ] Polling atualiza sem recarregar página.
- [ ] Marcar mensagens como lidas.
- [ ] Criar notificação (admin/coordenador) e verificar push nos usuários.
- [ ] Marcar notificação como lida.

## 8. Configurações
- [ ] Atualizar perfil (nome, CRM/CRP, especialidade).
- [ ] Alterar preferências (tema, agenda view) e verificar persistência.
- [ ] Ajustar branding (logo, cor) e checar em todo o app.
- [ ] Criar unidade nova no painel admin.
- [ ] Gerar backup manual e baixar arquivo.

## 9. Segurança e Acesso
- [ ] Usuário sem permissão tenta acessar relatório (deve receber 403).
- [ ] Sessão expira após inatividade (token invalidado).
- [ ] Tentativas de login com senha errada (rate limit acionado).
- [ ] Log de auditoria registra ações principais (CRUD paciente, agendamento, evolução).

## 10. Performance percebida
- [ ] Carregamento dashboard < 2s (dados seed).
- [ ] Agenda com 200 agendamentos rende bem (scroll suave).
- [ ] Download de relatório acontece em tempo razoável (<10s).

## 11. Pós-go-live (Hypercare)
- [ ] Monitorar erros Sentry (zero 5xx).
- [ ] Checar backups automáticos no dia seguinte.
- [ ] Reunir feedback da equipe clínica e registrar ajustes.

### Observações
- Registrar evidências (prints, vídeo) especialmente para bugs.
- Atualizar este roteiro sempre que novas features surgirem.

---

Use este checklist em conjunto com o `Checklist_GoLive.md` para garantir cobertura completa.*** End Patch

