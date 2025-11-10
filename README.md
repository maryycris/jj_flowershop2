# J'J FLOWERSHOP - Laravel Application

A full-stack flower shop management system built with Laravel, featuring separated backend and frontend architecture.

## 📂 Project Structure

```
JJ_FLOWERSHOP CAPSTONE/
│
├── backend/              # Backend (Server-Side)
│   ├── app/             # Controllers, Models, Services
│   ├── config/          # Configuration files
│   ├── database/        # Migrations & Seeders
│   ├── routes/          # API routes
│   ├── storage/         # File storage
│   └── vendor/          # Composer dependencies
│
├── frontend/            # Frontend (Client-Side)
│   ├── resources/       # Views, CSS, JS
│   ├── routes/          # Web routes
│   ├── public/          # Public assets
│   └── package.json     # NPM dependencies
│
├── public/              # Entry point (Root)
│   └── index.php        # Main entry point
│
├── .env                 # Environment configuration
├── start_backend.bat    # Start backend server (Windows)
├── start_frontend.bat   # Start frontend dev server (Windows)
└── Procfile             # Deployment configuration
```

## 🚀 Quick Start

### Prerequisites
- PHP 8.2+
- Composer
- Node.js & NPM
- MySQL (via XAMPP)
- XAMPP (Apache & MySQL)

### Installation

1. **Install Backend Dependencies:**
   ```bash
   cd backend
   composer install
   ```

2. **Install Frontend Dependencies:**
   ```bash
   cd frontend
   npm install
   ```

3. **Configure Environment:**
   - Copy `.env.example` to `.env` in the root
   - Update database credentials in `.env`
   - Set `APP_URL=http://localhost:8000`

4. **Setup Database:**
   ```bash
   cd backend
   php artisan migrate
   php artisan db:seed
   php artisan storage:link
   ```

### Running the Application

**Option 1: Using Batch Scripts (Windows)**
```bash
# Terminal 1 - Backend
start_backend.bat

# Terminal 2 - Frontend
start_frontend.bat
```

**Option 2: Manual Commands**
```bash
# Terminal 1 - Backend
cd backend
php artisan serve

# Terminal 2 - Frontend
cd frontend
npm run dev
```

### Access the Application
- **Frontend:** http://localhost:8000
- **Backend API:** http://localhost:8000/api

## 📁 Directory Details

### Backend (`backend/`)
- **Controllers:** `app/Http/Controllers/`
  - `Admin/` - Admin panel controllers
  - `Clerk/` - Clerk management controllers
  - `Customer/` - Customer-facing controllers
  - `Driver/` - Delivery driver controllers
  - `Api/` - API endpoints (JSON responses)
- **Models:** `app/Models/`
- **Services:** `app/Services/`
- **Routes:** `routes/api.php` - API routes only
- **Migrations:** `database/migrations/`
- **Seeders:** `database/seeders/`

### Frontend (`frontend/`)
- **Views:** `resources/views/` - Blade templates
- **Assets:** `resources/css/`, `resources/js/`
- **Routes:** `routes/web.php` - Web routes
- **Public:** `public/` - Images, static files

## 🔧 Key Features

- **Role-Based Access Control:** Admin, Clerk, Customer, Driver
- **Product Management:** Catalog, Customization, Inventory
- **Order Management:** Cart, Checkout, Delivery Tracking
- **Payment Processing:** Secure payment handling
- **Loyalty Program:** Customer loyalty cards
- **Real-time Notifications:** Order status updates
- **Delivery Management:** Driver assignment and tracking

## 🛠️ Development

### Running Migrations
```bash
cd backend
php artisan migrate
php artisan migrate:fresh --seed  # Reset database
```

### Clearing Caches
```bash
cd backend
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### Building Frontend Assets
```bash
cd frontend
npm run build  # Production build
npm run dev    # Development with hot reload
```

## 📝 Notes

- Backend and frontend are physically separated for better organization
- Views are loaded from `frontend/resources/views/`
- API routes are in `backend/routes/api.php`
- Web routes are in `frontend/routes/web.php`
- Public path is configured to point to root `public/` directory

## 🔐 Security

- Environment variables in `.env` (never commit `.env`)
- CSRF protection enabled
- Password hashing with bcrypt
- SQL injection protection via Eloquent ORM

## 📄 License

This project is proprietary software for J'J FLOWERSHOP.

---

**Built with Laravel 12** 🚀
