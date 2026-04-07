<?php
include '../db.php';
include '../header.php';

$sql_most = "
SELECT make, model, style, AVG(per_sale.earnings) AS avg_per_sale, COUNT(*) AS sales_count
FROM (
    SELECT
        v.make,
        v.model,
        v.style,
        s.sale_id,
        (
            GREATEST(COALESCE(s.sale_price, 0), COALESCE(s.total_due, 0))
            - p.price_paid
            - COALESCE(SUM(r.actual_cost), 0)
            - COALESCE(s.salesperson_commission, 0)
        ) AS earnings
    FROM sale s
    INNER JOIN vehicle v ON s.vehicle_id = v.vehicle_id
    INNER JOIN purchase p ON p.vehicle_id = s.vehicle_id
    LEFT JOIN repair r ON r.purchase_id = p.purchase_id
    GROUP BY s.sale_id, v.make, v.model, v.style, s.sale_price, s.total_due, p.price_paid, s.salesperson_commission
) AS per_sale
GROUP BY make, model, style
ORDER BY avg_per_sale DESC
LIMIT 1
";

$sql_least = "
SELECT make, model, style, AVG(per_sale.earnings) AS avg_per_sale, COUNT(*) AS sales_count
FROM (
    SELECT
        v.make,
        v.model,
        v.style,
        s.sale_id,
        (
            GREATEST(COALESCE(s.sale_price, 0), COALESCE(s.total_due, 0))
            - p.price_paid
            - COALESCE(SUM(r.actual_cost), 0)
            - COALESCE(s.salesperson_commission, 0)
        ) AS earnings
    FROM sale s
    INNER JOIN vehicle v ON s.vehicle_id = v.vehicle_id
    INNER JOIN purchase p ON p.vehicle_id = s.vehicle_id
    LEFT JOIN repair r ON r.purchase_id = p.purchase_id
    GROUP BY s.sale_id, v.make, v.model, v.style, s.sale_price, s.total_due, p.price_paid, s.salesperson_commission
) AS per_sale
GROUP BY make, model, style
ORDER BY avg_per_sale ASC
LIMIT 1
";

$min_repair_cost = 1000;
$sql_repairs_over_cost = "
SELECT repair_id, purchase_id, problem_description, est_repair_cost, actual_cost
FROM repair
WHERE est_repair_cost > " . ((float) $min_repair_cost) . "
";

$sql_vehicles_no_warranty = "
SELECT DISTINCT s.vehicle_id
FROM sale s
WHERE NOT EXISTS (
    SELECT 1
    FROM warranty w
    WHERE w.sale_id = s.sale_id
)
";

$sql_outstanding_balances = "
SELECT
    s.sale_id,
    s.sale_date,
    v.vehicle_id,
    CONCAT(v.make, ' ', v.model, ' ', v.year) AS vehicle_label,
    c.customer_id,
    TRIM(CONCAT(c.first_name, ' ', c.last_name)) AS customer_name,
    COALESCE(s.total_due, s.sale_price, 0) AS total_due,
    COALESCE(pp.total_paid, 0) AS total_paid,
    (COALESCE(s.total_due, s.sale_price, 0) - COALESCE(pp.total_paid, 0)) AS balance_due,
    pp.last_payment_date
FROM sale s
INNER JOIN customer c ON s.customer_id = c.customer_id
INNER JOIN vehicle v ON s.vehicle_id = v.vehicle_id
LEFT JOIN (
    SELECT
        p.sale_id,
        SUM(COALESCE(p.amount, 0)) AS total_paid,
        MAX(COALESCE(p.paid_date, p.payment_date)) AS last_payment_date
    FROM payment p
    GROUP BY p.sale_id
) pp ON pp.sale_id = s.sale_id
WHERE (COALESCE(s.total_due, s.sale_price, 0) - COALESCE(pp.total_paid, 0)) > 0.01
ORDER BY balance_due DESC, s.sale_id ASC
";

$res_most = $conn->query($sql_most);
$row_most = $res_most ? $res_most->fetch_assoc() : null;

$sql_top_salesperson = "
SELECT salesperson_employee_id, COUNT(*) AS num_sales
FROM sale
GROUP BY salesperson_employee_id
ORDER BY num_sales DESC
LIMIT 1
";

$res_top_salesperson = $conn->query($sql_top_salesperson);
$row_top_salesperson = $res_top_salesperson ? $res_top_salesperson->fetch_assoc() : null;
$res_least = $conn->query($sql_least);
$row_least = $res_least ? $res_least->fetch_assoc() : null;

$res_repairs_over_cost = $conn->query($sql_repairs_over_cost);

$res_vehicles_no_warranty = $conn->query($sql_vehicles_no_warranty);
$row_vehicles_no_warranty = $res_vehicles_no_warranty ? $res_vehicles_no_warranty->fetch_assoc() : null;

$res_outstanding_balances = $conn->query($sql_outstanding_balances);
?>

