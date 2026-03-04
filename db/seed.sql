USE mgis_tshirt_shop;

INSERT INTO products (slug, name, description, base_price, design_code, is_featured) VALUES
('city-grid', 'City Grid Tee', 'Urban linework design inspired by map layers and transit routes.', 22.00, 'DESIGN-001', 1),
('summit-lines', 'Summit Lines Tee', 'Minimal contour art with a hiking and outdoors theme.', 22.00, 'DESIGN-002', 1),
('retro-wave', 'Retro Wave Tee', 'Sunset stripes and a retro print treatment.', 24.00, 'DESIGN-003', 0),
('midnight-ink', 'Midnight Ink Tee', 'High-contrast single-ink design for a bold look.', 24.00, 'DESIGN-004', 0),
('lakeside-run', 'Lakeside Run Tee', 'Athletic-inspired shirt with motion graphics.', 23.00, 'DESIGN-005', 0),
('signal-flare', 'Signal Flare Tee', 'Sharp geometric print with a modern graphic feel.', 23.00, 'DESIGN-006', 0),
('north-shore', 'North Shore Tee', 'Coastal typography with a relaxed resort aesthetic.', 22.00, 'DESIGN-007', 0),
('field-notes', 'Field Notes Tee', 'Adventure sketch layout with map-inspired marks.', 22.00, 'DESIGN-008', 0),
('campus-press', 'Campus Press Tee', 'Collegiate print style for a campus merch feel.', 21.00, 'DESIGN-009', 1);

INSERT INTO product_variants (product_id, color_name, color_hex, size_code, image_path, quantity_in_stock)
SELECT p.id, colors.color_name, colors.color_hex, sizes.size_code, NULL, 12
FROM products p
CROSS JOIN (
    SELECT 'Black' AS color_name, '#111111' AS color_hex
    UNION ALL SELECT 'White', '#F5F5F5'
    UNION ALL SELECT 'Navy', '#1F3159'
    UNION ALL SELECT 'Forest', '#355E3B'
) colors
CROSS JOIN (
    SELECT 'S' AS size_code
    UNION ALL SELECT 'M'
    UNION ALL SELECT 'L'
    UNION ALL SELECT 'XL'
    UNION ALL SELECT '2XL'
) sizes;

INSERT INTO users (first_name, last_name, email, password_hash)
VALUES ('Demo', 'Customer', 'demo@example.com', '$2y$10$exampleplaceholderhashforplanningonly');
