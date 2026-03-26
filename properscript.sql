CREATE DATABASE carsales_app;
USE carsales_app;

-- =====================================
-- 1. EMPLOYEES / PEOPLE
-- =====================================

CREATE TABLE Buyer (
    buyer_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone VARCHAR(20)
);

CREATE TABLE Salesperson (
    salesperson_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone VARCHAR(20)
);

CREATE TABLE Seller (
    seller_id INT AUTO_INCREMENT PRIMARY KEY,
    seller_name VARCHAR(100) NOT NULL,
    seller_type ENUM('Dealer', 'Private Seller', 'Auction House') NOT NULL,
    phone VARCHAR(20),
    location VARCHAR(100)
);

CREATE TABLE Customer (
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
    late_payment_count INT DEFAULT 0,
    avg_days_late DECIMAL(6,2) DEFAULT 0.00,
    reported_to_credit_bureau BOOLEAN DEFAULT FALSE
);

CREATE TABLE EmploymentHistory (
    employment_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    employer_name VARCHAR(100) NOT NULL,
    job_title VARCHAR(100),
    supervisor_name VARCHAR(100),
    supervisor_phone VARCHAR(20),
    employer_address VARCHAR(150),
    start_date DATE,
    end_date DATE,
    is_current BOOLEAN DEFAULT FALSE,

    CONSTRAINT fk_employment_customer
        FOREIGN KEY (customer_id) REFERENCES Customer(customer_id)
        ON DELETE CASCADE
);

-- =====================================
-- 2. VEHICLE
-- =====================================

CREATE TABLE Vehicle (
    vehicle_id INT AUTO_INCREMENT PRIMARY KEY,
    vin VARCHAR(30) NOT NULL UNIQUE,
    make VARCHAR(50) NOT NULL,
    model VARCHAR(50) NOT NULL,
    year INT NOT NULL,
    color VARCHAR(30),
    miles INT,
    vehicle_condition VARCHAR(50),
    book_price DECIMAL(10,2),
    style VARCHAR(30),
    interior_color VARCHAR(30),
    current_status ENUM('In Inventory', 'Sold', 'In Repair', 'Wholesale', 'Repossessed') DEFAULT 'In Inventory'
);

-- =====================================
-- 3. PURCHASE SIDE
-- =====================================

CREATE TABLE Purchase (
    purchase_id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_id INT NOT NULL,
    buyer_id INT NOT NULL,
    seller_id INT NOT NULL,
    purchase_date DATE NOT NULL,
    location VARCHAR(100),
    is_auction BOOLEAN NOT NULL DEFAULT FALSE,
    price_paid DECIMAL(10,2) NOT NULL,

    CONSTRAINT fk_purchase_vehicle
        FOREIGN KEY (vehicle_id) REFERENCES Vehicle(vehicle_id),

    CONSTRAINT fk_purchase_buyer
        FOREIGN KEY (buyer_id) REFERENCES Buyer(buyer_id),

    CONSTRAINT fk_purchase_seller
        FOREIGN KEY (seller_id) REFERENCES Seller(seller_id)
);

CREATE TABLE Repair (
    repair_id INT AUTO_INCREMENT PRIMARY KEY,
    purchase_id INT NOT NULL,
    problem_no INT NOT NULL,
    problem_description VARCHAR(255) NOT NULL,
    est_repair_cost DECIMAL(10,2),
    actual_cost DECIMAL(10,2),

    CONSTRAINT fk_repair_purchase
        FOREIGN KEY (purchase_id) REFERENCES Purchase(purchase_id)
        ON DELETE CASCADE,

    CONSTRAINT uq_purchase_problemno
        UNIQUE (purchase_id, problem_no)
);

-- =====================================
-- 4. SALES SIDE
-- =====================================

CREATE TABLE Sale (
    sale_id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_id INT NOT NULL,
    customer_id INT NOT NULL,
    salesperson_id INT NOT NULL,
    sale_date DATE NOT NULL,
    total_due DECIMAL(10,2) NOT NULL,
    down_payment DECIMAL(10,2) DEFAULT 0.00,
    financed_amount DECIMAL(10,2) DEFAULT 0.00,
    sale_price DECIMAL(10,2) NOT NULL,
    salesperson_commission DECIMAL(10,2),

    CONSTRAINT fk_sale_vehicle
        FOREIGN KEY (vehicle_id) REFERENCES Vehicle(vehicle_id),

    CONSTRAINT fk_sale_customer
        FOREIGN KEY (customer_id) REFERENCES Customer(customer_id),

    CONSTRAINT fk_sale_salesperson
        FOREIGN KEY (salesperson_id) REFERENCES Salesperson(salesperson_id)
);

-- =====================================
-- 5. WARRANTY SIDE
-- =====================================

