-- green fees
INSERT INTO rates (group_id, sort_order, description, price, notes) VALUES
((SELECT id FROM rate_groups WHERE slug='green-fees'),1,'Membership',25.00,NULL),
((SELECT id FROM rate_groups WHERE slug='green-fees'),2,'18 Holes',40.00,NULL),
((SELECT id FROM rate_groups WHERE slug='green-fees'),3,'All Day',50.00,NULL),
((SELECT id FROM rate_groups WHERE slug='green-fees'),4,'Juniors (Under 18)',5.00,'per 9 holes; $10 per 18 holes');

-- reduced membership card rates
INSERT INTO rates (group_id, sort_order, description, price) VALUES
((SELECT id FROM rate_groups WHERE slug='reduced'),1,'Membership',12.00),
((SELECT id FROM rate_groups WHERE slug='reduced'),2,'18 Holes',20.00),
((SELECT id FROM rate_groups WHERE slug='reduced'),3,'All Day',25.00);

-- cart rentals
INSERT INTO rates (group_id, sort_order, description, price) VALUES
((SELECT id FROM rate_groups WHERE slug='cart-rentals'),1,'9 Holes',10.00),
((SELECT id FROM rate_groups WHERE slug='cart-rentals'),2,'18 Holes',18.00),
((SELECT id FROM rate_groups WHERE slug='cart-rentals'),3,'All Day',30.00);
