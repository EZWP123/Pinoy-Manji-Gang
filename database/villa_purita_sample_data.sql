-- Villa Purita Subdivision Sample Data
-- Insert sample properties with various statuses and types

-- Insert sample properties with different statuses (occupied, vacant, for_sale)
INSERT INTO properties (subdivision_id, unit_number, property_type, bedrooms, bathrooms, area_sqm, price, status, latitude, longitude, description, created_at) VALUES

-- Occupied Units (Gray #7f8c8d)
(1, 'VP-001', 'house', 3, 2, 120.5, 4500000, 'occupied', 10.25780, 123.80100, 'Spacious 3-bedroom house with modern amenities', NOW()),
(1, 'VP-002', 'house', 4, 3, 185.0, 6200000, 'occupied', 10.25850, 123.80050, 'Premium 4-bedroom villa with garden', NOW()),
(1, 'VP-003', 'house', 2, 1, 85.0, 2800000, 'occupied', 10.25761, 123.80115, 'Cozy 2-bedroom starter home', NOW()),
(1, 'VP-004', 'lot', 0, 0, 200.0, 1800000, 'occupied', 10.25900, 123.80200, 'Commercial lot in prime location', NOW()),
(1, 'VP-005', 'condo', 2, 1, 75.0, 2200000, 'occupied', 10.25720, 123.80150, 'Modern condo unit with facilities', NOW()),

-- Vacant Units (Green #27ae60)
(1, 'VP-006', 'house', 3, 2, 125.0, 4800000, 'vacant', 10.25790, 123.80080, 'Newly renovated 3-bedroom house', NOW()),
(1, 'VP-007', 'house', 5, 3, 210.0, 7500000, 'vacant', 10.25870, 123.80020, 'Luxury 5-bedroom mansion with pool', NOW()),
(1, 'VP-008', 'lot', 0, 0, 250.0, 2200000, 'vacant', 10.25750, 123.80130, 'Residential corner lot', NOW()),
(1, 'VP-009', 'apartment', 1, 1, 55.0, 1800000, 'vacant', 10.25700, 123.80160, 'Studio apartment unit', NOW()),
(1, 'VP-010', 'condo', 3, 2, 125.0, 3500000, 'vacant', 10.25810, 123.80110, 'Large condo with balcony overlooking community', NOW()),

-- For Sale Units (Blue #3498db)
(1, 'VP-011', 'house', 3, 2, 118.0, 4400000, 'for_sale', 10.25770, 123.80090, 'Ready for occupancy 3-bedroom house', NOW()),
(1, 'VP-012', 'house', 4, 2, 175.0, 5900000, 'for_sale', 10.25920, 123.80140, '4-bedroom house with garage', NOW()),
(1, 'VP-013', 'lot', 0, 0, 180.0, 1600000, 'for_sale', 10.25820, 123.80170, 'Vacant lot ready for development', NOW()),
(1, 'VP-014', 'condo', 2, 2, 95.0, 2900000, 'for_sale', 10.25740, 123.80120, 'Modern 2-bedroom condo unit', NOW()),
(1, 'VP-015', 'apartment', 2, 1, 70.0, 2100000, 'for_sale', 10.25680, 123.80180, '2-bedroom apartment with parking', NOW()),

-- Additional variety
(1, 'VP-016', 'house', 2, 2, 95.0, 3200000, 'occupied', 10.25950, 123.80070, 'Compact 2-bedroom townhouse', NOW()),
(1, 'VP-017', 'house', 3, 2, 130.0, 5100000, 'vacant', 10.25720, 123.80190, 'Contemporary design 3-bedroom house', NOW()),
(1, 'VP-018', 'lot', 0, 0, 220.0, 1950000, 'for_sale', 10.25860, 123.80100, 'Large corner lot with road access', NOW()),
(1, 'VP-019', 'condo', 1, 1, 45.0, 1500000, 'occupied', 10.25650, 123.80140, 'Affordable studio condo', NOW()),
(1, 'VP-020', 'house', 2, 1, 80.0, 2600000, 'for_sale', 10.25800, 123.80160, 'Single story 2-bedroom house', NOW());

-- Insert sample inquiries from interested buyers
INSERT INTO inquiries (property_id, name, email, phone, message, created_at) VALUES
(11, 'John Santos', 'john.santos@email.com', '+63 917 123 4567', 'Interested in VP-011. Can we schedule a viewing?', NOW()),
(12, 'Maria Garcia', 'maria.garcia@email.com', '+63 916 234 5678', 'Looking for 4-bedroom house. Details about VP-012?', NOW()),
(14, 'Robert Cruz', 'robert.cruz@email.com', '+63 918 345 6789', 'Can you send more photos of VP-014?', NOW()),
(6, 'Angela Fontanilla', 'angela.f@email.com', '+63 919 456 7890', 'Interested in renting VP-006 if available', NOW()),
(13, 'Michael Reyes', 'michael.reyes@email.com', '+63 917 567 8901', 'Looking for investment property. VP-013 specifications?', NOW());

-- Insert availability log entries
INSERT INTO availability_log (property_id, old_status, new_status, changed_by, reason, created_at) VALUES
(1, 'vacant', 'occupied', 1, 'Property sold and occupied by buyer', NOW()),
(2, 'vacant', 'occupied', 1, 'New resident moved in', NOW()),
(6, 'occupied', 'vacant', 1, 'Previous resident relocated', NOW()),
(7, 'vacant', 'vacant', 1, 'Unit prepared for new listing', NOW()),
(11, 'vacant', 'for_sale', 1, 'Listed for sale by homeowner', NOW());
