create database investment;
use investment;

CREATE TABLE users (
    investor_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    address VARCHAR(255),
    email VARCHAR(100) UNIQUE NOT NULL,
    contact_number BIGINT NOT NULL
);

ALTER TABLE USERS AUTO_INCREMENT = 1000;

CREATE TABLE investments (
    investment_id INT PRIMARY KEY AUTO_INCREMENT,
    investor_id INT NOT NULL,
    asset_type VARCHAR(50) NOT NULL,
    amount DECIMAL(15, 2) NOT NULL CHECK (amount > 0),
    purchase_date DATE NOT NULL,
    FOREIGN KEY (investor_id) REFERENCES users(investor_id)
);

ALTER TABLE investments
ADD CONSTRAINT chk_asset_type
CHECK (asset_type IN ('stocks', 'gold', 'real_estate', 'mutual_funds', 'fixed_deposits', 'bonds'));


ALTER TABLE investments AUTO_INCREMENT = 1000;

CREATE TABLE transactions (
    transaction_id INT PRIMARY KEY AUTO_INCREMENT,
    investment_id INT NOT NULL,
    transaction_date DATE NOT NULL,
    transaction_type VARCHAR(10) CHECK (transaction_type IN ('buy', 'sell')),
    amount DECIMAL(15, 2) NOT NULL,
    FOREIGN KEY (investment_id) REFERENCES investments(investment_id)
);

ALTER TABLE transactions AUTO_INCREMENT = 10000;


CREATE TABLE portfolio_performance (
    performance_id INT PRIMARY KEY AUTO_INCREMENT,
    investor_id INT NOT NULL,
    investment_id INT NOT NULL,
    record_date DATE NOT NULL,
    purchase_value DECIMAL(15, 2) NOT NULL,
    current_value DECIMAL(15, 2) NOT NULL,
    net_gain_or_loss DECIMAL(15, 2) default 0.00,
    FOREIGN KEY (investor_id) REFERENCES users(investor_id),
    FOREIGN KEY (investment_id) REFERENCES investments(investment_id)
);

CREATE TABLE payment_history (
    payment_id INT PRIMARY KEY AUTO_INCREMENT,
    transaction_id INT NOT NULL,
    payment_date DATE NOT NULL,
    amount DECIMAL(15, 2) NOT NULL CHECK (amount > 0),
    payment_method VARCHAR(50) NOT NULL DEFAULT 'cash' CHECK (payment_method IN ('cash', 'upi', 'creditcard', 'debitcard', 'bank transfer')),
    FOREIGN KEY (transaction_id) REFERENCES transactions(transaction_id)
);
ALTER TABLE payment_history AUTO_INCREMENT = 100;
CREATE TABLE financial_advisors (
    advisor_id INT PRIMARY KEY AUTO_INCREMENT,
    advisor_name VARCHAR(100) NOT NULL,
    contact_number BIGINT NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL
);

CREATE TABLE user_advisor (
    advisor_id INT NOT NULL,
    investor_id INT NOT NULL,
    PRIMARY KEY (advisor_id, investor_id),
    FOREIGN KEY (advisor_id) REFERENCES financial_advisors(advisor_id),
    FOREIGN KEY (investor_id) REFERENCES users(investor_id)
);


DELIMITER //

CREATE TRIGGER check_sell_date
BEFORE INSERT ON transactions
FOR EACH ROW
BEGIN
    -- Check if the transaction type is 'sell'
    IF NEW.transaction_type = 'sell' THEN
        -- Compare the transaction date with the purchase date
        IF NEW.transaction_date < (
            SELECT purchase_date
            FROM investments
            WHERE investment_id = NEW.investment_id
        ) THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Sell transaction date cannot be earlier than the purchase date.';
        END IF;
    END IF;
END //

DELIMITER ;
 

 DELIMITER //

CREATE FUNCTION calculate_user_profit_loss (user_id INT)
RETURNS DECIMAL(15, 2)
DETERMINISTIC
BEGIN
    DECLARE total_purchase_value DECIMAL(15, 2) DEFAULT 0;
    DECLARE total_current_value DECIMAL(15, 2) DEFAULT 0;
    DECLARE net_profit_loss DECIMAL(15, 2);

    -- Calculate total purchase value (sum of all investments)
    SELECT SUM(amount) INTO total_purchase_value
    FROM investments
    WHERE investor_id = user_id;

    -- Calculate total current value (sum of current values of all investments)
    SELECT SUM(current_value) INTO total_current_value
    FROM portfolio_performance
    WHERE investor_id = user_id;

    -- Calculate net profit or loss
    SET net_profit_loss = total_current_value - total_purchase_value;

    -- Return net profit or loss
    RETURN net_profit_loss;
END //

DELIMITER ;

DELIMITER //

