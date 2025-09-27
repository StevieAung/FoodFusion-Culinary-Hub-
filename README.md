🍳🍲 FoodFusion – Culinary Hub Web Application

📌 Overview

FoodFusion is a web platform designed for home cooks and food enthusiasts to explore, share, and collaborate on recipes. Users can browse admin-curated recipes, submit their own to the Community Cookbook, rate and comment on dishes, and access culinary and educational resources.

The system supports multiple roles:

- Super Admin – full control of admins and platform settings  
- Admin – manage recipes, curate collections, and moderate user submissions  
- User/Member – submit recipes, rate and comment, access community features  

The main goal is to provide an engaging culinary community with secure authentication, recipe management, and interactive features.

🚀 Tech Stack

- Frontend: PHP, Bootstrap 5, JavaScript (Vanilla + JSON/AJAX handling)  
- Backend: PHP  
- Database: MySQL  
- Features: Image uploads, real-time comment submission, ratings  

✨ Features

✔️ User registration and login with password encryption ✔️ Admin-curated recipe collections ✔️ Community Cookbook for user-submitted recipes ✔️ Ratings and comments system with AJAX submission ✔️ Recipe submission modal with image preview ✔️ Collapsible recipe details (ingredients & instructions) ✔️ Contact Us page with social links, email, and phone ✔️ Responsive, mobile-friendly design ✔️ Cookie consent and accessibility features  

📊 Future Improvements

🔹 Push notifications for new recipes or comments  
🔹 Enhanced analytics for popular recipes and user engagement  
🔹 Integration with third-party recipe APIs  
🔹 React front-end migration for improved scalability  
🔹 Enhanced search and filtering capabilities  

🏗️ Project Structure

FoodFusion/
│── Assets/
│ └── images/ # Recipe images, chef images, placeholders
│── modals/
│ └── modals.php # Recipe submission modal
│── includes/
│ ├── head_tags.php
│ ├── navbar.php
│ ├── footer_tags.php
│ └── script_tags.php
│── Database/
│ ├── db.php
│ └── db.sql # Database schema
│── home_page.php
│── community_cookbook.php
│── recipe_collections.php
│── contact_us.php
│── contact_submit.php
│── login.php
│── register.php
│── admin_login.php
│── style.css # Global styles
│── uploads/ # Uploaded images
│ └── cuisine/
│── README.md

markdown
Copy code

⚙️ Setup Guide

Follow these steps to run the project locally using XAMPP:

1️⃣ **Install Requirements**  
- XAMPP (with Apache & MySQL)  
- Web browser (Chrome/Firefox recommended)  
- VS Code or any code editor  

2️⃣ **Clone or Download Project**  
Place the project folder inside your XAMPP htdocs directory:  
- Mac: `/Applications/XAMPP/htdocs/FoodFusion`  
- Windows: `C:/xampp/htdocs/FoodFusion`  

3️⃣ **Database Setup**  
- Start Apache and MySQL in XAMPP  
- Open phpMyAdmin at `http://localhost/phpmyadmin`  
- Create a new database (e.g., `foodfusion_db`)  
- Import the provided SQL file:  
  - Go to Import tab → Select `Database/db.sql` → Click Go  

4️⃣ **Configure Database**  
Edit `Database/db.php` and update your MySQL credentials:  

```php
$conn = new mysqli('localhost', 'root', '', 'foodfusion_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
5️⃣ Run the Project
Open your browser and go to: http://localhost/FoodFusion/home_page.php

Register as a User to submit recipes and comment

Admins can log in via admin_login.php

Super Admin can manage admins if created manually

📖 About Me

I am an aspiring Software Engineer with practical experience in web development, databases, and interactive web applications.

🎓 Level 5 Diploma in Computing – NCC Education
💻 Skilled in PHP, MySQL, JavaScript, Bootstrap
🌱 Currently learning React
🔍 Interested in building engaging web applications

🤝 Let’s Connect

📧 Email: stevieaung90@gmail.com
📞 Phone: +959777395589
