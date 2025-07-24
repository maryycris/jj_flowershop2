JJ FLOWERSHOP SYSTEM (Capstone Project)
---------------------------------------

🔧 SYSTEM REQUIREMENTS:
- PHP >= 8.0
- Composer
- MySQL / phpMyAdmin
- Laravel 10+ (already included via composer)

📁 FOLDER STRUCTURE:
- app/Http/Controllers          → All system controllers per role
- resources/views/              → Blade UI for admin, customer, clerk, driver
- routes/web.php                → Web routes with role middleware
- database/migrations/          → User, product, order, delivery tables
- public/                       → Entry point for Laravel (`php artisan serve`)

⚙️ INSTALLATION GUIDE:
1. Extract this project folder to: `C:/xampp/htdocs/JJ_Flowershop_Capstone`
2. Open terminal / VSCode in the project root
3. Run the following commands:

   composer install  
   cp .env.example .env  
   php artisan key:generate  
   php artisan migrate  

📌 Sample Dummy Logins:
- Admin: admin@example.com / password
- Clerk: clerk@example.com / password
- Customer: customer@example.com / password
- Driver: driver@example.com / password

🗺️ Google Maps Setup:
- Required for customer address pinning & driver tracking.
- Get a Google Maps API Key from: https://console.cloud.google.com
- Replace `YOUR_GOOGLE_MAPS_API_KEY` inside the view files.

🚀 RUNNING THE SYSTEM:
- Start Apache and MySQL via XAMPP
- Run `php artisan serve`
- Open browser: http://127.0.0.1:8000

📣 Notes:
- Session uses DB: `sessions` table is auto-created after `php artisan migrate`
- Be sure `.env` is properly set with:
  DB_CONNECTION=mysql  
  DB_DATABASE=jj_flowershop  
  DB_USERNAME=root  
  DB_PASSWORD=

---
Developed by: Mary Cris Camasura  
Capstone: J'J Flower Shop E-Commerce & Inventory System
