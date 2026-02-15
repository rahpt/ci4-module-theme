<?php

namespace Rahpt\Ci4ModuleTheme;

use Rahpt\Ci4Module\ModuleRegistry;

/**
 * ThemeManager - Manages theme selection based on modules.json
 */
class ThemeManager {

    protected static array $styles = [];
    protected static array $scripts = [];

    /**
     * Returns the configured layout for a module.
     * If not defined, returns the default from config.
     */
    public static function getModuleLayout(string $module): string {
        $registry = service('modules');
        $available = $registry->getAvailableModules();
        $config = config(\Rahpt\Ci4Module\Config\Modules::class);
        
        // Determina o tema (Deste módulo, do config ou fallback 'adminlte')
        $theme = $available[$module]['theme'] ?? $config->defaultTheme ?? 'adminlte';
        
        // Caminho canônico usando o namespace PSR-4 completo do pacote de temas
        return "Rahpt\\Ci4ModuleTheme\\Views\\layouts\\{$theme}";
    }

    /**
     * Sets the module theme in modules.json
     */
    public static function setModuleTheme(string $module, string $theme): void {
        $registry = service('modules');
        $data = $registry->all($module);
        
        if (isset($data[$module])) {
            $data[$module]['theme'] = $theme;
            $registry->put($module, $data[$module]);
        }
    }

    /**
     * Adds a CSS file to the head.
     */
    public static function addStyle(string $href): void {
        if (!in_array($href, self::$styles)) {
            self::$styles[] = $href;
        }
    }

    /**
     * Adds a JS file to the footer.
     */
    public static function addScript(string $src): void {
        if (!in_array($src, self::$scripts)) {
            self::$scripts[] = $src;
        }
    }

    /**
     * Renders all registered styles as HTML link tags.
     */
    public static function renderStyles(): string {
        $html = '';
        foreach (self::$styles as $style) {
            $url = (str_starts_with($style, 'http')) ? $style : base_url($style);
            $html .= '<link rel="stylesheet" href="' . $url . '">' . PHP_EOL;
        }
        return $html;
    }

    /**
     * Renders all registered scripts as HTML script tags.
     */
    public static function renderScripts(): string {
        $html = '';
        foreach (self::$scripts as $script) {
            $url = (str_starts_with($script, 'http')) ? $script : base_url($script);
            $html .= '<script src="' . $url . '"></script>' . PHP_EOL;
        }
        return $html;
    }
}
