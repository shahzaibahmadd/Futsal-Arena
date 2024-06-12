CREATE DATABASE futsal_db;
USE futsal_db;

CREATE TABLE users(
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  mobile INT NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);





CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,   
    location VARCHAR(255) NOT NULL,
    date DATE NOT NULL,
    time_slot VARCHAR(11) NOT NULL,
    field_number INT NOT NULL,  -- Added field number column
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


CREATE TABLE ContactSubmissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- Ensure passwords are hashed in a real application
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);



INSERT INTO admin_users (username, email, password) VALUES ('admin', 'admin@nasar.com', '$2y$10$gG/e4EZRglrz0S7ntgM1KOHqoQePYbXvBW3y5GNa3vZAosoIdmqZy');

CREATE TABLE coaches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    mobile INT(20) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    experience INT NOT NULL,
    specialties TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE coaching_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    coach_id INT NOT NULL,
    location VARCHAR(255) NOT NULL,
    date DATE NOT NULL,
    time_slot VARCHAR(11) NOT NULL,
    max_trainees INT NOT NULL,
    field_no INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (coach_id) REFERENCES coaches(id) ON DELETE CASCADE
);



CREATE TABLE trainees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    mobile VARCHAR(20) NOT NULL,
    email VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trainee_id INT NOT NULL,
    coaching_session_id INT NOT NULL,
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (trainee_id) REFERENCES trainees(id) ON DELETE CASCADE,
    FOREIGN KEY (coaching_session_id) REFERENCES coaching_sessions(id) ON DELETE CASCADE
);



CREATE TABLE Packages (
    package_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    duration INT NOT NULL, -- Duration in days, hours, etc. Adjust data type as needed
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE Venue (
    venue_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    location VARCHAR(255) NOT NULL,
    description TEXT,
    num_fields INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);





CREATE TABLE Reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    venue_id INT, -- Assuming venues have separate IDs from coaching sessions
    coaching_session_id INT,
    rating DECIMAL(3, 1) NOT NULL, -- Assuming rating out of 5. Adjust precision as needed
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    -- FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    -- FOREIGN KEY (venue_id) REFERENCES venue(venue_id) ON DELETE CASCADE,
    -- FOREIGN KEY (coaching_session_id) REFERENCES coaching_sessions(id) ON DELETE CASCADE
);




CREATE TABLE Transactions (
    transaction_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    booking_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payment_method VARCHAR(100) NOT NULL,
    status VARCHAR(50) NOT NULL, -- Status can be 'pending', 'completed', etc.
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
);




CREATE TABLE Wallet (
    wallet_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    balance DECIMAL(10, 2) DEFAULT 0.00, -- Initial balance set to 0.00
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);







CREATE TABLE admin_wallet (
    wallet_id INT AUTO_INCREMENT PRIMARY KEY,
    admin_user_id INT NOT NULL,
    balance DECIMAL(10, 2) DEFAULT 0.00, -- Initial balance set to 0.00
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_user_id) REFERENCES admin_users(id) ON DELETE CASCADE
);



INSERT INTO admin_wallet (admin_user_id, balance)
VALUES (
    (SELECT id FROM admin_users WHERE email = 'admin@nasar.com'),
    1000.00 -- Set the initial balance here
);

-- UPDATE admin_wallet
-- SET balance = balance + 10.00  -- Assuming you're adding $10 to the admin's wallet
-- WHERE admin_user_id = 5;  -- Assuming admin's user_id is 5
