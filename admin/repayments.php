<?php
session_start();
require_once "../includes/db.php";

// Protect page
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Get loan_id
if (!isset($_GET['loan_id'])) {
    header("Location: loans.php");
    exit;
}

$loan_id = (int)$_GET['loan_id'];

// Fetch loan details
$stmt = $pdo->prepare("
    SELECT l.*, m.name, m.phone
    FROM loans l
    JOIN members m ON l.member_id = m.id
    WHERE l.id = ?
");
$stmt->execute([$loan_id]);
$loan = $stmt->fetch();

if (!$loan) {
    die("Loan not found!");
}

// Handle new repayment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['amount'])) {
    $amount = (float) $_POST['amount'];

    // Insert repayment
    $stmt = $pdo->prepare("INSERT INTO loan_repayments (loan_id, amount) VALUES (?, ?)");
    $stmt->execute([$loan_id, $amount]);

    // Calculate total paid
    $total_paid = $pdo->prepare("SELECT SUM(amount) FROM loan_repayments WHERE loan_id=?");
    $total_paid->execute([$loan_id]);
    $total_paid = $total_paid->fetchColumn();

    // Mark loan as repaid if fully paid
    if ($total_paid >= $loan['amount']) {
        $stmt = $pdo->prepare("UPDATE loans SET status='repaid' WHERE id=?");
        $stmt->execute([$loan_id]);
    }

    $success = "Repayment recorded successfully!";
}

// Fetch all repayments
$stmt = $pdo->prepare("SELECT * FROM loan_repayments WHERE loan_id=? ORDER BY payment_date DESC");
$stmt->execute([$loan_id]);
$repayments = $stmt->fetchAll();
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/admin_navbar.php"; ?>

<div class="container py-5">
    <h2 class="text-success mb-4">💵 Manage Repayments</h2>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <!-- Loan Info -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Loan Details</h5>
            <p><strong>Member:</strong> <?= htmlspecialchars($loan['name']) ?> (<?= $loan['phone'] ?>)</p>
            <p><strong>Loan Amount:</strong> <?= number_format($loan['amount'], 2) ?></p>
            <p><strong>Status:</strong> <span class="badge bg-<?= $loan['status']=='approved'?'success':($loan['status']=='pending'?'warning':'primary') ?>"><?= ucfirst($loan['status']) ?></span></p>
        </div>
    </div>

    <!-- Record New Repayment -->
    <?php if ($loan['status'] != 'repaid'): ?>
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">➕ Record New Repayment</h5>
            <form method="POST" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Amount Paid</label>
                    <input type="number" name="amount" class="form-control" step="0.01" required>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-success w-100">Record Repayment</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- Repayment History -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">📋 Repayment History</h5>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="table-success">
                        <tr>
                            <th>#</th>
                            <th>Amount Paid</th>
                            <th>Payment Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($repayments as $i => $r): ?>
                        <tr>
                            <td><?= $i+1 ?></td>
                            <td><?= number_format($r['amount'],2) ?></td>
                            <td><?= $r['payment_date'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($repayments)): ?>
                        <tr>
                            <td colspan="3" class="text-center">No repayments recorded yet</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<?php include "../includes/footer.php"; ?>
