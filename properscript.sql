DROP DATABASE IF EXISTS carsales_app;
CREATE DATABASE carsales_app;
USE carsales_app;

-- =========================================================
-- 1) EMPLOYEE
-- Use one table instead of separate buyer / salesperson tables
-- =========================================================
CREATE TABLE employee (
    employee_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone VARCHAR(20),
    role ENUM('buyer', 'salesperson', 'both') NOT NULL DEFAULT 'salesperson'
);

-- =========================================================
-- 2) VEHICLE
-- =========================================================
CREATE TABLE vehicle (
    vehicle_id INT AUTO_INCREMENT PRIMARY KEY,
    vin VARCHAR(30) UNIQUE,
    make VARCHAR(50) NOT NULL,
    model VARCHAR(50) NOT NULL,
    year INT,
    color VARCHAR(30),
    miles INT,
    vehicle_condition VARCHAR(50),
    book_price DECIMAL(10,2),
    style VARCHAR(30),
    interior_color VARCHAR(30),
    current_status ENUM('in_stock', 'sold', 'repairing') NOT NULL DEFAULT 'in_stock'
);

-- =========================================================
-- 3) PURCHASE
-- Keep seller/dealer as text for simplicity
-- =========================================================
CREATE TABLE purchase (
    purchase_id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_id INT NOT NULL,
    buyer_employee_id INT NOT NULL,
    seller_name VARCHAR(100),
    purchase_date DATE NOT NULL,
    location VARCHAR(100),
    is_auction TINYINT(1) NOT NULL DEFAULT 0,
    price_paid DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_purchase_vehicle
        FOREIGN KEY (vehicle_id) REFERENCES vehicle(vehicle_id)
        ON DELETE CASCADE,
    CONSTRAINT fk_purchase_employee
        FOREIGN KEY (buyer_employee_id) REFERENCES employee(employee_id)
        ON DELETE RESTRICT
);

-- =========================================================
-- 4) REPAIR
-- =========================================================
CREATE TABLE repair (
    repair_id INT AUTO_INCREMENT PRIMARY KEY,
    purchase_id INT NOT NULL,
    problem_description VARCHAR(255) NOT NULL,
    est_repair_cost DECIMAL(10,2) DEFAULT 0.00,
    actual_cost DECIMAL(10,2) DEFAULT 0.00,
    CONSTRAINT fk_repair_purchase
        FOREIGN KEY (purchase_id) REFERENCES purchase(purchase_id)
        ON DELETE CASCADE
);

-- =========================================================
-- 5) CUSTOMER
-- =========================================================
CREATE TABLE customer (
    customer_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone VARCHAR(20),
    address VARCHAR(100),
    city VARCHAR(50),
    state VARCHAR(50),
    zip VARCHAR(15),
    gender VARCHAR(20),
    date_of_birth DATE,
    taxpayer_id VARCHAR(30),
    late_payment_count INT NOT NULL DEFAULT 0,
    avg_days_late DECIMAL(6,2) NOT NULL DEFAULT 0.00,
    reported_to_credit_bureau TINYINT(1) NOT NULL DEFAULT 0
);

-- =========================================================
-- 6) EMPLOYMENT HISTORY
-- =========================================================
CREATE TABLE employment_history (
    employment_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    employer_name VARCHAR(100) NOT NULL,
    job_title VARCHAR(100),
    supervisor_name VARCHAR(100),
    supervisor_phone VARCHAR(20),
    employer_address VARCHAR(150),
    start_date DATE,
    end_date DATE,
    is_current TINYINT(1) NOT NULL DEFAULT 0,
    CONSTRAINT fk_employment_customer
        FOREIGN KEY (customer_id) REFERENCES customer(customer_id)
        ON DELETE CASCADE
);

-- =========================================================
-- 7) SALE
-- =========================================================
CREATE TABLE sale (
    sale_id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_id INT NOT NULL,
    customer_id INT NOT NULL,
    salesperson_employee_id INT NOT NULL,
    sale_date DATE NOT NULL,
    total_due DECIMAL(10,2) NOT NULL,
    down_payment DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    financed_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    sale_price DECIMAL(10,2) NOT NULL,
    salesperson_commission DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    CONSTRAINT fk_sale_vehicle
        FOREIGN KEY (vehicle_id) REFERENCES vehicle(vehicle_id)
        ON DELETE RESTRICT,
    CONSTRAINT fk_sale_customer
        FOREIGN KEY (customer_id) REFERENCES customer(customer_id)
        ON DELETE RESTRICT,
    CONSTRAINT fk_sale_employee
        FOREIGN KEY (salesperson_employee_id) REFERENCES employee(employee_id)
        ON DELETE RESTRICT
);

-- =========================================================
-- 8) PAYMENT
-- =========================================================
CREATE TABLE payment (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    customer_id INT NOT NULL,
    payment_date DATE NOT NULL,
    due_date DATE NOT NULL,
    paid_date DATE,
    amount DECIMAL(10,2) NOT NULL,
    bank_account VARCHAR(50),
    is_late TINYINT(1) NOT NULL DEFAULT 0,
    days_late INT NOT NULL DEFAULT 0,
    CONSTRAINT fk_payment_sale
        FOREIGN KEY (sale_id) REFERENCES sale(sale_id)
        ON DELETE CASCADE,
    CONSTRAINT fk_payment_customer
        FOREIGN KEY (customer_id) REFERENCES customer(customer_id)
        ON DELETE CASCADE
);


-- =====================================================
-- 9) WARRANTY
-- =====================================================

CREATE TABLE warranty (
    warranty_id INT AUTO_INCREMENT PRIMARY KEY,

    sale_id INT NOT NULL,
    vehicle_id INT NOT NULL,
    customer_id INT NOT NULL,
    employee_id INT NOT NULL,

    warranty_name VARCHAR(100) NOT NULL,
    warranty_sale_date DATE NOT NULL,
    start_date DATE NOT NULL,
    length_months INT NOT NULL,

    cost DECIMAL(10,2) NOT NULL,
    deductible DECIMAL(10,2) NOT NULL,
    items_covered TEXT,

    total_cost DECIMAL(10,2) NOT NULL,
    monthly_cost DECIMAL(10,2) DEFAULT 0.00,
    warranty_commission DECIMAL(10,2) DEFAULT 0.00,
    

    CONSTRAINT fk_warranty_sale
        FOREIGN KEY (sale_id) REFERENCES sale(sale_id)
        ON DELETE CASCADE,

    CONSTRAINT fk_warranty_vehicle
        FOREIGN KEY (vehicle_id) REFERENCES vehicle(vehicle_id)
        ON DELETE RESTRICT,

    CONSTRAINT fk_warranty_customer
        FOREIGN KEY (customer_id) REFERENCES customer(customer_id)
        ON DELETE RESTRICT,

    CONSTRAINT fk_warranty_employee
        FOREIGN KEY (employee_id) REFERENCES employee(employee_id)
        ON DELETE RESTRICT
);


