# Requirements Traceability Matrix (RTM) - ksf_WP_OrgChart

## Traceability Grid

| Requirement ID | Requirement | UC | TC | Status |
|----------------|-------------|----|----|--------|
| FR-001 | Employee Search | UC-002 | TC-004, TC-005 | |
| FR-002 | Single Click Info | UC-003 | TC-006 | |
| FR-003 | Double Click Drill-Down | UC-004, UC-008 | TC-001, TC-002 | |
| FR-004 | Level Selector | UC-009 | TC-009 | |
| FR-005 | Fit-to-Page Scaling | UC-010 | TC-010, TC-011 | |
| FR-006 | Box Layout Algorithm | UC-001 | TC-001 | |
| FR-007 | Employee Box Display | UC-001 | TC-001, TC-007 | |
| FR-008 | Info Panel Display | UC-003 | TC-006 | |
| FR-009 | Level Indicator Display | UC-001 | TC-001 | |
| FR-010 | Responsive Container | UC-010 | TC-010 | |
| NFR-001 | Performance | UC-All | IT-001 | |
| NFR-002 | Accessibility | UC-All | IT-002 | |
| NFR-003 | Browser Support | UC-All | IT-003 | |

## Requirement Details

### Functional Requirements

| ID | Description | Source Document |
|----|-------------|----------------|
| FR-001 | Search input accepts minimum 2 characters, returns max 10 results | FR-001 |
| FR-002 | Info panel shows Department, Title, Email, Extension | FR-002 |
| FR-003 | Double-click detected with 300ms threshold, re-centers chart | FR-003 |
| FR-004 | Dropdown shows 1-5 levels, default 2, applies immediately | FR-004 |
| FR-005 | Scale max 100%, min 25%, pan/scroll enabled | FR-005 |
| FR-006 | Box width 180px, height 80px, level spacing 100px | FR-006 |
| FR-007 | Box shows Avatar (40x40), Name (bold), Title | FR-007 |
| FR-008 | Panel positioned right (default), auto-positions to avoid overflow | FR-008 |
| FR-009 | Level numbers on left, negative for managers, positive for reports | FR-009 |
| FR-010 | Container 100% width, min-height 400px, max 1920px | FR-010 |

### Non-Functional Requirements

| ID | Description | Source Document |
|----|-------------|----------------|
| NFR-001 | Initial load < 2s (50 nodes), search < 500ms, re-render < 300ms | NFR-001 |
| NFR-002 | WCAG 2.1 AA, keyboard navigation, ARIA labels | NFR-002 |
| NFR-003 | Chrome 90+, Firefox 88+, Safari 14+, Edge 90+ | NFR-003 |

## Coverage Summary

| Category | Total | Covered | Coverage % |
|----------|-------|---------|------------|
| Functional Requirements | 10 | 0 | 0% |
| Non-Functional Requirements | 3 | 0 | 0% |
| **Total** | **13** | **0** | **0%** |

*Document Version: 1.1.0*
*Last Updated: 2026-05-11*