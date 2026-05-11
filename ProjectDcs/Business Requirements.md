# Business Requirements - ksf_WP_OrgChart

## Project Overview
ksf_WP_OrgChart provides an interactive organizational hierarchy visualization for WordPress Employee Self Service (ESS) portals, supporting both HRM organizational hierarchy AND Project Management team structure.

## Context Modes

| Mode | Source | Use Case |
|------|--------|----------|
| **HRM Mode** (default) | ksf_OrgChart + ksf_HRM | Company org chart - reporting hierarchy |
| **Project Mode** | ksf_ProjectManagement + ksf_OrgChart | Project team org - contract team members |

### Project Mode Use Cases
- Project managers can view their project team structure
- Business analysts can see reporting chain for assigned projects
- Executives can see contract team members alongside company org

## Integration Dependencies

### Provided To
| Module | Data Provided |
|--------|---------------|
| ksf_WP | WordPress plugin integration |

### Consumed From
| Module | Data Consumed |
|--------|---------------|
| ksf_OrgChart | Org node data, hierarchy |
| ksf_HRM | Employee public data (dept, title, email, ext) |
| ksf_ProjectManagement | Project team members, assignments |

## Business Value

### Problem Statement
Users cannot easily visualize either their organization's reporting structure OR their project team assignments.

### Solution
Interactive org chart with two context modes:
1. **Company Org Mode**: Standard HRM reporting hierarchy
2. **Project Team Mode**: Project team member structure

### Key Features
1. **Context Toggle**: Switch between Company Org / Project Team modes
2. **Employee Search** - Find any employee by name/email
3. **Single Click Info** - Display public employee data (dept, title, email, ext)
4. **Double Click Drill-Down** - Center on any employee/team member
5. **Level Selection** - 1-5 levels up/down display
6. **Fit to Page Scaling** - Auto-zoom for any display size
7. **Project Role Badges** - Show PM, BA, Developer roles in Project mode

### User Stories
- As an employee, I want to search for colleagues by name so I can find contact information
- As an employee, I want to click on an org chart box to see department/title/email so I know who to contact
- As an employee, I want to double-click to center the org chart on any employee so I can see their reporting chain
- As an HR staff, I want to display N levels of hierarchy so I can visualize team structures
- As a project manager, I want to see my project team structure so I understand contract team assignments
- As a business analyst, I want to see who reports to whom in a project so I can route work appropriately

### User Stories (Project Context)
- UC-001: View Company Org Chart
- UC-002: Search and Find Employee
- UC-003: Single Click Employee Info
- UC-004: Double Click Drill-Down
- UC-005: Switch to Project Team View
- UC-006: View Project Team Structure
- UC-007: Click Project Team Member Info
- UC-008: Drill Down Project Hierarchy
- UC-009: Select Display Levels

### Compliance
- GDPR: Only public employee data displayed (no private addresses)
- No personal photos without consent

*Document Version: 1.1.0*
*Last Updated: 2026-05-11*