# Food Delivery Order System (PHP + MySQL)

A mini food-delivery backend system built to practice REST API design, relational database modeling, and SQL analytics.

## Features
- Browse restaurants with city/rating filters
- View restaurant menus
- Place orders (writes to multiple related tables)
- Add new customers
- REST API endpoints (JSON) for restaurants, order lookup, and order creation
- Analytics dashboard using window functions, subqueries, and joins

## Tech Stack
- PHP (mysqli, prepared statements)
- MySQL
- Bootstrap (styling)

## Database Schema
- `restaurants` — id, name, city, rating
- `menu_items` — id, restaurant_id (FK), name, category, price
- `customers` — id, name, city
- `orders` — id, customer_id (FK), restaurant_id (FK), order_date, status
- `order_items` — id, order_id (FK), item_id (FK), quantity, price_at_order

## API Endpoints
- `GET /api/restaurants.php?city=Mumbai` — list restaurants, optional city filter
- `GET /api/order_status.php?id=1` — get full order details with items
- `POST /api/orders.php` — create a new order
```json
  {
    "customer_id": 1,
    "restaurant_id": 1,
    "items": [{ "item_id": 1, "quantity": 2 }]
  }
```

## Sample Analytics Queries
- Best-selling item per restaurant (using `RANK() OVER (PARTITION BY...)`)
- Average order value by city
- Peak ordering hours
- Restaurant revenue ranking

## Setup
1. Import `seed_data.sql` into a MySQL database named `food_delivery`
2. Update `db.php` with your MySQL credentials
3. Run on any PHP/Apache server (tested on XAMPP)