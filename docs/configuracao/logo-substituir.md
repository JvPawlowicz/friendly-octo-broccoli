# Como Substituir o Logo do Sistema

## 📍 Locais onde o logo aparece:

1. **Sidebar** (menu lateral) - `resources/views/components/layout/sidebar.blade.php`
2. **Componente Application Logo** - `resources/views/components/application-logo.blade.php`
3. **Página de Login** - `resources/views/auth/login.blade.php` (atualmente usa texto)
4. **Página Inicial** - `resources/views/welcome.blade.php` (atualmente usa texto)

## 📝 Passos para substituir:

### 1. Adicionar o arquivo do logo

Coloque seu arquivo de logo em:
```
public/images/logo.png
```

**Formatos suportados:** PNG, SVG, JPG, WEBP

**Tamanhos recomendados:**
- Logo horizontal: 200x60px
- Logo quadrado: 60x60px
- Logo vertical: 60x120px

### 2. Arquivos que serão atualizados automaticamente

Os seguintes arquivos já foram preparados para usar o logo:
- ✅ `resources/views/components/application-logo.blade.php` - Componente principal
- ✅ `resources/views/components/layout/sidebar.blade.php` - Sidebar
- ✅ `resources/views/auth/login.blade.php` - Login (opcional)
- ✅ `resources/views/welcome.blade.php` - Landing page (opcional)

### 3. Como funciona

O sistema verifica se existe `public/images/logo.png`:
- **Se existir:** Usa a imagem do logo
- **Se não existir:** Usa o texto do nome do sistema como fallback

## 🎨 Personalização adicional

Se quiser usar um caminho diferente ou nome de arquivo diferente, edite:

```blade
{{-- Em application-logo.blade.php --}}
@if(file_exists(public_path('images/logo.png')))
    <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" {{ $attributes }}>
@else
    {{-- Fallback para texto --}}
@endif
```

## ✅ Verificação

Após adicionar o logo:
1. Limpe o cache: `php artisan view:clear`
2. Verifique se o arquivo existe: `ls public/images/logo.png`
3. Acesse o sistema e verifique se o logo aparece

