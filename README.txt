Job Management System (PHP + MySQL + JS + HTML + CSS)
----------------------------------------------------
Included files:
- sql/database.sql          : Create database and tables + sample admin user
- config.php               : Database connection (edit DB credentials)
- functions.php            : Helper functions used across pages
- index.php                : Login page
- register.php             : User registration (for new users)
- dashboard.php            : Admin/user dashboard (overview & today's profit)
- products.php             : CRUD for products
- purchases.php            : Record product purchases (packs)
- sales.php                : Record sales (packs or pieces)
- reports.php              : Daily activity report (filter by date)
- reset_password.php       : Reset password form (for logged in users)
- logout.php               : Logout
- assets/css/style.css     : Basic styling (Tailored)
- assets/js/app.js         : Front-end JS for UI interactions

Default credentials (created in database.sql):
- Admin user:
    email: admin@admin.com
    password: Admin@123

Notes:
- Update sql/database.sql into your MySQL server (use phpMyAdmin or mysql CLI).
- Edit config.php database credentials to match your environment.
- The system uses PDO and prepared statements to avoid SQL injection.
- Each record (product, purchase, sale) is tied to the user_id to support multiple users.
- Profit calculation:
  Pack Sales: (sale_price - purchase_price) * quantity
  Piece Sales: (sale_price - (purchase_price / pieces_per_pack)) * quantity

