<?php
session_start();
require_once "../includes/db.php";

// Protect page only admin
if (!isset($_SESSION['member_id'])) {
    header("Location: ../login.php");
    exit;
}

$admin_name = $_SESSION['member_name'];

// Fetch summary statistics
$total_members = $pdo->query("SELECT COUNT(*) FROM members WHERE role='member'")->fetchColumn();
$total_contributions = $pdo->query("SELECT SUM(amount) FROM contributions")->fetchColumn();
$total_loans_pending = $pdo->query("SELECT COUNT(*) FROM loans WHERE status='pending'")->fetchColumn();
$total_loans_approved = $pdo->query("SELECT COUNT(*) FROM loans WHERE status='approved'")->fetchColumn();
?>

<?php include "../includes/admin_navbar.php"; ?>

<div class="container py-5">

  <h2 class="text-success mb-4">Welcome, <?= htmlspecialchars($admin_name) ?> 👋</h2>

  <!-- Dashboard Cards -->
  <div class="row g-4">
    <div class="col-md-3">
      <div class="card shadow-sm border-0 text-center">
        <div class="card-body">
          <h5 class="card-title text-success">👥 Members</h5>
          <p class="display-6"><?= $total_members ?></p>
          <a href="members.php" class="btn btn-success btn-sm">Manage Members</a>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm border-0 text-center">
        <div class="card-body">
          <h5 class="card-title text-success">💰 Contributions</h5>
          <p class="display-6"><?= $total_contributions ? number_format($total_contributions,2) : '0.00' ?></p>
          <a href="contributions.php" class="btn btn-success btn-sm">Manage Contributions</a>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm border-0 text-center">
        <div class="card-body">
          <h5 class="card-title text-success">📄 Pending Loans</h5>
          <p class="display-6"><?= $total_loans_pending ?></p>
          <a href="loans.php" class="btn btn-success btn-sm">View Loans</a>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm border-0 text-center">
        <div class="card-body">
          <h5 class="card-title text-success">✅ Approved Loans</h5>
          <p class="display-6"><?= $total_loans_approved ?></p>
          <a href="loans.php" class="btn btn-success btn-sm">View Loans</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Quick Links -->
  <div class="row mt-5">
    <div class="col-md-3 mb-3">
      <a href="members.php" class="btn btn-outline-success w-100 p-3">Manage Members</a>
    </div>
    <div class="col-md-3 mb-3">
      <a href="contributions.php" class="btn btn-outline-success w-100 p-3">Record Contributions</a>
    </div>
    <div class="col-md-3 mb-3">
      <a href="loans.php" class="btn btn-outline-success w-100 p-3">Manage Loans</a>
    </div>
    <div class="col-md-3 mb-3">
      <a href="reports.php" class="btn btn-outline-success w-100 p-3">Generate Reports</a>
    </div>
  </div>

</div>

<?php include "../includes/footer.php"; ?>
