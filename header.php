<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JonesAuto Admin</title>
    <link rel="stylesheet" href="/barecarsales_app/style.css">
</head>

<body>
    <div class="container">
        <h1>Jones Auto Database Interface</h1>
        <nav class="navbar">
            <ul class="menu">
                <li><a href="/barecarsales_app/index.php">Home</a></li>

                <li class="dropdown">
                    <a href="#">Reports</a>
                    <ul class="submenu">
                        <li><a href="/barecarsales_app/reports/index.php">Overview</a></li>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#">Repairs</a>
                    <ul class="submenu">
                        <li><a href="/barecarsales_app/repair/repairs.php">View All Repairs</a></li>
                        <li><a href="/barecarsales_app/repair/repair_create.php">Add Repair</a></li>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#">Customers</a>
                    <ul class="submenu">
                        <li><a href="/barecarsales_app/customer/customers.php">View All Customers</a></li>
                        <li><a href="/barecarsales_app/customer/customer_create.php">Add Customer</a></li>
                        <li><a href="/barecarsales_app/employment_history/employment_historys.php">Employment History</a></li>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#">Employees</a>
                    <ul class="submenu">
                        <li><a href="/barecarsales_app/employee/employees.php">View All Employees</a></li>
                        <li><a href="/barecarsales_app/employee/employee_create.php">Add Employee</a></li>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#">Sales</a>
                    <ul class="submenu">
                        <li><a href="/barecarsales_app/sale/sales.php">View All Sales</a></li>
                        <li><a href="/barecarsales_app/sale/sale_create.php">Add Sale</a></li>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#">Payments</a>
                    <ul class="submenu">
                        <li><a href="/barecarsales_app/payment/payments.php">View All Payments</a></li>
                        <li><a href="/barecarsales_app/payment/payment_create.php">Add Payment</a></li>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#">Warranties</a>
                    <ul class="submenu">
                        <li><a href="/barecarsales_app/warranty/warranties.php">View All Warranties</a></li>
                        <li><a href="/barecarsales_app/warranty/warranty_create.php">Add Warranty</a></li>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#">Purchases</a>
                    <ul class="submenu">
                        <li><a href="/barecarsales_app/purchase/purchases.php">View All Purchases</a></li>
                        <li><a href="/barecarsales_app/purchase/purchase_create.php">Add Purchase</a></li>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#">Vehicles</a>
                    <ul class="submenu">
                        <li><a href="/barecarsales_app/vehicle/vehicles.php">View All Vehicles</a></li>
                        <li><a href="/barecarsales_app/vehicle/vehicle_create.php">Add Vehicle</a></li>
                        <li><a href="/barecarsales_app/vehicle/available_vehicles.php">In Stock</a></li>
                    </ul>
                </li>
            </ul>
        </nav>