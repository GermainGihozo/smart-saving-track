<?php include "includes/header.php"; ?>

<!-- Hero Section -->
<header class="bg-light text-center py-5">
  <div class="container">
    <h1 class="display-4 fw-bold text-success">Welcome to Smart Saving Track</h1>
    <p class="lead">Your digital solution to manage savings, contributions, and loans with ease & transparency.</p>
    <a href="member/register.php" class="btn btn-success btn-lg me-2">Join as Member</a>
    <a href="member/login.php" class="btn btn-outline-success btn-lg">Login</a>
  </div>
</header>

<!-- Features Section -->
<section class="py-5">
  <div class="container">
    <div class="row text-center">
      <div class="col-md-4 mb-4">
        <div class="card shadow-sm h-100 border-0">
          <div class="card-body">
            <h5 class="card-title text-success">📊 Track Contributions</h5>
            <p class="card-text">Easily monitor members’ savings history and balances in real-time.</p>
          </div>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="card shadow-sm h-100 border-0">
          <div class="card-body">
            <h5 class="card-title text-success">💵 Manage Loans</h5>
            <p class="card-text">Submit loan requests, approve applications, and follow repayments with transparency.</p>
          </div>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="card shadow-sm h-100 border-0">
          <div class="card-body">
            <h5 class="card-title text-success">🔔 Notifications</h5>
            <p class="card-text">Get reminders for payments, meetings, and important group announcements.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>


<?php include "includes/footer.php";

// echo password_hash('123', PASSWORD_DEFAULT);
?>