CREATE TABLE WarrantyPolicy (
    policy_id INT AUTO_INCREMENT PRIMARY KEY,
    policy_name VARCHAR(100) NOT NULL,
    description VARCHAR(255),
    component_type VARCHAR(100) NOT NULL,
    length_months INT NOT NULL,
    deductible DECIMAL(10,2) NOT NULL,
    base_cost DECIMAL(10,2) NOT NULL,
    monthly_interest_rate DECIMAL(5,4) DEFAULT 0.0100
);

CREATE TABLE Warranty (
    warranty_id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    vehicle_id INT NOT NULL,
    customer_id INT NOT NULL,
    salesperson_id INT NOT NULL,
    policy_id INT,
    warranty_sale_date DATE NOT NULL,
    warranty_start_date DATE NOT NULL,
    length_months INT NOT NULL,
    cost DECIMAL(10,2) NOT NULL,
    deductible DECIMAL(10,2) NOT NULL,
    total_cost DECIMAL(10,2) NOT NULL,
    monthly_cost DECIMAL(10,2),
    financed BOOLEAN NOT NULL DEFAULT FALSE,
    warranty_commission DECIMAL(10,2),

    CONSTRAINT fk_warranty_sale
        FOREIGN KEY (sale_id) REFERENCES Sale(sale_id)
        ON DELETE CASCADE,

    CONSTRAINT fk_warranty_vehicle
        FOREIGN KEY (vehicle_id) REFERENCES Vehicle(vehicle_id),

    CONSTRAINT fk_warranty_customer
        FOREIGN KEY (customer_id) REFERENCES Customer(customer_id),

    CONSTRAINT fk_warranty_salesperson
        FOREIGN KEY (salesperson_id) REFERENCES Salesperson(salesperson_id),

    CONSTRAINT fk_warranty_policy
        FOREIGN KEY (policy_id) REFERENCES WarrantyPolicy(policy_id)
);

CREATE TABLE WarrantyCoverageItem (
    coverage_item_id INT AUTO_INCREMENT PRIMARY KEY,
    warranty_id INT NOT NULL,
    item_name VARCHAR(100) NOT NULL,

    CONSTRAINT fk_coverage_warranty
        FOREIGN KEY (warranty_id) REFERENCES Warranty(warranty_id)
        ON DELETE CASCADE
);

-- =====================================
-- 6. PAYMENTS
-- =====================================

CREATE TABLE Payment (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    customer_id INT NOT NULL,
    payment_date DATE NOT NULL,
    due_date DATE NOT NULL,
    paid_date DATE,
    amount DECIMAL(10,2) NOT NULL,
    bank_account VARCHAR(50),
    is_late BOOLEAN DEFAULT FALSE,
    days_late INT DEFAULT 0,

    CONSTRAINT fk_payment_sale
        FOREIGN KEY (sale_id) REFERENCES Sale(sale_id)
        ON DELETE CASCADE,

    CONSTRAINT fk_payment_customer
        FOREIGN KEY (customer_id) REFERENCES Customer(customer_id)
);

CREATE TABLE WarrantyPayment (
    warranty_payment_id INT AUTO_INCREMENT PRIMARY KEY,
    warranty_id INT NOT NULL,
    customer_id INT NOT NULL,
    payment_date DATE NOT NULL,
    due_date DATE NOT NULL,
    paid_date DATE,
    amount DECIMAL(10,2) NOT NULL,
    bank_account VARCHAR(50),
    is_late BOOLEAN DEFAULT FALSE,
    days_late INT DEFAULT 0,

    CONSTRAINT fk_warranty_payment_warranty
        FOREIGN KEY (warranty_id) REFERENCES Warranty(warranty_id)
        ON DELETE CASCADE,

    CONSTRAINT fk_warranty_payment_customer
        FOREIGN KEY (customer_id) REFERENCES Customer(customer_id)
);

-- =====================================
-- 7. OPTIONAL COMMISSION HISTORY
-- =====================================

CREATE TABLE SalesCommission (
    commission_id INT AUTO_INCREMENT PRIMARY KEY,
    salesperson_id INT NOT NULL,
    sale_id INT NULL,
    warranty_id INT NULL,
    commission_type ENUM('Vehicle Sale', 'Warranty Sale') NOT NULL,
    commission_rate DECIMAL(5,2) NOT NULL,
    commission_amount DECIMAL(10,2) NOT NULL,

    CONSTRAINT fk_commission_salesperson
        FOREIGN KEY (salesperson_id) REFERENCES Salesperson(salesperson_id),

    CONSTRAINT fk_commission_sale
        FOREIGN KEY (sale_id) REFERENCES Sale(sale_id)
        ON DELETE SET NULL,

    CONSTRAINT fk_commission_warranty
        FOREIGN KEY (warranty_id) REFERENCES Warranty(warranty_id)
        ON DELETE SET NULL
);