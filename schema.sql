CREATE DATABASE IF NOT EXISTS food_delivery;
USE food_delivery;

CREATE TABLE restaurants (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  city VARCHAR(50),
  rating DECIMAL(2,1)
);

CREATE TABLE menu_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  restaurant_id INT,
  name VARCHAR(100),
  category VARCHAR(50),
  price DECIMAL(10,2),
  FOREIGN KEY (restaurant_id) REFERENCES restaurants(id)
);

CREATE TABLE customers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  city VARCHAR(50)
);

CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_id INT,
  restaurant_id INT,
  order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  status VARCHAR(20) DEFAULT 'placed',
  FOREIGN KEY (customer_id) REFERENCES customers(id),
  FOREIGN KEY (restaurant_id) REFERENCES restaurants(id)
);

CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT,
  item_id INT,
  quantity INT,
  price_at_order DECIMAL(10,2),
  FOREIGN KEY (order_id) REFERENCES orders(id),
  FOREIGN KEY (item_id) REFERENCES menu_items(id)
);