<h2>Reports</h2>

<h3>Best by model and style (average per sale)</h3>

<p>
    <strong>> Most profitable (model + style):</strong>
    <?php if ($row_most): ?>
        <?= htmlspecialchars($row_most['make'] . ' ' . $row_most['model'] . (!empty($row_most['style']) ? ' — ' . $row_most['style'] : '')) ?>
        (avg $<?= htmlspecialchars(number_format((float) $row_most['avg_per_sale'], 2)) ?> per sale,
        <?= (int) $row_most['sales_count'] ?> sale(s) in this group)
    <?php else: ?>
        <em>No data (need sales with matching purchases).</em>
    <?php endif; ?>
</p>

<h3>Salesperson with the Most Sales</h3>

<p>
    <strong>> Top Performer:</strong>
    <?php if ($row_top_salesperson): ?>
        Employee ID: <?= htmlspecialchars($row_top_salesperson['salesperson_employee_id']) ?>
        with <?= (int) $row_top_salesperson['num_sales'] ?> total sales
    <?php else: ?>
        <em>No sales data available.</em>
    <?php endif; ?>
</p>

<p>
    <strong>Least profitable (model + style):</strong>
    <?php if ($row_least): ?>
        <?= htmlspecialchars($row_least['make'] . ' ' . $row_least['model'] . (!empty($row_least['style']) ? ' — ' . $row_least['style'] : '')) ?>
        (avg $<?= htmlspecialchars(number_format((float) $row_least['avg_per_sale'], 2)) ?> per sale,
        <?= (int) $row_least['sales_count'] ?> sale(s) in this group)
    <?php else: ?>
        <em>No data (need sales with matching purchases).</em>
    <?php endif; ?>
</p>

<h3>Repairs with estimated cost above $<?= htmlspecialchars(number_format((float) $min_repair_cost, 2)) ?></h3>

<?php if ($res_repairs_over_cost && $res_repairs_over_cost->num_rows > 0): ?>
    <ul>
        <?php while ($repair = $res_repairs_over_cost->fetch_assoc()): ?>
            <li>
                Repair #<?= htmlspecialchars($repair['repair_id']) ?>,
                Purchase #<?= htmlspecialchars($repair['purchase_id']) ?> —
                <?= htmlspecialchars($repair['problem_description']) ?>
                (est $<?= htmlspecialchars(number_format((float) $repair['est_repair_cost'], 2)) ?>,
                actual $<?= htmlspecialchars(number_format((float) $repair['actual_cost'], 2)) ?>)
            </li>
        <?php endwhile; ?>
    </ul>
<?php else: ?>
    <p><em>No repairs found above that estimated cost.</em></p>
<?php endif; ?>

<h3>Vehicles sold without warranty</h3>

<?php if ($row_vehicles_no_warranty): ?>
    <ul>
        <li>Vehicle #<?= htmlspecialchars($row_vehicles_no_warranty['vehicle_id']) ?></li>
        <?php while ($vehicle = $res_vehicles_no_warranty->fetch_assoc()): ?>
            <li>Vehicle #<?= htmlspecialchars($vehicle['vehicle_id']) ?></li>
        <?php endwhile; ?>
    </ul>
<?php else: ?>
    <p><em>No vehicles found without warranty.</em></p>
<?php endif; ?>

<h3>Customers with outstanding balances</h3>

<?php if ($res_outstanding_balances && $res_outstanding_balances->num_rows > 0): ?>
    <table>
        <tr>
            <th>Sale ID</th>
            <th>Sale Date</th>
            <th>Customer</th>
            <th>Vehicle</th>
            <th>Total Due</th>
            <th>Total Paid</th>
            <th>Balance Due</th>
            <th>Last Payment</th>
        </tr>
        <?php while ($row = $res_outstanding_balances->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['sale_id']) ?></td>
                <td><?= htmlspecialchars($row['sale_date'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['customer_id'] . ' - ' . $row['customer_name']) ?></td>
                <td><?= htmlspecialchars($row['vehicle_label'] . ' (Vehicle #' . $row['vehicle_id'] . ')') ?></td>
                <td>$<?= htmlspecialchars(number_format((float) $row['total_due'], 2)) ?></td>
                <td>$<?= htmlspecialchars(number_format((float) $row['total_paid'], 2)) ?></td>
                <td><strong>$<?= htmlspecialchars(number_format((float) $row['balance_due'], 2)) ?></strong></td>
                <td><?= htmlspecialchars($row['last_payment_date'] ?? 'No payments') ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php elseif ($res_outstanding_balances): ?>
    <p><em>No outstanding balances found. (All sales appear fully paid.)</em></p>
<?php else: ?>
    <p><em>Could not run outstanding balance report: <?= htmlspecialchars($conn->error) ?></em></p>
<?php endif; ?>

<?php
include '../footer.php';
$conn->close();
?>
