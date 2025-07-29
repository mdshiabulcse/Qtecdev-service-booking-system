markdown
# Service Booking System - Laravel & Vue.js

## Project Setup Guide

### Prerequisites
- PHP 8.1+
- Composer
- MySQL
- Node.js v22+
- npm/yarn

### Backend Setup (Laravel)

1. Clone the repository:
   ```bash
   git clone https://github.com/mdshiabulcse/Qtecdev-service-booking-system.git
   cd Qtecdev-service-booking-system/backend
Install dependencies:

bash
composer install
Configure environment:

bash
cp .env.example .env
php artisan key:generate
Update .env with your database credentials:

env
- DB_DATABASE=your_db_name
- DB_USERNAME=your_db_user
- DB_PASSWORD=your_db_password
- Run migrations and seed data:

bash
php artisan migrate
php artisan db:seed
OR visit these URLs after setting up your project:

- your_project_url/migrate

- your_project_url/db-seed

Start the Laravel development server:

bash
php artisan serve
Frontend Setup (Vue.js)
Navigate to frontend directory:

bash
cd ../frontend
Install dependencies:

bash
npm install
Configure API URL:
Edit frontend/src/api/index.js:

- javascript

const api = axios.create({
baseURL: import.meta.env.VITE_API_URL || 'http://your_laravel_url/api',
})
Start the development server:

- bash
npm run dev




# Service Booking System - Backend Documentation

## Table of Contents
1. [API Controllers](#api-controllers)
2. [Request Validations](#request-validations)
3. [Data Models](#data-models)
4. [Middleware](#middleware)
5. [Database Structure](#database-structure)
6. [API Endpoints](#api-endpoints)
7. [Authentication Flow](#authentication-flow)

---

## API Controllers

### AdminController
**Path**: `app/Http/Controllers/AdminController.php`  
**Responsibilities**:
- User management (list all users)
- Booking status updates

**Methods**:
- `getAllUsers()` - Retrieves all registered users (admin only)
- `updateBookingStatus()` - Modifies booking status (admin only)

### AuthController
**Path**: `app/Http/Controllers/AuthController.php`  
**Responsibilities**:
- User authentication
- Session management

**Methods**:
- `register()` - Creates new customer accounts
- `login()` - Authenticates users
- `logout()` - Terminates sessions
- `checkAuth()` - Verifies authentication status

### BookingController
**Path**: `app/Http/Controllers/BookingController.php`  
**Responsibilities**:
- Booking lifecycle management

**Methods**:
- `index()` - Lists user's bookings
- `store()` - Creates new bookings

### ServiceController
**Path**: `app/Http/Controllers/ServiceController.php`  
**Responsibilities**:
- Service catalog management

**Methods**:
- `index()` - Lists available services
- `store()` - Creates new services (admin)
- `update()` - Modifies services (admin)
- `destroy()` - Removes services (admin)

---

## Request Validations

### BookingRequest
**Path**: `app/Http/Requests/BookingRequest.php`  
**Validation Rules**:
- Service must exist
- Booking date cannot be in past
- Required fields enforcement

### LoginRequest
**Path**: `app/Http/Requests/LoginRequest.php`  
**Validation Rules**:
- Valid email format
- Password requirement
- Credential verification

### RegisterRequest
**Path**: `app/Http/Requests/RegisterRequest.php`  
**Validation Rules**:
- Name requirements
- Unique email validation
- Password complexity rules
- Password confirmation

### ServiceRequest
**Path**: `app/Http/Requests/ServiceRequest.php`  
**Validation Rules**:
- Service name constraints
- Price validation (numeric, non-negative)
- Status enum enforcement

---

## Data Models

### Booking
**Path**: `app/Models/Booking.php`  
**Attributes**:
- `user_id` - Customer reference
- `service_id` - Service reference
- `booking_date` - Scheduled date
- `status` - Current state (pending/confirmed/cancelled/completed)

**Relationships**:
- Belongs to `User`
- Belongs to `Service`

### Service
**Path**: `app/Models/Service.php`  
**Attributes**:
- `name` - Service title
- `description` - Detailed explanation
- `price` - Cost value
- `status` - Availability state

### User
**Path**: `app/Models/User.php`  
**Attributes**:
- `name` - Full name
- `email` - Unique identifier
- `password` - Secure hash
- `is_admin` - Privilege flag

---

## Middleware

### AdminMiddleware
**Path**: `app/Http/Middleware/AdminMiddleware.php`  
**Functionality**:
1. Verifies authenticated user
2. Checks admin privileges
3. Rejects unauthorized access (403)

**Applied Routes**:
- All admin-only endpoints

---

## Database Structure

### Migrations
**Users Table**:
- ID, name, email, password, is_admin (boolean), timestamps

**Services Table**:
- ID, name, description, price (decimal), status (string), timestamps

**Bookings Table**:
- ID, user_id (foreign), service_id (foreign), booking_date (date), status (string), timestamps

### Seeder Data
**Default Admin**:
- Email: admin@shiabul.com
- Password: 12345678
- Admin privileges: true

**Sample Services**:
- 5 pre-defined services with varying prices

**Test Users**:
- 3 customer accounts with sample bookings

---

## API Endpoints

| Method | Endpoint                  | Description                     | Access       |
|--------|---------------------------|---------------------------------|--------------|
| POST   | `/register`               | Customer registration           | Public       |
| POST   | `/login`                  | User authentication             | Public       |
| GET    | `/services`               | List available services         | Public       |
| POST   | `/logout`                 | Session termination             | Authenticated|
| GET    | `/check-auth`             | Verify authentication           | Authenticated|
| GET    | `/bookings`               | List user bookings              | Authenticated|
| POST   | `/bookings`               | Create new booking              | Authenticated|
| POST   | `/services`               | Add new service                 | Admin        |
| PUT    | `/services/{service}`     | Update service                  | Admin        |
| DELETE | `/services/{service}`     | Remove service                  | Admin        |
| GET    | `/admin/users`            | List all users                  | Admin        |
| PUT    | `/admin/bookings/{booking}`| Update booking status          | Admin        |

---

## Authentication Flow

1. **Registration**
    - Validates user data
    - Creates customer account
    - Returns success response

2. **Login**
    - Verifies credentials
    - Generates Sanctum token
    - Returns authentication token

3. **Protected Access**
    - Requires `Authorization: Bearer {token}` header
    - Validates token on each request
    - Rejects expired/invalid tokens (401)

4. **Admin Verification**
    - Checks `is_admin` flag
    - Rejects non-admin users (403)
    - Processes privileged actions

---





# Service Booking System - Frontend Documentation

## Project Structure Overview





# Service Booking System - Frontend Documentation

## Core Functionality

### 1. User Interface Components
**Location:** `src/components/`

![UI Components](./ux_file/home.png)  
*Sample reusable components in action*

- **AppBar.vue**: Main navigation header with user controls
- **NavigationDrawer.vue**: Side menu for app navigation
- **ServiceCard.vue**: Display component for service items
- **BookingForm.vue**: Form for creating new bookings

### 2. Application Views
**Location:** `src/views/`

#### Authentication Views
![Login Screen](./ux_file/login.png)  
*User authentication interface*

- **Login.vue**: User sign-in form
- **Register.vue**: New account registration

#### Service Management
![Service Catalog](./ux_file/service.png)  
*Service catalog display*

- **ServicesList.vue**: Grid view of available services
- **ServiceForm.vue**: Admin service creation/editing

#### Booking Management
![Booking Flow](./ux_file/booking.png)  
*Booking creation workflow*

- **BookingsList.vue**: User's booking history
- **CreateBooking.vue**: New booking form

#### Admin Dashboard
![Admin Panel](./ux_file/admin.png)  
*Administrative control center*

- **AdminDashboard.vue**: Management console for admins

### 3. Application Infrastructure

#### API Communication
- Handles all backend interactions
- Organized by feature (auth, bookings, services)
- Includes centralized request/response handling

#### State Management
- Manages global application state
- Handles user authentication status
- Stores frequently accessed data

#### Routing System
- Controls page navigation
- Implements route guards for security
- Supports lazy loading for performance



### Customer Journey
1. Registration
2. Service browsing
3. Booking


### Admin Workflow
1. Login
2. Dashboard
3. Service management
4. Booking oversight

## Technical Specifications

- **Framework**: Vue.js 3
- **UI Library**: Vuetify 3
- **State Management**: Pinia
- **Routing**: Vue Router
- **HTTP Client**: Axios
- **Build Tool**: Vite

> **Note**: Replace example image URLs with actual screenshots of your application