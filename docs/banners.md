# Page Banners

> [!info] Quick Reference
> **Audience:** Site administrator
> **Location:** Admin Dashboard → Banners, or navigate directly to `/admin/banners`
> **Direct URL:** `/admin/banners`

Page banners are eye-catching announcement bars that appear at the top or bottom of any page on the website. Use them to highlight important news, seasonal reminders, course closures, or special events — without touching any other part of the page.

---

## Table of Contents

- [Concepts](#concepts)
  - [What is a Banner?](#what-is-a-banner)
  - [How Banners Work](#how-banners-work)
  - [Banner Colours](#banner-colours)
  - [Scheduling](#scheduling)
  - [Dismissable Banners](#dismissable-banners)
- [How to View All Banners](#how-to-view-all-banners)
- [How to Create a Banner](#how-to-create-a-banner)
- [How to Edit a Banner](#how-to-edit-a-banner)
- [How to Delete a Banner](#how-to-delete-a-banner)
- [How to Temporarily Disable a Banner](#how-to-temporarily-disable-a-banner)
- [Field Reference](#field-reference)
- [Tips & Gotchas](#tips--gotchas)

---

## Concepts

### What is a Banner?

A **banner** is a coloured notification bar that stretches across the width of a page. It appears either just below the page header image (called **Top** position) or just above the footer (called **Bottom** position). Banners are great for short, attention-grabbing messages — think one or two sentences at most.

<!-- SCREENSHOT: The homepage with a blue "info" banner visible just below the hero/header image, showing how it looks to a site visitor -->

### How Banners Work

- Each banner is tied to a specific **page path** — the part of the web address after the domain name. For example, the home page is `/`, the rates page is `/rates`, and the events page is `/events`.
- Only the banners assigned to the page a visitor is currently viewing are shown. A banner on `/rates` will not appear on `/events`.
- Banners are sorted by their **Sort Order** number, lowest first. If two banners are on the same page, the one with the lower sort order appears first (higher up).
- The system checks three things before showing a banner to a visitor:
  1. The **Active** checkbox must be checked.
  2. If a **Start Date** was set, that date must have already passed.
  3. If an **End Date** was set, that date must not have passed yet.

### Banner Colours

| Colour Name | Appearance | Best Used For |
|-------------|-----------|---------------|
| **Info** | Blue bar | General announcements, reminders |
| **Warning** | Yellow bar | Cautions, schedule changes, weather notices |
| **Danger** | Red bar | Urgent alerts, closures |
| **None** | Plain/transparent | Subtle messages that blend with the page |

<!-- SCREENSHOT: The create/edit form with the Colour dropdown open, showing all four options -->

### Scheduling

You can set a **Start Date** and/or **End Date** on a banner to control exactly when it appears:

- Leave both blank → banner shows any time it is Active.
- Set only a Start Date → banner becomes visible on that date and stays visible indefinitely (while Active).
- Set only an End Date → banner shows immediately (while Active) and automatically disappears on that date.
- Set both → banner appears only between those two dates.

This means you can set up a seasonal banner weeks in advance and never have to remember to remove it.

### Dismissable Banners

If the **Dismissable** option is turned on, a small **×** button appears on the banner. When a visitor clicks it, the banner disappears immediately and will not reappear for that visitor for 30 days, even if they navigate to other pages or come back later. This preference is stored in their browser — clearing browser cookies will reset it.

> [!note] Dismissal is per-visitor and per-browser
> If someone dismisses a banner on their phone, it will still appear on their desktop (and vice versa) until they dismiss it there too. You cannot "reset" dismissals for all visitors at once.

---

## How to View All Banners

1. Log in to the admin panel.
2. Click **Banners** in the navigation menu, or go directly to `/admin/banners`.
3. You will see a table listing every banner in the system.

<!-- SCREENSHOT: The full /admin/banners list page showing the table with several banners, including columns for Page, Position, Text (truncated), Colour, Sort Order, Dismissable, Active, Start, and End, plus Edit and Delete buttons -->

The table shows each banner's key details at a glance:

| Column | What It Means |
|--------|---------------|
| **Page** | The page path where this banner will appear |
| **Position** | Top (below the header image) or Bottom (above the footer) |
| **Text** | A preview of the banner message (long text is cut off) |
| **Colour** | The colour style applied to the banner |
| **Sort** | The order number — lower numbers appear first |
| **Dismiss** | Yes/No — whether visitors can close this banner |
| **Active** | Whether the banner is currently turned on |
| **Start / End** | Scheduled start and end dates, if any |

---

## How to Create a Banner

1. Go to **Admin → Banners** (`/admin/banners`).
2. Click the **Create Banner** button near the top of the page.

<!-- SCREENSHOT: The /admin/banners page with the "Create Banner" button highlighted -->

3. Fill in the form fields (see [Field Reference](#field-reference) for details on each one).

<!-- SCREENSHOT: The full Create Banner form with all fields visible and the Page field showing the autocomplete suggestions dropdown -->

4. Type your message in the **Text** field. Keep it short — one or two sentences works best.
5. Choose a **Colour** that matches the urgency of your message (blue for general info, yellow for caution, red for urgent).
6. In the **Page** field, type the page path where you want the banner to appear. You can start typing and suggestions will appear based on your site's menu. Examples:
   - Home page: `/`
   - Rates page: `/rates`
   - Events page: `/events`
   - Contact page: `/contact`
7. Choose **Top** to place the banner below the page header image, or **Bottom** to place it just above the footer.
8. *(Optional)* Set **Start At** and/or **End At** dates if you want the banner to appear only during a specific window.
9. *(Optional)* Check **Dismissable** if you want visitors to be able to close the banner.
10. Leave **Active** checked to make the banner live immediately (or as soon as the Start Date arrives).
11. Click **Create Banner** to save.

> [!tip] Test before you go live
> After creating a banner, open the page it's assigned to in a new browser tab (not the admin panel) to confirm it looks right and appears where you expect.

---

## How to Edit a Banner

1. Go to **Admin → Banners** (`/admin/banners`).
2. Find the banner you want to change in the list.
3. Click the **Edit** button on that row.

<!-- SCREENSHOT: The banner list table with the Edit button on one row highlighted/circled -->

4. The edit form opens, pre-filled with the current values.

<!-- SCREENSHOT: The Edit Banner form pre-filled with existing data, showing all fields -->

5. Make your changes and click **Update Banner** to save.

> [!note] Changes are immediate
> As soon as you save, the updated banner appears on the live site. There is no draft or preview mode — visitors will see the change right away.

---

## How to Delete a Banner

1. Go to **Admin → Banners** (`/admin/banners`).
2. Find the banner in the list.
3. Click the **Delete** button on that row.
4. A confirmation dialog will appear asking you to confirm. Click **OK** (or confirm) to permanently delete it.

<!-- SCREENSHOT: The browser's confirmation dialog that appears after clicking Delete, asking "Are you sure you want to delete this banner?" -->

> [!warning] Deletion is permanent
> Deleted banners cannot be recovered. If you only want to hide a banner temporarily, uncheck **Active** instead of deleting it — that way you can re-enable it later without re-entering everything.

---

## How to Temporarily Disable a Banner

You do not need to delete a banner to stop showing it. You can turn it off and on at any time:

1. Go to **Admin → Banners** (`/admin/banners`).
2. Click **Edit** on the banner you want to pause.
3. Uncheck the **Active** checkbox.
4. Click **Update Banner**.

The banner will disappear from the site immediately. Uncheck **Active** whenever you want to bring it back.

---

## Field Reference

| Field | Required? | What to Enter |
|-------|-----------|---------------|
| **Page** | Yes | The page path where this banner should appear. Always starts with `/`. Use `/` for the home page. Examples: `/rates`, `/events`, `/contact` |
| **Position** | Yes | **Top** — appears below the page header image. **Bottom** — appears above the footer. |
| **Colour** | Yes | **Info** (blue), **Warning** (yellow), **Danger** (red), or **None** (plain). |
| **Text** | Yes | The message to display. Plain text; keep it to one or two sentences. Supports template variables (see tip below). |
| **Start At** | No | Date and time when the banner should start appearing. Leave blank to show immediately (when Active). |
| **End At** | No | Date and time when the banner should stop appearing. Leave blank to show indefinitely. |
| **Sort Order** | No | A number controlling display order when multiple banners share the same page and position. Lower number = displayed first. Defaults to `0`. |
| **Dismissable** | No | Check this to add an × close button. Visitors who click it won't see the banner again for 30 days. |
| **Active** | No | Uncheck to hide the banner without deleting it. Checked by default. |

> [!tip] Dynamic text with template variables
> You can include contact details from your site's Theme Settings directly in a banner message using these placeholders. They are replaced automatically with your actual club information when the banner is displayed.
>
> | Placeholder | Replaced With |
> |-------------|---------------|
> | `{{name}}` | Club name |
> | `{{phone}}` | Club phone number |
> | `{{email}}` | Club email address |
> | `{{address1}}` | Street address line 1 |
> | `{{address2}}` | Street address line 2 |
> | `{{city}}` | City |
>
> **Example text:** `Call us at {{phone}} to book your tee time.`
> **Displayed as:** `Call us at (509) 422-3666 to book your tee time.`

---

## Tips & Gotchas

> [!tip] One banner per page is usually enough
> Stacking multiple banners on the same page can make the site feel cluttered. If you have more than one message to share, consider whether they could be combined into one banner or placed on different pages.

> [!warning] The Page field must match exactly
> The page path must exactly match what appears in the browser's address bar — including capitalisation. The path `/Rates` is different from `/rates`. Stick to lowercase. If your banner is not appearing, double-check the page path against the actual URL.

> [!warning] The home page path is a single slash
> To put a banner on the home page, the Page field should be exactly `/` — just a forward slash, nothing else. Do not leave it blank.

> [!note] Scheduling uses the server's clock, not yours
> Start and End dates are compared against the web server's time (US Pacific). If you're in a different time zone, account for the difference when setting times.

> [!note] Visitors don't see changes until they reload
> If a visitor already has a page open when you update or remove a banner, they won't see the change until they refresh their browser. This is normal behaviour for any website.

> [!tip] Use Sort Order to control stacking
> If you regularly run multiple banners on the homepage — for example, a weather alert plus a seasonal promo — assign them sort order numbers like `10` and `20` so there's room to insert new ones between them without renumbering everything.

> [!tip] Check the live page after every change
> The admin panel does not show a preview of how a banner looks on the real page. After creating or editing a banner, open the target page in a regular browser tab to confirm the colour, position, and message all look right.