CREATE TRIGGER prevent_duplicate_user
BEFORE INSERT ON users
FOR EACH ROW
BEGIN
    -- Check if the email already exists
    IF EXISTS (SELECT 1 FROM users WHERE email = NEW.email) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'A user with this email already exists.';
    END IF;

    -- Check if the contact number already exists
    IF EXISTS (SELECT 1 FROM users WHERE contact_number = NEW.contact_number) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'A user with this contact number already exists.';
    END IF;
END //

DELIMITER ;

DELIMITER //

CREATE TRIGGER prevent_duplicate_advisor
BEFORE INSERT ON financial_advisors
FOR EACH ROW
BEGIN
    -- Check if the email already exists
    IF EXISTS (SELECT 1 FROM financial_advisors WHERE email = NEW.email) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'An advisor with this email already exists.';
    END IF;

    -- Check if the contact number already exists
    IF EXISTS (SELECT 1 FROM financial_advisors WHERE contact_number = NEW.contact_number) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'An advisor with this contact number already exists.';
    END IF;
END //

DELIMITER ;

DELIMITER //

CREATE PROCEDURE add_investment(
    IN user_id INT,
    IN asset_type VARCHAR(50),
    IN amount DECIMAL(15, 2),
    IN purchase_date DATE
)
BEGIN
    DECLARE new_investment_id INT;
    DECLARE total_purchase_value DECIMAL(15, 2);
    DECLARE total_current_value DECIMAL(15, 2);
    DECLARE net_profit_loss DECIMAL(15, 2);
    
    -- Step 1: Insert the new investment into the investments table
    INSERT INTO investments (investor_id, asset_type, amount, purchase_date)
    VALUES (user_id, asset_type, amount, purchase_date);

    -- Get the ID of the newly inserted investment
    SET new_investment_id = LAST_INSERT_ID();

    -- Step 2: Check if portfolio_performance already has a record for this user
    -- If no record exists, create one; otherwise, update the current value
    SELECT SUM(amount) INTO total_purchase_value
    FROM investments
    WHERE investor_id = user_id;

    SELECT SUM(current_value) INTO total_current_value
    FROM portfolio_performance
    WHERE investor_id = user_id;

    -- Step 3: Calculate net profit/loss
    SET net_profit_loss = total_current_value - total_purchase_value;

    -- Insert or update the portfolio performance for the user
    INSERT INTO portfolio_performance (investor_id, investment_id, record_date, purchase_value, current_value)
    VALUES (user_id, new_investment_id, purchase_date, amount, amount)
    ON DUPLICATE KEY UPDATE current_value = total_current_value;

    -- Step 4: Calculate net profit/loss using the custom function
    SET net_profit_loss = calculate_user_profit_loss(user_id);

    -- Output the net profit or loss
    SELECT net_profit_loss AS net_profit_loss;

END //

DELIMITER ;


DELIMITER //

CREATE FUNCTION calculate_total_investment_value(user_id INT)
RETURNS DECIMAL(15, 2)
DETERMINISTIC
BEGIN
    DECLARE total_investment_value DECIMAL(15, 2) DEFAULT 0;

    -- Step 1: Calculate the total value of all investments (current_value from portfolio_performance)
    SELECT SUM(current_value) INTO total_investment_value
    FROM portfolio_performance
    WHERE investor_id = user_id;

    -- Step 2: Return the total investment value
    RETURN total_investment_value;
END //

DELIMITER ;

DELIMITER //

CREATE TRIGGER after_investment_insert
AFTER INSERT ON investments
FOR EACH ROW
BEGIN
    -- Insert a corresponding "buy" transaction into the transactions table
    INSERT INTO transactions (investment_id, transaction_date, transaction_type, amount)
    VALUES (NEW.investment_id, NEW.purchase_date, 'buy', NEW.amount);
END //

DELIMITER ;
DELIMITER //

CREATE TRIGGER after_sell_transaction
AFTER INSERT ON transactions
FOR EACH ROW
BEGIN
    -- Check if the transaction type is 'sell'
    IF NEW.transaction_type = 'sell' THEN
        -- Update the current value and calculate the profit or loss in portfolio_performance
        UPDATE portfolio_performance
        SET 
            current_value = current_value - NEW.amount, -- Subtract the sell amount from the current value
            net_gain_or_loss = (current_value - purchase_value + NEW.amount ) -- Recalculate profit or loss
        WHERE investment_id = NEW.investment_id;
    END IF;
END //

DELIMITER ;

DELIMITER //

CREATE TRIGGER after_transaction_insert
AFTER INSERT ON transactions
FOR EACH ROW
BEGIN
    -- Insert a placeholder record into payment_history
    INSERT INTO payment_history (transaction_id, payment_date, amount)
    VALUES (NEW.transaction_id, NEW.transaction_date, NEW.amount);
END;
//

DELIMITER ;
