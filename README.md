# CodeIgniter 4 Module Theme Manager

[![Version](https://img.shields.io/badge/version-1.0.1-blue.svg)](https://github.com/rahpt/ci4-module-theme)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)  
[![PHP](https://img.shields.io/badge/php-%3E%3D8.1-brightgreen.svg)](https://php.net)

Sistema de gerenciamento de temas para módulos CodeIgniter 4. Permite que cada módulo use um tema diferente e fornece helpers para renderização de views.

---

## 📋 Características

- ✅ **Multi-Theme** - Cada módulo pode ter seu próprio tema
- ✅ **Layout Dinâmico** - Troca automática de layout por módulo
- ✅ **Theme Hooks** - Sistema de eventos para extensibilidade
- ✅ **Helper Functions** - Funções convenientes para views
- ✅ **Default Themes** - AdminLTE, Bootstrap e custom

---

## 🚀 Instalação

```bash
composer require rahpt/ci4-module-theme
```

---

## 📖 Uso Básico

### Definir Tema no Módulo

```php
// app/Modules/Dashboard/Config/Module.php
class Module extends BaseModule
{
    public string $name = 'Dashboard';
    public string $theme = 'adminlte';  // Define o tema
}
```

### Obter Layout do Módulo

```php
// No Controller
use Rahpt\Ci4ModuleTheme\ThemeManager;

public function index()
{
    return view('Dashboard\index', [
        'layout' => ThemeManager::getModuleLayout('Dashboard')
    ]);
}
```

### Na View

```php
<!-- app/Modules/Dashboard/Views/index.php -->
<?= $this->extend($layout) ?>

<?= $this->section('content') ?>
    <h1>Bem-vindo ao Dashboard</h1>
<?= $this->endSection() ?>
```

---

## 🎨 Temas Disponíveis

### 1. AdminLTE (Padrão)

```php
public string $theme = 'adminlte';
```

**Layout**: `app/Views/layouts/adminlte.php`

**Características**:
- Sidebar navigation
- Top navbar
- Footer
- Responsivo
- Dark/Light modes

### 2. Bootstrap

```php
public string $theme = 'bootstrap';
```

**Layout**: `app/Views/layouts/bootstrap.php`

**Características**:
- Navbar superior
- Container-based
- Grid system
- Componentes padrão

### 3. Custom

```php
public string $theme = 'meu-tema-custom';
```

**Layout**: `app/Views/layouts/meu-tema-custom.php`

---

## 🔧 API Reference

### ThemeManager::getModuleLayout()

Retorna o caminho do layout para um módulo específico.

```php
ThemeManager::getModuleLayout(string $moduleName): string
```

**Exemplo**:
```php
$layout = ThemeManager::getModuleLayout('Dashboard');
// 'layouts/adminlte'
```

### ThemeManager::getTheme()

Retorna o tema configurado para um módulo.

```php
ThemeManager::getTheme(string $moduleName): string
```

**Exemplo**:
```php
$theme = ThemeManager::getTheme('Dashboard');
// 'adminlte'
```

---

## 🎯 Criando Temas Customizados

### 1. Criar Layout

**`app/Views/layouts/meu-tema.php`**:
```php
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title><?= $this->renderSection('title') ?> - Meu App</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/meu-tema.css') ?>">
    <?= $this->renderSection('styles') ?>
</head>
<body>
    <!-- Header -->
    <header>
        <nav>
            <!-- Menu aqui -->
        </nav>
    </header>

    <!-- Main Content -->
    <main>
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Footer -->
    <footer>
        © <?= date('Y') ?> Meu App
    </footer>

    <script src="<?= base_url('assets/js/meu-tema.js') ?>"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
```

### 2. Configurar no Módulo

```php
class Module extends BaseModule
{
    public string $theme = 'meu-tema';
}
```

### 3. Usar na View

```php
<?= $this->extend($layout) ?>

<?= $this->section('title') ?>
    Dashboard
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="dashboard">
        <h1>Dashboard</h1>
        <!-- Conteúdo -->
    </div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
    <script src="<?= base_url('assets/js/dashboard.js') ?>"></script>
<?= $this->endSection() ?>
```

---

## 🪝 Theme Hooks (Events)

### HookRegistry

Sistema de hooks para extensibilidade.

```php
use Rahpt\Ci4ModuleTheme\HookRegistry;

// Registrar hook
HookRegistry::register('before_layout', function($data) {
    // Adicionar variável global
    $data['app_version'] = '1.0.0';
    return $data;
});

// Executar hooks
$data = HookRegistry::execute('before_layout', $viewData);
```

### Hooks Disponíveis

- `before_layout` - Antes de renderizar layout
- `after_layout` - Após renderizar layout
- `before_content` - Antes do conteúdo
- `after_content` - Após o conteúdo

---

## 📦 Estrutura de Temas

```
app/Views/layouts/
├── adminlte.php           # Layout AdminLTE
├── bootstrap.php          # Layout Bootstrap
├── custom/
│   ├── theme1.php
│   ├── theme2.php
│   └── shared/
│       ├── header.php
│       ├── sidebar.php
│       └── footer.php
└── partials/
    ├── breadcrumbs.php
    ├── alerts.php
    └── pagination.php
```

---

## 🎨 Exemplo Completo: Layout AdminLTE

```php
<!-- app/Views/layouts/adminlte.php -->
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $this->renderSection('title', true) ?> | Admin</title>

    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <?= $this->renderSection('styles') ?>
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('logout') ?>">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Main Sidebar -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="<?= base_url() ?>" class="brand-link">
                <span class="brand-text font-weight-light">Admin Panel</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column">
                        <?php
                        use Rahpt\Ci4ModuleNav\MenuRegistry;
                        foreach (MenuRegistry::all() as $item):
                        ?>
                            <li class="nav-item">
                                <a href="<?= base_url($item['url']) ?>" class="nav-link">
                                    <i class="nav-icon <?= $item['icon'] ?? 'fas fa-circle' ?>"></i>
                                    <p><?= $item['label'] ?></p>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header (Breadcrumbs) -->
            <div class="content-header">
                <div class="container-fluid">
                    <?= $this->renderSection('content-header') ?>
                    <?= render_breadcrumbs() ?>
                </div>
            </div>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <?= $this->renderSection('content') ?>
                </div>
            </section>
        </div>

        <!-- Footer -->
        <footer class="main-footer">
            <strong>© <?= date('Y') ?> Meu App.</strong> Todos os direitos reservados.
        </footer>
    </div>

    <!-- AdminLTE JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    
    <?= $this->renderSection('scripts') ?>
</body>
</html>
```

---

## 🔧 Configuração Avançada

### Tema Diferente por Ambiente

```php
class Module extends BaseModule
{
    public string $theme;
    
    public function __construct()
    {
        parent::__construct();
        
        // Desenvolvimento: tema simples
        // Produção: tema completo
        $this->theme = ENVIRONMENT === 'production' 
            ? 'adminlte' 
            : 'bootstrap';
    }
}
```

### Tema Baseado em Permissões

```php
class Module extends BaseModule
{
    public string $theme;
    
    public function __construct()
    {
        parent::__construct();
        
        // Admin: tema completo
        // User: tema simplificado
        $this->theme = auth()->user()->isAdmin() 
            ? 'adminlte-full' 
            : 'adminlte-simple';
    }
}
```

---

## 🧪 Testes

```bash
composer test
```

---

## 📄 Licença

MIT License

---

## 👏 Créditos

Desenvolvido por **Rahpt**

---

**Versão**: 1.0.1  
**Última Atualização**: 2026-02-15
