# Test Plan - ksf_WP_OrgChart

## Overview
ksf_WP_OrgChart provides interactive org chart visualization for WordPress ESS portals, supporting HRM and Project contexts.

## Test Scope

### In Scope
- WordPress adapter (OrgChartWidget)
- OrgChartPresenter (HRM + Project modes)
- OrgChartScaler (fit-to-page)
- UI components (OrgChartViewer, SearchBox, LevelSelector)
- JavaScript interaction handlers

### Out of Scope
- ksf_OrgChart business logic (tested separately)
- ksf_HRM employee data (tested separately)
- ksf_PM project data (tested separately)

---

## Test Environment

### Dependencies
```json
{
    "ksfraser/ksf-wp-orgchart": "*",
    "ksfraser/ksf-orgchart": "*", 
    "ksfraser/ksf-hrm": "*",
    "ksfraser/ksf-project-management": "*"
}
```

### Test Configuration
```php
// tests/Unit/bootstrap.php
require_once __DIR__ . '/../../vendor/autoload.php';
```

---

## Unit Test Cases

### TC-001: OrgChartPresenter HRM Mode Load
```php
public function testLoadOrgNodeHrmMode(): void
{
    // Arrange
    $nodeId = 123;
    $presenter = new OrgChartPresenter($this->mockOrgChartService, $this->mockHrmService);
    
    // Act
    $node = $presenter->loadOrgNode($nodeId, 'hrm');
    
    // Assert
    $this->assertInstanceOf(OrgNode::class, $node);
}
```

### TC-002: OrgChartPresenter Project Mode Load
```php
public function testLoadOrgNodeProjectMode(): void
{
    // Arrange  
    $nodeId = 456;
    $presenter = new OrgChartPresenter($this->mockOrgChartService, $this->mockPmService);
    
    // Act
    $node = $presenter->loadOrgNode($nodeId, 'project');
    
    // Assert
    $this->assertInstanceOf(ProjectOrgNode::class, $node);
}
```

### TC-003: Context Mode Toggle
```php
public function testContextModeToggle(): void
{
    // Arrange
    $presenter = new OrgChartPresenter(...);
    
    // Act - switch to project mode
    $presenter->setContext('project');
    
    // Assert
    $this->assertSame('project', $presenter->getContext());
}
```

### TC-004: Search Employees HRM Mode
```php
public function testSearchEmployeesHrmMode(): void
{
    // Arrange
    $query = 'john';
    $presenter = new OrgChartPresenter(...);
    
    // Act
    $results = $presenter->searchEmployees($query, 'hrm');
    
    // Assert
    $this->assertIsArray($results);
    $this->assertNotEmpty($results);
}
```

### TC-005: Search Employees Project Mode
```php
public function testSearchEmployeesProjectMode(): void
{
    // Arrange
    $query = 'pm';
    $presenter = new OrgChartPresenter(...);
    
    // Act
    $results = $presenter->searchEmployees($query, 'project');
    
    // Assert
    $this->assertIsArray($results);
}
```

### TC-006: Employee Public Data
```php
public function testGetEmployeePublicData(): void
{
    // Arrange
    $nodeId = 123;
    $presenter = new OrgChartPresenter(...);
    
    // Act
    $data = $presenter->getEmployeePublicData($nodeId);
    
    // Assert
    $this->assertArrayHasKey('department', $data);
    $this->assertArrayHasKey('title', $data);
    $this->assertArrayHasKey('email', $data);
    $this->assertArrayNotHasKey('address', $data); // GDPR
}
```

### TC-007: Get Direct Reports
```php
public function testGetDirectReports(): void
{
    // Arrange
    $managerId = 100;
    $presenter = new OrgChartPresenter(...);
    
    // Act
    $reports = $presenter->getDirectReports($managerId);
    
    // Assert
    $this->assertIsArray($reports);
}
```

### TC-008: Get Manager Chain
```php
public function testGetManagerChain(): void
{
    // Arrange
    $nodeId = 200;
    $presenter = new OrgChartPresenter(...);
    
    // Act
    $chain = $presenter->getManagerChain($nodeId);
    
    // Assert
    $this->assertIsArray($chain);
    $this->assertContainsOnly(OrgNode::class, $chain);
}
```

### TC-009: Load Hierarchy with Levels
```php
public function testLoadHierarchyWithLevels(): void
{
    // Arrange
    $centerId = 123;
    $levelsUp = 3;
    $levelsDown = 2;
    
    // Act
    $hierarchy = $presenter->loadHierarchy($centerId, $levelsUp, $levelsDown);
    
    // Assert
    $this->assertIsArray($hierarchy);
    $this->assertNotEmpty($hierarchy);
}
```

### TC-010: OrgChartScaler Fit Calculation
```php
public function testScalerFitCalculation(): void
{
    // Arrange
    $nodes = [/* 15 nodes */];
    $containerWidth = 800;
    $containerHeight = 600;
    
    $scaler = new OrgChartScaler();
    
    // Act
    $scale = $scaler->calculateFit($nodes, $containerWidth, $containerHeight);
    
    // Assert
    $this->assertLessThanOrEqual(1.0, $scale['scaleX']);
    $this->assertLessThanOrEqual(1.0, $scale['scaleY']);
}
```

### TC-011: OrgChartScaler ViewBox Calculation
```php
public function testScalerViewBoxCalculation(): void
{
    // Arrange
    $nodes = $this->createHierarchyNodes(7);
    $options = ['BOX_WIDTH' => 180, 'BOX_HEIGHT' => 80];
    
    $scaler = new OrgChartScaler();
    
    // Act
    $viewBox = $scaler->calculateViewBox($nodes, $options);
    
    // Assert
    $this->assertArrayHasKey('width', $viewBox);
    $this->assertArrayHasKey('height', $viewBox);
    $this->assertGreaterThan(0, $viewBox['width']);
}
```

### TC-012: Project Team Load
```php
public function testGetProjectTeam(): void
{
    // Arrange
    $projectId = 50;
    $presenter = new OrgChartPresenter(...);
    
    // Act
    $team = $presenter->getProjectTeam($projectId);
    
    // Assert
    $this->assertIsArray($team);
    foreach ($team as $member) {
        $this->assertArrayHasKey('role', $member);
    }
}
```

---

## Integration Test Cases

### IT-001: WP Shortcode Render
```
Setup: WordPress with ksf_WP_OrgChart activated
Input: [org_chart employee_id="123"]
Expected: Org chart SVG rendered with centered node
```

### IT-002: WP Widget Render
```
Setup: WordPress ESS page with Org Chart widget
Input: Search "john", select levels=3
Expected: Org chart renders with 3 levels up/down
```

### IT-003: Context Switch HRM to Project
```
Setup: ESS org chart page loaded
Input: Click "Project Team" toggle, select project
Expected: Chart re-renders with project team centered
```

---

## Test Execution

### Command
```bash
cd /home/kevin/Documents/ksf_WP_OrgChart
./vendor/bin/phpunit --testdox
```

### Coverage Target
- Presenter: 100%
- Scaler: 100%  
- UI Components: View instantiation only

*Document Version: 1.1.0*
*Last Updated: 2026-05-11*