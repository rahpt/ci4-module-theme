<?php

namespace Rahpt\Ci4ModuleTheme\Support;

/**
 * Breadcrumbs - Manage and render breadcrumbs for the application.
 */
class Breadcrumbs
{
    protected static array $items = [];

    /**
     * Add a breadcrumb item.
     * 
     * @param string $label The display text
     * @param string|null $url The URL (null for active/last item)
     */
    public static function add(string $label, ?string $url = null): void
    {
        self::$items[] = [
            'label' => $label,
            'url'   => $url ? (str_starts_with($url, 'http') ? $url : base_url($url)) : null
        ];
    }

    /**
     * Set multiple breadcrumbs at once, clearing existing ones.
     */
    public static function set(array $breadcrumbs): void
    {
        self::$items = [];
        foreach ($breadcrumbs as $label => $url) {
            self::add($label, is_numeric($label) ? null : $url);
        }
    }

    /**
     * Clear all breadcrumbs.
     */
    public static function clear(): void
    {
        self::$items = [];
    }

    /**
     * Render breadcrumbs as HTML (AdminLTE 3 style).
     */
    public static function render(): string
    {
        if (empty(self::$items)) {
            // Tenta obter do menu automaticamente se nada foi definido
            if (function_exists('breadcrumb_from_menu')) {
                $menuItems = \breadcrumb_from_menu();
                if (count($menuItems) > 1) { // Mais do que apenas o 'Home'
                    foreach ($menuItems as $item) {
                        self::add($item['label'], $item['route']);
                    }
                }
            }
        }

        if (empty(self::$items)) {
            self::add('Home', '/');
        }

        $html = '<ol class="breadcrumb float-sm-right">';
        
        foreach (self::$items as $index => $item) {
            $isActive = ($index === count(self::$items) - 1);
            $class = $isActive ? 'breadcrumb-item active' : 'breadcrumb-item';
            
            $html .= '<li class="' . $class . '">';
            if ($item['url'] && !$isActive) {
                $html .= '<a href="' . $item['url'] . '">' . $item['label'] . '</a>';
            } else {
                $html .= $item['label'];
            }
            $html .= '</li>';
        }

        $html .= '</ol>';
        
        return $html;
    }
}
