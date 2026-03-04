# Admin Dashboard

> [!info] Quick Reference
> **Audience:** Site administrator
> **Location:** Log in → you land here automatically
> **Direct URL:** `/admin`

The Admin Dashboard is your home base for managing the Okanogan Valley Golf Club website. Every button, link, and setting for the site is reachable from here. Think of it as a control panel — you don't do most of your work *on* this page, but you use it to navigate to wherever you need to go.

---

## Table of Contents

- [Concepts](#concepts)
- [How to Get Here](#how-to-get-here)
- [Dashboard Layout](#dashboard-layout)
  - [Pages Section](#pages-section)
  - [Settings Section](#settings-section)
  - [Developer Tools Section](#developer-tools-section)
  - [Recent Activity Section](#recent-activity-section)
- [Quick-Start: Where Do I Go To…](#quick-start-where-do-i-go-to)
- [Tips & Gotchas](#tips--gotchas)

---

## Concepts

### What is the Admin Panel?
The Admin Panel is a private area of the website that only logged-in administrators can see. Visitors to the public site never have access to it. You use it to update content, change settings, and manage everything that appears on the website.

### How changes reach the live site
When you save something in the admin panel, it takes effect immediately — there is no "publish" step. Visitors to the website will see your changes as soon as their browser loads the next page. If a change doesn't appear right away, try a hard refresh in your browser (`Ctrl + Shift + R` on Windows, `Cmd + Shift + R` on Mac).

### Sections vs. Settings
The admin panel has two types of areas:
- **Content sections** (Pages) — where you manage things visitors read, see, or download (events, gallery images, rates, etc.)
- **Configuration settings** (Settings) — where you control how the site looks and behaves (theme colors, navigation menu, user accounts)

---

## How to Get Here

1. Go to `https://www.okanoganvalleygolf.com/login`
2. Enter your administrator email and password.
3. Click **Log In**.
4. You will land on the Admin Dashboard at `/admin`.

> [!tip]
> Bookmark `/admin` directly. Once you are logged in, visiting that URL always brings you back to this page.

<!-- SCREENSHOT: The login page at /login — show the email/password form with the site logo above it -->

---

## Dashboard Layout

The dashboard is divided into four collapsible boxes. Each box has a title bar — click the title bar to expand or collapse that section.

<!-- SCREENSHOT: The full Admin Dashboard page as it appears when first loaded — all four sections visible, showing the Pages, Settings, Developer Tools, and Recent Activity boxes -->

---

### Pages Section

This section contains buttons that take you to each content area of the site. These are the places you will visit most often.

<!-- SCREENSHOT: The Pages section of the dashboard — the grid of colored buttons (Homepage, Gallery, Rates, etc.) with the section header "Pages" visible -->

| Button | What it manages |
|--------|----------------|
| **Homepage** | The main landing page — hero image and text, the three content cards, and the bottom section |
| **Gallery** | The photo gallery — upload new photos, remove old ones, change captions, reorder images |
| **Rates** | Green fees and other pricing — organized into groups (e.g., "Weekday Rates") with individual line items inside each group |
| **Membership** | Membership plans and their benefits — organized the same way as Rates (groups → items) |
| **Banners** | Temporary announcement bars that appear at the top or bottom of specific pages (e.g., "Course closed for maintenance") |
| **Events** | The golf calendar — add tournaments, league days, and other events; mark dates as cancelled; enter tournament results |
| **Board Members** | The list of board members shown on the About/Board page — names, titles, email addresses |
| **Board Minutes** | Meeting minutes available for download — upload PDFs with the meeting date |

---

### Settings Section

This section contains buttons for site-wide configuration. Changes here affect the whole site, not just one page.

<!-- SCREENSHOT: The Settings section of the dashboard — the row of buttons (Theme, Menu, Users, Logs) with the section header "Settings" visible -->

| Button | What it manages |
|--------|----------------|
| **Theme** | Site-wide appearance — colors, fonts, logo image, favicon (the tiny icon in the browser tab), header style, site name, contact details |
| **Menu** | The navigation bar at the top of every page — add links, create dropdown menus, change the order |
| **Users** | Administrator accounts — create new admin logins, change passwords, deactivate old accounts |
| **Logs** | A running record of activity and errors on the site — useful for troubleshooting if something goes wrong |

---

### Developer Tools Section

> [!warning]
> This section is only visible when the site is in **debug mode** (a development setting). On the live production site, this section is hidden entirely. Do not rely on these links for day-to-day work.

These tools are for the site developer to test and diagnose technical issues. As a site admin you can ignore this section — it will not appear on the public site.

| Button | Purpose |
|--------|---------|
| **Debug Info** | Shows technical details about the current page request |
| **Test 404** | Loads the "Page Not Found" error page to confirm it looks correct |
| **Test 500** | Loads the "Server Error" page to confirm it looks correct |
| **Test Email** | Sends a test email to confirm the site's email delivery is working |

---

### Recent Activity Section

At the bottom of the dashboard is a feed showing the last 5 activity log entries. These are automatically recorded by the site — you do not need to do anything to maintain them.

<!-- SCREENSHOT: The Recent Activity section — showing 2–3 sample log entries with their timestamps and colored level badges (error = red, warning = yellow, info = blue) -->

Each entry shows:
- **Level badge** — color-coded label indicating severity:
  - Blue **info** — routine activity (a user logged in, content was saved)
  - Yellow **warning** — something unexpected happened but the site recovered
  - Red **error** — something failed and may need attention
- **Message** — a brief description of what happened
- **Timestamp** — when it occurred

Click **View all logs** at the bottom of the section to see the full log history.

> [!note]
> If no log entries appear, the logs table may be empty (normal on a fresh install) or an error occurred reading it. Either way, the rest of the dashboard works normally.

---

## Quick-Start: Where Do I Go To…

| I want to… | Go to |
|------------|-------|
| Change the homepage hero image or text | **Pages → Homepage** |
| Add or remove a photo from the gallery | **Pages → Gallery** |
| Update green fee prices | **Pages → Rates** |
| Add a new tournament to the calendar | **Pages → Events → Add Event** |
| Post tournament results | **Pages → Events → (find the event) → Results** |
| Cancel an event occurrence | **Pages → Events → (find the event) → Cancel** |
| Add a temporary announcement banner | **Pages → Banners → Add Banner** |
| Upload new meeting minutes | **Pages → Board Minutes → Upload** |
| Update a board member's title or email | **Pages → Board Members** |
| Change the site's primary color | **Settings → Theme** |
| Add a new link to the navigation bar | **Settings → Menu** |
| Create a new admin account | **Settings → Users → Add User** |
| Check if an email error was logged | **Settings → Logs** |
| Change the site name or contact phone number | **Settings → Theme** |

---

## Tips & Gotchas

> [!tip] Changes are instant
> There is no draft/preview mode. The moment you click **Save**, the change is live on the public website. For big changes (like replacing the hero image), consider doing it during off-peak hours.

> [!tip] Stuck? Check the logs first
> If something doesn't seem to be saving or an email didn't send, go to **Settings → Logs** and look for a recent red **error** entry. The message there usually explains what went wrong.

> [!warning] Deleting is permanent
> Deleting a gallery image, event, board member, or any other item cannot be undone. The site has no recycle bin or undo feature. When in doubt, check twice before clicking Delete.

> [!warning] You cannot delete your own account
> The Users section will not let you delete the account you are currently logged in with. This prevents accidentally locking yourself out. To remove your own account, have another admin do it — or create a second admin first, log in as that user, then delete the original.

> [!note] Self-registration is disabled
> The public-facing "Register" page does not work — it redirects to the login page. New admin accounts must be created manually by an existing administrator using **Settings → Users**.

> [!note] Session timeout
> If you leave the admin panel open in your browser for an extended period without activity, your session may expire and you will be redirected to the login page. Any unsaved form work will be lost. Save frequently.

> [!info] One admin layout, one site
> The admin panel uses the same visual theme as the public website (same colors, same fonts). This is intentional — changes you make in Theme settings affect both the public site and the admin panel simultaneously.
