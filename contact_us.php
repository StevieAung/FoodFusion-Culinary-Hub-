<?php
session_start();
include './Database/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us - FoodFusion</title>
    <?php include './includes/head_tags.php'; ?>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #fff3cd; margin:0; padding:0; }
        .container { max-width: 1200px; margin: 50px auto; padding: 0 15px; }
        h2 { text-align: center; margin-bottom: 40px; color: #333; }
        .contact-wrapper { display: flex; gap: 30px; flex-wrap: wrap; }

        /* Left Column - Social Links */
        .contact-social { flex: 1; min-width: 250px; background: #fff; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .contact-social h3 { margin-top:0; color:#222; }
        .contact-social p { margin: 10px 0; color: #555; }
        .social-links a { display: inline-block; margin-right: 10px; font-size: 1.5em; color: #333; transition: 0.3s; }
        .social-links a:hover { color: #ffc107; }

        /* Right Column - Form */
        .contact-form { flex: 1; min-width: 300px; background: #fff; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .contact-form h3 { margin-top:0; color:#222; }
        .contact-form label { display:block; margin:10px 0 5px; font-weight: 600; }
        .contact-form input, .contact-form textarea, .contact-form button {
            width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ccc; margin-bottom: 15px; font-size: 1em;
        }
        .contact-form button {
            background: #28a745; color: #fff; border: none; cursor: pointer; transition:0.3s;
        }
        .contact-form button:hover { background: #218838; }

        /* Alerts */
        .alert { border-radius: 12px; max-width: 600px; margin: 20px auto; padding: 15px; }

        @media(max-width:768px){ .contact-wrapper { flex-direction: column; } }
    </style>
</head>
<body>
    <?php include './includes/navbar.php'; ?>
    
    <!-- Success/Error Alerts -->
    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success text-center">Your message has been sent successfully!</div>
    <?php elseif(isset($_GET['error'])): ?>
        <div class="alert alert-danger text-center">There was an error sending your message. Please try again.</div>
    <?php endif; ?>

    <div class="container">
        <h2>Contact Us</h2>
        <div class="contact-wrapper">

            <!-- Left Column - Social Links -->
            <div class="contact-social">
                <h3>Connect with FoodFusion</h3>
                <p>Follow us on our social media platforms for updates, recipes, and culinary tips!</p>
                <div class="social-links">
                    <a href="https://facebook.com" target="_blank"><i class="fab fa-facebook"></i></a>
                    <a href="https://twitter.com" target="_blank"><i class="fab fa-twitter"></i></a>
                    <a href="https://instagram.com" target="_blank"><i class="fab fa-instagram"></i></a>
                    <a href="https://linkedin.com" target="_blank"><i class="fab fa-linkedin"></i></a>
                </div>
                <p>Email: <a href="mailto:contact@foodfusion.com">contact@foodfusion.com</a></p>
                <p>Phone: +123 456 7890</p>
            </div>

            <!-- Right Column - Contact Form -->
            <div class="contact-form">
                <h3>Send Us a Message</h3>
                <form action="contact_submit.php" method="POST">
                    <label for="name">Full Name</label>
                    <input type="text" name="name" id="name" required>

                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required>

                    <label for="phone">Phone Number</label>
                    <input type="tel" name="phone" id="phone" required>

                    <label for="message">Message</label>
                    <textarea name="message" id="message" rows="5" required></textarea>

                    <button type="submit">Send Message</button>
                </form>
            </div>

        </div>
    </div>
    <?php include './includes/footer_tags.php'; ?>
    <?php include './includes/script_tags.php'; ?>
</body>
</html>
