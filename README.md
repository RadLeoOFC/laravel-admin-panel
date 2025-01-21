# Laravel Admin Panel

## Description
This project is an educational project for creating an admin panel using the Laravel framework. It demonstrates the process of setting up the development environment, creating a Laravel project, and interacting with Git and GitHub.

## Technologies Used
- PHP: version 8.2.12
- Composer: version 2.8.4
- Laravel: version 5.11.2
- MySQL (MariaDB): version 15.1 (Distrib 10.4.32-MariaDB)

## Installation and Setup Steps

### 1. Install XAMPP
XAMPP includes Apache and MySQL, which are required to run Laravel. Download and install XAMPP from the official XAMPP website  (https://www.apachefriends.org/index.html).

Once installed, open XAMPP and start the Apache and MySQL servers.

### 2. Install Composer 
Composer is used for managing dependencies in Laravel projects.

1. Download and install Composer from the official website (https://getcomposer.org/download/).
2. After installation, verify Composer is installed correctly by running the following command:

   composer -v

You should see the version of Composer, for example: Composer version 2.8.4.

### 3. Set Up System Environment Variables

To make Composer and PHP accessible from the command line, you need to set up system environment variables.

1. Open system properties:
   – On Windows: Right-click on "This PC" → "Properties" → "Advanced system settings".
   – Click on "Environment Variables".

2. Under "System Variables", add the following paths:
   – PHP: C:\xampp\php
   – Composer: C:\ProgramData\ComposerSetup\bin

3. Verify the settings by running the following commands in your terminal:

   php -v
   composer -v

You should see the version of PHP and Composer, for example:

    PHP version: PHP 8.2.12
    Composer version: Composer version 2.8.4

### 4. Create a New Laravel Project

1. Navigate to the folder where you want to create the project (e.g., htdocs in XAMPP):
   
   cd C:\xampp\htdocs

2. Create the Laravel project using the following command:

laravel new internship-project

During setup, select MySQL as the database.

3. Navigate to the newly created project folder:

cd internship-project

4. Run the Laravel development server:

php artisan serve

5. Open your browser and visit the following URL to access the Laravel application: http://127.0.0.1:8000

You should see the default Laravel welcome page.

### 5. Initialize Git Repository and Set Up GitHub

1. Create a new repository on GitHub named laravel-admin-panel.

2. Initialize a local Git repository in your project folder:

   git init

3. Add the remote repository URL from GitHub:

git remote add origin <GitHub_Repository_URL>

4. Commit the initial Laravel setup:

git add .
git commit -m "Initial Laravel project setup"

5. Push the changes to GitHub:

git push -u origin main

### 6. Familiarize with Laravel Structure

The project follows the standard Laravel structure:

- `app/`  
  Contains the business logic of the application, including models and controllers.

- `routes/web.php`  
  Defines the routes for handling HTTP requests.

- `resources/views/`  
  Contains the views, which use Blade templates for rendering HTML.

- `public/`  
  Stores public files such as images, styles, and scripts.

### 7. Best Practices

– Do not add `.env` file to the repository.  
  Ensure it is listed in .gitignore to protect sensitive information such as database credentials and API keys.

– Use meaningful commit messages.  
  Example:  

  git commit -m "Add user authentication routes"

Documentation

    Official Laravel Documentation https://laravel.com/docs/11.x
    Composer Documentation https://getcomposer.org/doc/

Author

This project was created by Radislav Lebedev as part of an educational internship to demonstrate working with the Laravel framework.
