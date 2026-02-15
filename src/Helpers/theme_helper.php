<?php

use Rahpt\Ci4ModuleTheme\ThemeManager;

if (!function_exists('theme_styles')) {
    /**
     * Renders registered theme styles.
     */
    function theme_styles(): string {
        return ThemeManager::renderStyles();
    }
}

if (!function_exists('theme_scripts')) {
    /**
     * Renders registered theme scripts.
     */
    function theme_scripts(): string {
        return ThemeManager::renderScripts();
    }
}

if (!function_exists('add_style')) {
    /**
     * Adds a style to the theme.
     */
    function add_style(string $href): void {
        ThemeManager::addStyle($href);
    }
}

if (!function_exists('add_script')) {
    /**
     * Adds a script to the theme.
     */
    function add_script(string $src): void {
        ThemeManager::addScript($src);
    }
}

if (!function_exists('hook')) {
    /**
     * Renders a view hook.
     */
    function hook(string $name, array $params = []): string {
        return \Rahpt\Ci4ModuleTheme\HookRegistry::render($name, $params);
    }
}

if (!function_exists('register_hook')) {
    /**
     * Registers content to a view hook.
     */
    function register_hook(string $name, $content): void {
        \Rahpt\Ci4ModuleTheme\HookRegistry::register($name, $content);
    }
}

if (!function_exists('module_asset')) {
    /**
     * Returns the URL for a module asset.
     */
    function module_asset(string $module, string $path): string {
        return base_url('modules/' . strtolower($module) . '/' . ltrim($path, '/'));
    }
}

if (!function_exists('set_breadcrumb')) {
    /**
     * Adds an item to the breadcrumbs.
     */
    function set_breadcrumb(string $label, ?string $url = null): void {
        \Rahpt\Ci4ModuleTheme\Support\Breadcrumbs::add($label, $url);
    }
}

if (!function_exists('render_breadcrumbs')) {
    /**
     * Renders the breadcrumbs HTML.
     */
    function render_breadcrumbs(): string {
        return \Rahpt\Ci4ModuleTheme\Support\Breadcrumbs::render();
    }
}
