# Menu Management

> [!info] Quick Reference
> **Audience:** Site administrator
> **Location:** Admin panel → **Menu** (sidebar)
> **Direct URL:** `/admin/menu`

The navigation menu is fully database-driven. Every item you see in the top nav bar is a row in the database, managed entirely through the admin interface — no code changes needed.

---

## Table of Contents

- [Concepts](#concepts)
- [Visibility Rules](#visibility-rules)
- [Viewing the Menu List](#viewing-the-menu-list)
- [Adding a Menu Item](#adding-a-menu-item)
- [Editing a Menu Item](#editing-a-menu-item)
- [Creating a Dropdown](#creating-a-dropdown)
- [Reordering Items](#reordering-items)
- [Hiding vs. Deleting an Item](#hiding-vs-deleting-an-item)
- [System Items](#system-items)
- [Icons Reference](#icons-reference)
- [Field Reference](#field-reference)

---

## Concepts

### Top-Level vs. Child Items

Items with no parent appear directly in the navbar. Items assigned a **Parent Menu Item** appear inside a dropdown under that parent.

```
Home              ← top-level
About             ← top-level
Golf              ← top-level (parent, URL = #)
  ├── Rates       ← child of Golf
  ├── Course Map  ← child of Golf
  └── Pro Shop    ← child of Golf
Contact           ← top-level
```

A parent item typically uses `#` as its URL so it acts as a label, not a link.

### Display Order

Items are sorted by a numeric order value. Lower numbers appear first. Siblings (items sharing the same parent, or all top-level items) are ordered independently — reordering a child does not affect top-level ordering.

---

## Visibility Rules

Each item has one of three visibility settings:

| Setting | Who Sees It |
|---|---|
| **Public** | Everyone — guests, logged-in members, admins |
| **Authenticated** | Logged-in users only (members + admins) |
| **Admin** | Admin users only |

> [!note]
> Visibility applies per-item. A child item can have a stricter visibility than its parent. For example: parent is Public, child is Authenticated — guests will see the parent dropdown label but not that child inside it.

The **Active** toggle is separate from visibility. An inactive item is hidden from everyone regardless of their role.

---

## Viewing the Menu List

Navigate to **Admin → Menu**. You'll see a table of all menu items in display order, with children indented under their parents.

<!-- SCREENSHOT: Full menu list table — show top-level items and indented children, the ↑↓ order arrows, pencil/trash action buttons, and colored Active/System tags -->

Column guide:

| Column | Meaning |
|---|---|
| **Order** | ↑/↓ arrows to move item up or down |
| **Title** | Display name shown in the navbar |
| **URL** | Link destination |
| **Icon** | Font Awesome class (if set) |
| **Parent** | Parent item name, or — for top-level |
| **Visibility** | public / authenticated / admin |
| **Status** | Green = Active, Gray = Inactive |
| **Actions** | Edit (pencil) / Delete (red trash) |

Items tagged **System** are protected — they cannot be edited or deleted.

---

## Adding a Menu Item

1. Go to **Admin → Menu**.
2. Click **Add Menu Item** (top-right).
3. Fill in the form (see [Field Reference](#field-reference) below).
4. Click **Save Menu Item**.

The new item is added at the end of its group. Use the ↑/↓ arrows to move it into position afterward.

<!-- SCREENSHOT: The "Add Menu Item" form with example values filled in (e.g., Title: "Scorecard", URL: "/scorecard", Visibility: Public, Active checked) -->

### Quick Example — Adding a Top-Level Link

| Field | Value |
|---|---|
| Title | Scorecard |
| URL | `/scorecard` |
| Parent | *(leave blank)* |
| Visibility | Public |
| Active | ✓ |

### Quick Example — Adding a Dropdown Child

| Field | Value |
|---|---|
| Title | Course Map |
| URL | `/course-map` |
| Parent | Golf *(select from the dropdown)* |
| Visibility | Public |
| Active | ✓ |

---

## Editing a Menu Item

1. In the menu list, click the **pencil icon** next to the item.
2. Change any fields you need.
3. Click **Update Menu Item**.

<!-- SCREENSHOT: Edit form for an existing item — all fields visible and populated with real data -->

> [!tip]
> You can move a top-level item into a dropdown (or vice versa) just by changing the Parent field. The item keeps its URL and all other settings.

---

## Creating a Dropdown

Dropdowns need two things: a parent item (the label) and one or more child items (the links inside).

### Step 1 — Create the Parent (Label)

| Field | Value |
|---|---|
| Title | Golf *(or whatever the group label should be)* |
| URL | `#` |
| Parent | *(leave blank)* |
| Visibility | Public |
| Active | ✓ |

Using `#` as the URL makes the item a non-clickable label. The dropdown opens on hover (desktop) or tap (mobile).

<!-- SCREENSHOT: Live navbar with a dropdown open — show the parent label and the child links inside it -->

### Step 2 — Add Children

Create each child item and set **Parent Menu Item** to the parent you just created. Children appear inside the dropdown in their own display order.

> [!warning]
> Only one level of nesting is supported. You cannot put a dropdown inside another dropdown.

---

## Reordering Items

Use the **↑** and **↓** arrow buttons in the Order column. Each click swaps the item with its neighbor.

<!-- SCREENSHOT: Close-up of the Order column showing ↑↓ buttons; ideally show a row with the green highlight that briefly appears after a move -->

Rules:
- Top-level items only swap with other top-level items.
- Children only swap with their siblings (same parent).
- After moving, the page reloads and scrolls back to the item, which flashes green briefly.

> [!tip]
> To move an item many positions, click the arrow repeatedly — there is no drag-and-drop yet.

---

## Hiding vs. Deleting an Item

### Hiding (recommended for temporary removal)

Edit the item and **uncheck Active**. The item disappears from the navbar but stays in the database and can be re-enabled at any time.

<!-- SCREENSHOT: Edit form with Active checkbox unchecked; then show the list view with that item showing an "Inactive" gray tag -->

### Deleting (permanent)

Click the **red trash icon** and confirm the dialog.

> [!warning]
> Deleting a **parent item removes all of its children too**. Before deleting a dropdown parent, decide whether to reassign the children first or delete them along with it. This cannot be undone.

---

## System Items

Items marked **System** are built into the application and cannot be edited or deleted through the admin interface. They show a gray **System** badge in the list.

<!-- SCREENSHOT: A menu list row with the gray "System" badge visible; edit/delete buttons should be absent or visibly disabled -->

If you need to change a system item, contact your developer.

---

## Icons Reference

The **Icon** field accepts any [Font Awesome 5 Free](https://fontawesome.com/icons?m=free) class string. The icon appears inline before the title text in the navbar.

| Icon | Class to Enter |
|---|---|
| Home | `fas fa-home` |
| Calendar | `fas fa-calendar-alt` |
| Golf flag | `fas fa-flag` |
| Info | `fas fa-info-circle` |
| Phone | `fas fa-phone` |
| Email | `fas fa-envelope` |
| User | `fas fa-user` |
| Camera | `fas fa-camera` |
| Weather | `fas fa-cloud-sun` |
| Rates/Dollar | `fas fa-dollar-sign` |
| External link | `fas fa-external-link-alt` |

Leave the field blank if you don't want an icon.

> [!tip]
> Browse the full free library at [fontawesome.com/icons](https://fontawesome.com/icons). Filter by **Free** to see what's available without a paid license.

---

## Field Reference

| Field | Required | Notes |
|---|---|---|
| **Title** | Yes | Text shown in the navbar. 1–100 characters. |
| **URL** | Yes | Relative (`/about`) or full URL (`https://…`). Use `#` for dropdown parents. |
| **Icon** | No | Font Awesome class (e.g., `fas fa-home`). Leave blank for no icon. |
| **Parent Menu Item** | No | Select a top-level item to nest this inside its dropdown. Leave blank for top-level. |
| **Visibility** | Yes | `Public`, `Authenticated`, or `Admin` — see [Visibility Rules](#visibility-rules). |
| **Active** | — | Checked = visible in navbar. Unchecked = hidden from everyone. |
| **Open in new tab** | — | Checked = link opens in a new browser tab. Recommended for external URLs. |

---

## Tips & Gotchas

> [!warning] Dropdown parent must use `#` as the URL
> If you give a dropdown parent a real page URL, tapping it on mobile navigates away instead of opening the dropdown.

> [!note] Children don't inherit the parent's visibility
> Each item has its own visibility setting. A child set to Public under an Admin-only parent won't be seen by guests (the parent is hidden), but the child's own visibility setting still applies independently.

> [!note] Reorder arrows are sibling-scoped
> The arrows won't move an item from one parent to another. To change a child's parent, edit the item and change the Parent field.

> [!warning] Deleting a parent deletes its children too
> When in doubt, hide the item (uncheck Active) instead of deleting it.
