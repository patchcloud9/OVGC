-- standard
INSERT INTO membership_items (group_id,sort_order,name,price) VALUES
((SELECT id FROM membership_groups WHERE slug='standard'),1,'Single',750.00),
((SELECT id FROM membership_groups WHERE slug='standard'),2,'Couple',1200.00),
((SELECT id FROM membership_groups WHERE slug='standard'),3,'Reduced Single *',400.00),
((SELECT id FROM membership_groups WHERE slug='standard'),4,'Reduced Couple *',750.00);

-- lifetime
INSERT INTO membership_items (group_id,sort_order,name,price) VALUES
((SELECT id FROM membership_groups WHERE slug='lifetime'),1,'Lifetime Single',7250.00),
((SELECT id FROM membership_groups WHERE slug='lifetime'),2,'Lifetime Couple',11500.00);

-- under-30
INSERT INTO membership_items (group_id,sort_order,name,price) VALUES
((SELECT id FROM membership_groups WHERE slug='under-30'),1,'Junior (Under 18)',65.00),
((SELECT id FROM membership_groups WHERE slug='under-30'),2,'College (18–24)',120.00),
((SELECT id FROM membership_groups WHERE slug='under-30'),3,'Young Adult (19–30)',400.00);

-- other prices
INSERT INTO membership_items (group_id,sort_order,name,price) VALUES
((SELECT id FROM membership_groups WHERE slug='other-prices'),1,'Yearly Cart Storage – Electric',300.00),
((SELECT id FROM membership_groups WHERE slug='other-prices'),2,'Yearly Cart Storage – Gas',250.00),
((SELECT id FROM membership_groups WHERE slug='other-prices'),3,'Yearly Trail Fee (Carts from home)',60.00),
((SELECT id FROM membership_groups WHERE slug='other-prices'),4,'Daily Trail Fee (Carts from home)',12.00);
