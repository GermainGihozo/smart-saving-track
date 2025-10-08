<?php
session_start();
require_once "../includes/db.php";

// Protect page
if (!isset($_SESSION['member_id'])) {
    header("Location: login.php");
    exit;
}

// Approve loan
if (isset($_GET['approve'])) {
    $loan_id = (int) $_GET['approve'];
    $stmt = $pdo->prepare("UPDATE loans SET status='approved', approval_date=NOW() WHERE id=?");
    $stmt->execute([$loan_id]);
    $success = "Loan approved successfully!";
}

// Reject loan
if (isset($_GET['reject'])) {
    $loan_id = (int) $_GET['reject'];
    $stmt = $pdo->prepare("UPDATE loans SET status='rejected' WHERE id=?");
    $stmt->execute([$loan_id]);
    $success = "Loan rejected successfully!";
}

// Fetch loans with member info
$loans = $pdo->query("
    SELECT l.id, m.name, m.phone, l.amount, l.status, l.request_date, l.approval_date
    FROM loans l
    JOIN members m ON l.member_id = m.id
    ORDER BY l.request_date DESC
")->fetchAll();
?>

<?php 
// include "../includes/header.php";
 ?>
<?php include "../includes/admin_navbar.php"; ?>

<div class="container py-5">
  <h2 class="text-success mb-4">💳 Loans Management</h2>

  <?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= $success ?></div>
  <?php endif; ?>

  <div class="card shadow-sm">
    <div class="table-responsive">
  <table class="table table-striped table-bordered align-middle">
    <thead class="table-success">
      <tr>
        <th>#</th>
        <th>Member</th>
        <th>Phone</th>
        <th>Amount</th>
        <th>Status</th>
        <th>Request Date</th>
        <th>Approval Date</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($loans as $i => $loan): ?>
      <tr>
        <td><?= $i + 1 ?></td>
        <td><?= htmlspecialchars($loan['name']) ?></td>
        <td><?= htmlspecialchars($loan['phone']) ?></td>
        <td><?= number_format($loan['amount'], 2) ?></td>
        <td>
          <?php if ($loan['status'] == 'pending'): ?>
            <span class="badge bg-warning">Pending</span>
          <?php elseif ($loan['status'] == 'approved'): ?>
            <span class="badge bg-success">Approved</span>
          <?php elseif ($loan['status'] == 'rejected'): ?>
            <span class="badge bg-danger">Rejected</span>
          <?php elseif ($loan['status'] == 'repaid'): ?>
            <span class="badge bg-primary">Repaid</span>
          <?php endif; ?>
        </td>
        <td><?= $loan['request_date'] ?></td>
        <td><?= $loan['approval_date'] ?? '-' ?></td>
        <td class="text-center">
          <?php if ($loan['status'] == 'pending'): ?>
            <a href="?approve=<?= $loan['id'] ?>" class="btn btn-success btn-sm mb-1">Approve</a>
            <a href="?reject=<?= $loan['id'] ?>" class="btn btn-danger btn-sm mb-1">Reject</a>
          <?php elseif ($loan['status'] == 'approved'): ?>
            <a href="repayments.php?loan_id=<?= $loan['id'] ?>" class="btn btn-primary btn-sm">Repayments</a>
          <?php else: ?>
            <span class="text-muted">-</span>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

</div>

<?php include "../includes/footer.php"; ?>
