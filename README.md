# CodeIgniter 4 Module Theme Manager & Design System

[![Version](https://img.shields.io/badge/version-1.3.0-blue.svg)](https://github.com/rahpt/ci4-module-theme)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![PHP](https://img.shields.io/badge/php-%3E%3D8.1-brightgreen.svg)](https://php.net)
[![CodeIgniter](https://img.shields.io/badge/CodeIgniter-%3E%3D4.5-orange.svg)](https://codeigniter.com)

Sistema corporativo de temas, layouts dinâmicos e biblioteca de componentes visuais (Design System) para módulos CodeIgniter 4. Possui fábrica completa de UI (`UIComponentFactory` / `ui()`), suporte a `ViewComponent`, bloqueio estrito de hosts externos não autorizados em produção, proteção CSP com Nonce criptográfico e suporte a Subresource Integrity (SRI).

---

## 🏛️ Filosofia de Design e Apresentação

> **Módulos fornecem dados e regras de negócio; o Tema fornece a apresentação.**
>
> Evite gerar HTML bruto espalhado em controllers ou models. O `ci4-module-theme` oferece uma camada desacoplada de apresentação onde componentes visuais consistentes, acessíveis e padronizados são renderizados com segurança e isolamento.

---

## 📋 Índice

- [Características](#-características)
- [Instalação](#-instalação)
- [Design System (UIComponentFactory)](#-design-system-uicomponentfactory)
- [Componentes de View (ViewComponent)](#-componentes-de-view-viewcomponent)
- [Segurança de Assets e Bloqueio em Produção](#-segurança-de-assets-e-bloqueio-em-produção)
- [Proteção CSP Nonce e Subresource Integrity (SRI)](#-proteção-csp-nonce-e-subresource-integrity-sri)
- [Uso Básico de Temas e Layouts](#-uso-básico-de-temas-e-layouts)
- [Breadcrumbs e Hooks de Layout](#-breadcrumbs-e-hooks-de-layout)
- [Helpers Disponíveis](#-helpers-disponíveis)
- [Histórico de Versões](#-histórico-de-versões)
- [Licença](#-licença)

---

## ✨ Características

### Design System & Componentes UI
- ✅ **Fábrica de Componentes (`ui()`)** - Cards, Alertas acessíveis, Badges, Modais, Tabelas com suporte nativo a empty states e spinners de carregamento padronizados.
- ✅ **ViewComponent Desacoplado** - Renderizador modular fluente com passagem de dados encapsulados e conversão automática para string (`__toString()`).
- ✅ **Empty State Elegante** - Componente padronizado para feedback visual quando listagens não possuem registros cadastrados.

### Segurança em Produção & Zero-Trust
- ✅ **Bloqueio Estrito de CDNs Não Autorizadas em Produção** - Requisições para scripts ou estilos hospedados em origens não homologadas são **bloqueadas sumariamente** em ambiente de produção (`ThemeManager::validateAssetUrl()` retorna `null`), emitindo o evento `rahpt.asset.blocked`.
- ✅ **Proteção Contra Injeção em Runtime (`AssetRegistry`)** - Inclusão de hosts permitidos via `allowHostFromConfig()` a partir de configurações ou manifestos confiáveis, alertando e impedindo injeção dinâmica arbitrária em produção.
- ✅ **Bloqueio de Esquemas Maliciosos** - URLs contendo `javascript:`, `data:` ou `vbscript:` são neutralizadas antes da renderização.
- ✅ **Suporte a CSP Nonce** - Injeção automática do atributo `nonce="..."` em tags `<script>` e `<link>` para compatibilidade total com Content Security Policy estrita.
- ✅ **Subresource Integrity (SRI)** - Suporte a hashes de integridade em recursos externos para evitar contaminação por supply-chain attacks.

### Gerenciamento de Temas & Layouts
- ✅ **Multi-Theme por Módulo** - Cada módulo pode especificar seu tema independente (`adminlte`, `bootstrap`, `custom`).
- ✅ **Hooks de Layout em Camadas** - Injeção dinâmica de conteúdo e componentes antes e depois do layout (`hook()`).
- ✅ **Assets Isolados por Módulo** - Resolução segura de caminhos estáticos empacotados com `module_asset()`.

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

### 3. Badges de Status
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

### 7. Indicador de Carregamento (Loading)
```php
<?= ui()->loading('Carregando informações fiscais...') ?>
```

---

## 🧩 Componentes de View (`ViewComponent`)

Para isolar blocos de interface complexos em views reutilizáveis com propriedades tipadas:

```php
use Rahpt\Ci4ModuleTheme\Support\ViewComponent;

// Criando e renderizando um componente isolado
$component = ViewComponent::make('App\Modules\Contratos\Views\components\status_card', [
    'title' => 'Contratos Ativos',
    'total' => 42
]);

// Renderização em templates
echo $component->render(['highlight' => true]);

// Ou simplesmente interpolando como string
echo $component;
```

---

## 🛡️ Segurança de Assets e Bloqueio em Produção

### Bloqueio Automático em Produção
Em ambiente de produção (`ENVIRONMENT === 'production'`), qualquer tentativa de carregar assets de domínios externos não homologados é **rejeitada sumariamente**:
- A tag `<link>` ou `<script>` não é renderizada (retorna `null`).
- É registrado um log de alerta no sistema.
- O evento `rahpt.asset.blocked` é emitido para auditoria.

### Registro Confiável de CDNs Permitidas
Para homologar origens externas seguras, utilize a via confiável em arquivos de configuração ou inicialização:

```php
use Rahpt\Ci4ModuleTheme\Support\AssetRegistry;

// Caminho confiável para configuração e manifestos de módulos
AssetRegistry::allowHostFromConfig('cdn.jsdelivr.net');
AssetRegistry::allowHostFromConfig('cdnjs.cloudflare.com');

// Registrar biblioteca vendor homologada com versão
AssetRegistry::registerVendor('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js', [
    'type' => 'js',
    'version' => '4.4.0'
]);
```

> **Aviso de Segurança**: Chamar `AssetRegistry::allowHost()` dinamicamente em runtime durante uma requisição web em produção emitirá avisos de auditoria, desencorajando injeções arbitrárias de código.

---

## 🔒 Proteção CSP Nonce e Subresource Integrity (SRI)

### Nonce Criptográfico
Em seu filtro de segurança ou BaseController:

```php
use Rahpt\Ci4ModuleTheme\ThemeManager;

// Gerar e configurar o token Nonce único por requisição
$nonce = bin2hex(random_bytes(16));
ThemeManager::setNonce($nonce);

// Ao renderizar scripts e estilos no layout:
echo theme_styles();  // <link rel="stylesheet" href="..." nonce="...">
echo theme_scripts(); // <script src="..." nonce="..."></script>
```

---

## 📖 Uso Básico de Temas e Layouts

### Definir Tema no Módulo
```php
// app/Modules/Contratos/Config/Module.php
class Module extends BaseModule
{
    public string $name = 'Contratos';
    public string $theme = 'adminlte'; // 'adminlte', 'bootstrap' ou 'custom'
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
        <?= ui()->card('Dashboard de Contratos', '<p>Conteúdo do módulo...</p>') ?>
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

// Registrar hook de interface
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
| `theme_styles()` | Renderiza tags `<link>` dos estilos registrados com Nonce CSP. |
| `theme_scripts()` | Renderiza tags `<script>` dos scripts registrados com Nonce CSP. |
| `add_style(string $href)` | Registra folha de estilo para o layout atual com validação de host. |
| `add_script(string $src)` | Registra script JS para o layout atual com validação de host. |
| `module_asset(string $module, string $path)` | Gera URL segura para asset localizado na pasta do módulo. |
| `set_breadcrumb(string $label, ?string $url)` | Adiciona etapa à trilha de navegação contextual. |
| `render_breadcrumbs()` | Renderiza a trilha de breadcrumbs em HTML semântico. |
| `hook(string $name, array $params)` | Executa e renderiza os ouvintes do hook de view. |
| `register_hook(string $name, $content)` | Anexa conteúdo, callback ou `ViewComponent` a um hook de view. |

---

## 🕒 Histórico de Versões

### [1.3.0] - 2026-09-26
- **Segurança**: Rejeição estrita de hosts externos não autorizados em ambiente de produção (bloqueio determinístico do asset).
- **Segurança**: Blindagem do `AssetRegistry` contra injeção dinâmica de origens externas em runtime (`allowHostFromConfig`).
- **Segurança**: Disparo do evento `rahpt.asset.blocked` ao detectar tentativas de inclusão não homologada.
- **Melhoria**: Design System expandido com `UIComponentFactory` e helper `ui()`.
- **Melhoria**: Suporte a Content Security Policy (CSP) com tokens Nonce e Subresource Integrity (SRI).

### [1.2.0] - 2026-09-26
- **Novo**: Suporte completo a `ViewComponent` com renderização isolada e fluente.
- **Novo**: Allowlist de domínios seguros (`allowHost`) e registro formal de vendors em `AssetRegistry`.
- **Novo**: Helper `module_asset()` para resolução de caminhos estáticos de módulos.

### [1.0.1] - 2026-02-15
- Estabilização inicial dos layouts base.

---

## 📄 Licença

Distribuído sob a licença MIT. Veja `LICENSE` para mais detalhes.

Desenvolvido por **Rahpt**  
Mantido pela equipe Rahpt / CodeIgniter 4 Modular Platform.
