<?php

declare(strict_types=1);

namespace Ksfraser\Tests\Unit\WP\OrgChart\Service;

use Ksfraser\WP\OrgChart\Service\OrgChartScaler;
use PHPUnit\Framework\TestCase;

class OrgChartScalerTest extends TestCase
{
    private OrgChartScaler $scaler;

    protected function setUp(): void
    {
        $this->scaler = new OrgChartScaler();
    }

    public function testCalculateViewBoxWithEmptyNodes(): void
    {
        $result = $this->scaler->calculateViewBox([]);

        $this->assertArrayHasKey('width', $result);
        $this->assertArrayHasKey('height', $result);
        $this->assertSame(400, $result['width']);
        $this->assertSame(300, $result['height']);
    }

    public function testCalculateViewBoxWithNodes(): void
    {
        $nodes = [
            ['id' => 1, 'level' => 0],
            ['id' => 2, 'level' => 1],
            ['id' => 3, 'level' => 1],
        ];

        $result = $this->scaler->calculateViewBox($nodes);

        $this->assertArrayHasKey('width', $result);
        $this->assertArrayHasKey('height', $result);
        $this->assertArrayHasKey('minLevel', $result);
        $this->assertArrayHasKey('maxLevel', $result);
        $this->assertArrayHasKey('maxInLevel', $result);
        $this->assertSame(0, $result['minLevel']);
        $this->assertSame(1, $result['maxLevel']);
        $this->assertSame(2, $result['maxInLevel']);
    }

    public function testCalculateFit(): void
    {
        $viewBox = ['width' => 800, 'height' => 600];
        $result = $this->scaler->calculateFit($viewBox, 400, 300);

        $this->assertArrayHasKey('scaleX', $result);
        $this->assertArrayHasKey('scaleY', $result);
        $this->assertSame(0.5, $result['scaleX']);
        $this->assertSame(0.5, $result['scaleY']);
    }

    public function testCalculateFitMaxScale(): void
    {
        $viewBox = ['width' => 100, 'height' => 100];
        $result = $this->scaler->calculateFit($viewBox, 500, 500);

        $this->assertArrayHasKey('scaleX', $result);
        $this->assertArrayHasKey('scaleY', $result);
        $this->assertLessThanOrEqual(1.0, $result['scaleX']);
    }

    public function testCalculateNodePosition(): void
    {
        $node = [
            'level' => 0,
            'indexInLevel' => 0,
            'countInLevel' => 1,
        ];

        $viewBox = [
            'width' => 400,
            'height' => 300,
            'minLevel' => 0,
            'maxInLevel' => 1,
        ];

        $result = $this->scaler->calculateNodePosition($node, $viewBox);

        $this->assertArrayHasKey('x', $result);
        $this->assertArrayHasKey('y', $result);
        $this->assertArrayHasKey('level', $result);
        $this->assertSame(0, $result['level']);
    }

    public function testConstants(): void
    {
        $this->assertSame(180, OrgChartScaler::BOX_WIDTH);
        $this->assertSame(80, OrgChartScaler::BOX_HEIGHT);
        $this->assertSame(100, OrgChartScaler::LEVEL_SPACING);
        $this->assertSame(20, OrgChartScaler::NODE_SPACING);
        $this->assertSame(1.0, OrgChartScaler::MAX_SCALE);
        $this->assertSame(0.25, OrgChartScaler::MIN_SCALE);
    }
}