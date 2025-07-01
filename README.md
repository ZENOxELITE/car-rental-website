# Elite Car Rentals - Car Rental Website

A complete car rental website with car catalog, booking system, MySQL database integration, and automated email confirmations.

## Features

- **Responsive Design**: Modern, mobile-friendly interface
- **Car Catalog**: Display cars with filtering by category (Luxury, SUV, Sedan, Sports)
- **Car Details**: Detailed view with features and specifications
- **Booking System**: Complete booking form with validation
- **Database Integration**: MySQL database for cars and bookings
- **Email Notifications**: Automated confirmation emails to customers
- **Admin Features**: Booking management and car availability checking

## Technology Stack

- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Email**: PHP Mail function (configurable for SMTP)
- **Styling**: Custom CSS with responsive design

## Installation & Setup

### Prerequisites

- Web server (Apache/Nginx)
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Mail server configuration (optional for email functionality)

### Step 1: Database Setup

1. Create a MySQL database named `car_rental`
2. Import the database structure and sample data:
   ```sql
   mysql -u your_username -p car_rental < database_setup.sql
   ```

### Step 2: Configuration

1. Edit `includes/config.php` and update the database credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   define('DB_NAME', 'car_rental');
   ```

2. Configure email settings (optional):
   ```php
   define('SMTP_HOST', 'your_smtp_host');
   define('SMTP_USER', 'your_email@domain.com');
   define('SMTP_PASS', 'your_email_password');
   ```

### Step 3: Web Server Setup

1. Copy all files to your web server directory
2. Ensure PHP has write permissions for error logging
3. Configure your web server to serve the `index.html` as the default page

### Step 4: Testing

1. Open your browser and navigate to your website
2. Test car browsing and filtering
3. Test the booking form with sample data
4. Check database for booking entries
5. Verify email functionality (if configured)

## File Structure

```
car-rental/
├── css/
│   └── style.css              # Main stylesheet
├── js/
│   └── script.js              # Frontend JavaScript
├── includes/
│   ├── config.php             # Database and email configuration
│   └── functions.php          # PHP utility functions
├── index.html                 # Main homepage
├── cars.php                   # Car data API endpoint
├── process_booking.php        # Booking form handler
├── database_setup.sql         # Database schema and sample data
└── README.md                  # This file
```

## Database Schema

### Cars Table
- `id` (Primary Key)
- `name` (Car name)
- `category` (luxury, sedan, suv, sports)
- `price_per_day` (Daily rental price)
- `image` (Car image URL)
- `description` (Car description)
- `features` (Comma-separated features)
- `created_at` (Timestamp)

### Bookings Table
- `id` (Primary Key)
- `car_id` (Foreign Key to cars)
- `customer_name` (Customer full name)
- `email` (Customer email)
- `phone` (Customer phone)
- `start_date` (Rental start date)
- `end_date` (Rental end date)
- `message` (Additional customer message)
- `status` (pending, confirmed, cancelled)
- `created_at` (Timestamp)

## API Endpoints

### GET /cars.php
Returns all available cars in JSON format.

**Response:**
```json
{
  "success": true,
  "message": "Cars retrieved successfully",
  "data": [...]
}
```

### POST /process_booking.php
Processes a new booking request.

**Request Body:**
```json
{
  "car_id": 1,
  "customer_name": "John Doe",
  "email": "john@example.com",
  "phone": "+1234567890",
  "start_date": "2024-01-15",
  "end_date": "2024-01-20",
  "message": "Optional message"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Booking created successfully",
  "data": {
    "booking_id": 123,
    "total_cost": 445,
    "days": 5
  }
}
```

## Email Template

The system sends a professional HTML email confirmation that includes:
- Booking confirmation details
- Car information
- Rental period and total cost
- Next steps and contact information
- Company branding

## Customization

### Adding New Cars
1. Insert new records into the `cars` table
2. Ensure proper image URLs and feature lists
3. Cars will automatically appear on the website

### Modifying Email Template
Edit the `generateEmailTemplate()` function in `includes/functions.php`

### Styling Changes
Modify `css/style.css` for design customizations

### Adding New Features
- Extend the database schema as needed
- Add new PHP functions in `includes/functions.php`
- Update frontend JavaScript in `js/script.js`

## Security Features

- Input sanitization and validation
- SQL injection prevention with prepared statements
- XSS protection with HTML escaping
- Email validation
- Date validation
- Car availability checking

## Troubleshooting

### Common Issues

1. **Database Connection Failed**
   - Check database credentials in `config.php`
   - Ensure MySQL service is running
   - Verify database exists

2. **Email Not Sending**
   - Check email configuration in `config.php`
   - Verify mail server settings
   - Check server mail logs

3. **Cars Not Loading**
   - Check browser console for JavaScript errors
   - Verify `cars.php` is accessible
   - Check database connection

4. **Booking Form Not Working**
   - Check form validation in browser console
   - Verify `process_booking.php` permissions
   - Check database table structure

## Support

For support and questions, please check:
1. Browser developer console for JavaScript errors
2. Server error logs for PHP errors
3. Database logs for connection issues

## License

This project is open source and available under the MIT License.
