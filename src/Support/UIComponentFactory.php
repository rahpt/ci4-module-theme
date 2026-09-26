<?php

namespace Rahpt\Ci4ModuleTheme\Support;

/**
 * UIComponentFactory - Standardized modular UI design system renderer for all Rahpt modules.
 */
class UIComponentFactory
{
    /**
     * Renders a modern container Card.
     */
    public function card(string $title, string $content, array $options = []): string
    {
        $class = $options['class'] ?? 'card-outline card-primary';
        $tools = $options['tools'] ?? '';
        $footer = isset($options['footer']) ? '<div class="card-footer">' . $options['footer'] . '</div>' : '';

        $titleHtml = $title !== '' ? '<div class="card-header"><h3 class="card-title">' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h3><div class="card-tools">' . $tools . '</div></div>' : '';

        return sprintf(
            '<div class="card %s shadow-sm">%s<div class="card-body">%s</div>%s</div>',
            htmlspecialchars($class, ENT_QUOTES, 'UTF-8'),
            $titleHtml,
            $content,
            $footer
        );
    }

    /**
     * Renders an accessible Alert box.
     */
    public function alert(string $message, string $type = 'info', bool $dismissible = true): string
    {
        $dismissBtn = $dismissible ? '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' : '';
        return sprintf(
            '<div class="alert alert-%s %s alert-dismissible fade show" role="alert">%s%s</div>',
            htmlspecialchars($type, ENT_QUOTES, 'UTF-8'),
            $dismissible ? '' : '',
            $dismissBtn,
            $message
        );
    }

    /**
     * Renders a Badge.
     */
    public function badge(string $text, string $type = 'primary'): string
    {
        return sprintf(
            '<span class="badge badge-%s">%s</span>',
            htmlspecialchars($type, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($text, ENT_QUOTES, 'UTF-8')
        );
    }

    /**
     * Renders a clean responsive HTML Table.
     */
    public function table(array $headers, array $rows, array $options = []): string
    {
        $class = $options['class'] ?? 'table table-hover table-striped';

        $thead = '<thead><tr>';
        foreach ($headers as $header) {
            $thead .= '<th>' . htmlspecialchars($header, ENT_QUOTES, 'UTF-8') . '</th>';
        }
        $thead .= '</tr></thead>';

        $tbody = '<tbody>';
        if (empty($rows)) {
            $tbody .= '<tr><td colspan="' . count($headers) . '" class="text-center text-muted py-4">Nenhum registro encontrado.</td></tr>';
        } else {
            foreach ($rows as $row) {
                $tbody .= '<tr>';
                foreach ($row as $cell) {
                    $tbody .= '<td>' . $cell . '</td>';
                }
                $tbody .= '</tr>';
            }
        }
        $tbody .= '</tbody>';

        return sprintf(
            '<div class="table-responsive"><table class="%s">%s%s</table></div>',
            htmlspecialchars($class, ENT_QUOTES, 'UTF-8'),
            $thead,
            $tbody
        );
    }

    /**
     * Renders a Modal dialog structure.
     */
    public function modal(string $id, string $title, string $body, array $options = []): string
    {
        $footer = $options['footer'] ?? '<button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>';
        return sprintf(
            '<div class="modal fade" id="%s" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog %s" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">%s</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body">%s</div>
                        <div class="modal-footer">%s</div>
                    </div>
                </div>
            </div>',
            htmlspecialchars($id, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($options['size'] ?? '', ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
            $body,
            $footer
        );
    }

    /**
     * Renders an empty state display with icon, title, description, and call to action.
     */
    public function emptyState(string $title, string $description, ?string $actionHtml = null, ?string $icon = 'folder-open'): string
    {
        $iconHtml = $icon ? '<div class="text-muted mb-3" style="font-size: 3rem;"><i class="fas fa-' . htmlspecialchars($icon, ENT_QUOTES, 'UTF-8') . '"></i></div>' : '';
        $action = $actionHtml ? '<div class="mt-3">' . $actionHtml . '</div>' : '';

        return sprintf(
            '<div class="text-center py-5 px-3 border rounded bg-light my-3">
                %s
                <h4 class="font-weight-bold text-dark">%s</h4>
                <p class="text-muted mb-0 mx-auto" style="max-width: 480px;">%s</p>
                %s
            </div>',
            $iconHtml,
            htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($description, ENT_QUOTES, 'UTF-8'),
            $action
        );
    }

    /**
     * Renders a loading spinner.
     */
    public function loading(string $text = 'Carregando...'): string
    {
        return sprintf(
            '<div class="d-flex align-items-center justify-content-center py-4">
                <div class="spinner-border text-primary mr-2" role="status"><span class="sr-only">Loading...</span></div>
                <span class="text-muted">%s</span>
            </div>',
            htmlspecialchars($text, ENT_QUOTES, 'UTF-8')
        );
    }
}
