-- Seed Theme Settings
-- Initializes theme configuration with current Bulma-based color palette and site info

INSERT INTO theme_settings (
    primary_color,
    secondary_color,
    accent_color,
    danger_color,
    navbar_color,
    navbar_hover_color,
    navbar_text_color,
    homepage_logo_path,
    secondary_logo_path,
    favicon_path,
    site_name,
    contact_email,
    phone_number,
    address1,
    address2,
    city_state_zip,
    footer_tagline,
    header_style,
    card_style
) VALUES (
    '#667eea',  -- Primary: Bulma purple/blue (used in nav gradient start)
    '#764ba2',  -- Secondary: Bulma purple (used in nav gradient end)
    '#48c78e',  -- Accent: Bulma success green (used for success messages/buttons)
    '#f14668',  -- Danger: Destructive color used for delete buttons, etc.
    '#667eea',  -- Navbar: Background color for navigation bar
    '#ffffff',  -- Navbar Hover: Text color when hovering over nav items
    '#ffffff',  -- Navbar Text: Default text color for navbar links
    NULL,       -- Homepage logo: no custom image yet
    NULL,       -- Secondary logo: reserved for future use
    NULL,       -- Favicon: No custom favicon yet (uses browser default)
    'Base Framework', -- Site name
    'contact@example.com', -- Contact email
    NULL, -- phone number
    NULL, -- address line 1
    NULL, -- address line 2
    NULL, -- city/state/zip
    'Customizable theme and gallery', -- Footer tagline
    'static',   -- Header: Static positioning (default)
    'default'   -- Cards: Default Bulma card style
);
