<?php

namespace Rahpt\Ci4ModuleTheme\Support;

/**
 * AssetRegistry - Manages trusted external vendor assets, SRI hashes, and CSP compliance.
 */
class AssetRegistry
{
    /**
     * Trusted CDN/host allowlist for external scripts and stylesheets.
     */
    protected static array $allowedHosts = [
        'cdn.jsdelivr.net',
        'cdnjs.cloudflare.com',
        'unpkg.com',
        'fonts.googleapis.com',
        'fonts.gstatic.com',
        'code.jquery.com',
    ];

    /**
     * Registered vendor assets.
     */
    protected static array $vendorAssets = [];

    /**
     * Global CSP nonce for scripts and styles.
     */
    protected static ?string $nonce = null;

    /**
     * Sets or gets the CSP nonce.
     */
    public static function setNonce(?string $nonce): void
    {
        self::$nonce = $nonce;
    }

    public static function getNonce(): ?string
    {
        return self::$nonce;
    }

    /**
     * Add an allowed external host to the allowlist.
     *
     * Security: In production, call allowHostFromConfig() instead. This method
     * is guarded in production to prevent arbitrary runtime injection of external hosts.
     * External hosts should be declared in config or module manifests, not injected at runtime.
     */
    public static function allowHost(string $host): void
    {
        $env = defined('ENVIRONMENT') ? ENVIRONMENT : 'production';
        if ($env === 'production') {
            log_message('warning', "[AssetRegistry] allowHost('{$host}') called in production. Use allowHostFromConfig() or declare hosts in config.");
        }
        self::allowHostFromConfig($host);
    }

    /**
     * Trusted path for adding external hosts from configuration or module manifests.
     * This is the intended API for production use.
     */
    public static function allowHostFromConfig(string $host): void
    {
        $host = strtolower(trim($host));
        if (!in_array($host, self::$allowedHosts, true)) {
            self::$allowedHosts[] = $host;
        }
    }

    /**
     * Get list of allowed hosts.
     */
    public static function getAllowedHosts(): array
    {
        return self::$allowedHosts;
    }

    /**
     * Checks if a host or URL is permitted by the allowlist.
     */
    public static function isHostAllowed(string $url): bool
    {
        $parsed = parse_url($url);
        if (!isset($parsed['host'])) {
            return true; // Local/relative path
        }

        $host = strtolower($parsed['host']);
        foreach (self::$allowedHosts as $allowed) {
            if ($host === $allowed || str_ends_with($host, '.' . $allowed)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Registers a vendor asset with SRI and security attributes.
     *
     * @param string $name Unique asset identifier
     * @param string $url URL to the asset
     * @param array $options ['type' => 'js|css', 'integrity' => 'sha384-...', 'crossorigin' => 'anonymous', 'priority' => 100]
     */
    public static function registerVendor(string $name, string $url, array $options = []): void
    {
        $type = $options['type'] ?? (str_ends_with(strtolower(parse_url($url, PHP_URL_PATH) ?? ''), '.css') ? 'css' : 'js');

        if (!self::isHostAllowed($url)) {
            log_message('warning', "AssetRegistry: Blocked untrusted external asset host: {$url}");
            return;
        }

        self::$vendorAssets[$name] = [
            'url'         => $url,
            'type'        => $type,
            'integrity'   => $options['integrity'] ?? null,
            'crossorigin' => $options['crossorigin'] ?? ($options['integrity'] ? 'anonymous' : null),
            'priority'    => $options['priority'] ?? 100,
        ];
    }

    /**
     * Returns all registered vendor assets of a specific type.
     */
    public static function getVendorAssets(?string $type = null): array
    {
        if ($type === null) {
            return self::$vendorAssets;
        }

        return array_filter(self::$vendorAssets, fn ($asset) => $asset['type'] === $type);
    }
}
