<?php

namespace Rahpt\Ci4ModuleTheme;

use Rahpt\Ci4ModuleTheme\Support\ViewComponent;

/**
 * HookRegistry - Manages view hooks for modular extensions with priority ordering and structured components.
 */
class HookRegistry
{
    /**
     * @var array<string, array<int, array{priority: int, order: int, content: mixed}>>
     */
    protected static array $hooks = [];
    protected static int $insertionCounter = 0;

    /**
     * Registers a callback, ViewComponent, or content to a specific hook.
     * Lower priority numbers execute earlier (e.g. 10 runs before 100).
     */
    public static function register(string $hookName, mixed $content, int $priority = 100): void
    {
        if (!isset(self::$hooks[$hookName])) {
            self::$hooks[$hookName] = [];
        }

        self::$hooks[$hookName][] = [
            'priority' => $priority,
            'order'    => self::$insertionCounter++,
            'content'  => $content,
        ];
    }

    /**
     * Renders all content registered to a hook, executed in priority order.
     */
    public static function render(string $hookName, array $params = []): string
    {
        if (empty(self::$hooks[$hookName])) {
            return '';
        }

        // Stable sort by priority ASC, then insertion order ASC
        $items = self::$hooks[$hookName];
        usort($items, function ($a, $b) {
            if ($a['priority'] === $b['priority']) {
                return $a['order'] <=> $b['order'];
            }
            return $a['priority'] <=> $b['priority'];
        });

        $output = '';
        foreach ($items as $item) {
            $content = $item['content'];

            if ($content instanceof ViewComponent) {
                $output .= $content->render($params);
            } elseif (is_callable($content)) {
                $output .= call_user_func($content, $params);
            } elseif (is_object($content) && method_exists($content, 'render')) {
                $output .= $content->render($params);
            } else {
                $output .= (string)$content;
            }
        }

        return $output;
    }

    /**
     * Clears registered hooks (useful for testing or cache refresh).
     */
    public static function clear(?string $hookName = null): void
    {
        if ($hookName !== null) {
            unset(self::$hooks[$hookName]);
        } else {
            self::$hooks = [];
            self::$insertionCounter = 0;
        }
    }
}
