CREATE TABLE inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product VARCHAR(128) NOT NULL,
    product_type VARCHAR(64) AFTER product,
    items_in_stock INT NOT NULL DEFAULT 0,
    items_requested INT NOT NULL DEFAULT 0,
    picture VARCHAR(255),
    price DECIMAL(10,2) NOT NULL,
    diameter VARCHAR(32),
    `length` VARCHAR(32),
    `description` TEXT NOT NULL,
    `3d_image` VARCHAR(255),
    `tech_drawing` VARCHAR(255)
);
