<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Rahpt\Ci4ModuleTheme\ThemeManager;

class ThemeManagerTest extends TestCase
{
    public function testAddStyle()
    {
        ThemeManager::addStyle('test.css');
        $html = ThemeManager::renderStyles();
        $this->assertStringContainsString('test.css', $html);
    }

    public function testAddScript()
    {
        ThemeManager::addScript('test.js');
        $html = ThemeManager::renderScripts();
        $this->assertStringContainsString('test.js', $html);
    }
}
