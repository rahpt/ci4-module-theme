<?php

namespace Rahpt\Ci4ModuleTheme;

/**
 * HookRegistry - Manages view hooks for modular extensions.
 */
class HookRegistry {

    protected static array $hooks = [];

    /**
     * Registers a callback or string content to a specific hook.
     */
    public static function register(string $hookName, $content): void {
        if (!isset(self::$hooks[$hookName])) {
            self::$hooks[$hookName] = [];
        }
        self::$hooks[$hookName][] = $content;
    }

    /**
     * Renders all content registered to a hook.
     */
    public static function render(string $hookName, array $params = []): string {
        if (!isset(self::$hooks[$hookName])) {
            return '';
        }

        $output = '';
        foreach (self::$hooks[$hookName] as $content) {
            if (is_callable($content)) {
                $output .= call_user_func($content, $params);
            } else {
                $output .= $content;
            }
        }

        return $output;
    }
}
