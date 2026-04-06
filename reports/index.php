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

<?php
include '../footer.php';
$conn->close();
?>
