## Features

- **Customer Management**:

    - Add a new customer.
    - Retrieve customer details.
    - Retrieve a paginated list of customer orders.

- **Product Management**:

    - Add new products.
    - List products with pagination.

- **Order Management**:
    - Place orders and calculate total amounts.
    - Deduct stock with high-concurrency handling.
    - Asynchronous order processing using queues.

## Run Application with Docker Compose

1. **Ensure that Docker and Docker Compose are installed** on your system.
2. In the project directory, run the following command:

    ```bash

    git clone https://github.com/mhd21/nordic-order
    cd nordic-order

    docker-compose up
    ```

    Once the containers are running, the application will be accessible at:
    http://localhost:8000

## Run Application Manually

Prerequisites

- PHP 8.2
- Composer
- MySQL or another database service

### Steps to Set Up:

Set up a MySQL instance:
Ensure that you have a running MySQL instance available.

Clone the repository and navigate to the project directory:

```bash
git clone https://github.com/mhd21/nordic-order
cd nordic-order
```

Prepare the .env file:
Copy the example environment file to .env:

```bash
cp .env.example .env
```

Update the database settings in the .env file to match your MySQL instance:

```dotenv
DB_CONNECTION=mysql
DB_HOST=your-mysql-host
DB_PORT=3306
DB_DATABASE=your-database-name
DB_USERNAME=your-database-username
DB_PASSWORD=your-database-password
```

Install dependencies using Composer:

```bash
composer install
```

Generate the application key:

```bash
php artisan key:generate
```

Run database migrations and seeders:

```bash
php artisan migrate --seed
```

Start the queue worker:

```bash
php artisan queue:work
```

Start the Laravel development server:

```bash
php artisan serve
```

The application will be accessible at:
http://localhost:8000

## Running Tests

1. Configure `.env.testing` for a separate test database:

    - Example `.env.testing`:
        ```
        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=testing_database
        DB_USERNAME=root
        DB_PASSWORD=password
        ```

2. Run migrations for the test database:

    ```bash
    php artisan migrate --env=testing
    ```

3. Run the test suite:
    ```bash
    php artisan test
    ```

## Additional Notes

- Ensure that required PHP extensions such as pdo_mysql, mbstring, bcmath, etc., are installed.
- If you use Docker Compose, the .env file will be configured automatically.
- For a manual setup, verify that your .env file contains the correct database credentials and other necessary environment settings.

- If you change the test database configuration in `.env.testing`, ensure to run the `migrate` command for the test environment.
- This project demonstrates a queue-based approach to handle stock deduction and high concurrency. It uses the `database` driver for simplicity but can be extended to use `Redis`, `RabbitMQ`, or other queue drivers for real-world scalability.

## API Endpoints

- **BASE ENDPOINT** localhost:8000/api

### Products

- **POST /products**: Add a new product.
- **GET /products**: List all products with pagination.

### Customers

- **POST /customers**: Add a new customer.
- **GET /customers/{id}**: Retrieve a customer's details.
- **GET /customers/{id}/orders**: Retrieve a paginated list of a customer's orders.

### Orders

- **POST /orders**: Place an order.
    - Input: Customer ID, an array of product IDs and quantities.
    - Output: Order details (order ID, total amount).
    - Logic: Save the order, deduct stock in a queue, and confirm the order.

## Notes on Concurrency and Stock Deduction

This project addresses concurrency and stock deduction using:

1. **Queues**:

    - Orders are saved immediately, but stock deduction and order confirmation are processed asynchronously using Laravel's queue system.
    - For this test project, the `database` queue driver is used. For production, consider using `Redis` or other message queues for better performance and scalability.

2. **Atomic Database Operations**:
    - Stock deductions use atomic operations to ensure no over-deduction occurs even under high concurrency.
