# OrderEase: Simple PHP & MySQL Tracking System
[![GitHub license](https://img.shields.io/github/license/khdxsohee/OrderEase?style=for-the-badge)](https://github.com/khdxsohee/OrderEase/blob/main/LICENSE)
[![GitHub stars](https://img.shields.io/github/stars/khdxsohee/OrderEase?style=for-the-badge)](https://github.com/khdxsohee/OrderEase/stargazers)
[![GitHub forks](https://img.shields.io/github/forks/khdxsohee/OrderEase?style=for-the-badge)](https://github.com/khdxsohee/OrderEase/network/members)
[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-blue?style=for-the-badge&logo=php)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-orange?style=for-the-badge&logo=mysql)](https://www.mysql.com/)
[![Built with HTML/CSS/JS](https://img.shields.io/badge/Built%20with-HTML%2FCSS%2FJS-red?style=for-the-badge&logo=html5)](https://developer.mozilla.org/en-US/docs/Web/HTML)
[![Maintenance](https://img.shields.io/badge/Maintained%3F-yes-brightgreen.svg?style=for-the-badge)](https://github.com/khdxsohee/OrderEase/graphs/commit-activity)
[![GitHub last commit](https://img.shields.io/github/last-commit/khdxsohee/OrderEase?style=for-the-badge)](https://github.com/khdxsohee/OrderEase/commits/main)
[![Project Status](https://img.shields.io/badge/Status-Active-green?style=for-the-badge)](https://github.com/khdxsohee/OrderEase)
-----

## Table of Contents

  * 🌟 Introduction
  * ✨ Features
  * 🚀 Getting Started
      * Prerequisites
      * Installation Steps
          * 1. Clone the Repository
          * 2. Database Setup
          * 3. Configure Database Connection
          * 4. Place Project Files
  * 💡 Usage
      * Admin Panel
      * Customer Tracking Interface
  * 📂 Project Structure
  * 🛠️ Customization
  * 🔒 Security Considerations
  * 🛣️ Future Enhancements
  * 🐛 Troubleshooting
  * 🤝 Contributing
  * 📜 License
  * 📞 Contact

-----

## 🌟 Introduction

Welcome to **OrderEase**, a straightforward yet effective web-based tracking management system built with PHP and MySQL. This application is designed to streamline the process of managing and tracking product deliveries or service statuses. It offers a clean administrative interface for businesses to add and update tracking information, alongside a user-friendly public portal where customers can easily track their orders using a unique ID.

**OrderEase** prioritizes simplicity, ease of use, and a professional aesthetic, making it an ideal solution for small to medium-sized businesses looking for a quick and efficient tracking solution.

-----

## ✨ Features

**OrderEase** comes packed with essential features to get your tracking system up and running:

  * **Unique Tracking IDs:** Effortlessly create and assign custom, unique tracking IDs (e.g., `ORD123`, `SHIP789`) to each order or service.
  * **Comprehensive Order Details:** Store all vital information for every tracking entry:
      * **Customer Information:** Full name and complete address.
      * **Product Details:** Track multiple products per order, including their respective quantities.
      * **Financials:** Record the total price of the order.
      * **Timestamps:** Log the order date and automatically update a "last updated" timestamp on any modification.
  * **Intuitive Status Management:**
      * **Payment Status:** Clearly define the payment state (`Pending`, `Paid`, `Refunded`).
      * **Delivery Status:** Monitor the entire delivery journey (`Pending`, `Processing`, `Shipped`, `Out for Delivery`, `Delivered`, `Cancelled`).
  * **Dedicated Admin Panel:** A secure (once authentication is implemented) interface for administrators to seamlessly add new tracking records and update existing ones with ease.
  * **User-Friendly Customer Interface:** A simple, public-facing page where customers can input their tracking ID to instantly view the real-time status of their order.
  * **Modern & Professional UI:** A clean, responsive, and visually appealing design crafted with HTML, CSS, and dynamic JavaScript, ensuring a smooth user experience.
  * **Visual Icons:** Integrated Font Awesome icons provide clear, instant visual cues for various statuses and data points, enhancing clarity and engagement.

-----

## 🚀 Getting Started

Follow these steps to get **OrderEase** up and running on your local machine or a web server.

### Prerequisites

Before you begin, ensure you have the following installed:

  * **Web Server:** Apache or Nginx
  * **PHP:** Version 7.4 or higher (with `mysqli` extension enabled)
  * **MySQL / MariaDB:** Database server (e.g., MySQL 8.0+)
  * **Composer (Optional but Recommended):** For dependency management, though not strictly required for this basic setup.
  * **A Local Development Environment:** (e.g., XAMPP, WAMP, MAMP) is highly recommended for local setup.

### Installation Steps

#### 1\. Clone the Repository

Start by cloning the **OrderEase** repository to your local machine:

```bash
git clone https://github.com/khdxsohee/OrderEase.git
```

#### 2\. Database Setup

1.  **Access Database:** Open your preferred MySQL client (e.g., phpMyAdmin, MySQL Workbench, or your command line).

2.  **Create Database:** Create a new database. We'll name it `tracking_db` for consistency.

    ```sql
    CREATE DATABASE IF NOT EXISTS `tracking_db`;
    USE `tracking_db`;
    ```

3.  **Create Table:** Execute the following SQL query to create the `tracking_items` table:

    ```sql
    CREATE TABLE `tracking_items` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `tracking_id` VARCHAR(20) UNIQUE NOT NULL,
        `customer_name` VARCHAR(255) NOT NULL,
        `customer_address` TEXT NOT NULL,
        `products` TEXT NOT NULL, -- Stores JSON string of products and quantities
        `price` DECIMAL(10, 2) NOT NULL,
        `order_date` DATE NOT NULL,
        `last_updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `payment_status` ENUM('Pending', 'Paid', 'Refunded') DEFAULT 'Pending',
        `delivery_status` ENUM('Pending', 'Processing', 'Shipped', 'Out for Delivery', 'Delivered', 'Cancelled') DEFAULT 'Pending'
    );
    ```

#### 3\. Configure Database Connection

1.  Navigate into your cloned project directory: `cd OrderEase`

2.  Open the `db_config.php` file using a text editor.

3.  Update the database credentials (`$servername`, `$username`, `$password`, `$dbname`) to match your MySQL setup.

    ```php
    <?php
    // db_config.php
    $servername = "localhost";
    $username = "root";     // Your MySQL username
    $password = "";         // Your MySQL password (empty if none for root in local setup)
    $dbname = "tracking_db"; // The database you created

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4"); // Ensures proper character encoding
    ?>
    ```

    **Security Warning:** For local development, using `root` with an empty password might be common. **However, for any production environment, it is CRITICAL to use strong, unique passwords and dedicated, restricted database users.**

#### 4\. Place Project Files

1.  Move the entire `OrderEase` project folder into your web server's document root.
      * **XAMPP:** `C:\xampp\htdocs\`
      * **WAMP:** `C:\wamp\www\`
      * **MAMP:** `/Applications/MAMP/htdocs/`
      * **Other Servers:** Consult your server's documentation.

-----

## 💡 Usage

### Admin Panel

The admin panel allows you to add and update tracking records. For a live deployment, **it is highly recommended to implement a robust authentication system** to secure these pages.

  * **Access Admin Dashboard (Conceptual):**

      * Once deployed, navigate to `http://localhost/OrderEase/admin_dashboard.php` (you would need to create this file based on the previous documentation's suggestion to list all items).

  * **Adding a New Tracking Item:**

    1.  Go to `http://localhost/OrderEase/admin_add.php`.
    2.  Fill in the order details, including product names and quantities (you can add multiple product rows dynamically).
    3.  Select the **Payment Status** and **Delivery Status**.
    4.  Click **"Add Item"**.

  * **Updating a Tracking Item:**

    1.  Typically, you'd navigate from an admin listing page (e.g., `admin_dashboard.php`) to the update page.
    2.  Access `http://localhost/OrderEase/admin_update.php?tracking_id=YOUR_TRACKING_ID` (replace `YOUR_TRACKING_ID` with an actual ID, e.g., `BBB236`).
    3.  The form will pre-fill with existing data, including product details.
    4.  Make your desired changes and click **"Update Item"**.

### Customer Tracking Interface

This is the public-facing portal for your customers.

  * **Track Your Order:**
    1.  Visit `http://localhost/OrderEase/track.php`.
    2.  Enter the unique **Tracking ID** provided to the customer.
    3.  Click **"Track Order"**.
    4.  The system will display a modern, clear view of the order's current details, including payment and delivery statuses with intuitive icons.

-----

## 📂 Project Structure

```
OrderEase/tracking-system
├── admin_add.php            # Admin page to add new tracking entries
├── admin_update.php         # Admin page to update existing tracking entries
├── admin_dashboard.php         # Admin page to update existing tracking entries
├── admin_delete.php         # Admin page to update existing tracking entries
├── db_config.php            # Database connection configuration
├── track.php                # Customer-facing page to input tracking ID
├── view_tracking.php        # Customer-facing page to display tracking details
└── style.css                # Global CSS styles for the application
├── readme.md         # Admin page to update existing tracking entries
```

-----

## 🛠️ Customization

You can easily customize **OrderEase** to fit your specific needs:

  * **Styling:** Modify `style.css` to change colors, fonts, layout, and overall visual appearance.
  * **Status Options:** Edit the `ENUM` values in the `tracking_items` table (via SQL) and update the `<select>` options in `admin_add.php` and `admin_update.php` to add or remove payment/delivery statuses.
  * **Icons:** Change or add new Font Awesome icons by referencing their library.
  * **Data Fields:** Add new columns to the `tracking_items` table and integrate them into the PHP forms and display pages.

-----

## 🔒 Security Considerations

While **OrderEase** provides core functionality, it's a basic setup. For **production environments**, consider these crucial security enhancements:

  * **Authentication:** Implement a proper admin login system for `admin_add.php`, `admin_update.php`, and `admin_dashboard.php`. Use secure password hashing (`password_hash`, `password_verify`).
  * **Prepared Statements:** Replace `mysqli_real_escape_string()` with **prepared statements** for all database queries to fully prevent SQL injection.
  * **Input Validation:** Implement robust server-side validation for all form inputs to ensure data integrity and prevent malicious submissions.
  * **Error Reporting:** Disable `display_errors` in `php.ini` on live servers to prevent sensitive information from being exposed in error messages. Use proper error logging instead.
  * **HTTPS:** Always use HTTPS to encrypt data in transit.

-----

## 🛣️ Future Enhancements

We envision several enhancements to make **OrderEase** even more powerful:

  * **Admin Dashboard:** A dedicated page to list, search, filter, and manage all tracking records.
  * **Delete Functionality:** Add the ability for admins to delete tracking entries.
  * **User Management:** Allow for multiple admin users with different roles.
  * **Tracking History/Timeline:** Display a detailed log of status changes for each order.
  * **Email/SMS Notifications:** Automated alerts for status updates to customers.
  * **API Endpoints:** Create simple RESTful API endpoints for integration with other systems.
  * **Advanced Reporting:** Generate reports based on delivery statuses, payment statuses, etc.
  * **Better UI/UX for Products:** Implement a more user-friendly way to add/edit products for many items, possibly with auto-suggestions.

-----

## 🐛 Troubleshooting

If you encounter any issues, please refer to these common solutions:

  * **"Connection failed" error:**
      * Verify your database credentials in `db_config.php`.
      * Ensure your MySQL server is running.
  * **PHP errors:**
      * Check your web server's error logs.
      * Ensure all PHP files are in the correct directory.
  * **JavaScript (Add/Remove Product) buttons not working:**
      * Open your browser's developer console (F12) and check the "Console" tab for JavaScript errors.
      * Ensure the JavaScript code is correctly placed within `<script>` tags at the end of `admin_add.php` and `admin_update.php`.
  * **Products not pre-filling in `admin_update.php`:**
      * Check the browser console for JS errors.
      * Verify the `products` column in your database contains valid JSON data.
  * **Icons not displaying:**
      * Confirm the Font Awesome CDN link is correctly included in the `<head>` section of `track.php` and `view_tracking.php`.
      * Check your internet connection if loading from CDN.

-----

## 🤝 Contributing

Contributions are welcome\! If you have suggestions for improvements, feature requests, or find bugs, please feel free to:

1.  Fork the repository.
2.  Create a new branch (`git checkout -b feature/your-feature-name` or `bugfix/your-bug-fix`).
3.  Make your changes.
4.  Commit your changes (`git commit -m 'Add new feature'`).
5.  Push to the branch (`git push origin feature/your-feature-name`).
6.  Open a Pull Request.

-----

## 📜 License

This project is licensed under the [MIT License](https://opensource.org/licenses/MIT). See the `LICENSE` file for details.

-----

## 📞 Contact

For any questions or inquiries, please reach out to:

**GitHub:** [khdxsohee](https://github.com/khdxsohee)

-----
