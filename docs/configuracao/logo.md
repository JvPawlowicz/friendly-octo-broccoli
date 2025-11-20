# 🎨 Como Adicionar o Logo do Equidade

Este guia explica como adicionar o logo do grupo Equidade ao sistema.

## 📍 Localização do Logo

O sistema procura o logo no seguinte caminho:
```
public/images/logo.png
```

## 📝 Passos para Adicionar o Logo

### 1. Preparar a Imagem

- **Formato**: PNG (recomendado) ou SVG
- **Tamanho recomendado**: 
  - Largura: 200-300px
  - Altura: proporcional (mantém aspecto)
  - Fundo: Transparente (PNG) ou branco
- **Resolução**: Mínimo 200px de largura para boa qualidade

### 2. Fazer Upload do Logo

#### Opção A: Via FTP/SFTP (Produção)
1. Conecte-se ao servidor via FTP/SFTP
2. Navegue até: `public_html/public/images/`
3. Crie a pasta `images` se não existir
4. Faça upload do arquivo como `logo.png`

#### Opção B: Via File Manager (Hostinger)
1. Acesse o File Manager no hPanel
2. Navegue até: `public_html/public/images/`
3. Crie a pasta `images` se não existir
4. Faça upload do arquivo como `logo.png`

#### Opção C: Localmente (Desenvolvimento)
1. Coloque o arquivo em: `public/images/logo.png`
2. O sistema detectará automaticamente

### 3. Verificar Permissões

Certifique-se de que o arquivo tem permissões de leitura:
```bash
chmod 644 public/images/logo.png
```

### 4. Testar

Após adicionar o logo:
1. Limpe o cache: `php artisan view:clear`
2. Recarregue a página
3. O logo deve aparecer no:
   - Sidebar (menu lateral)
   - Página de login (se configurado)
   - PDFs de relatórios (se configurado)

## 🎯 Onde o Logo Aparece

O logo é exibido automaticamente nos seguintes locais:

1. **Sidebar (Menu Lateral)**
   - Topo do menu lateral
   - Tamanho: altura de 40px (h-10)

2. **Página de Login** (se configurado)
   - Componente `application-logo`

3. **PDFs de Relatórios** (futuro)
   - Cabeçalho dos relatórios exportados

## 🔄 Fallback

Se o logo não for encontrado, o sistema exibe:
- Um ícone SVG padrão (círculo com check)
- O nome "Equidade" em texto

## 📐 Tamanhos e Proporções

O sistema ajusta automaticamente o tamanho do logo:
- **Sidebar**: Altura máxima de 40px (h-10)
- **Responsivo**: Mantém proporção em diferentes telas

## 🛠️ Personalização Avançada

### Alterar Caminho do Logo

Edite os arquivos:
- `resources/views/components/layout/sidebar.blade.php`
- `resources/views/components/application-logo.blade.php`

Altere a variável:
```php
$logoPath = 'images/logo.png'; // Altere aqui
```

### Múltiplos Formatos

O sistema suporta:
- PNG (recomendado)
- JPG/JPEG
- SVG (melhor qualidade em qualquer tamanho)

Para usar SVG, altere a extensão:
```php
$logoPath = 'images/logo.svg';
```

## ✅ Checklist

- [ ] Logo preparado no formato correto
- [ ] Upload realizado para `public/images/logo.png`
- [ ] Permissões configuradas (644)
- [ ] Cache limpo (`php artisan view:clear`)
- [ ] Logo visível no sidebar
- [ ] Logo responsivo em diferentes telas

## 🆘 Troubleshooting

### Logo não aparece

1. **Verificar caminho**: Confirme que o arquivo está em `public/images/logo.png`
2. **Verificar permissões**: `chmod 644 public/images/logo.png`
3. **Limpar cache**: `php artisan view:clear && php artisan cache:clear`
4. **Verificar nome**: O arquivo deve ser exatamente `logo.png` (minúsculas)

### Logo muito grande/pequeno

1. Edite o CSS no arquivo `sidebar.blade.php`:
   ```blade
   <img src="{{ asset($logoPath) }}" alt="Equidade" class="h-12 w-auto">
   ```
   Ajuste `h-12` para o tamanho desejado (h-8, h-10, h-12, h-16)

### Logo cortado

1. Verifique a proporção da imagem original
2. Ajuste o CSS para manter proporção:
   ```blade
   class="h-10 w-auto" // Mantém proporção
   ```

## 📞 Suporte

Se precisar de ajuda, consulte:
- Documentação: `DEPLOY_HOSTINGER.md`
- Suporte técnico: Entre em contato com o desenvolvedor

---

**Nota**: O logo é uma representação visual importante da marca Equidade. Certifique-se de usar a versão oficial e atualizada do logo.

