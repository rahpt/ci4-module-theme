<?php

namespace Rahpt\Ci4ModuleTheme\Support;

/**
 * ViewComponent - Represents a structured, safe view component for hooks and layouts.
 */
class ViewComponent
{
    protected string $view;
    protected array $data;

    public function __construct(string $view, array $data = [])
    {
        $this->view = $view;
        $this->data = $data;
    }

    /**
     * Factory method for clean chainable declaration:
     * ViewComponent::make('alerts/system', $data)
     */
    public static function make(string $view, array $data = []): static
    {
        return new static($view, $data);
    }

    /**
     * Renders the view component with CodeIgniter view() helper.
     */
    public function render(array $extraData = []): string
    {
        $mergedData = array_merge($this->data, $extraData);
        if (function_exists('view')) {
            try {
                return view($this->view, $mergedData);
            } catch (\Throwable $e) {
                log_message('error', "Failed to render ViewComponent [{$this->view}]: " . $e->getMessage());
                return '';
            }
        }
        return '';
    }

    public function __toString(): string
    {
        return $this->render();
    }
}
