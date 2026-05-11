# UAT Plan - ksf_WP_OrgChart

## Overview
ksf_WP_OrgChart provides interactive org chart visualization for WordPress ESS portals with HRM and Project context modes.

## Test Strategy

### Approach
- Manual testing by QA team
- Test scenarios executed in WordPress test environment
- Precondition: WordPress ESS portal with test users

### Test Environment
- WordPress 6.x with ksf_WP_OrgChart plugin
- Test users: 10 employees in org hierarchy, 3 projects with team members
- ksf_OrgChart, ksf_HRM, ksf_PM test data loaded

---

## UAT Scenarios

### UAT-001: View Company Org Chart
**Objective**: Verify org chart renders with company hierarchy

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | Login to ESS portal as employee | Dashboard displayed |
| 2 | Navigate to Org Chart page | Org chart SVG rendered |
| 3 | Verify centered node is current user | User box at visual center |
| 4 | Verify levels (2 up, 2 down) | Managers above, reports below |
| 5 | Verify fit-to-page | Chart fits container width |

**Pass Criteria**: Chart displays with correct hierarchy and scaling

---

### UAT-002: Search and Select Employee
**Objective**: Verify search functionality finds employees

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | Type "joh" in search box | Autocomplete shows "John Smith" |
| 2 | Click on suggestion | Chart re-centers on John Smith |
| 3 | Verify John is now center | John box at visual center |
| 4 | Verify levels still 2 | 2 levels up/down displayed |

**Pass Criteria**: Search finds correct employee, chart re-centers

---

### UAT-003: Single Click Employee Info
**Objective**: Verify single click shows info panel

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | Click on org box (not center) | Box highlighted (blue border) |
| 2 | Verify info panel appears | Panel shows: Dept, Title, Email, Ext |
| 3 | Click X to dismiss | Panel closes |

**Pass Criteria**: Info panel displays public employee data

---

### UAT-004: Double Click Drill-Down
**Objective**: Verify double-click re-centers chart

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | Double-click on org box | Chart animates and re-centers |
| 2 | Verify clicked box is now center | Clicked box at visual center |
| 3 | Verify manager chain updated | New manager chain above |

**Pass Criteria**: Double-click detected, chart re-centers

---

### UAT-005: Change Display Levels
**Objective**: Verify level selector changes hierarchy depth

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | Click level selector | Dropdown shows 1-5 |
| 2 | Select "3 levels" | Chart re-renders with 3 levels |
| 3 | Verify 3 levels up/down | 3 manager levels, 3 report levels |

**Pass Criteria**: Chart depth changes to selected level

---

### UAT-006: Switch to Project Team Mode
**Objective**: Verify context toggle switches data source

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | Click "Project Team" context toggle | Mode switches |
| 2 | Verify project selector appears | Dropdown shows projects |
| 3 | Select project | Project team org chart loads |
| 4 | Verify PM at center | PM box at center |

**Pass Criteria**: Mode switches, project team displayed

---

### UAT-007: View Project Team Structure
**Objective**: Verify project mode shows team members with roles

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | Switch to Project Team mode | Project selector shown |
| 2 | Select project "Acme Corp" | Team cards load |
| 3 | Verify role badges | PM (blue), BA (green), Dev (purple) |
| 4 | Click on PM card | Info panel shows PM details |

**Pass Criteria**: Project team cards displayed with role badges

---

### UAT-008: GDPR Compliance Check
**Objective**: Verify only public data shown

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | Click on any org box | Info panel shows |
| 2 | Verify no private data | No home address, SSN, etc. |
| 3 | Verify public data only | Dept, Title, Email, Ext shown |

**Pass Criteria**: Info panel contains only public fields

---

## UAT Summary

| UC | Name | Tester | Date | Result |
|----|------|--------|------|--------|
| UAT-001 | View Company Org Chart | | | |
| UAT-002 | Search and Select Employee | | | |
| UAT-003 | Single Click Employee Info | | | |
| UAT-004 | Double Click Drill-Down | | | |
| UAT-005 | Change Display Levels | | | |
| UAT-006 | Switch to Project Team Mode | | | |
| UAT-007 | View Project Team Structure | | | |
| UAT-008 | GDPR Compliance Check | | | |

**Sign-off**: ______________________ Date: ____________

*Document Version: 1.1.0*
*Last Updated: 2026-05-11*