<?php 
include '../db.php';

$errors = [];
$sale_id = '';
$customer_id = '';
$payment_date ='';
$due_date = '';
$paid_date = '';
$amount = '';
$bank_account = '';

$is_late = 0;
$days_late = 0;

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sale_id = trim($_POST['sale_id'] ?? '');
    $customer_id = trim($_POST['customer_id'] ?? '');
    $payment_date = trim($_POST['payment_date'] ?? '');
    $due_date = trim($_POST['due_date'] ?? '');
    $paid_date = trim($_POST['paid_date'] ?? '');
    $amount = trim($_POST['amount'] ?? '');
    $bank_account = trim($_POST['bank_account'] ?? '');

    
}
?>