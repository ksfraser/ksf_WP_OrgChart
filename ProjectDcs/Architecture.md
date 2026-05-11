# Architecture - ksf_WP_OrgChart

## Overview
ksf_WP_OrgChart is a WordPress adapter providing interactive org chart visualization for Employee Self Service (ESS) portals, supporting both HRM organizational hierarchy AND Project Management team structure.

## Context Modes

The adapter supports two context modes:

| Mode | Source | Use Case |
|------|--------|----------|
| **HRM Mode** (default) | ksf_OrgChart + ksf_HRM | Company org chart - reporting hierarchy |
| **Project Mode** | ksf_ProjectManagement + ksf_OrgChart | Project team org - contract team members |

### Mode Switching
```php
// Shortcode attribute switches mode
[org_chart employee_id="123" context="project" project_id="456"]
[org_chart employee_id="123" context="hrm"]  // default
```

## Component Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                         UI Layer                                 │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────────────────┐│
│  │ SearchBox   │  │ LevelSelect │  │ OrgChartViewer (SVG)    ││
│  │ Component   │  │ Component   │  │ Component               ││
│  └─────────────┘  └─────────────┘  └─────────────────────────┘│
│  ┌─────────────────────────────────────────────────────────────┐│
│  │ ContextMode: [HRM Org] [Project Team]                      ││
│  └─────────────────────────────────────────────────────────────┘│
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                     Presenter Layer                             │
│  ┌─────────────────────────────────────────────────────────────┐
│  │ OrgChartPresenter                                            │
│  │ - loadOrgNode(nodeId)                                        │
│  │ - loadHierarchy(nodeId, levelsUp, levelsDown, context)    │
│  │ - searchEmployees(query, context)                            │
│  │ - getEmployeePublicData(nodeId, context)                     │
│  │ - getProjectTeam(projectId) → ksf_PM                       │
│  └─────────────────────────────────────────────────────────────┘
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                     Adapter Layer                               │
│  ┌─────────────────────────────────────────────────────────────┐
│  │ OrgChartWidget (WP Widget/Shortcode)                         │
│  │ - register_hooks()                                           │
│  │ - render_shortcode($atts)                                    │
│  └─────────────────────────────────────────────────────────────┘
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                    Business Logic (Consumed)                    │
│  ┌─────────────────────┐     ┌─────────────────────┐             │
│  │ ksf_OrgChart       │     │ ksf_HRM             │             │
│  │ - OrgNode           │     │ - EmployeePublic    │             │
│  │ - OrgEdge           │     │   DataAdapter        │             │
│  │ - OrgChartService   │     │ - EmployeeRepo       │             │
│  └─────────────────────┘     └─────────────────────┘             │
│  ┌─────────────────────┐     ┌─────────────────────┐             │
│  │ ksf_ProjectManagement │  │ ksf_PM              │             │
│  │ - ProjectNode         │  │ - ProjectEmployee    │             │
│  │ - ProjectAssignment    │  │   Repo               │             │
│  │ - ProjectService       │  │ - ProjectRepo        │             │
│  └─────────────────────┘     └─────────────────────┘             │
└─────────────────────────────────────────────────────────────────┘
```

## View Components

### OrgChartViewer
- **Technology**: SVG with JavaScript interaction
- **Data**: OrgNode hierarchy rendered as boxes
- **Modes**:
  - HRM: Standard reporting hierarchy
  - Project: Project team members with role badges
- **States**: 
  - Default: Show centered node + levels
  - Single-click selected: Highlight + show info panel
  - Double-click drilled: Re-center on selected node

### SearchBox
- **Technology**: HTML input + AJAX autocomplete
- **Data**: Employee names/emails from ksf_HRM (HRM mode) or ksf_PM (Project mode)
- **Modes**: Filters by context type
- **States**: Empty, Typing, Suggestions shown, Selected

### LevelSelector
- **Technology**: HTML select dropdown
- **Options**: 1-5 levels (default: 2)
- **States**: Default selection applied

### ContextModeToggle
- **Technology**: HTML radio/segmented control
- **Options**: "Company Org" / "Project Team"
- **Behavior**: Switches data source and re-renders chart

## Presenter Contracts

```php
interface OrgChartPresenterInterface
{
    public function loadOrgNode(int $nodeId): ?OrgNode;
    public function loadHierarchy(int $centerNodeId, int $levelsUp, int $levelsDown): array;
    public function searchEmployees(string $query): array;
    public function getEmployeePublicData(int $nodeId): array;
    public function getDirectReports(int $nodeId): array;
    public function getManagerChain(int $nodeId): array;
}
```

## Data Flow

1. **User types in SearchBox** → AJAX → Presenter.searchEmployees() → ksf_HRM → Autocomplete suggestions
2. **User clicks suggestion** → Presenter.loadOrgNode() → ksf_OrgChart → Display node centered
3. **User selects level dropdown** → Presenter.loadHierarchy() → ksf_OrgChart → Re-render SVG
4. **User single-clicks org box** → Presenter.getEmployeePublicData() → ksf_HRM → Show info panel
5. **User double-clicks org box** → Presenter.loadHierarchy() centered → ksf_OrgChart → Re-center display

## Fit-to-Page Scaling

```javascript
class OrgChartScaler {
    // Calculate SVG viewBox based on:
    // - Number of nodes displayed
    // - Box dimensions (default 180x80)
    // - Level spacing (default 100px vertical)
    // - Node spacing (default 20px horizontal)
    
