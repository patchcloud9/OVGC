# Homepage — Editing Hero Content & Featured Sections

The homepage is the first thing visitors see when they come to the website. As an admin, you can change the large banner at the top (called the "hero"), the three feature cards in the middle, and the content block at the bottom — all without touching any code.

---

## Table of Contents

1. [How the Homepage Is Organized](#how-the-homepage-is-organized)
2. [Getting to the Homepage Editor](#getting-to-the-homepage-editor)
3. [Template Variables — Insert Club Info Automatically](#template-variables--insert-club-info-automatically)
4. [Editing the Hero Banner](#editing-the-hero-banner)
5. [Editing the Feature Cards](#editing-the-feature-cards)
6. [Editing the Bottom Content Section](#editing-the-bottom-content-section)
7. [Saving Your Changes](#saving-your-changes)
8. [Field Reference](#field-reference)
9. [Tips & Gotchas](#tips--gotchas)

---

## How the Homepage Is Organized

The public homepage has four main zones, from top to bottom:

| Zone | What It Is |
|------|------------|
| **Hero Banner** | Full-width banner with a title, subtitle, and background color or photo |
| **Feature Cards** | Three side-by-side info cards, each with a heading, description, and optional button |
| **Weather & Camera** | Auto-updating widgets (not editable here — they run automatically) |
| **Bottom Content Section** | A two-column block with a heading, paragraph text, and optional photo |

<!-- SCREENSHOT: Full public homepage as seen by a visitor — show all four zones labeled with numbered callouts -->

---

## Getting to the Homepage Editor

1. Log in to the admin panel.
2. In the top navigation, click **Admin** (or go directly to `/admin`).
3. Click **Homepage** in the admin menu.

You will land on the **Homepage Settings** form.

<!-- SCREENSHOT: Admin navigation menu with "Homepage" link highlighted -->

> [!info] One form, one save
> Everything on this page — hero, cards, and bottom section — is saved with a single **Save Settings** button at the bottom of the form. You do not need to save each section separately.

---

## Template Variables — Insert Club Info Automatically

At the top of the Homepage Settings form, you will see a yellow reference box listing special codes called **template variables**. These are shortcuts you can type anywhere in a text field, and the website will automatically replace them with your club's real contact information.

| What you type | What visitors see |
|---------------|-------------------|
| `{{name}}` | The club name |
| `{{email}}` | A clickable email link |
| `{{phone}}` | A clickable phone number |
| `{{address1}}` | Street address (line 1) |
| `{{address2}}` | Street address (line 2) |
| `{{city}}` | City, state, and zip |

**Example:** If you type `Call us at {{phone}} or email {{email}}` in a card description, visitors will see those as live, clickable links.

> [!tip] Where to change these values
> The actual club name, phone, address, and email are managed under **Admin → Theme Settings** (`/admin/theme`). Change them there once, and they update everywhere on the site automatically.

<!-- SCREENSHOT: The yellow "Template Variables" reference box at the top of the Homepage Settings form -->

---

## Editing the Hero Banner

The hero banner is the large, attention-grabbing strip at the very top of the homepage. It can show a background photo or a solid color, and it can display a title and subtitle on top.

<!-- SCREENSHOT: The "Hero Section" form box in Homepage Settings, showing the title/subtitle fields and color pickers -->

### Set the Title and Subtitle

1. In the **Hero Section** box, find the **Title** field.
2. Type the main heading you want (e.g., `Welcome to Okanogan Valley Golf Club`). Leave it blank to hide the title entirely.
3. Find the **Subtitle** field and type a short line of supporting text (e.g., `18 holes of championship golf`). Leave it blank to hide it. Supports template variables (see above).

### Change Text Colors

Each text field has a matching color picker next to it. You can change the color in two ways:

- **Click the colored square** to open a color chooser and pick visually.
- **Type a hex color code** directly in the text box next to the square (e.g., `#ffffff` for white).

Both controls stay in sync — change one and the other updates automatically.

> [!note] What is a hex color code?
> A hex code is a six-character code starting with `#` that represents a color. `#ffffff` is white, `#000000` is black, `#667eea` is a medium purple-blue. You can find hex codes using any online color picker.

<!-- SCREENSHOT: Close-up of one color picker pair — the colored square open, the hex input field next to it, and the sync behavior visible -->

### Set the Background

You have two choices for the hero background:

**Option A — Solid Color:**
Use the **Background Color** picker to choose a color. The default is a blue-purple.

**Option B — Photo:**
1. Click **Choose File** under **Background Image**.
2. Select a photo from your computer (JPEG, PNG, GIF, or WebP; max 5 MB).
3. Save the form — the photo will appear as the hero background.

> [!warning] Image takes priority
> If you have both a background color and a background image uploaded, the **photo will always be shown** and the color will be hidden behind it. To show only the color, remove the image first.

### Remove the Hero Photo

If you want to go back to a solid color background after uploading a photo:

1. Find the thumbnail preview of your current hero photo.
2. Click the red **Remove Image** button below it.
3. Confirm when prompted. The photo is deleted immediately.

> [!warning] Removal is instant
> Clicking "Remove Image" deletes the photo right away — you do not need to click Save Settings afterward. There is no undo.

<!-- SCREENSHOT: The hero image upload area showing a current image thumbnail and the red "Remove Image" button -->

---

## Editing the Feature Cards

Below the hero, the homepage shows three cards side by side. Each card has a heading, a description, and an optional button.

<!-- SCREENSHOT: The "Feature Cards" section in the admin form, showing Card 1's fields expanded — title, text area, button text, and button link -->

### Edit a Card

Each card (Card 1, Card 2, Card 3) has the same four fields:

1. **Title** — The bold heading at the top of the card. Required; cannot be left blank.
2. **Description** — The paragraph below the heading. Optional; supports template variables and line breaks.
3. **Button Text** — The label on the card's button (e.g., `Learn More`, `Book a Tee Time`). Leave blank to hide the button entirely.
4. **Button Link** — The page the button leads to when clicked. Use the path after the domain name.

**Button Link examples:**

| Where you want to send visitors | What to type |
|----------------------------------|--------------|
| The About page | `/about` |
| The Rates page | `/rates` |
| The Contact page | `/contact` |
| An external website | `https://www.example.com` |

> [!tip] Hiding a button
> If you do not want a button on a card, simply leave the **Button Text** field empty. No button will appear, even if you have a link typed in.

<!-- SCREENSHOT: The three cards as they appear on the live public homepage, showing heading, description text, and a "Learn More" button on one card -->

---

## Editing the Bottom Content Section

At the bottom of the homepage (above the footer) is a two-column block — text on one side and a photo on the other. This is a good place for an "About the Club" summary or a seasonal message.

<!-- SCREENSHOT: The "Bottom Content Section" form box, showing the layout radio buttons, title/text fields, and image upload area -->

### Choose the Layout

Two radio button options control which side the photo sits on:

| Option | What it looks like |
|--------|--------------------|
| **Text Left, Image Right** | Paragraph on the left, photo on the right |
| **Image Left, Text Right** | Photo on the left, paragraph on the right |

Select the one that looks best with your photo.

<!-- SCREENSHOT: Side-by-side mockup or the live bottom section showing both layout options (one real, one described) -->

### Add the Title and Text

1. **Title** — The section heading (e.g., `About Our Course`). Required.
2. **Text** — The body paragraph(s). Optional; you can press Enter for line breaks. Supports template variables.

### Add or Change the Photo

1. Click **Choose File** under **Bottom Section Image**.
2. Select a photo (JPEG, PNG, GIF, or WebP; max 5 MB).
3. Save the form.

To remove the photo, click the red **Remove Image** button below the current image preview and confirm.

> [!note] Section visibility
> The bottom section only appears on the public homepage if at least one of Title, Text, or Image has content. If all three are empty, the section is hidden automatically.

---

## Saving Your Changes

When you are done editing any part of the form:

1. Scroll to the bottom of the page.
2. Click the green **Save Settings** button.
3. Wait for the page to reload. A green success bar will appear at the top confirming your changes were saved.

<!-- SCREENSHOT: The bottom of the form showing the "Save Settings" and "Back to Admin" buttons, and a green success flash message at the top of the page after saving -->

> [!tip] Preview your changes
> After saving, click **Back to Admin** and then click the site logo or navigate to `/` to see the public homepage with your changes applied.

---

## Field Reference

### Hero Section

| Field | Required | Max Length | Notes |
|-------|----------|------------|-------|
| Title | No | 100 characters | Leave blank to hide |
| Title Color | No | — | Hex code, e.g. `#ffffff` |
| Subtitle | No | 255 characters | Supports `{{template}}` variables |
| Subtitle Color | No | — | Hex code |
| Background Color | No | — | Hex code; hidden if image is uploaded |
| Background Image | No | 5 MB max | JPEG, PNG, GIF, or WebP |

### Feature Cards (applies to each of Card 1, 2, 3)

| Field | Required | Max Length | Notes |
|-------|----------|------------|-------|
| Title | **Yes** | 100 characters | Card heading |
| Description | No | Unlimited | Supports template variables and line breaks |
| Button Text | No | 100 characters | Leave blank to hide the button |
| Button Link | No | 255 characters | Use `/path` for internal pages or full URL for external |

### Bottom Content Section

| Field | Required | Max Length | Notes |
|-------|----------|------------|-------|
| Layout | **Yes** | — | Choose text-left or image-left |
| Title | **Yes** | 255 characters | Section heading |
| Text | No | Unlimited | Supports template variables and line breaks |
| Image | No | 5 MB max | JPEG, PNG, GIF, or WebP |

---

## Tips & Gotchas

> [!tip] Keep hero text short
> The hero title and subtitle overlay the background. Very long text can wrap awkwardly on phones. Aim for a title under 6–8 words and a subtitle under 12–15 words.

> [!tip] Use high-contrast text colors
> If your hero background is dark (a dark photo or dark color), use a light text color like `#ffffff`. If the background is light, use a dark color like `#1a1a1a`. Low contrast makes text hard to read.

> [!warning] Image removal is permanent
> Clicking **Remove Image** on either the hero or bottom section deletes the file immediately — no confirmation step beyond the browser pop-up, and no undo. Download the photo to your computer first if you think you might want it back.

> [!warning] Card titles cannot be blank
> All three card titles are required. If you try to save with a blank card title, the form will fail silently (you will be returned to the form without a save confirmation). Check that all three titles have text.

> [!note] Line breaks in descriptions
> When you press Enter in a card description or the bottom section text, those line breaks show up as paragraph breaks on the live site. You do not need to do anything special — just write naturally.

> [!note] Button links for external sites
> If a button should open a different website (e.g., an online booking service), use the full address including `https://`, like `https://www.golfnow.com/`. For pages on your own site, use just the path, like `/contact`.

> [!tip] Template variables only work in certain fields
> Template variables (`{{phone}}`, `{{email}}`, etc.) work in the hero subtitle, card descriptions, and the bottom section title and text. They do **not** work in the hero title, card titles, or button text fields.
