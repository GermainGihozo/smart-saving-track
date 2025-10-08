<?php
session_start();
require '../includes/db.php';

// ✅ Ensure member is logged in
if (!isset($_SESSION['member_id']) || $_SESSION['role'] !== 'member') {
    header("Location: ../login.php");
    exit;
}

$member_id = $_SESSION['member_id'];
$member_name = $_SESSION['member_name'];

// ✅ 1. Total Contributions (Savings)
$stmt = $pdo->prepare("SELECT IFNULL(SUM(amount), 0) FROM contributions WHERE member_id = ?");
$stmt->execute([$member_id]);
$total_savings = $stmt->fetchColumn();

// ✅ 2. Total Loans
$stmt = $pdo->prepare("SELECT IFNULL(SUM(amount), 0) FROM loans WHERE member_id = ?");
$stmt->execute([$member_id]);
$total_loans = $stmt->fetchColumn();

// ✅ 3. Total Repayments (via loans)
$stmt = $pdo->prepare("
    SELECT IFNULL(SUM(r.amount), 0)
    FROM repayments r
    INNER JOIN loans l ON r.loan_id = l.id
    WHERE l.member_id = ?
");
$stmt->execute([$member_id]);
$total_repaid = $stmt->fetchColumn();

// ✅ 4. Remaining Loan Balance
$loan_balance = $total_loans - $total_repaid;

// ✅ 5. Recent Transactions (latest 5 from contributions + repayments)
$stmt = $pdo->prepare("
    (SELECT c.amount, c.date, 'Contribution' AS type FROM contributions c WHERE c.member_id = ?)
    UNION ALL
    (SELECT r.amount, r.date, 'Repayment' AS type FROM repayments r INNER JOIN loans l ON r.loan_id = l.id WHERE l.member_id = ?)
    ORDER BY date DESC LIMIT 5
");
$stmt->execute([$member_id, $member_id]);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Member Dashboard - Smart Saving Track</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: "Poppins", sans-serif;
      background: linear-gradient(135deg, #e3f2fd, #e8f5e9);
      min-height: 100vh;
      overflow-x: hidden;
    }

    .dashboard-header {
      background: linear-gradient(90deg, #198754, #20c997);
      color: white;
      text-align: center;
      padding: 40px 20px;
      border-radius: 0 0 40px 40px;
      animation: fadeInDown 1s ease;
    }

    @keyframes fadeInDown {
      from {opacity: 0; transform: translateY(-30px);}
      to {opacity: 1; transform: translateY(0);}
    }

    .card {
      border-radius: 15px;
      border: none;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    footer {
      text-align: center;
      padding: 15px;
      background: #198754;
      color: white;
      margin-top: 40px;
      font-size: 0.9rem;
    }

    @keyframes fadeInUp {
      from {opacity: 0; transform: translateY(30px);}
      to {opacity: 1; transform: translateY(0);}
    }

    .fade-in {
      animation: fadeInUp 1s ease;
    }
  </style>
</head>
<body>

  <!-- Dashboard Header -->
  <div class="dashboard-header mb-5">
    <h2>Welcome, <?= htmlspecialchars($member_name) ?> 👋</h2>
    <p>Manage your savings, loans, and repayments effortlessly</p>
    <a href="../login.php" class="btn btn-danger">Logout</a>
  </div>

  <div class="container fade-in">
    <div class="row g-4 text-center mb-4">
      <div class="col-md-3 col-6">
        <div class="card p-4">
          <h6 class="text-muted">Total Savings</h6>
          <h3 class="text-success">RWF <?= number_format($total_savings, 2) ?></h3>
        </div>
      </div>
      <div class="col-md-3 col-6">
        <div class="card p-4">
          <h6 class="text-muted">Total Loans</h6>
          <h3 class="text-primary">RWF <?= number_format($total_loans, 2) ?></h3>
        </div>
      </div>
      <div class="col-md-3 col-6">
        <div class="card p-4">
          <h6 class="text-muted">Total Repaid</h6>
          <h3 class="text-info">RWF <?= number_format($total_repaid, 2) ?></h3>
        </div>
      </div>
      <div class="col-md-3 col-6">
        <div class="card p-4">
          <h6 class="text-muted">Loan Balance</h6>
          <h3 class="text-danger">RWF <?= number_format($loan_balance, 2) ?></h3>
        </div>
      </div>
    </div>

    <!-- Recent Transactions Table -->
    <div class="card p-4 shadow-sm">
      <h5 class="mb-3">📊 Recent Transactions</h5>
      <div class="table-responsive">
        <table class="table table-striped align-middle">
          <thead class="table-success">
            <tr>
              <th>Date</th>
              <th>Type</th>
              <th>Amount (RWF)</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($transactions): ?>
              <?php foreach ($transactions as $t): ?>
                <tr>
                  <td><?= htmlspecialchars($t['date']) ?></td>
                  <td><?= htmlspecialchars($t['type']) ?></td>
                  <td><?= number_format($t['amount'], 2) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="3" class="text-center text-muted">No recent transactions found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <footer>
    <p>© <?= date('Y') ?> Smart Saving Track — All rights reserved.</p>
  </footer>

</body>
</html>
