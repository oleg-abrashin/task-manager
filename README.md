# Task Manager

A simple task management web application built with Laravel 12 and PHP 8.3+.

## Features

- Create, edit, and delete tasks
- Drag-and-drop task reordering with automatic priority updates
- Project management: group tasks by project
- Filter tasks by project via dropdown
- MySQL-backed persistence

## Requirements

- PHP >= 8.3
- Composer
- MySQL >= 8.0
- Node.js >= 18 (only if you need to compile frontend assets)

## Setup

### 1. Clone the repository

```bash
git clone https://github.com/pcruz-git/task-manager.git
cd task-manager
```

### 2. Install dependencies

```bash
composer install
```

### 3. Configure environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set your MySQL credentials:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_manager
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 4. Create the database

```sql
CREATE DATABASE task_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. Run migrations

```bash
php artisan migrate
```

### 6. (Optional) Seed sample data

```bash
php artisan db:seed
```

This creates 3 sample projects with 5 tasks each.

### 7. Start the development server

```bash
php artisan serve
```

Visit [http://localhost:8000](http://localhost:8000) in your browser.

## Deployment

For production deployment:

```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
```

Configure your web server (Nginx/Apache) to point the document root to the `public/` directory.

## Usage

1. **Create a project** first — go to Projects > New Project
2. **Create tasks** — go to Tasks > New Task, pick a project, enter a name
3. **Reorder tasks** — drag tasks using the handle (&#9776;) on the left; priority numbers update automatically
4. **Filter by project** — use the dropdown on the tasks page to view only a specific project's tasks

## Stack

- Laravel 12
- PHP 8.3
- MySQL
- Bootstrap 5.3
- SortableJS (drag & drop)