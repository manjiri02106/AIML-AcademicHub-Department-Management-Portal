# AIML AcademicHub – Department Management Portal

## Overview
This project provides a production-oriented administrator module for an AI & ML department management portal built with PHP 8, MySQL, Bootstrap 5, and XAMPP.

## Features
- Secure admin login with PHP sessions and password hashing
- Modern responsive admin dashboard
- User management with CRUD, search, filters, and status controls
- Roles and permissions management
- Master settings for department and academic preferences
- Backup management with SQL file creation and history

## Project Structure
- admin/ - Dashboard, users, roles, settings, backups
- assets/ - CSS and JS
- includes/ - Shared PHP templates and helpers
- database/ - SQL schema
- backups/ - Generated SQL backup files

## Installation on XAMPP
1. Place this project in the XAMPP htdocs folder.
2. Start Apache and MySQL from the XAMPP control panel.
3. Open phpMyAdmin and create a database named aiml_academichub.
4. Import the SQL file from database/schema.sql.
5. Open http://localhost/AIML-AcademicHub-Department-Management-Portal/login.php.
6. Sign in with:
   - Email: admin@aiml.edu
   - Password: admin123

## Notes
- The default password is stored using password_hash().
- Backup generation uses mysqldump if available in the XAMPP MySQL installation.
- The module is intentionally structured to be extended with additional academic modules.
