-- Seed Homepage Settings
-- Inserts default homepage content into homepage_settings table

INSERT INTO homepage_settings (
    hero_title,
    hero_subtitle,
    hero_title_color,
    hero_subtitle_color,
    card1_text,
    card1_button_text,
    card1_button_link,
    card2_text,
    card2_button_text,
    card2_button_link,
    card3_text,
    card3_button_text,
    card3_button_link,
    bottom_section_text
) VALUES (
    'Welcome Home',
    'Your PHP MVC Framework',
    '#ffffff',
    '#f5f5f5',
    'No bloat, no dependencies. Just clean, efficient PHP code that gets the job done.',
    'Learn More',
    '/about',
    'Clear MVC structure with straightforward routing. Perfect for learning or building your own framework.',
    'Learn More',
    '/about',
    'Created as a teaching tool to demonstrate core PHP concepts without the complexity of large frameworks.',
    'Learn More',
    '/about',
    'This is a minimal MVC framework built from scratch to demonstrate routing, controllers, models, and views. Every component is designed to be easy to understand and modify.'
);