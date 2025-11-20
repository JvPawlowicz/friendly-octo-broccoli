---
status: proposta
date: 2025-11-12
deciders:
  - Equipe técnica Equidade+
context: |
  Evoluções, avaliações e relatórios exigem exportação em PDF. Precisamos de geração consistente, com suporte a
  layout customizado (logo, cabeçalho, rodapé), assinaturas simples e textos longos. A solução deve rodar em ambiente
  Node (Railway) sem depender de serviços externos caros.
decision: |
  Utilizar @react-pdf/renderer em worker BullMQ dedicado para gerar PDFs assinados, armazenando resultado temporário
  no bucket S3 e invalidando após download ou 7 dias.
consequences: |
  Positivas:
    - Geração usando a mesma stack React (componentização dos layouts).
    - Escala horizontal via worker específico na Railway.
    - Controle sobre conteúdo e branding.
  Negativas:
    - Conversão pode ser lenta para documentos muito longos (performance precisa ser monitorada).
    - Necessidade de montar components específicos para PDF (não reaproveita 100% UI web).
alternatives:
  - nome: Puppeteer + headless Chrome
    descricao: |
      Renderização de páginas HTML diretamente via Chromium.
    pros:
      - Renderização fiel ao layout web (CSS full).
    cons:
      - Consumo alto de memória/CPU.
      - Deploy mais complexo (dependências do Chromium nos containers).
  - nome: Serviço externo (PDFMonkey, DocRaptor)
    descricao: |
      Envio de payload HTML/JSON para serviço third-party.
    pros:
      - Simplifica implementação.
    cons:
      - Custo recorrente.
      - Dependência externa + requisitos de LGPD/BAA.
references:
  - tipo: link
    descricao: @react-pdf/renderer docs
    url: https://react-pdf.org/
  - tipo: link
    descricao: Exemplo interno de layout PDF (Notion)
    url: https://notion.so/equidade/pdf-layout-sample

