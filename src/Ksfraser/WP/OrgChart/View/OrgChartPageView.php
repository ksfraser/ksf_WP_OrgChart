<?php

declare(strict_types=1);

namespace Ksfraser\WP\OrgChart\View;

class OrgChartPageView
{
    public const ACTION_SEARCH = 'search';
    public const ACTION_LOAD = 'load';
    public const ACTION_CONTEXT = 'context';

    private string $nonce = '';
    private array $employeeData = [];
    private array $config = [];

    public function __construct()
    {
        $this->config = [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonceField' => 'ksf_orgchart_nonce',
            'defaultLevelsUp' => 2,
            'defaultLevelsDown' => 2,
            'minSearchLength' => 2,
        ];
    }

    public function setNonce(string $nonce): void
    {
        $this->nonce = $nonce;
    }

    public function setEmployeeData(array $data): void
    {
        $this->employeeData = $data;
    }

    public function renderContainer(): string
    {
        $html = '<div class="ksf-orgchart-container" data-nonce="' . esc_attr($this->nonce) . '">';
        $html .= $this->renderToolbar();
        $html .= $this->renderContextToggle();
        $html .= $this->renderOrgChartSvg();
        $html .= '</div>';
        return $html;
    }

    private function renderToolbar(): string
    {
        $searchId = 'ksf-orgchart-search-' . uniqid();
        $levelsUpId = 'ksf-orgchart-levels-up';
        $levelsDownId = 'ksf-orgchart-levels-down';

        return <<<HTML
<div class="ksf-orgchart-toolbar">
    <div class="ksf-orgchart-search">
        <label for="{$searchId}">Search:</label>
        <input type="text" id="{$searchId}" class="ksf-orgchart-search-input" 
               placeholder="Employee name or title..." 
               data-nonce-field="{$this->config['nonceField']}">
        <div class="ksf-orgchart-search-results" id="{$searchId}-results"></div>
    </div>
    <div class="ksf-orgchart-levels">
        <div class="ksf-orgchart-level-control">
            <label for="{$levelsUpId}">Levels Up:</label>
            <input type="number" id="{$levelsUpId}" class="ksf-orgchart-levels-input" 
                   value="{$this->config['defaultLevelsUp']}" min="0" max="5">
        </div>
        <div class="ksf-orgchart-level-control">
            <label for="{$levelsDownId}">Levels Down:</label>
            <input type="number" id="{$levelsDownId}" class="ksf-orgchart-levels-input" 
                   value="{$this->config['defaultLevelsDown']}" min="0" max="10">
        </div>
        <button type="button" class="ksf-orgchart-refresh-btn button button-primary">
            Refresh
        </button>
    </div>
</div>
HTML;
    }

    private function renderContextToggle(): string
    {
        return <<<HTML
<div class="ksf-orgchart-context-toggle">
    <label>View Mode:</label>
    <div class="ksf-orgchart-context-buttons">
        <button type="button" class="ksf-orgchart-context-btn active" data-context="hrm">
            HR Org Chart
        </button>
        <button type="button" class="ksf-orgchart-context-btn" data-context="project">
            Project Team
        </button>
    </div>
    <div class="ksf-orgchart-context-project" style="display:none;">
        <label for="ksf-orgchart-project-id">Project:</label>
        <select id="ksf-orgchart-project-id" class="ksf-orgchart-project-select">
            <option value="">-- Select Project --</option>
        </select>
    </div>
</div>
HTML;
    }

    private function renderOrgChartSvg(): string
    {
        return <<<HTML
<div class="ksf-orgchart-wrapper">
    <div class="ksf-orgchart-svg-container">
        <svg class="ksf-orgchart-svg" viewBox="0 0 800 600">
            <defs>
                <marker id="orgchart-arrow" markerWidth="10" markerHeight="10" 
                        refX="9" refY="3" orient="auto" markerUnits="strokeWidth">
                    <path d="M0,0 L0,6 L9,3 z" fill="#666"/>
                </marker>
            </defs>
            <g class="ksf-orgchart-nodes"></g>
            <g class="ksf-orgchart-connectors"></g>
        </svg>
    </div>
    <div class="ksf-orgchart-zoom-controls">
        <button type="button" class="ksf-orgchart-zoom-btn" data-action="zoom-in">+</button>
        <button type="button" class="ksf-orgchart-zoom-btn" data-action="zoom-out">-</button>
        <button type="button" class="ksf-orgchart-zoom-btn" data-action="zoom-fit">Fit</button>
    </div>
</div>
<div class="ksf-orgchart-employee-popup" style="display:none;">
    <button type="button" class="ksf-orgchart-popup-close">&times;</button>
    <div class="popup-content"></div>
</div>
HTML;
    }

    public function renderNodeHtml(array $node): string
    {
        $nodeId = $node['id'] ?? 0;
        $name = $node['name'] ?? 'Unknown';
        $title = $node['title'] ?? '';
        $department = $node['department'] ?? '';

        return <<<HTML
<g class="ksf-orgchart-node" data-node-id="{$nodeId}" data-level="{$node['level'] ?? 0}">
    <rect class="ksf-orgchart-node-bg" width="180" height="80" rx="4"/>
    <text class="ksf-orgchart-node-name" x="90" y="30">{$name}</text>
    <text class="ksf-orgchart-node-title" x="90" y="50">{$title}</text>
    <text class="ksf-orgchart-node-dept" x="90" y="65">{$department}</text>
</g>
HTML;
    }

    public function renderConnectorHtml(int $fromNodeId, int $toNodeId, array $fromPos, array $toPos): string
    {
        $x1 = $fromPos['x'] + 90;
        $y1 = $fromPos['y'] + 80;
        $x2 = $toPos['x'] + 90;
        $y2 = $toPos['y'];

        return <<<HTML
<path class="ksf-orgchart-connector" 
      d="M{$x1},{$y1} L{$x1},y" 
      marker-end="url(#orgchart-arrow)"/>
HTML;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function enqueueScripts(): void
    {
        $version = filemtime(__FILE__);
        wp_enqueue_style('ksf-orgchart', plugin_dir_url(__FILE__) . '../css/orgchart.css', [], $version);
        wp_enqueue_script('ksf-orgchart', plugin_dir_url(__FILE__) . '../js/orgchart.js', ['jquery'], $version, true);
        wp_localize_script('ksf-orgchart', 'ksfOrgChartConfig', $this->config);
    }
}