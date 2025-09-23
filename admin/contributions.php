<?php
session_start();
require_once "../includes/db.php";

// Protect page: only admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Handle contribution submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['member_id'], $_POST['amount'])) {
    $member_id = $_POST['member_id'];
    $amount = $_POST['amount'];
    $date = date("Y-m-d H:i:s");

    $stmt = $pdo->prepare("INSERT INTO contributions (member_id, amount, contribution_date) VALUES (?, ?, ?)");
    $stmt->execute([$member_id, $amount, $date]);

    $success = "Contribution recorded successfully!";
}

// Fetch members
$members = $pdo->query("SELECT id, name, phone FROM members WHERE role='member'")->fetchAll();

// Fetch contributions
$contributions = $pdo->query("
    SELECT c.id, m.name, m.phone, c.amount, c.contribution_date
    FROM contributions c
    JOIN members m ON c.member_id = m.id
    ORDER BY c.contribution_date DESC
")->fetchAll();
?>

<?php include "../includes/admin_navbar.php"; ?>

<div class="container py-5">

  <h2 class="text-success mb-4">💰 Contributions Management</h2>

  <?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= $success ?></div>
  <?php endif; ?>

  <!-- Record Contribution Form -->
  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <h5 class="card-title mb-3">➕ Record Contribution</h5>
      <form method="POST">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Select Member</label>
            <select name="member_id" class="form-select" required>
              <option value="">-- Choose Member --</option>
              <?php foreach ($members as $member): ?>
                <option value="<?= $member['id'] ?>">
                  <?= htmlspecialchars($member['name']) ?> (<?= $member['phone'] ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Amount</label>
            <input type="number" name="amount" class="form-control" step="0.01" required>
          </div>
          <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-success w-100">Save Contribution</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Contributions Table -->
  <div class="card shadow-sm">
    <div class="card-body">
      <h5 class="card-title mb-3">📋 All Contributions</h5>
      <table class="table table-striped table-bordered">
        <thead class="table-success">
          <tr>
            <th>#</th>
            <th>Member Name</th>
            <th>Phone</th>
            <th>Amount</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($contributions as $i => $c): ?>
            <tr>
              <td><?= $i + 1 ?></td>
              <td><?= htmlspecialchars($c['name']) ?></td>
              <td><?= htmlspecialchars($c['phone']) ?></td>
              <td><?= number_format($c['amount'], 2) ?></td>
              <td><?= $c['contribution_date'] ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<?php include "../includes/footer.php"; ?>
