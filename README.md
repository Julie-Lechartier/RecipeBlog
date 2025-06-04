# 🍽️ Online Recipe Sharing Platform

A web application where users can share their own recipes, comment on others, and manage everything through an admin dashboard.

---

## 📋 Features

- 🧑‍🍳 Users can:
    - Register and log in
    - Create, edit, and publish their own recipes
    - Browse recipes from other users
    - Comment on recipes

- 🛠️ Admin dashboard:
    - Manage users, recipes, ingredients, units, and comments
    - Update the homepage banner

- 🎨 UI / UX:
    - Built with Bootstrap 5 for responsive design
    - Select2 integration for enhanced dropdowns
    - Clean templating with Twig

---

## 🧱 Tech Stack

- **Framework**: [Symfony](https://symfony.com/)
- **Database ORM**: Doctrine
- **Templating**: Twig
- **Assets**: Asset Mapper
- **Styling**: Bootstrap 5
- **JavaScript Enhancements**: Select2
- **Authentication**: Symfony Security (User roles: `ROLE_USER`, `ROLE_ADMIN`)

---

## 🚀 Installation

### Prerequisites

- PHP >= 8.1
- Composer
- Symfony CLI (recommended)
- A web server (e.g., Apache, Nginx)
- A database (e.g., MySQL or PostgreSQL)

### Setup

1. Clone the repository:

```bash
git clone https://github.com/your-username/your-repo-name.git
cd your-repo-name
```
2. Install dependencies:
```bash
composer install
```
3. Set up environment variables::
```bash
cp .env.local
```
Edit .env.local to configure your database and other secrets

4. Create the database and run migrations:
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```
5. Load initial data (Fixtures):
```bash
php bin/console doctrine:fixtures:load
```

6. Start the local server:
```bash
symfony server:start
```

Created by Julie Lechartier -- feel free to contribute !