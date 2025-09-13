# Kasir Lite - Lightweight POS System

A fast, simple, and secure Point of Sale (POS) system built with Laravel 10 for UMKM (Usaha Mikro, Kecil, dan Menengah). Features multi-payment support, thermal receipt printing, cash session management, and real-time stock tracking.

## 🚀 Features

### Core POS Features
- **Product Management**: SKU, barcode, categories, stock tracking
- **Multi-Payment Support**: CASH, QRIS, EDC, TRANSFER, EWALLET
- **Tax & Discount**: Per-item and total discounts, configurable tax rates
- **Invoice Management**: Auto-incrementing invoice numbers per outlet/month
- **Stock Management**: Real-time stock updates and movement tracking

### Cash Management
- **Cash Sessions**: Open/close shifts with variance tracking
- **Payment Reconciliation**: Automatic expected vs actual cash calculation

### Receipt & Printing
- **Thermal Receipt**: Optimized for 58mm/80mm thermal printers
- **Browser Printing**: Print directly from web browser (Ctrl/Cmd+P)

### Security & Multi-tenancy
- **Laravel Sanctum**: Token-based API authentication
- **Role-based Access**: cashier, supervisor, owner roles
- **Outlet Isolation**: Multi-outlet support with data separation

## 🛠 Tech Stack

- **Backend**: PHP 8.2, Laravel 10
- **Database**: MySQL 8
- **Authentication**: Laravel Sanctum
- **Frontend**: Blade templates with Alpine.js
- **Timezone**: Asia/Jakarta
- **Docker**: Development and production ready

## 📋 Requirements

- PHP 8.2+
- Composer
- MySQL 8.0+
- Node.js (for frontend assets)

## 🚀 Quick Start

### Local Development Setup

1. **Clone the repository**
```bash
git clone https://github.com/your-repo/kasir-lite.git
cd kasir-lite
```

2. **Install dependencies**
```bash
composer install
npm install && npm run build
```

3. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Database configuration**
Edit `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kasir_lite
DB_USERNAME=root
DB_PASSWORD=
```

5. **Run migrations and seed data**
```bash
php artisan migrate --seed
```

6. **Start the application**
```bash
php artisan serve
```

Your application will be available at `http://localhost:8000`

### Docker Setup

1. **Build and start containers**
```bash
docker-compose up -d --build
```

2. **Setup application inside container**
```bash
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate --seed
```

3. **Access the application**
- **Main App**: http://localhost
- **phpMyAdmin**: http://localhost:8080

## 🔐 Authentication & API Access

### Demo Credentials

After running the seeder, you can use these demo accounts:

```
Owner Account:
Email: owner@demo.local
Password: password
Role: owner

Cashier Account:
Email: cashier@demo.local
Password: password
Role: cashier
```

### Getting API Token

For API access, you'll need to implement login endpoints or use Laravel Sanctum's token creation. Here's a quick way using tinker:

```bash
php artisan tinker
```

```php
$user = App\Models\User::where('email', 'cashier@demo.local')->first();
$token = $user->createToken('POS Token')->plainTextToken;
echo $token;
```

Save this token for API requests.

## 📊 API Documentation

### Base URL
```
Local: http://localhost:8000/api
Docker: http://localhost/api
```

### Authentication
Include the bearer token in all API requests:
```
Authorization: Bearer your-token-here
```

### Key Endpoints

#### Products
```http
GET /api/products                    # Get paginated products
GET /api/products?search=teh         # Search by name/SKU
GET /api/products?barcode=123456     # Find by barcode
```

#### Sales
```http
POST /api/sales                      # Create new sale
```

Sample request:
```json
{
  "outlet_id": 1,
  "sold_at": "2025-09-13T10:15:00",
  "items": [
    {
      "product_id": 1,
      "price": 15000,
      "qty": 2,
      "discount": 0
    },
    {
      "product_id": 2,
      "price": 8000,
      "qty": 1,
      "discount": 1000
    }
  ],
  "discount_total": 1000,
  "tax_percent": 10,
  "rounding": 0,
  "payments": [
    {
      "method": "CASH",
      "amount": 30000
    },
    {
      "method": "QRIS",
      "amount": 2000
    }
  ],
  "note": "Customer note"
}
```

#### Cash Sessions
```http
POST /api/cash-sessions/open         # Open new session
POST /api/cash-sessions/close        # Close active session
GET  /api/cash-sessions/active       # Get active session
```

#### Receipts
```http
GET /receipt/{sale_id}               # View printable receipt
```

## 🧪 Testing

Run the test suite:
```bash
php artisan test
```

Run specific test:
```bash
php artisan test --filter SaleServiceTest
```

## 📄 Receipt Printing

### Browser Printing
1. Navigate to `/receipt/{sale_id}`
2. Press `Ctrl+P` (Windows/Linux) or `Cmd+P` (Mac)
3. Select your thermal printer
4. Set paper size to 58mm or 80mm
5. Print

### Thermal Printer Setup
The receipt view is optimized for thermal printers with:
- **58mm width** for compact printers
- **80mm width** for standard thermal printers
- **Automatic formatting** with dashed separators
- **Monospace font** for consistent alignment

## 💾 Database Schema

### Key Tables
- `outlets` - Store/branch information
- `users` - Staff with role-based access
- `products` - Inventory with stock tracking
- `sales` - Transaction records
- `sale_items` - Individual items in each sale
- `payments` - Payment method details
- `cash_sessions` - Shift management
- `stock_movements` - Inventory movement history
- `invoice_sequences` - Auto-incrementing invoice numbers

## 🔧 Configuration

### Timezone
The system is configured for Indonesia timezone (`Asia/Jakarta`). To change:

1. Update `config/app.php`:
```php
'timezone' => 'Asia/Jakarta',
```

2. Update database seeds and any hardcoded times as needed.

### Invoice Format
Invoice numbers follow the format: `{OUTLET_CODE}/{YYYYMM}/{4-digit}`

Example: `CBB/202509/0042`

- `CBB` = Outlet code
- `202509` = Year and month
- `0042` = Sequential number for that outlet/month

## 📋 Postman Collection

Import the Postman collection from `/postman/KasirLite.postman_collection.json` for easy API testing.

### Collection Variables
- `base_url`: http://localhost:8000 (adjust for your setup)
- `auth_token`: Your authentication token

## 🧪 Example cURL Requests

### Get Products
```bash
curl -X GET "http://localhost:8000/api/products" \
  -H "Authorization: Bearer your-token-here" \
  -H "Accept: application/json"
```

### Create Sale
```bash
curl -X POST "http://localhost:8000/api/sales" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your-token-here" \
  -d '{
    "outlet_id": 1,
    "sold_at": "2025-09-13T10:15:00",
    "items": [
      {
        "product_id": 1,
        "price": 5000,
        "qty": 2,
        "discount": 0
      }
    ],
    "discount_total": 0,
    "tax_percent": 10,
    "rounding": 0,
    "payments": [
      {
        "method": "CASH",
        "amount": 11000
      }
    ],
    "note": "Test sale"
  }'
```

## 🔒 Security Considerations

### Production Deployment
1. **Environment**:
   - Set `APP_ENV=production`
   - Set `APP_DEBUG=false`
   - Generate strong `APP_KEY`

2. **Database**:
   - Use strong database passwords
   - Limit database user permissions
   - Enable SSL connections

3. **API Security**:
   - Use HTTPS in production
   - Implement rate limiting
   - Regular token rotation

## 📝 License

This project is licensed under the MIT License.

---

**Built with ❤️ for Indonesian UMKM**
# kasir-lite