    calculateViewBox(nodes, options) {
        const levels = Math.max(...nodes.map(n => n.level)) - 
                       Math.min(...nodes.map(n => n.level)) + 1;
        const maxInLevel = Math.max(...levels.map(l => 
            nodes.filter(n => n.level === l).length));
        
        return {
            width: maxInLevel * (BOX_WIDTH + NODE_SPACING) + PADDING,
            height: levels * (BOX_HEIGHT + LEVEL_SPACING) + PADDING
        };
    }
    
    scaleToContainer(viewBox, containerWidth, containerHeight) {
        const scaleX = containerWidth / viewBox.width;
        const scaleY = containerHeight / viewBox.height;
        const scale = Math.min(scaleX, scaleY, 1.0); // Max 100%
        
        return { scaleX: scale, scaleY: scale };
    }
}
```

## Box Layout Algorithm

```
                    [Manager 2 Levels Up]
                           │
                    [Manager 1 Level Up]
                           │
            ┌──────────────┼──────────────┐
            │              │              │
    [Colleague Left]  [CENTER]   [Colleague Right]
            │              │              │
            └──────────────┼──────────────┘
                           │
                    [Direct Report 1]
                           │
                    [Direct Report 2]
```

## WordPress Integration

### Shortcode
```
[org_chart employee_id="123" levels_up="2" levels_down="3"]
```

### Widget
WP Widget titled "Organization Chart" with:
- Employee search field
- Level selector (1-5)
- Render button

### Enqueue
```php
wp_enqueue_style('ksf-orgchart-css', $plugin_url . 'css/orgchart.css');
wp_enqueue_script('ksf-orgchart-js', $plugin_url . 'js/orgchart.js', ['jquery'], null, true);
```

## Security Considerations

1. **Data Access**: Only ksf_HRM public employee data exposed
2. **No Private Data**: No addresses, personal info in org chart display
3. **Auth Required**: Shortcode/widget only render for authenticated users
4. **GDPR Compliance**: org chart nodes only show public fields

## File Structure

```
ksf_WP_OrgChart/
├── src/Ksfraser/WP/OrgChart/
│   ├── Entity/
│   │   └── OrgChartView.php          # View model for SVG rendering
│   ├── Service/
│   │   └── OrgChartScaler.php        # Fit-to-page calculations
│   ├── Presenter/
│   │   └── OrgChartPresenter.php     # MVP presenter
│   ├── View/
│   │   ├── OrgChartViewer.vue.php    # Main viewer component
│   │   ├── SearchBox.vue.php         # Search component
│   │   └── LevelSelector.vue.php     # Level selector component
│   ├── Adapter/
│   │   └── OrgChartWidget.php        # WP widget/shortcode
│   └── Events/
│       ├── NodeSelectedEvent.php     # Single click event
│       └── NodeDrilledEvent.php       # Double click drill-down event
├── tests/Unit/
│   ├── Presenter/
│   │   └── OrgChartPresenterTest.php
│   └── Service/
│       └── OrgChartScalerTest.php
├── css/
│   └── orgchart.css                  # Styles for boxes and layout
├── js/
│   └── orgchart.js                   # Interaction handlers
├── pages/
│   └── ess-orgchart.php              # ESS page template
├── ProjectDcs/                       # Documentation
└── composer.json                     # Dependencies
```

*Document Version: 1.0.0*
*Last Updated: 2026-05-11*