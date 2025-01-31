
# Laravel Admin Panel

## Project Overview
This project is a Laravel-based admin panel that provides functionalities for managing products and categories. It includes authentication, profile management, and an admin dashboard.

## Features
- User Authentication (Login, Registration, Password Reset)
- Admin Dashboard (Protected Routes for Admins)
- Product Management (CRUD: Create, Read, Update, Delete)
- Category Management (CRUD: Create, Read, Update, Delete)
- Caching (Optimized queries for improved performance)
- Middleware Protection (Routes secured by authentication middleware)

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
│   │   │   ├── ProfileController.php
│   ├── Models/
│   │   ├── Product.php
│   │   ├── Category.php
│   │   ├── User.php
│── resources/
│   ├── views/
│   │   ├── auth/
│   │   ├── categories/
│   │   ├── components/
│   │   ├── layouts/
│   │   ├── products/
│   │   ├── profile/
│   │   ├── dashboard.blade.php
│   │   ├── welcome.blade.php
│── routes/
│   │── auth.php
│   │── console.php
│   ├── web.php
│── .env
│── README.md
```


## Available Routes

| Method  | URL                                        | Description                      | Middleware |
|---------|--------------------------------------------|----------------------------------|------------|
| **GET**  | `http://127.0.0.1:8000/`                 | Home Page                        | -          |
| **GET**  | `http://127.0.0.1:8000/login`            | Login Page                       | -          |
| **GET**  | `http://127.0.0.1:8000/admin`            | Admin Dashboard                  | `auth`     |
| **GET**  | `http://127.0.0.1:8000/products`         | List all products                | `auth`     |
| **GET**  | `http://127.0.0.1:8000/products/create`  | Show create product form         | `auth`     |
| **POST** | `http://127.0.0.1:8000/products`         | Store a new product              | `auth`     |
| **GET**  | `http://127.0.0.1:8000/products/16/edit` | Edit product with ID 16          | `auth`     |
| **GET**  | `http://127.0.0.1:8000/categories`       | List all categories              | `auth`     |
| **GET**  | `http://127.0.0.1:8000/categories/create`| Show create category form        | `auth`     |
| **POST** | `http://127.0.0.1:8000/categories`       | Store a new category             | `auth`     |
| **GET**  | `http://127.0.0.1:8000/categories/1/edit`| Edit category with ID 1          | `auth`     |





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
```

### Clear Cache Manually

`php artisan cache:clear`


***

## Security & Best Practices

- **Environment Variables** are excluded from Git (.env is in .gitignore).
- **Middleware Protection** ensures only authenticated users can access admin pages.
- **Validation Rules** prevent invalid data entry.

***

## About This Project

This is an educational project created by **Radislav Lebedev** for learning and practice purposes.

