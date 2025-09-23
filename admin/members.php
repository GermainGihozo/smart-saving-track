<?php
session_start();
require_once "../includes/db.php";

// (Later we’ll protect this page with admin authentication)
// For now, let’s focus on member management

// Handle new member form submission
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $phone    = trim($_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO members (name, phone, password, role) VALUES (?, ?, ?, 'member')");
        $stmt->execute([$name, $phone, $password]);
        $message = "<div class='alert alert-success'>✅ Member registered successfully!</div>";
    } catch (PDOException $e) {
        $message = "<div class='alert alert-danger'>❌ Error: " . $e->getMessage() . "</div>";
    }
}

// Fetch all members
$members = $pdo->query("SELECT * FROM members WHERE role='member' ORDER BY created_at DESC")->fetchAll();
?>

<?php include "../includes/admin_navbar.php"; ?>

<div class="container py-5">
  <h2 class="mb-4 text-success">👥 Manage Members</h2>

  <?php if ($message) echo $message; ?>

  <!-- Add Member Form -->
  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <h5 class="card-title text-success">➕ Add New Member</h5>
      <form method="POST">
        <div class="row mb-3">
          <div class="col-md-4">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">Phone Number</label>
            <input type="text" name="phone" class="form-control" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
        </div>
        <button type="submit" class="btn btn-success">Save Member</button>
      </form>
    </div>
  </div>

  <!-- Members List -->
  <div class="card shadow-sm">
    <div class="card-body">
      <h5 class="card-title text-success">📋 Members List</h5>
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Status</th>
            <th>Registered On</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($members as $m): ?>
            <tr>
              <td><?= $m['id'] ?></td>
              <td><?= htmlspecialchars($m['name']) ?></td>
              <td><?= htmlspecialchars($m['phone']) ?></td>
              <td>
                <span class="badge bg-<?= $m['status'] === 'active' ? 'success' : 'secondary' ?>">
                  <?= ucfirst($m['status']) ?>
                </span>
              </td>
              <td><?= $m['created_at'] ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<?php include "../includes/footer.php"; ?>
