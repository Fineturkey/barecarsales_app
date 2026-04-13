<?php
include 'db.php';
include 'header.php';

$counts = [];
$tables = [
    'vehicle'   => 'Vehicles',
    'customer'  => 'Customers',
    'employee'  => 'Employees',
    'sale'      => 'Sales',
    'payment'   => 'Payments',
    'repair'    => 'Repairs',
    'warranty'  => 'Warranties',
    'purchase'  => 'Purchases',
];
foreach ($tables as $table => $label) {
    $res = $conn->query("SELECT COUNT(*) AS n FROM `$table`");
    $counts[$label] = $res ? (int) $res->fetch_assoc()['n'] : 0;
}

$in_stock_res = $conn->query("SELECT COUNT(*) AS n FROM vehicle WHERE current_status = 'in_stock'");
$in_stock = $in_stock_res ? (int) $in_stock_res->fetch_assoc()['n'] : 0;
?>

<h2>Welcome to Jones Auto</h2>

<img src="/barecarsales_app/images/cars.avif" class="hero-img" alt="Jones Auto showroom">

<h3>Database Summary</h3>
<div class="stat-grid">
    <?php foreach ($counts as $label => $count): ?>
        <div class="stat-card">
            <span class="stat-value"><?= $count ?></span>
            <span class="stat-label"><?= htmlspecialchars($label) ?></span>
        </div>
    <?php endforeach; ?>
    <div class="stat-card stat-highlight">
        <span class="stat-value"><?= $in_stock ?></span>
        <span class="stat-label">In Stock</span>
    </div>
</div>

<h3>Quick Actions</h3>
<div class="quick-links">
    <a class="btn" href="/barecarsales_app/vehicle/available_vehicles.php">In-Stock Vehicles</a>
    <a class="btn" href="/barecarsales_app/sale/sale_create.php">New Sale</a>
    <a class="btn" href="/barecarsales_app/customer/customer_create.php">New Customer</a>
    <a class="btn" href="/barecarsales_app/reports/index.php">Reports</a>
</div>

<?php
include 'footer.php';
$conn->close();
?>