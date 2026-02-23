-- Seed Purchase Content
-- Inserts the default Purchase page content

INSERT INTO purchase_content (
    page_title,
    page_subtitle,
    content_text,
    contact_email,
    button_text,
    button_url
) VALUES (
    'Purchase',
    '',
    'Thank you for your interest in purchasing artwork from Gail''s Original Fine Art. To proceed with a purchase of an origional artwork, please contact me directly at {email} with the details of the artwork you wish to acquire.\n\nFor prints and other merchandise featuring my artwork, please visit our Fine Art America store by clicking the button below.',
    NULL,
    'Fine Art America',
    'https://fineartamerica.com/profiles/gail-butler'
);
