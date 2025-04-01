-- Sample Data for CertiFlow
-- Date: April 3, 2025

-- Insert roles
INSERT INTO roles (name, description) VALUES
('admin', 'Administrator with full system access'),
('engineer', 'Electrical engineer with certificate creation privileges'),
('office', 'Office staff with customer management privileges');

-- Insert permissions
INSERT INTO permissions (name, description) VALUES
('manage_users', 'Create, edit and delete users'),
('manage_customers', 'Create, edit and delete customers'),
('create_certificates', 'Create new certificates'),
('view_certificates', 'View existing certificates'),
('edit_certificates', 'Edit existing certificates'),
('delete_certificates', 'Delete existing certificates'),
('manage_settings', 'Manage system settings'),
('access_reports', 'Access and generate reports');

-- Insert role permissions
INSERT INTO role_permissions (role_id, permission_id) VALUES
(1, 1), (1, 2), (1, 3), (1, 4), (1, 5), (1, 6), (1, 7), (1, 8), -- Admin has all permissions
(2, 2), (2, 3), (2, 4), (2, 5), -- Engineer permissions
(3, 2), (3, 4), (3, 8); -- Office staff permissions

-- Insert admin user (password: admin123)
INSERT INTO users (username, password, email, first_name, last_name, role_id) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@gerrardselectrical.co.uk', 'Admin', 'User', 1);

-- Insert certificate types
INSERT INTO certificate_types (name, description, template_path) VALUES
('Electrical Installation Certificate', 'Full electrical installation certification', 'certificates/templates/installation.php'),
('Electrical Installation Condition Report', 'Periodic inspection report', 'certificates/templates/eicr.php'),
('Minor Electrical Installation Works Certificate', 'For small installation works', 'certificates/templates/minor_works.php');

-- Insert email templates
INSERT INTO email_templates (name, subject, body, variables) VALUES
('certificate_complete', 'Your Electrical Certificate is Ready', '<p>Dear {customer_name},</p><p>Your electrical certificate is now ready. You can log in to our customer portal to view and download it.</p><p>Regards,<br>Gerrards Electrical</p>', '["customer_name"]'),
('account_created', 'Your Customer Portal Account', '<p>Dear {customer_name},</p><p>An account has been created for you on our customer portal. Please use the following details to log in:</p><p>Username: {username}<br>Password: {password}</p><p>Regards,<br>Gerrards Electrical</p>', '["customer_name", "username", "password"]');

-- Insert settings
INSERT INTO settings (setting_key, setting_value, setting_group) VALUES
('company_name', 'Gerrards Electrical', 'company'),
('company_address', '123 Main Street, London, UK', 'company'),
('company_phone', '020 1234 5678', 'company'),
('company_email', 'info@gerrardselectrical.co.uk', 'company'),
('certificate_prefix', 'GE-', 'certificates'),
('smtp_host', 'smtp.example.com', 'email'),
('smtp_port', '587', 'email'),
('smtp_username', 'smtp_user', 'email'),
('smtp_password', 'smtp_password', 'email'),
('smtp_encryption', 'tls', 'email');