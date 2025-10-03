<?php
include_once 'code.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Skill_Swaps</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
    rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">
</head>

<body>
  <header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">
      <a href="index.php" class="logo d-flex align-items-center me-auto">
        <h1 class="sitename">SkillSwaps</h1>
      </a>

      <nav id="navmenu" class="navmenu pe-5 me-5">
        <ul>
          <li><a href="index.php" class="active">Home<br></a></li>
          <li><a href="about.php">About</a></li>
          <!-- <li><a href="courses.php">Courses</a></li> -->
          <li><a href="trainers.php">Users</a></li>
          <li><a href="events.php">Blogs</a></li>
          <!-- <li><a href="pricing.php">Pricing</a></li> -->
          <li class="dropdown"><a href="#"><span>Skills</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
            <ul>
              <li><a href="#">Soft Skills</a></li>
              <li class="dropdown"><a href="#"><span>IT</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                <ul>
                  <li><a href="#">Web developing</a></li>
                  <li><a href="#">App developing</a></li>
                  <li><a href="#">Graphic designer</a></li>
                  <li><a href="#">IOT</a></li>
                  <li><a href="#">FreeLancer</a></li>
                </ul>
              </li>
              <li><a href="#">Sales</a></li>
              <li><a href="#">Fashion Designer</a></li>
              <li><a href="#">Language Learner</a></li>
            </ul>
          </li>
          <li><a href="contact.php">Contact</a></li>
          <?php
          if (isset($_SESSION['user_id'])) {
            ?>
            <li class="dropdown"><a href="#"><span><?php echo htmlspecialchars($_SESSION['username']); ?></span> <i
                  class="bi bi-chevron-down toggle-dropdown"></i></a>
              <ul>
                <?php if ($_SESSION['role'] === 'admin') { ?>
                  <li><a href="../Admin_Panel/public.php?index">Dashboard</a></li>
                  <?php
                } else { ?>
                  <li><a href="../User_Panel/public.php?index">Dashboard</a></li>
                <?php } ?>
                <li><a href="logout.php">Logout</a></li>
              </ul>
            </li>
            <?php
          } else {
            ?>
            <li class="dropdown"><a href="#"><span>Account</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
              <ul>
                <li><a href="../Admin_Panel/signin.php">Sign In</a></li>
                <li><a href="../Admin_Panel/signup.php">Sign Up</a></li>
              </ul>
            </li>
            <?php
          }
          ?>

        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

      <a class="btn-getstarted" href="courses.php">Get Started</a>

    </div>
  </header>