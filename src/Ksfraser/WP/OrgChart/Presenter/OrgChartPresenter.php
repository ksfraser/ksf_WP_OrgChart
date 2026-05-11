<?php

declare(strict_types=1);

namespace Ksfraser\WP\OrgChart\Presenter;

use Ksfraser\WP\OrgChart\Entity\OrgChartView;
use Ksfraser\OrgChart\Service\OrgChartService;
use Ksfraser\OrgChart\Entity\OrgNode;
use Ksfraser\HRM\Service\EmployeeService;

class OrgChartPresenter
{
    private const MODE_HRM = 'hrm';
    private const MODE_PROJECT = 'project';

    private OrgChartService $orgChartService;
    private EmployeeService $hrmService;
    private ?object $pmService;
    private string $context = self::MODE_HRM;

    public function __construct(
        OrgChartService $orgChartService,
        EmployeeService $hrmService,
        ?object $pmService = null
    ) {
        $this->orgChartService = $orgChartService;
        $this->hrmService = $hrmService;
        $this->pmService = $pmService;
    }

    public function getContext(): string
    {
        return $this->context;
    }

    public function setContext(string $context): self
    {
        if (!in_array($context, [self::MODE_HRM, self::MODE_PROJECT])) {
            throw new \InvalidArgumentException("Invalid context mode: {$context}");
        }
        $this->context = $context;
        return $this;
    }

    public function loadOrgNode(int $nodeId, ?string $context = null): ?OrgNode
    {
        $mode = $context ?? $this->context;
        return $this->orgChartService->getNode($nodeId);
    }

    public function loadHierarchy(int $centerNodeId, int $levelsUp, int $levelsDown, ?string $context = null): array
    {
        $mode = $context ?? $this->context;
        $nodes = [];
        $centerNode = $this->orgChartService->getNode($centerNodeId);

        if ($centerNode === null) {
            return [];
        }

        $nodes[] = $this->nodeToArray($centerNode, 0);

        for ($i = 1; $i <= $levelsUp; $i++) {
            $parentId = $this->getParentId($centerNodeId, $i);
            if ($parentId !== null) {
                $parent = $this->orgChartService->getNode($parentId);
                if ($parent !== null) {
                    $nodes[] = $this->nodeToArray($parent, -$i);
                }
            }
        }

        $reports = $this->orgChartService->getChildren($centerNodeId);
        $j = 1;
        foreach ($reports as $report) {
            $nodes[] = $this->nodeToArray($report, $j++);
        }

        return $this->calculatePositions($nodes);
    }

    public function searchEmployees(string $query, ?string $context = null): array
    {
        $mode = $context ?? $this->context;

        if (strlen($query) < 2) {
            return [];
        }

        if ($mode === self::MODE_PROJECT && $this->pmService !== null) {
            return $this->pmService->searchProjectMembers($query);
        }

        return $this->hrmService->searchEmployees($query);
    }

    public function getEmployeePublicData(int $nodeId, ?string $context = null): array
    {
        $mode = $context ?? $this->context;
        $node = $this->orgChartService->getNode($nodeId);

        if ($node === null) {
            return [];
        }

        $employeeId = $node->getHeadId() ?? $node->getId();
        $employee = $this->hrmService->getEmployee($employeeId);

        if ($employee === null) {
            return [];
        }

        return [
            'department' => $employee->getDepartment() ?? 'Unknown',
            'title' => $employee->getJobTitle() ?? 'Employee',
            'email' => $employee->getCompanyEmail() ?? '',
            'phoneExt' => $employee->getPhoneExt() ?? '',
        ];
    }

    public function getDirectReports(int $managerId): array
    {
        $children = $this->orgChartService->getChildren($managerId);
        return array_map(fn($n) => $this->nodeToArray($n, 1), $children);
    }

    public function getManagerChain(int $nodeId): array
    {
        $chain = [];
        $current = $this->orgChartService->getNode($nodeId);
        $level = -1;

        while ($current !== null && $current->getParentId() !== null) {
            $parent = $this->orgChartService->getNode($current->getParentId());
            if ($parent === null) {
                break;
            }
            $chain[] = $this->nodeToArray($parent, $level--);
            $current = $parent;
        }

        return $chain;
    }

    public function getProjectTeam(int $projectId): array
    {
        if ($this->pmService === null) {
            return [];
        }

        return $this->pmService->getProjectTeam($projectId);
    }

    private function nodeToArray(OrgNode $node, int $level): array
    {
        $headId = $node->getHeadId();
        $employee = $headId !== null ? $this->hrmService->getEmployee($headId) : null;

        return [
            'id' => $node->getId(),
            'name' => $employee ? $employee->getFirstName() . ' ' . $employee->getLastName() : 'Unknown',
            'title' => $employee ? $employee->getJobTitle() : '',
            'department' => $employee ? $employee->getDepartment() : '',
            'level' => $level,
        ];
    }

    private function getParentId(int $nodeId, int $levelsUp): ?int
    {
        $current = $this->orgChartService->getNode($nodeId);
        for ($i = 0; $i < $levelsUp && $current !== null; $i++) {
            $currentId = $current->getParentId();
            if ($currentId === null) {
                return null;
            }
            $current = $this->orgChartService->getNode($currentId);
        }
        return $current?->getId();
    }

    private function calculatePositions(array $nodes): array
    {
        $levels = [];
        foreach ($nodes as &$node) {
            $level = $node['level'];
            if (!isset($levels[$level])) {
                $levels[$level] = [];
            }
            $levels[$level][] = &$node;
        }

        foreach ($levels as $levelNodes) {
            $count = count($levelNodes);
            foreach ($levelNodes as $index => &$node) {
                $node['indexInLevel'] = $index;
                $node['countInLevel'] = $count;
            }
        }

        return $nodes;
    }
}