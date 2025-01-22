# TASKS 1: Project & Environment Setup

## Description
This document outlines the steps taken to set up a Laravel development environment, initialize a project, and configure Git for version control.
This project is an educational project for creating an admin panel using the Laravel framework. It demonstrates the process of setting up the development environment, creating a Laravel project, and interacting with Git and GitHub.

## Technologies Used
- PHP: version 8.2.12
- Composer: version 2.8.4
- Laravel: version 5.11.2
- MySQL (MariaDB): version 15.1 (Distrib 10.4.32-MariaDB)
- Git: 2.47.1.windows.2 

---

## Step 1: Install and Configure Tools

### Tools Installed:
- **XAMPP:** Provides Apache and MySQL services.
- **Composer:** Dependency management tool for PHP.
- **PHP:** Latest stable version.
- **Git:** Version control system.


### Installation Steps:

1. **XAMPP Installation**
   - Downloaded from [https://www.apachefriends.org](https://www.apachefriends.org).
   - Installed with Apache and MySQL enabled. Once installed, open XAMPP and start the Apache and MySQL servers.
   
2. **Composer Installation**
   - Downloaded and installed Composer from [https://getcomposer.org](https://getcomposer.org).
   - Verified installation using:
     `composer --version`  

3. **PHP Configuration**
   - Installed PHP as part of XAMPP.
   - Set up system variables for `PHP` and `Composer`:
     - Added the following paths to system variables:
       - `PHP` path: `C:\xampp\php`
       - `Composer` path: `C:\ProgramData\ComposerSetup\bin`
   - Verified installation using:
     `php -v`
     `composer -v`

You should see the version of PHP and Composer, for example:
     PHP version: PHP 8.2.12
     Composer version: Composer version 2.8.4
     

4. **Git Installation**
   - Installed Git for Windows from [https://git-scm.com](https://git-scm.com).
   - Verified installation using:
     `git --version`
     
---

**Verification of Tools:**
- **PHP Version:** `php -v`
- **Composer Version:** `composer -v`
- **MySQL Version:** `mysql --version`
- **Git Version:** `git --version`

---

## Step 2: Create a New Laravel Project

### Steps to Create the Project:

1. **Navigate to the Target Directory:**
   `cd C:\xampp\htdocs`

2. **Create the Laravel project using the following command:**

   `laravel new internship-project`

During setup, select MySQL as the database.

3. **Navigate to the newly created project folder:**

   `cd internship-project`

4. **Run the Laravel development server:**

   `php artisan serve`

5. **Open your browser and visit the following URL to access the Laravel application:** [http://127.0.0.1:8000](http://127.0.0.1:8000)

You should see the default Laravel welcome page.

---

## Step 3: Initialize Git and Push to GitHub

### Steps to Configure Git and GitHub:

1. **Initialize Git in the Project Directory:**
   `git init`

2. **Create a GitHub Repository:**
- Created a new repository named laravel-admin-panel on GitHub.

3. **Link Local Repository to GitHub:**
- Add the remote repository using the following command:
   `git remote add origin https://github.com/RadLeoOFC/laravel-admin-panel`

4. **Commit the Initial Setup:**
- Add all files to staging:
   `git add .`
- Commit the changes:
   `git commit -m "Initial Laravel project setup"`

5. **Push the Code to GitHub:**
- Push the initial commit to the main branch:
   `git push -u origin main`

---

## Step 4: Familiarize with Laravel Structure

### Overview of Key Directories and Files:

1. **`app/` Directory:**
   - Contains the core of the application.
   - **Subdirectories:**
     - **Models:** Located in `app/Models`. Represents database tables and handles business logic.
     - **Controllers:** Located in `app/Http/Controllers`. Contains application logic and communicates between models and views.

---

2. **`routes/` Directory:**
   - Defines the application's routes.
   - **Key File:**
     - **`web.php`:** Contains routes for web requests.
     - **`api.php`:** Contains routes for API endpoints.

---

3. **`resources/views/` Directory:**
   - Contains Blade templates for the application's views.
   - **Example:**
     - `welcome.blade.php`: Default welcome page template.

---

4. **`public/` Directory:**
   - Publicly accessible files such as assets (CSS, JavaScript, images).
   - **Entry Point:**
     - `index.php`: The entry point for all HTTP requests.

---

5. **`config/` Directory:**
   - Contains configuration files for the application.
   - **Examples:**
     - `app.php`: General application configuration.
     - `database.php`: Database connection settings.

---

6. **`.env` File:**
   - Stores environment-specific settings like database credentials and API keys.
   - **Important:** Do not add `.env` file to the repository. Ensure it is listed in .gitignore to protect sensitive information such as database credentials and API keys.

---

### **Documentation:**
- Refer to the [Official Laravel Documentation](https://laravel.com/docs) for an in-depth explanation of the framework's folder structure.
- [Composer Documentation](https://getcomposer.org/doc/)

### **Author:** 
- This project was created by Radislav Lebedev as part of an educational internship to demonstrate working with the Laravel framework.










