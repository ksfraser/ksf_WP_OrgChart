<?php

declare(strict_types=1);

namespace Ksfraser\WP\OrgChart\Service;

class OrgChartScaler
{
    public const BOX_WIDTH = 180;
    public const BOX_HEIGHT = 80;
    public const LEVEL_SPACING = 100;
    public const NODE_SPACING = 20;
    public const PADDING = 40;
    public const MAX_SCALE = 1.0;
    public const MIN_SCALE = 0.25;

    public function calculateViewBox(array $nodes, array $options = []): array
    {
        $boxWidth = $options['BOX_WIDTH'] ?? self::BOX_WIDTH;
        $boxHeight = $options['BOX_HEIGHT'] ?? self::BOX_HEIGHT;
        $levelSpacing = $options['LEVEL_SPACING'] ?? self::LEVEL_SPACING;
        $nodeSpacing = $options['NODE_SPACING'] ?? self::NODE_SPACING;
        $padding = $options['PADDING'] ?? self::PADDING;

        if (empty($nodes)) {
            return [
                'width' => 400,
                'height' => 300,
            ];
        }

        $levels = [];
        foreach ($nodes as $node) {
            $level = $node['level'] ?? 0;
            if (!isset($levels[$level])) {
                $levels[$level] = 0;
            }
            $levels[$level]++;
        }

        $minLevel = min(array_keys($levels));
        $maxLevel = max(array_keys($levels));
        $levelCount = $maxLevel - $minLevel + 1;
        $maxInLevel = max($levels);

        $width = $maxInLevel * ($boxWidth + $nodeSpacing) + $padding * 2;
        $height = $levelCount * ($boxHeight + $levelSpacing) + $padding * 2;

        return [
            'width' => $width,
            'height' => $height,
            'minLevel' => $minLevel,
            'maxLevel' => $maxLevel,
            'maxInLevel' => $maxInLevel,
        ];
    }

    public function calculateFit(array $viewBox, int $containerWidth, int $containerHeight): array
    {
        $scaleX = $containerWidth / $viewBox['width'];
        $scaleY = $containerHeight / $viewBox['height'];
        $scale = min($scaleX, $scaleY, self::MAX_SCALE);
        $scale = max($scale, self::MIN_SCALE);

        return [
            'scaleX' => $scale,
            'scaleY' => $scale,
        ];
    }

    public function calculateNodePosition(array $node, array $viewBox, array $options = []): array
    {
        $boxWidth = $options['BOX_WIDTH'] ?? self::BOX_WIDTH;
        $boxHeight = $options['BOX_HEIGHT'] ?? self::BOX_HEIGHT;
        $levelSpacing = $options['LEVEL_SPACING'] ?? self::LEVEL_SPACING;
        $nodeSpacing = $options['NODE_SPACING'] ?? self::NODE_SPACING;
        $padding = $options['PADDING'] ?? self::PADDING;

        $level = $node['level'] ?? 0;
        $indexInLevel = $node['indexInLevel'] ?? 0;
        $countInLevel = $node['countInLevel'] ?? 1;

        $centerOffset = ($viewBox['width'] - $boxWidth) / 2;
        $levelOffset = ($viewBox['maxInLevel'] - $countInLevel) * ($boxWidth + $nodeSpacing) / 2;

        $x = $padding + $centerOffset + $levelOffset + $indexInLevel * ($boxWidth + $nodeSpacing);
        $y = $padding + ($level - ($viewBox['minLevel'] ?? 0)) * ($boxHeight + $levelSpacing);

        return [
            'x' => $x,
            'y' => $y,
            'level' => $level,
        ];
    }
}