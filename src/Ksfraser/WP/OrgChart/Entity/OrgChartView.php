<?php

declare(strict_types=1);

namespace Ksfraser\WP\OrgChart\Entity;

class OrgChartView
{
    private int $centerNodeId;
    private array $nodes = [];
    private int $levelsUp = 2;
    private int $levelsDown = 2;
    private string $context = 'hrm';
    private ?int $projectId = null;
    private array $viewBox = [];

    public function getCenterNodeId(): int
    {
        return $this->centerNodeId;
    }

    public function setCenterNodeId(int $centerNodeId): self
    {
        $this->centerNodeId = $centerNodeId;
        return $this;
    }

    public function getNodes(): array
    {
        return $this->nodes;
    }

    public function setNodes(array $nodes): self
    {
        $this->nodes = $nodes;
        return $this;
    }

    public function getLevelsUp(): int
    {
        return $this->levelsUp;
    }

    public function setLevelsUp(int $levelsUp): self
    {
        $this->levelsUp = $levelsUp;
        return $this;
    }

    public function getLevelsDown(): int
    {
        return $this->levelsDown;
    }

    public function setLevelsDown(int $levelsDown): self
    {
        $this->levelsDown = $levelsDown;
        return $this;
    }

    public function getContext(): string
    {
        return $this->context;
    }

    public function setContext(string $context): self
    {
        $this->context = $context;
        return $this;
    }

    public function getProjectId(): ?int
    {
        return $this->projectId;
    }

    public function setProjectId(?int $projectId): self
    {
        $this->projectId = $projectId;
        return $this;
    }

    public function getViewBox(): array
    {
        return $this->viewBox;
    }

    public function setViewBox(array $viewBox): self
    {
        $this->viewBox = $viewBox;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'centerNodeId' => $this->centerNodeId,
            'nodes' => $this->nodes,
            'levelsUp' => $this->levelsUp,
            'levelsDown' => $this->levelsDown,
            'context' => $this->context,
            'projectId' => $this->projectId,
            'viewBox' => $this->viewBox,
        ];
    }

    public static function fromArray(array $data): self
    {
        $view = new self();
        $view->setCenterNodeId($data['centerNodeId'] ?? 0);
        $view->setNodes($data['nodes'] ?? []);
        $view->setLevelsUp($data['levelsUp'] ?? 2);
        $view->setLevelsDown($data['levelsDown'] ?? 2);
        $view->setContext($data['context'] ?? 'hrm');
        $view->setProjectId($data['projectId'] ?? null);
        $view->setViewBox($data['viewBox'] ?? []);
        return $view;
    }
}