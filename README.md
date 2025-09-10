# MelioHealth – Backend (Laravel)

## Overview
This is the **backend service** for MelioHealth, a full-stack digital healthcare platform.  
The backend provides secure APIs, authentication, patient record management, and integration with third-party services (video conferencing and payments).

## Features
- **Authentication & Authorization** with Laravel Passport (separate roles for patients and admins)  
- **Patient Records**: Secure storage and management of symptoms and history  
- **Appointments API**: Scheduling and tracking appointments  
- **Payments**: Stripe integration for subscription and billing  
- **Video Conferencing Support**: Socket.io integration for live doctor-patient sessions  
- **Admin Management**: APIs for analytics, user roles, and activity logs  

## Technologies
- **Framework:** Laravel (PHP)  
- **Database:** MySQL / MariaDB  
- **Authentication:** Laravel Passport (OAuth2)  
- **APIs:** REST APIs for frontend and third-party integrations  
- **Deployment:** DigitalOcean, Docker, CI/CD pipelines  
- **Other Tools:** Git, Bitbucket, AWS S3  

## Installation & Setup
1. Clone the repository:  
   ```bash
   git clone https://github.com/waleedmazhar64/meliohealth-backend.git
   cd meliohealth-backend

2. Install dependencies:
   composer install

3. Copy .env.example to .env and configure database + API keys.
   php artisan key:generate
   php artisan migrate
   php artisan passport:install

4. Run the server:
   php artisan serve


**API Endpoints**

POST /api/register → User registration (patients/admins)

POST /api/login → Login & token generation

GET /api/patient/symptoms → Fetch patient records

POST /api/appointments → Create appointment

POST /api/payment → Process subscription/payment


