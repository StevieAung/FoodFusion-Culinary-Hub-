<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>About Us | FoodFusion</title>
    <?php include './includes/head_tags.php'; ?>
</head>
<body class="d-flex flex-column min-vh-100 bg-warning-subtle">
  <!-- Navbar -->
  <?php include './includes/navbar.php'; ?>

  <!-- Hero Section -->
  <section class="about-hero text-center text-white d-flex align-items-center justify-content-center m-3">
    <div class="overlay"></div>
    <div class="container position-relative">
      <h1 class="display-4 fw-bold text-black">About FoodFusion</h1>
      <p class="lead text-black">Celebrating culinary creativity, community, and culture.</p>
    </div>
  </section>

  <!-- Philosophy Section -->
  <section class="py-5 bg-white m-3 rounded-4 shadow-sm">
    <div class="container">
      <div class="row justify-content-center text-center">
        <div class="col-md-8">
          <h2 class="fw-bold mb-4">Our Culinary Philosophy</h2>
          <p class="fs-5">
            At FoodFusion, we believe cooking is more than just preparing meals —
            it’s about connection, creativity, and cultural exchange. 
            Our platform encourages home cooks and food enthusiasts to share their passion,
            explore diverse cuisines, and create memorable experiences around the table.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- Values Section -->
  <section class="py-5">
    <div class="container">
      <h2 class="fw-bold text-center mb-5">Our Core Values</h2>
      <div class="row g-4">
        <div class="col-md-4">
          <div class="card h-100 text-center shadow-sm border-0 rounded-4 p-4">
            <i class="bi bi-heart-fill text-danger fs-1 mb-3"></i>
            <h5 class="fw-bold">Passion for Cooking</h5>
            <p>We are driven by a love for food and the joy it brings when shared with others.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card h-100 text-center shadow-sm border-0 rounded-4 p-4">
            <i class="bi bi-people-fill text-success fs-1 mb-3"></i>
            <h5 class="fw-bold">Community First</h5>
            <p>Our community is at the heart of FoodFusion. We value collaboration and inclusivity.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card h-100 text-center shadow-sm border-0 rounded-4 p-4">
            <i class="bi bi-globe2 text-primary fs-1 mb-3"></i>
            <h5 class="fw-bold">Cultural Diversity</h5>
            <p>We celebrate the richness of global cuisines and honor culinary traditions worldwide.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Team Section -->
  <section class="py-5 bg-light">
    <div class="container">
      <h2 class="fw-bold text-center mb-5">Meet the Team</h2>
      <div class="row g-4">
        <div class="col-md-4 text-center">
          <img src="./Assets/images/developer.jpeg" alt="Team Member" class="rounded-circle shadow mb-3" width="150" height="150">
          <h5 class="fw-bold">Stevie Aung</h5>
          <p class="text-muted">Founder & Lead Developer</p>
        </div>
        <div class="col-md-4 text-center">
          <img src="./Assets/images/culiniary_expert.jpeg" alt="Team Member" class="rounded-circle shadow mb-3" width="150" height="150">
          <h5 class="fw-bold">Jane Doe</h5>
          <p class="text-muted">Culinary Expert</p>
        </div>
        <div class="col-md-4 text-center">
          <img src="./Assets/images/manager.jpeg" alt="Team Member" class="rounded-circle shadow mb-3" width="150" height="150">
          <h5 class="fw-bold">John Smith</h5>
          <p class="text-muted">Community Manager</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <?php include './includes/footer_tags.php'; ?>
  <?php include './includes/script_tags.php'; ?>
</body>
</html>
