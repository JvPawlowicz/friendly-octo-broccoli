---
status: aprovada
date: 2025-11-12
deciders:
  - João Victor Gonzalez Pawlowicz
  - Equipe Equidade+ (produto + engenharia)
context: |
  Precisamos substituir o painel administrativo atual (Laravel + Filament) por uma solução alinhada à nova stack
  TypeScript hospedada na Railway. O painel deve permitir CRUD rápido de usuários, unidades, templates de avaliação,
  configurações de branding, auditoria e backups, mantendo produtividade alta e curva de aprendizagem baixa para o time.
decision: |
  Adotar Next.js 15 + Refine.dev (com Ant Design) como framework do painel administrativo, consumindo a mesma API NestJS.
consequences: |
  Positivas:
    - Stack unificada em TypeScript/React.
    - Geração rápida de CRUDs com hooks, data providers e autorização integrados.
    - Customização visual utilizando Ant Design e design tokens próprios.
    - Deploy simplificado na Railway compartilhando pipeline com demais apps.
  Negativas:
    - Dependência em Ant Design (precisamos customizar para manter identidade visual).
    - Equipe precisará aprender abstrações do Refine (data providers, resources).
alternatives:
  - nome: React Admin puro
    descricao: |
      Framework consolidado, porém com menos opinião sobre hooks e sem presets para NestJS.
    pros:
      - Comunidade grande.
      - Extensas integrações.
    cons:
      - Mais boilerplate para autorização e customizações complexas.
      - Não traz geradores prontos para formulários complexos como Refine.
  - nome: Painel personalizado em Next.js + UI própria
    descricao: |
      Construir todo o painel manualmente com componentes compartilhados.
    pros:
      - Total controle visual e sem dependência de frameworks externos.
    cons:
      - Alto custo de desenvolvimento e manutenção.
      - Menos produtividade para CRUDs e filtros avançados.
references:
  - tipo: link
    descricao: Refine.dev Documentation
    url: https://refine.dev/docs
  - tipo: link
    descricao: Avaliação interna (Notion) da equipe sobre frameworks admin
    url: https://notion.so/equidade/admin-framework-study

