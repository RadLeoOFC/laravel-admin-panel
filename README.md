# Laravel Admin Panel

## Project Overview
This project is a Laravel-based admin panel for managing products, categories, workspaces (desks), and memberships. It includes authentication, profile management, and an admin dashboard.

**Current Version:** `v2.0.0`

## Features
- **User Authentication** (Login, Registration, Password Reset)
- **Admin Dashboard** (Protected Routes for Admins)
- **Product Management** (CRUD: Create, Read, Update, Delete)
- **Category Management** (CRUD: Create, Read, Update, Delete)
- **Workspace Management (Desks)** (CRUD + Availability Check)
- **Membership Management** (CRUD + Rental Validation)
- **Caching** (Optimized queries for better performance)
- **Desk Availability Check** before booking
- **Payment Tracking & Membership Extensions**
- **Middleware Protection** (Routes secured by authentication)

##  Installation Guide

### 1. Clone the Repository

`git clone https://github.com/laravel-admin-panel.git`

`cd laravel-admin-panel`

### 2. Install Dependencies

`composer install`

`npm install`

### 3. Configure Environment

3.1. Copy the `.env.example` file and rename it to `.env`

`cp .env.example .env`

3.2. Generate the application key

`php artisan key:generate`

3.3. Set up your database credentials in `.env`

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=intern_db
DB_USERNAME=root
DB_PASSWORD=secret
```

3.4. Run Database Migrations

`php artisan migrate --seed`

3.5. Start the Development Server

`php artisan serve`

Visit `http://localhost:8000/` in your browser.



***

## Admin Access

Login Credentials (Default Admin User)

- Email: `admin@example.com`
- Password: `password`

You can change the default admin credentials in the database.


***

## Project Structure

> This is a **simplified structure** showing the main directories and files.

```
/laravel-admin-panel
│── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   ├── ProductController.php
│   │   │   ├── CategoryController.php
│   │   │   ├── DeskController.php
│   │   │   ├── MembershipController.php
│   │   │   ├── ProfileController.php
│   │   │   ├── ReportController.php
│   ├── Models/
│   │   ├── Product.php
│   │   ├── Category.php
│   │   ├── Desk.php
│   │   ├── Membership.php
│   │   ├── User.php
│── resources/
│   ├── views/
│   │   ├── auth/
│   │   ├── categories/
│   │   ├── desks/
│   │   ├── memberships/
│   │   ├── components/
│   │   ├── layouts/
│   │   ├── products/
│   │   ├── profile/
│   │   ├── reports/
│   │   ├── dashboard.blade.php
│── routes/
│   │── auth.php
│   │── console.php
│   ├── web.php
│── .env
│── README.md

```


## Available Routes

| Method   | URL                                        | Description                    | Middleware  |
|----------|--------------------------------------------|--------------------------------|-------------|
| **GET**  | `http://127.0.0.1:8000/`                   | Home Page                      | -           |
| **GET**  | `http://127.0.0.1:8000/login`              | Login Page                     | -           |
| **GET**  | `http://127.0.0.1:8000/admin`              | Admin Dashboard                |  `auth`     |
| **GET**  | `http://127.0.0.1:8000/products`           | List all products              |  `auth`     |
| **GET**  | `http://127.0.0.1:8000/products/create`    | Show create product form       |  `auth`     |
| **POST** | `http://127.0.0.1:8000/products`           | Store a new product            |  `auth`     |
| **GET**  | `http://127.0.0.1:8000/products/{id}/edit` | Edit product                   |  `auth`     |
| **GET**  | `http://127.0.0.1:8000/categories`         | List all categories            |  `auth`     |
| **GET**  | `http://127.0.0.1:8000/categories/create`  | Show create category form      |  `auth`     |
| **POST** | `http://127.0.0.1:8000/categories`         | Store a new category           |  `auth`     |
| **GET**  | `http://127.0.0.1:8000/categories/{id}/edit` | Edit category                |  `auth`     |
| **GET**  | `http://127.0.0.1:8000/desks`             | List all desks                  |  `auth`     |
| **GET**  | `http://127.0.0.1:8000/desks/create`      | Show create desk form           |  `auth`     |
| **POST** | `http://127.0.0.1:8000/desks`             | Store a new desk                |  `auth`     |
| **GET**  | `http://127.0.0.1:8000/desks/{id}/edit`   | Edit desk                       |  `auth`     |
| **DELETE** | `http://127.0.0.1:8000/desks/{id}`      | Delete desk                     |  `auth`     |
| **GET**  | `http://127.0.0.1:8000/memberships`       | List all memberships            |  `auth`     |
| **GET**  | `http://127.0.0.1:8000/memberships/create` | Show create membership form    |  `auth`     |
| **POST** | `http://127.0.0.1:8000/memberships`       | Store a new membership          |  `auth`     |
| **GET**  | `http://127.0.0.1:8000/memberships/{id}/edit` | Edit membership             |  `auth`     |
| **DELETE** | `http://127.0.0.1:8000/memberships/{id}` | Delete membership              |  `auth`     |
| **GET**  |   `http://127.0.0.1:8000/reports`           | View reports                  |  `auth`     |



***

## Desk Availability Check
Before creating a new membership, the system checks whether the selected desk is available for the requested period:

```php
$existing = Membership::where('desk_id', $request->desk_id)
    ->whereDate('start_date', '<=', $request->end_date)
    ->whereDate('end_date', '>=', $request->start_date)
    ->exists();

if ($existing) {
    return back()->withErrors(['desk_id' => 'This desk is already booked for the selected period.']);
}
```


***


## Payment Tracking & Membership Extensions

Payment tracking has been added with the following columns:

- `amount_paid` – Payment amount
- `payment_status` – Status (paid/unpaid)
- `payment_method` – Payment method

Users can extend memberships by updating end_date or creating a new record.

***

## Caching Implementation

To optimize query performance, caching is implemented in `ProductController.php`:

```php
use App\Services\ProductService;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $page = $request->input('page', 1); // Get the current page (default is 1)
    
        // Include the page in the cache key to store separate cache entries for each page
        $cacheKey = "products_search_{$search}_page_{$page}";
    
        $products = Cache::remember($cacheKey, 600, function () use ($search) {
            return $this->productService->getAllProducts($search);
        });
    
        return view('products.index', compact('products'));
    }
}
```

### Clear Cache Manually

`php artisan cache:clear`


***

## Security & Best Practices

- **Environment Variables** are excluded from Git (.env is in .gitignore).
- **Middleware Protection** ensures only authenticated users can access admin pages.
- **Validation Rules** prevent invalid data entry:

```php
$request->validate([
    'user_id' => 'required|exists:users,id',
    'desk_id' => 'required|exists:desks,id',
    'start_date' => 'required|date',
    'end_date' => 'required|date|after_or_equal:start_date',
    'membership_type' => 'required|in:daily,monthly,yearly',
    'price' => 'nullable|numeric|min:0',
]);
```

***

## Reporting
A reporting page has been added:

- **Active Memberships** (List of current tenants)
- **Desk Occupancy** (Which desks are currently in use)
- **Date Range Filtering** (Filter memberships active in a specific period)



***
## Final Steps

### 1. Testing

Run tests to ensure everything is working correctly:

`php artisan test`

### 2. Deployment Preparation

**Before deploying, tag a stable version:**

`git tag v2.0.0`

`git push origin v2.0.0`


***

## About This Project

This is an educational project created by **Radislav Lebedev** for learning and practice purposes.
**v2.0.0**







