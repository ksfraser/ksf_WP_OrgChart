<?php

declare(strict_types=1);

namespace Ksfraser\Tests\Unit\WP\OrgChart\Entity;

use Ksfraser\WP\OrgChart\Entity\OrgChartView;
use PHPUnit\Framework\TestCase;

class OrgChartViewTest extends TestCase
{
    public function testDefaultValues(): void
    {
        $view = new OrgChartView();

        $this->assertSame(0, $view->getCenterNodeId());
        $this->assertSame([], $view->getNodes());
        $this->assertSame(2, $view->getLevelsUp());
        $this->assertSame(2, $view->getLevelsDown());
        $this->assertSame('hrm', $view->getContext());
        $this->assertNull($view->getProjectId());
        $this->assertSame([], $view->getViewBox());
    }

    public function testSetCenterNodeId(): void
    {
        $view = new OrgChartView();
        $result = $view->setCenterNodeId(123);

        $this->assertInstanceOf(OrgChartView::class, $result);
        $this->assertSame(123, $view->getCenterNodeId());
    }

    public function testSetNodes(): void
    {
        $nodes = [
            ['id' => 1, 'name' => 'Manager', 'level' => 0],
            ['id' => 2, 'name' => 'Employee', 'level' => 1],
        ];

        $view = new OrgChartView();
        $result = $view->setNodes($nodes);

        $this->assertInstanceOf(OrgChartView::class, $result);
        $this->assertCount(2, $view->getNodes());
        $this->assertSame('Manager', $view->getNodes()[0]['name']);
    }

    public function testSetContext(): void
    {
        $view = new OrgChartView();
        $result = $view->setContext('project');

        $this->assertInstanceOf(OrgChartView::class, $result);
        $this->assertSame('project', $view->getContext());
    }

    public function testSetProjectId(): void
    {
        $view = new OrgChartView();
        $result = $view->setProjectId(456);

        $this->assertInstanceOf(OrgChartView::class, $result);
        $this->assertSame(456, $view->getProjectId());
    }

    public function testToArray(): void
    {
        $view = new OrgChartView();
        $view->setCenterNodeId(1)
             ->setLevelsUp(3)
             ->setLevelsDown(4)
             ->setContext('hrm')
             ->setProjectId(null);

        $array = $view->toArray();

        $this->assertArrayHasKey('centerNodeId', $array);
        $this->assertArrayHasKey('nodes', $array);
        $this->assertArrayHasKey('levelsUp', $array);
        $this->assertArrayHasKey('levelsDown', $array);
        $this->assertArrayHasKey('context', $array);
        $this->assertArrayHasKey('projectId', $array);
        $this->assertArrayHasKey('viewBox', $array);
    }

    public function testFromArray(): void
    {
        $data = [
            'centerNodeId' => 10,
            'nodes' => [['id' => 1, 'name' => 'Test']],
            'levelsUp' => 3,
            'levelsDown' => 5,
            'context' => 'project',
            'projectId' => 100,
            'viewBox' => ['width' => 800, 'height' => 600],
        ];

        $view = OrgChartView::fromArray($data);

        $this->assertSame(10, $view->getCenterNodeId());
        $this->assertCount(1, $view->getNodes());
        $this->assertSame(3, $view->getLevelsUp());
        $this->assertSame(5, $view->getLevelsDown());
        $this->assertSame('project', $view->getContext());
        $this->assertSame(100, $view->getProjectId());
        $this->assertSame(['width' => 800, 'height' => 600], $view->getViewBox());
    }

    public function testFromArrayWithDefaults(): void
    {
        $data = ['centerNodeId' => 5];

        $view = OrgChartView::fromArray($data);

        $this->assertSame(5, $view->getCenterNodeId());
        $this->assertSame([], $view->getNodes());
        $this->assertSame(2, $view->getLevelsUp());
        $this->assertSame(2, $view->getLevelsDown());
        $this->assertSame('hrm', $view->getContext());
        $this->assertNull($view->getProjectId());
    }
}