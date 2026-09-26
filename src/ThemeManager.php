<?php

namespace Rahpt\Ci4ModuleTheme;

use InvalidArgumentException;
use Rahpt\Ci4ModuleTheme\Support\AssetRegistry;

/**
 * ThemeManager - Manages theme selection, asset inclusion, CSP compliance, and layout resolution.
 */
class ThemeManager
{
    /**
     * @var array<string, array{url: string, integrity: ?string, crossorigin: ?string, nonce: ?string}>
     */
    protected static array $styles = [];

    /**
     * @var array<string, array{url: string, integrity: ?string, crossorigin: ?string, nonce: ?string}>
     */
    protected static array $scripts = [];

    /**
     * Registered/allowed themes list.
     */
    protected static array $registeredThemes = ['adminlte', 'main'];

    /**
     * Sets or gets the active CSP nonce.
     */
    public static function setNonce(?string $nonce): void
    {
        AssetRegistry::setNonce($nonce);
    }

    public static function getNonce(): ?string
    {
        return AssetRegistry::getNonce();
    }

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
     *
     * @param string $href URL or relative path
     * @param array $options ['integrity' => null, 'crossorigin' => null, 'nonce' => null]
     */
    public static function addStyle(string $href, array $options = []): void
    {
        $validated = self::validateAssetUrl($href);
        if ($validated !== null && !isset(self::$styles[$validated])) {
            self::$styles[$validated] = [
                'url'         => $validated,
                'integrity'   => $options['integrity'] ?? null,
                'crossorigin' => $options['crossorigin'] ?? ($options['integrity'] ?? null ? 'anonymous' : null),
                'nonce'       => $options['nonce'] ?? null,
            ];
        }
    }

    /**
     * Adds a JS file to the footer.
     * Rejects dangerous URI schemes (e.g. javascript:, data:).
     *
     * @param string $src URL or relative path
     * @param array $options ['integrity' => null, 'crossorigin' => null, 'nonce' => null]
     */
    public static function addScript(string $src, array $options = []): void
    {
        $validated = self::validateAssetUrl($src);
        if ($validated !== null && !isset(self::$scripts[$validated])) {
            self::$scripts[$validated] = [
                'url'         => $validated,
                'integrity'   => $options['integrity'] ?? null,
                'crossorigin' => $options['crossorigin'] ?? ($options['integrity'] ?? null ? 'anonymous' : null),
                'nonce'       => $options['nonce'] ?? null,
            ];
        }
    }

    /**
     * Validates that an asset URL is safe. Rejects javascript: or data: URIs and checks host allowlist.
     */
    protected static function validateAssetUrl(string $url): ?string
    {
        $trimmed = trim($url);

        // Disallow dangerous URI schemes
        if (preg_match('/^(javascript|data|vbscript):/i', $trimmed)) {
            log_message('warning', "Blocked dangerous asset URL: {$trimmed}");
            return null;
        }

        // Check host against trusted CDN allowlist if external
        if (!AssetRegistry::isHostAllowed($trimmed)) {
            log_message('warning', "Asset URL uses unapproved external host: {$trimmed}");
        }

        return $trimmed;
    }

    /**
     * Renders all registered styles as HTML link tags with attribute escaping and CSP attributes.
     */
    public static function renderStyles(): string
    {
        $html = '';
        $globalNonce = self::getNonce();

        // Render vendor CSS assets registered via AssetRegistry
        foreach (AssetRegistry::getVendorAssets('css') as $vendor) {
            $escapedUrl = self::escapeAttr($vendor['url']);
            $nonceAttr = $globalNonce ? ' nonce="' . self::escapeAttr($globalNonce) . '"' : '';
            $sriAttr = !empty($vendor['integrity']) ? ' integrity="' . self::escapeAttr($vendor['integrity']) . '" crossorigin="' . self::escapeAttr($vendor['crossorigin'] ?? 'anonymous') . '"' : '';
            $html .= '<link rel="stylesheet" href="' . $escapedUrl . '"' . $sriAttr . $nonceAttr . '>' . PHP_EOL;
        }

        // Render module styles
        foreach (self::$styles as $asset) {
            $rawUrl = $asset['url'];
            $url = (str_starts_with($rawUrl, 'http://') || str_starts_with($rawUrl, 'https://') || str_starts_with($rawUrl, '//'))
                ? $rawUrl
                : base_url($rawUrl);

            $escapedUrl = self::escapeAttr($url);
            $nonce = $asset['nonce'] ?? $globalNonce;
            $nonceAttr = $nonce ? ' nonce="' . self::escapeAttr($nonce) . '"' : '';
            $sriAttr = !empty($asset['integrity']) ? ' integrity="' . self::escapeAttr($asset['integrity']) . '" crossorigin="' . self::escapeAttr($asset['crossorigin'] ?? 'anonymous') . '"' : '';

            $html .= '<link rel="stylesheet" href="' . $escapedUrl . '"' . $sriAttr . $nonceAttr . '>' . PHP_EOL;
        }

        return $html;
    }

    /**
     * Renders all registered scripts as HTML script tags with attribute escaping and CSP attributes.
     */
    public static function renderScripts(): string
    {
        $html = '';
        $globalNonce = self::getNonce();

        // Render vendor JS assets registered via AssetRegistry
        foreach (AssetRegistry::getVendorAssets('js') as $vendor) {
            $escapedUrl = self::escapeAttr($vendor['url']);
            $nonceAttr = $globalNonce ? ' nonce="' . self::escapeAttr($globalNonce) . '"' : '';
            $sriAttr = !empty($vendor['integrity']) ? ' integrity="' . self::escapeAttr($vendor['integrity']) . '" crossorigin="' . self::escapeAttr($vendor['crossorigin'] ?? 'anonymous') . '"' : '';
            $html .= '<script src="' . $escapedUrl . '"' . $sriAttr . $nonceAttr . '></script>' . PHP_EOL;
        }

        // Render module scripts
        foreach (self::$scripts as $asset) {
            $rawUrl = $asset['url'];
            $url = (str_starts_with($rawUrl, 'http://') || str_starts_with($rawUrl, 'https://') || str_starts_with($rawUrl, '//'))
                ? $rawUrl
                : base_url($rawUrl);

            $escapedUrl = self::escapeAttr($url);
            $nonce = $asset['nonce'] ?? $globalNonce;
            $nonceAttr = $nonce ? ' nonce="' . self::escapeAttr($nonce) . '"' : '';
            $sriAttr = !empty($asset['integrity']) ? ' integrity="' . self::escapeAttr($asset['integrity']) . '" crossorigin="' . self::escapeAttr($asset['crossorigin'] ?? 'anonymous') . '"' : '';

            $html .= '<script src="' . $escapedUrl . '"' . $sriAttr . $nonceAttr . '></script>' . PHP_EOL;
        }

        return $html;
    }

    /**
     * Helper to safely escape attribute values.
     */
    protected static function escapeAttr(string $value): string
    {
        return function_exists('esc') ? esc($value, 'attr') : htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
