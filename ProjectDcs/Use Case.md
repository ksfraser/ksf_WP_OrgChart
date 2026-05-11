# Use Cases - ksf_WP_OrgChart

## Overview
ksf_WP_OrgChart supports two context modes: Company Org (HRM) and Project Team (Project Management).

---

## UC-001: View Company Org Chart

**Actor**: Employee  
**Precondition**: User authenticated in ESS portal  
**Trigger**: User navigates to Org Chart page  
**Basic Flow**:
1. System displays org chart with user at center
2. Shows 2 levels up (managers) and 2 levels down (reports)
3. Fit-to-page scaling applied

**Postcondition**: Org chart displayed with current user centered

---

## UC-002: Search Employee by Name

**Actor**: Employee  
**Precondition**: Org chart page loaded  
**Trigger**: User types in search box  
**Basic Flow**:
1. User types minimum 2 characters
2. System shows autocomplete suggestions (max 10)
3. Results display name and department
4. User clicks suggestion
5. System centers org chart on selected employee

**Postcondition**: Org chart re-centered on selected employee

---

## UC-003: Single Click Employee Info

**Actor**: Employee  
**Precondition**: Org chart displayed with nodes  
**Trigger**: User single-clicks on org box  
**Basic Flow**:
1. System highlights selected box (blue border)
2. Info panel appears to right of box
3. Panel shows: Department, Title, Email, Phone Ext
4. User clicks outside or X to dismiss

**Postcondition**: Info panel displayed

---

## UC-004: Double Click Drill-Down

**Actor**: Employee  
**Precondition**: Org chart displayed  
**Trigger**: User double-clicks on org box  
**Basic Flow**:
1. System detects double-click (300ms threshold)
2. System re-centers chart on clicked employee
3. Previous center fades out
4. New center animates in
5. Levels applied from selector

**Postcondition**: Org chart re-centered on clicked node

---

## UC-005: Switch to Project Team View

**Actor**: Project Manager / Business Analyst  
**Precondition**: Org chart page loaded in HRM mode  
**Trigger**: User clicks "Project Team" context toggle  
**Basic Flow**:
1. System shows project selector dropdown
2. User selects project from list
3. System loads project team members from ksf_PM
4. Org chart displays with project manager at center
5. Reports shown below (levelsDown)

**Postcondition**: Org chart shows project team structure

---

## UC-006: View Project Team Structure

**Actor**: Project Manager  
**Precondition**: Project Team mode active, project selected  
**Trigger**: System loads project team  
**Basic Flow**:
1. System loads project assignments from ksf_PM
2. System identifies project manager (PM role)
3. System loads PM's direct reports (BA, Developer roles)
4. Org chart displays centered on PM
5. Role badges displayed: PM (blue), BA (green), Dev (purple)

**Postcondition**: Project team org chart displayed

---

## UC-007: Click Project Team Member Info

**Actor**: Project Manager / Business Analyst  
**Precondition**: Project team org chart displayed  
**Trigger**: User single-clicks on team member box  
**Basic Flow**:
1. System highlights selected box
2. Info panel shows: Role, Department, Email, Phone
3. Extended data: Project assignment details

**Postcondition**: Info panel displayed for team member

---

## UC-008: Drill Down Project Hierarchy

**Actor**: Project Manager  
**Precondition**: Project team org chart displayed  
**Trigger**: User double-clicks on team member box  
**Basic Flow**:
1. System detects double-click
2. System identifies clicked employee
3. System loads clicked employee's org context (HRM mode for manager chain)
4. Org chart re-centers on clicked member

**Postcondition**: Org chart re-centered, manager chain visible

---

## UC-009: Select Display Levels

**Actor**: Employee / Project Manager  
**Precondition**: Org chart displayed  
**Trigger**: User selects level from dropdown  
**Basic Flow**:
1. User clicks level selector (1-5 options)
2. System re-renders org chart with new level count
3. Both levelsUp and levelsDown set to selected value
4. Fit-to-page scaling recalculated

**Postcondition**: Org chart displays with new level count

---

## UC-010: Fit-to-Page Scaling

**Actor**: Employee  
**Precondition**: Org chart displayed with multiple levels  
**Trigger**: System calculates viewBox  
**Basic Flow**:
1. System counts nodes in widest level
2. System calculates total levels displayed
3. System computes viewBox dimensions
4. System applies scale transform (max 100%)
5. Pan/scroll enabled for scaled charts

**Postcondition**: Org chart fits in container

---

## Use Case Summary Table

| UC | Name | Mode | Actor | Trigger |
|----|------|------|-------|---------|
| UC-001 | View Company Org Chart | HRM | Employee | Page load |
| UC-002 | Search Employee by Name | HRM | Employee | Search input |
| UC-003 | Single Click Employee Info | HRM | Employee | Single click |
| UC-004 | Double Click Drill-Down | HRM | Employee | Double click |
| UC-005 | Switch to Project Team View | Project | PM/BA | Context toggle |
| UC-006 | View Project Team Structure | Project | PM | Project select |
| UC-007 | Click Project Team Member Info | Project | PM/BA | Single click |
| UC-008 | Drill Down Project Hierarchy | Project | PM | Double click |
| UC-009 | Select Display Levels | Both | User | Level select |
| UC-010 | Fit-to-Page Scaling | Both | System | Render |

*Document Version: 1.1.0*
*Last Updated: 2026-05-11*