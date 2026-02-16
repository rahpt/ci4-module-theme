<?php

namespace Rahpt\Ci4ModuleTheme\Config;

/**
 * Registrar - Autoconfiguração de componentes do pacote ci4-module-theme no CodeIgniter 4.
 */
class Registrar
{
    /**
     * Registra o namespace 'Theme' de forma redundante para garantir a descoberta das views.
     */
    public static function Autoload(): array
    {
        return [
            'psr4' => [
                'Theme' => realpath(dirname(__DIR__) . '/Views'),
            ],
        ];
    }

    public static function View(): array
    {
        return [
            'namespaces' => [
                'Theme' => realpath(dirname(__DIR__) . '/Views'),
            ],
        ];
    }
}
