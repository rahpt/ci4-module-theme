# CodeIgniter 4 Module Theme Manager & Design System

[![Version](https://img.shields.io/badge/version-1.2.0-blue.svg)](https://github.com/rahpt/ci4-module-theme)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)  
[![PHP](https://img.shields.io/badge/php-%3E%3D8.1-brightgreen.svg)](https://php.net)

Sistema de gerenciamento de temas, layouts dinâmicos e biblioteca de componentes visuais (Design System) para módulos CodeIgniter 4. Conta com fábrica de UI (`UIComponentFactory`), suporte a `ViewComponent`, proteção CSP com Nonce criptográfico e allowlist de assets.

---

## 📋 Índice

- [Características](#-características)
- [Instalação](#-instalação)
- [Design System (UIComponentFactory)](#-design-system-uicomponentfactory)
- [Componentes de View (ViewComponent)](#-componentes-de-view-viewcomponent)
- [Gerenciamento de Assets e CSP Nonce](#-gerenciamento-de-assets-e-csp-nonce)
- [Uso Básico de Temas e Layouts](#-uso-básico-de-temas-e-layouts)
- [Breadcrumbs e Hooks de Layout](#-breadcrumbs-e-hooks-de-layout)
- [Helpers Disponíveis](#-helpers-disponíveis)
- [API Reference](#-api-reference)
- [Histórico de Versões](#-histórico-de-versões)
- [Licença](#-licença)

---

## ✨ Características

### Design System & Componentes UI
- ✅ **Fábrica de Componentes (`ui()`)** - Cards, Alertas, Badges, Modais, Tabelas com empty state e Loading spinners padronizados.
- ✅ **ViewComponent Fluente** - Renderizador modular desacoplado com passagem de dados e conversão para string (`__toString()`).
- ✅ **Empty State Reutilizável** - Telas elegantes de listas vazias com ícone, descrição e botão de ação.

### Segurança & Content Security Policy (CSP)
- ✅ **Suporte a CSP Nonce** - Injeção automática do atributo `nonce="..."` em scripts e folhas de estilo para atender às mais rigorosas políticas de CSP.
- ✅ **Allowlist de CDNs Externas** - Validação de integridade e bloqueio de scripts/CSS hospedados em hosts não autorizados (`allowHost()`).
- ✅ **Vendor Asset Registry** - Registro centralizado de bibliotecas de terceiros com dependências.

### Gerenciamento de Temas
- ✅ **Multi-Theme por Módulo** - Cada módulo pode especificar seu tema independente (`adminlte`, `bootstrap`, `custom`).
- ✅ **Hooks de Layout** - Injeção dinâmica de conteúdo antes/depois do layout e seções (`hook()`).
- ✅ **Breadcrumbs Integrados** - Marcação e renderização simplificada de navegação contextual.

---

## 🚀 Instalação

```bash
composer require rahpt/ci4-module-theme
```

---

## 🎨 Design System (`UIComponentFactory`)

O helper global `ui()` dá acesso à biblioteca de componentes de interface consistentes e acessíveis:

### 1. Cards
```php
<?= ui()->card('Visão Geral', '<p>Conteúdo do relatório...</p>', [
    'class' => 'card-outline card-primary',
    'tools' => '<button class="btn btn-sm btn-tool"><i class="fas fa-sync"></i></button>',
    'footer' => '<button class="btn btn-primary">Salvar</button>'
]) ?>
```

### 2. Alertas Acessíveis
```php
<?= ui()->alert('Operação realizada com sucesso!', 'success', true) ?>
```

### 3. Badges
```php
<?= ui()->badge('Ativo', 'success') ?>
<?= ui()->badge('Pendente', 'warning') ?>
```

### 4. Tabelas Responsivas com Empty State
```php
$headers = ['ID', 'Cliente', 'Status', 'Ações'];
$rows = [
    ['#1', 'Acme Inc', ui()->badge('Ativo', 'success'), '<a href="#">Editar</a>'],
    ['#2', 'Global Tech', ui()->badge('Inativo', 'secondary'), '<a href="#">Editar</a>']
];

<?= ui()->table($headers, $rows, ['class' => 'table table-hover table-striped']) ?>
```

### 5. Telas de Estado Vazio (Empty State)
Ideal para quando uma listagem não possui registros:
```php
<?= ui()->emptyState(
    'Nenhum contrato cadastrado',
    'Comece cadastrando o primeiro contrato de prestação de serviços da sua empresa.',
    '<a href="' . base_url('contratos/novo') . '" class="btn btn-primary"><i class="fas fa-plus mr-1"></i> Criar Contrato</a>',
    'file-contract'
) ?>
```

### 6. Modais Padronizados
```php
<?= ui()->modal(
    'modal-excluir',
    'Confirmar Exclusão',
    '<p>Tem certeza de que deseja remover este item permanentemente?</p>',
    ['footer' => '<button type="button" class="btn btn-danger">Excluir</button>']
) ?>
```

### 7. Indicador de Carregamento
```php
<?= ui()->loading('Carregando informações fiscais...') ?>
```

---

## 🧩 Componentes de View (`ViewComponent`)

Para isolar blocos de interface complexos em views reutilizáveis:

```php
use Rahpt\Ci4ModuleTheme\Support\ViewComponent;

// Criando e renderizando um componente
$component = ViewComponent::make('App\Modules\Contratos\Views\components\status_card', [
    'title' => 'Contratos Ativos',
    'total' => 42
]);

// Renderização direta ou em templates
echo $component->render(['highlight' => true]);

// Ou simplesmente interpolando como string
echo $component;
```

---

## 🛡️ Gerenciamento de Assets e CSP Nonce

### Proteção CSP com Nonce
No seu controller base ou filtro de segurança, configure o token Nonce:

```php
use Rahpt\Ci4ModuleTheme\ThemeManager;

// No Filter ou BaseController
$nonce = bin2hex(random_bytes(16));
ThemeManager::setNonce($nonce);

// Ao renderizar os scripts e estilos no layout:
echo theme_styles();  // <link rel="stylesheet" href="..." nonce="...">
echo theme_scripts(); // <script src="..." nonce="..."></script>
```

### Allowlist de CDNs Externas
Evite a injeção de assets de domínios não homologados:

```php
use Rahpt\Ci4ModuleTheme\Support\AssetRegistry;

// Permitir apenas origens confiáveis
AssetRegistry::allowHost('cdn.jsdelivr.net');
AssetRegistry::allowHost('cdnjs.cloudflare.com');

// Registrar biblioteca vendor
AssetRegistry::registerVendor('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js', [
    'type' => 'js',
    'version' => '4.4.0'
]);
```

### Assets de Módulos Locais
O helper `module_asset()` resolve com segurança o caminho de arquivos estáticos empacotados dentro de módulos:
```php
<img src="<?= module_asset('Contratos', 'images/logo.png') ?>" alt="Logo">
```

---

## 📖 Uso Básico de Temas e Layouts

### Definir Tema no Módulo
```php
// app/Modules/Contratos/Config/Module.php
class Module extends BaseModule
{
    public string $name = 'Contratos';
    public string $theme = 'adminlte'; // 'adminlte', 'main' ou personalizado
}
```

### No Controller
```php
use Rahpt\Ci4ModuleTheme\ThemeManager;

public function index()
{
    return view('App\Modules\Contratos\Views\index', [
        'layout' => ThemeManager::getModuleLayout('Contratos')
    ]);
}
```

### Na View
```php
<?= $this->extend($layout) ?>

<?= $this->section('content') ?>
    <div class="container-fluid">
        <?= ui()->card('Dashboard de Contratos', '<p>Bem-vindo!</p>') ?>
    </div>
<?= $this->endSection() ?>
```

---

## 🪝 Breadcrumbs e Hooks de Layout

```php
// No Controller
set_breadcrumb('Início', '/');
set_breadcrumb('Contratos', 'contratos');
set_breadcrumb('Detalhes');

// Registrar conteúdo dinâmico em hook
register_hook('before_layout', function($data) {
    return '<!-- Hook injetado antes do layout -->';
});

// Na View de Layout
<?= render_breadcrumbs() ?>
<?= hook('before_layout') ?>
```

---

## 🛠️ Helpers Disponíveis

| Função | Descrição |
| :--- | :--- |
| `ui()` | Fábrica de componentes visuais do Design System (`UIComponentFactory`). |
| `theme_styles()` | Renderiza as tags `<link>` dos estilos registrados com Nonce CSP. |
| `theme_scripts()` | Renderiza as tags `<script>` dos scripts registrados com Nonce CSP. |
| `add_style(string $href)` | Registra folha de estilo para o layout atual. |
| `add_script(string $src)` | Registra script JS para o layout atual. |
| `module_asset(string $module, string $path)` | Gera URL segura para asset localizado na pasta do módulo. |
| `set_breadcrumb(string $label, ?string $url)` | Adiciona etapa à trilha de navegação. |
| `render_breadcrumbs()` | Renderiza a trilha de breadcrumbs em HTML semântico. |
| `hook(string $name, array $params)` | Executa e renderiza os ouvintes do hook de view. |
| `register_hook(string $name, $content)` | Anexa conteúdo ou callback a um hook de view. |

---

## 🕒 Histórico de Versões

### [1.2.0] - 2026-09-26
- **Novo**: Design System nativo via `UIComponentFactory` e helper `ui()` (`card`, `alert`, `badge`, `table`, `modal`, `emptyState`, `loading`).
- **Novo**: Suporte completo a `ViewComponent` com renderização isolada e fluente.
- **Novo**: Suporte a Nonce CSP (`Content Security Policy`) em `ThemeManager` e `AssetRegistry`.
- **Novo**: Allowlist de domínios seguros (`allowHost`) e registro formal de vendors em `AssetRegistry`.
- **Novo**: Helper `module_asset()` para resolução de caminhos estáticos de módulos.

### [1.1.0] - 2026-02-16
- **Padronização**: Alinhamento com o ecossistema Rahpt v1.1.0.
- **Arquitetura**: Descoberta automática de views de tema via `Registrar`.
- **Suporte**: Refatoração do `HookRegistry`.

### [1.0.1] - 2026-02-15
- Estabilização inicial dos layouts base.

---

## 📄 Licença

MIT License. Desenvolvido por **Rahpt**.
