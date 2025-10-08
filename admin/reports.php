<?php
session_start();
require '../includes/db.php';

// ✅ Restrict to admin only
if (!isset($_SESSION['member_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Fetch all members for dropdown
$members = $pdo->query("SELECT id, name FROM members WHERE role='member'")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reports - Smart Saving Track</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f8f9fa;
      font-family: 'Poppins', sans-serif;
    }
    .nav-tabs .nav-link.active {
      background: #198754;
      color: #fff;
    }
    .card {
      border-radius: 15px;
    }
    footer {
      text-align: center;
      padding: 15px;
      margin-top: 40px;
      background: #198754;
      color: white;
    }
  </style>
</head>
<body>
<?php include "../includes/admin_navbar.php"; ?>

<div class="container py-5">
  <h2 class="text-success mb-4 text-center">📊 Reports Dashboard</h2>

  <!-- Tabs -->
  <ul class="nav nav-tabs mb-4" id="reportTabs">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#contributions">Contributions</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#loans">Loans</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#repayments">Repayments</a></li>
  </ul>

  <div class="tab-content">

    <!-- ==================== CONTRIBUTIONS REPORT ==================== -->
    <div class="tab-pane fade show active" id="contributions">
      <div class="card p-4 shadow-sm">
        <h5 class="text-success mb-3">Member Contributions Report</h5>
        <form class="row g-3 mb-4" method="GET">
          <div class="col-md-4">
            <label class="form-label">Select Member</label>
            <select name="c_member" class="form-select">
              <option value="">All Members</option>
              <?php foreach ($members as $m): ?>
                <option value="<?= $m['id'] ?>" <?= isset($_GET['c_member']) && $_GET['c_member']==$m['id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($m['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">From Date</label>
            <input type="date" name="c_from" value="<?= $_GET['c_from'] ?? '' ?>" class="form-control">
          </div>
          <div class="col-md-3">
            <label class="form-label">To Date</label>
            <input type="date" name="c_to" value="<?= $_GET['c_to'] ?? '' ?>" class="form-control">
          </div>
          <div class="col-md-2 align-self-end">
            <button class="btn btn-success w-100">Filter</button>
          </div>
        </form>

        <?php
        // Dynamic filter query for contributions
        $query = "SELECT c.*, m.name FROM contributions c JOIN members m ON c.member_id = m.id WHERE 1";
        $params = [];
        if (!empty($_GET['c_member'])) { $query .= " AND c.member_id = ?"; $params[] = $_GET['c_member']; }
        if (!empty($_GET['c_from']))   { $query .= " AND c.date >= ?"; $params[] = $_GET['c_from']; }
        if (!empty($_GET['c_to']))     { $query .= " AND c.date <= ?"; $params[] = $_GET['c_to']; }
        $query .= " ORDER BY c.date DESC";
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <div class="table-responsive">
          <table class="table table-striped align-middle">
            <thead class="table-success">
              <tr><th>Date</th><th>Member</th><th>Amount (RWF)</th><th>Note</th></tr>
            </thead>
            <tbody>
              <?php if ($rows): ?>
                <?php $total=0; foreach ($rows as $r): $total+=$r['amount']; ?>
                  <tr>
                    <td><?= htmlspecialchars($r['date']) ?></td>
                    <td><?= htmlspecialchars($r['name']) ?></td>
                    <td><?= number_format($r['amount'],2) ?></td>
                    <td><?= htmlspecialchars($r['note']) ?></td>
                  </tr>
                <?php endforeach; ?>
                <tr class="fw-bold table-light">
                  <td colspan="2">Total</td><td>RWF <?= number_format($total,2) ?></td><td></td>
                </tr>
              <?php else: ?>
                <tr><td colspan="4" class="text-center text-muted">No records found.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ==================== LOANS REPORT ==================== -->
    <div class="tab-pane fade" id="loans">
      <div class="card p-4 shadow-sm">
        <h5 class="text-success mb-3">Loans Report</h5>
        <form class="row g-3 mb-4" method="GET">
          <div class="col-md-4">
            <label class="form-label">Loan Status</label>
            <select name="l_status" class="form-select">
              <option value="">All</option>
              <option value="pending" <?= ($_GET['l_status'] ?? '')=='pending'?'selected':'' ?>>Pending</option>
              <option value="approved" <?= ($_GET['l_status'] ?? '')=='approved'?'selected':'' ?>>Approved</option>
              <option value="rejected" <?= ($_GET['l_status'] ?? '')=='rejected'?'selected':'' ?>>Rejected</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">From Date</label>
            <input type="date" name="l_from" value="<?= $_GET['l_from'] ?? '' ?>" class="form-control">
          </div>
          <div class="col-md-3">
            <label class="form-label">To Date</label>
            <input type="date" name="l_to" value="<?= $_GET['l_to'] ?? '' ?>" class="form-control">
          </div>
          <div class="col-md-2 align-self-end">
            <button class="btn btn-success w-100">Filter</button>
          </div>
        </form>

        <?php
        $query = "SELECT l.*, m.name FROM loans l JOIN members m ON l.member_id = m.id WHERE 1";
        $params = [];
        if (!empty($_GET['l_status'])) { $query .= " AND l.status = ?"; $params[] = $_GET['l_status']; }
        if (!empty($_GET['l_from']))   { $query .= " AND l.request_date >= ?"; $params[] = $_GET['l_from']; }
        if (!empty($_GET['l_to']))     { $query .= " AND l.request_date <= ?"; $params[] = $_GET['l_to']; }
        $query .= " ORDER BY l.request_date DESC";
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $loans = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <div class="table-responsive">
          <table class="table table-striped align-middle">
            <thead class="table-success">
              <tr><th>Request Date</th><th>Member</th><th>Amount</th><th>Status</th><th>Approval Date</th><th>Due Date</th></tr>
            </thead>
            <tbody>
              <?php if ($loans): ?>
                <?php $sum=0; foreach ($loans as $l): $sum+=$l['amount']; ?>
                  <tr>
                    <td><?= htmlspecialchars($l['request_date']) ?></td>
                    <td><?= htmlspecialchars($l['name']) ?></td>
                    <td><?= number_format($l['amount'],2) ?></td>
                    <td><?= ucfirst($l['status']) ?></td>
                    <td><?= htmlspecialchars($l['approval_date']) ?></td>
                    <td><?= htmlspecialchars($l['due_date']) ?></td>
                  </tr>
                <?php endforeach; ?>
                <tr class="fw-bold table-light">
                  <td colspan="2">Total Loan Amount</td><td>RWF <?= number_format($sum,2) ?></td><td colspan="3"></td>
                </tr>
              <?php else: ?>
                <tr><td colspan="6" class="text-center text-muted">No loans found.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ==================== REPAYMENTS REPORT ==================== -->
    <div class="tab-pane fade" id="repayments">
      <div class="card p-4 shadow-sm">
        <h5 class="text-success mb-3">Repayments Report</h5>
        <form class="row g-3 mb-4" method="GET">
          <div class="col-md-4">
            <label class="form-label">Select Member</label>
            <select name="r_member" class="form-select">
              <option value="">All Members</option>
              <?php foreach ($members as $m): ?>
                <option value="<?= $m['id'] ?>" <?= isset($_GET['r_member']) && $_GET['r_member']==$m['id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($m['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">From Date</label>
            <input type="date" name="r_from" value="<?= $_GET['r_from'] ?? '' ?>" class="form-control">
          </div>
          <div class="col-md-3">
            <label class="form-label">To Date</label>
            <input type="date" name="r_to" value="<?= $_GET['r_to'] ?? '' ?>" class="form-control">
          </div>
          <div class="col-md-2 align-self-end">
            <button class="btn btn-success w-100">Filter</button>
          </div>
        </form>

        <?php
        $query = "SELECT r.*, m.name 
                  FROM repayments r 
                  JOIN loans l ON r.loan_id = l.id 
                  JOIN members m ON l.member_id = m.id 
                  WHERE 1";
        $params = [];
        if (!empty($_GET['r_member'])) { $query .= " AND m.id = ?"; $params[] = $_GET['r_member']; }
        if (!empty($_GET['r_from']))   { $query .= " AND r.date >= ?"; $params[] = $_GET['r_from']; }
        if (!empty($_GET['r_to']))     { $query .= " AND r.date <= ?"; $params[] = $_GET['r_to']; }
        $query .= " ORDER BY r.date DESC";
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $repayments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <div class="table-responsive">
          <table class="table table-striped align-middle">
            <thead class="table-success">
              <tr><th>Date</th><th>Member</th><th>Amount</th></tr>
            </thead>
            <tbody>
              <?php if ($repayments): ?>
                <?php $totalr=0; foreach ($repayments as $r): $totalr+=$r['amount']; ?>
                  <tr>
                    <td><?= htmlspecialchars($r['date']) ?></td>
                    <td><?= htmlspecialchars($r['name']) ?></td>
                    <td><?= number_format($r['amount'],2) ?></td>
                  </tr>
                <?php endforeach; ?>
                <tr class="fw-bold table-light">
                  <td colspan="2">Total Repaid</td><td>RWF <?= number_format($totalr,2) ?></td>
                </tr>
              <?php else: ?>
                <tr><td colspan="3" class="text-center text-muted">No repayments found.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<footer>
  © <?= date('Y') ?> Smart Saving Track — Reports Dashboard
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
