<?php

namespace Rahpt\Ci4ModuleTheme;

use InvalidArgumentException;

/**
 * ThemeManager - Manages theme selection, asset inclusion, and layout resolution.
 */
class ThemeManager
{
    protected static array $styles = [];
    protected static array $scripts = [];

    /**
     * Registered/allowed themes list.
     */
    protected static array $registeredThemes = ['adminlte', 'main'];

    /**
     * Registers a new allowed theme layout.
     */
    public static function registerTheme(string $theme): void
    {
        $theme = strtolower(trim($theme));
        if (!in_array($theme, self::$registeredThemes, true)) {
            self::$registeredThemes[] = $theme;
        }
    }

    /**
     * Returns list of all registered theme names.
     */
    public static function getRegisteredThemes(): array
    {
        return self::$registeredThemes;
    }

    /**
     * Returns the configured layout for a module.
     * Ensures strict theme validation with safe fallback to 'adminlte'.
     */
    public static function getModuleLayout(string $module): string
    {
        $registry = service('modules');
        $available = $registry->getAvailableModules();
        $config = config(\Rahpt\Ci4Module\Config\Modules::class);

        $requestedTheme = $available[$module]['theme'] ?? $config->defaultTheme ?? 'adminlte';
        $theme = strtolower(trim($requestedTheme));

        // Strict name validation and allowlist check
        if (!preg_match('/^[a-z0-9_\-]+$/', $theme) || !in_array($theme, self::$registeredThemes, true)) {
            $theme = 'adminlte';
        }

        return "Rahpt\\Ci4ModuleTheme\\Views\\layouts\\{$theme}";
    }

    /**
     * Sets the module theme in modules.json after validating theme identifier.
     */
    public static function setModuleTheme(string $module, string $theme): void
    {
        $theme = strtolower(trim($theme));
        if (!preg_match('/^[a-z0-9_\-]+$/', $theme)) {
            throw new InvalidArgumentException("Invalid theme name: '{$theme}'.");
        }

        $registry = service('modules');
        $data = $registry->all($module);

        if (isset($data[$module])) {
            $data[$module]['theme'] = $theme;
            $registry->put($module, $data[$module]);
        }
    }

    /**
     * Adds a CSS file to the head.
     * Rejects dangerous URI schemes (e.g. javascript:, data:).
     */
    public static function addStyle(string $href): void
    {
        $validated = self::validateAssetUrl($href);
        if ($validated !== null && !in_array($validated, self::$styles, true)) {
            self::$styles[] = $validated;
        }
    }

    /**
     * Adds a JS file to the footer.
     * Rejects dangerous URI schemes (e.g. javascript:, data:).
     */
    public static function addScript(string $src): void
    {
        $validated = self::validateAssetUrl($src);
        if ($validated !== null && !in_array($validated, self::$scripts, true)) {
            self::$scripts[] = $validated;
        }
    }

    /**
     * Validates that an asset URL is safe. Rejects javascript: or data: URIs.
     */
    protected static function validateAssetUrl(string $url): ?string
    {
        $trimmed = trim($url);

        // Disallow dangerous URI schemes
        if (preg_match('/^(javascript|data|vbscript):/i', $trimmed)) {
            log_message('warning', "Blocked dangerous asset URL: {$trimmed}");
            return null;
        }

        return $trimmed;
    }

    /**
     * Renders all registered styles as HTML link tags with attribute escaping.
     */
    public static function renderStyles(): string
    {
        $html = '';
        foreach (self::$styles as $style) {
            $url = (str_starts_with($style, 'http://') || str_starts_with($style, 'https://') || str_starts_with($style, '//'))
                ? $style
                : base_url($style);

            $escapedUrl = function_exists('esc') ? esc($url, 'attr') : htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
            $html .= '<link rel="stylesheet" href="' . $escapedUrl . '">' . PHP_EOL;
        }
        return $html;
    }

    /**
     * Renders all registered scripts as HTML script tags with attribute escaping.
     */
    public static function renderScripts(): string
    {
        $html = '';
        foreach (self::$scripts as $script) {
            $url = (str_starts_with($script, 'http://') || str_starts_with($script, 'https://') || str_starts_with($script, '//'))
                ? $script
                : base_url($script);

            $escapedUrl = function_exists('esc') ? esc($url, 'attr') : htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
            $html .= '<script src="' . $escapedUrl . '"></script>' . PHP_EOL;
        }
        return $html;
    }
}
