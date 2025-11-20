---
status: aprovada
date: 2025-11-12
deciders:
  - João Victor Gonzalez Pawlowicz
  - Equipe técnica Equidade+
context: |
  O sistema manipula documentos clínicos (PDF, imagens) e precisa garantir armazenamento seguro, versionado
  por unidade e com possibilidade de exportação/backup. A Railway oferece volumes persistentes, mas com
  limitações de escala. Avaliamos opções para equilibrar simplicidade operacional, custo e conformidade.
decision: |
  Armazenar documentos clínicos em bucket S3-compatível (Wasabi/Backblaze) gerenciado via Railway secrets, mantendo
  link simbólico local para operação e utilizando prefixos `unit_{id}/`. Backups incluem referência para restauração.
consequences: |
  Positivas:
    - Escalabilidade e durabilidade (11 9s) para arquivos clínicos.
    - Custo previsível e integração simples via SDK S3 (AWS SDK ou MinIO client).
    - Facilidade para auditoria e exportações (listar prefixos por unidade).
  Negativas:
    - Adiciona dependência externa além da Railway.
    - Requer cuidado com custos de saída (egress) e políticas de retenção.
alternatives:
  - nome: Volume persistente Railway
    descricao: |
      Armazenar arquivos diretamente em disco da aplicação.
    pros:
      - Simplicidade operacional (sem serviço adicional).
      - Baixa latência local.
    cons:
      - Escala limitada (storage e throughput).
      - Falta de redundância geográfica.
      - Processo de backup/restauração mais manual.
  - nome: Armazenamento self-hosted (MinIO) em serviço Railway separado
    descricao: |
      Subir instância MinIO na Railway para gerenciar storage.
    pros:
      - Controle total sobre dados.
      - Compatível com SDK S3.
    cons:
      - Manutenção adicional (updates, monitoramento).
      - Complexidade para garantir redundância.
references:
  - tipo: link
    descricao: Wasabi S3 compatibility
    url: https://wasabi.com/help/wasabi-apis/
  - tipo: link
    descricao: Railway docs - externos (S3)
    url: https://docs.railway.app/guides/aws-s3

