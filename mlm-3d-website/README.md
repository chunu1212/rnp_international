MLM 3D Website - Starter Package
=================================

What's included:
- backend/ (PHP endpoints, DB connection)
- admin/ (admin 3D page + tools)
- frontend/ (simple landing + assets)
- database/mlm_schema.sql (schema)

Quick start (local):
1. Install PHP + MySQL on your machine (e.g., XAMPP, MAMP, LAMP).
2. Import database/mlm_schema.sql into your MySQL server.
   - Default DB name: mlm_db
3. Place the project in your webserver root (e.g., htdocs or www).
   Example path: /var/www/html/mlm-3d-website
4. Update backend/db.php with your DB credentials.
5. Open admin/admin_login.php in browser to login as admin:
   - Default admin username: admin
   - Default admin password: admin123
6. Visit admin/referral_3d.php to view the 3D referral network.

Notes:
- This is a starter project focused on visuals and structure. For production use:
  * Secure endpoints (HTTPS, strong passwords, rotate secrets)
  * Use proper session handling, rate-limiting, and input validation
  * Install Imagick on the server to enable PDF export
