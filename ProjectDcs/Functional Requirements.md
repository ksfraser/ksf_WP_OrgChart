# Functional Requirements - ksf_WP_OrgChart

## Overview
ksf_WP_OrgChart provides interactive organizational hierarchy visualization for WordPress Employee Self Service (ESS) portals.

## FR-001: Employee Search

### Description
Users can search for employees by name or email.

### Acceptance Criteria
- [ ] Search input accepts minimum 2 characters
- [ ] Autocomplete shows matching employees (max 10)
- [ ] Results display name and primary department
- [ ] Clicking result centers org chart on that employee
- [ ] Empty search shows nothing (no default results)

### Technical Details
- Source: ksf_HRM employee repository
- Debounce: 300ms
- Min query: 2 characters
- Max results: 10 suggestions

---

## FR-002: Single Click Information Display

### Description
Single-clicking an org chart box displays employee public data in an info panel.

### Acceptance Criteria
- [ ] Info panel appears on single click (not double)
- [ ] Panel shows: Department, Title, Company Email, Phone Extension
- [ ] Panel dismisses on click outside or another box click
- [ ] No private data displayed (GDPR compliant)

### Data Fields
| Field | Source | Format |
|-------|--------|--------|
| Department | ksf_HRM.employee.department | Text |
| Title | ksf_HRM.employee.jobTitle | Text |
| Email | ksf_HRM.employee.companyEmail | mailto: link |
| Phone Extension | ksf_HRM.employee.phoneExt | Text |

---

## FR-003: Double Click Drill-Down

### Description
Double-clicking an org chart box centers the chart on that employee.

### Acceptance Criteria
- [ ] Double-click detected (300ms threshold)
- [ ] Chart re-centers with clicked employee at center
- [ ] Manager chain shows above (levelsUp configurable)
- [ ] Direct reports show below (levelsDown configurable)
- [ ] Previous center fades/animates out

### Animation
- Duration: 400ms ease-out
- Effect: Fade out old, scale in new centered

---

## FR-004: Level Selector Dropdown

### Description
Dropdown to select number of hierarchy levels to display.

### Acceptance Criteria
- [ ] Dropdown shows options: 1, 2, 3, 4, 5 levels
- [ ] Default: 2 levels
- [ ] Selection applies immediately (no submit button)
- [ ] Levels apply both up (managers) and down (reports)
- [ ] "All Levels" option shows complete org (for small orgs)

### Level Calculation
```
levelsUp = selectedValue
levelsDown = selectedValue  
totalDisplayNodes = (2 * selectedValue) + 1 (center)
```

---

## FR-005: Fit-to-Page Scaling

### Description
Org chart automatically scales to fit container width.

### Acceptance Criteria
- [ ] SVG viewBox calculated based on nodes displayed
- [ ] Scale applied using transform: scale()
- [ ] Max scale: 100% (no upscaling)
- [ ] Min scale: 25% (for very large orgs)
- [ ] Pan/scroll enabled for scaled charts
- [ ] Scroll/pan container 100% viewport with overflow

### Calculation
```
viewBox.width = nodesInWidestLevel * (BOX_WIDTH + SPACING) + PADDING
viewBox.height = totalLevels * (BOX_HEIGHT + SPACING) + PADDING
scale = Math.min(containerWidth / viewBox.width, 1.0)
```

---

## FR-006: Box Layout Algorithm

### Description
Org chart boxes positioned using hierarchy layout algorithm.

### Acceptance Criteria
- [ ] Center employee at visual center
- [ ] Manager chain displayed vertically above (top to bottom = top to bottom)
- [ ] Direct reports displayed vertically below
- [ ] Colleagues (same manager) displayed horizontally
- [ ] Box width: 180px, Box height: 80px
- [ ] Level spacing: 100px vertical
- [ ] Node spacing: 20px horizontal

### Layout Example (2 levels)
```
                    [Manager]
                         │
            ┌────────────┼────────────┐
            │            │            │
       [Colleague]  [CENTER]    [Colleague]
            │            │            │
            └────────────┼────────────┘
                         │
                   [Direct Report 1]
                         │
                   [Direct Report 2]
```

---

## FR-007: Employee Box Display

### Description
Each org chart box displays standard employee public data.

### Acceptance Criteria
- [ ] Box shows: Avatar placeholder, Name, Title
- [ ] Avatar: 40x40 circle, initials fallback if no photo
- [ ] Name: Bold, truncate at 20 chars with "..."
- [ ] Title: Regular, truncate at 30 chars
- [ ] Click highlight: 2px solid blue border
- [ ] Selected state: Light blue background (#E3F2FD)

### Box HTML Structure
```html
<div class="org-box" data-employee-id="123">
  <div class="org-box-avatar">JS</div>
  <div class="org-box-info">
    <div class="org-box-name">John Smith</div>
    <div class="org-box-title">Sales Representative</div>
  </div>
</div>
```

---

## FR-008: Info Panel Display

### Description
Single-clicked box shows info panel with employee details.

### Acceptance Criteria
- [ ] Panel positioned to right of clicked box (default)
- [ ] Panel auto-positions to avoid viewport overflow
- [ ] Close X button in top-right corner
- [ ] Click outside panel dismisses it
- [ ] Smooth fade-in animation (200ms)

### Panel HTML Structure
```html
<div class="org-info-panel">
  <button class="org-info-close">&times;</button>
  <h3>John Smith</h3>
  <dl>
    <dt>Department</dt><dd>Sales</dd>
    <dt>Title</dt><dd>Sales Representative</dd>
    <dt>Email</dt><dd><a href="mailto:...">john@company.com</a></dd>
    <dt>Extension</dt><dd>234</dd>
  </dl>
</div>
```

---

## FR-009: Level Indicator Display

### Description
Org chart displays level indicators for context.

### Acceptance Criteria
- [ ] Level numbers shown on left side of chart
- [ ] Manager levels: Negative numbers (-1, -2)
- [ ] Center: 0
- [ ] Report levels: Positive numbers (+1, +2)
- [ ] Current level highlighted

---

## FR-010: Responsive Container

### Description
Org chart container is responsive for all device sizes.

### Acceptance Criteria
- [ ] Container: 100% width, min-height 400px
- [ ] Mobile: Single column, scroll horizontal
- [ ] Tablet: Single column, scroll horizontal  
- [ ] Desktop: Fit-to-width with scroll if needed
- [ ] Max container width: 1920px (centered)

---

## Non-Functional Requirements

### NFR-001: Performance
- Initial load: < 2 seconds for 50 nodes
- Search response: < 500ms
- Drill-down re-render: < 300ms

### NFR-002: Accessibility
- WCAG 2.1 AA compliant
- Keyboard navigation: Tab, Enter, Escape
- Screen reader: Proper ARIA labels

### NFR-003: Browser Support
- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+
- IE 11: Not supported

*Document Version: 1.0.0*
*Last Updated: 2026-05-11*