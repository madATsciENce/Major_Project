-- Sample data for registration_signup
INSERT INTO registration_signup (Name, Email, Phone_Number, Age, Select_Gender, Password, Confirm_Password) VALUES
('John Doe', 'john@example.com', '1234567890', 30, 'Male', 'password123', 'password123'),
('Jane Smith', 'jane@example.com', '0987654321', 28, 'Female', 'password456', 'password456');

-- Sample data for table2
INSERT INTO table2 (Direction, Area, Images, Hyperlink) VALUES
('North', 'Rishikesh', 'rishikesh.jpg', 'http://example.com/rishikesh'),
('South', 'Wayanad', 'wayanad.jpg', 'http://example.com/wayanad');

-- Sample data for hotels
INSERT INTO hotels (Area) VALUES
('Rishikesh'),
('Wayanad');

-- Sample data for destination
INSERT INTO destination (Area) VALUES
('Rishikesh'),
('Wayanad');
