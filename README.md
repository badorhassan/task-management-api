# Task Management System API

A RESTful API for managing tasks with user authentication, role-based access control, and task dependency management.

## Features
- JWT Authentication for stateless API authentication
- Spatie Permission for role-based access control
- Task dependencies with validation (a task cannot be completed until all its dependencies are completed)
- Filtering tasks by status, due date range, and assigned user
- Docker containerization for easy setup and deployment
- API Resources for consistent API responses
- Custom exception handling for API errors
- Middleware for role-based route protection
- Database seeders for initial data population

## Technology Stack

- Docker
- Composer
- Laravel 10
- PHP/8.2.12
- JWT
- Spatie
- Postman

DataBase ERD :

 <img src="https://github.com/badorhassan/task-management-api/blob/main/Task-Management-ERD.png" />
Within the download you'll find the following directories and files :

 <img src="https://github.com/badorhassan/task-management-api/blob/main/app structure.png" />


## Installation Options

### Option 1: Local Installation

1. **Clone the repository**
 
   git clone https://github.com/your-username/task-management-api.git
   cd task-management
 

2. **Install dependencies**
  
   composer install
  

3. **Set up environment file**
 
   cp .env.example .env
   

4. **Configure database settings in the .env file**
   
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=task-management
   DB_USERNAME=root
   DB_PASSWORD=your_password
   JWT_SECRET=your_jwt_secret_key
   JWT_EXPIRES_IN=60
  

5. **Generate application key**
   
   php artisan key:generate
  

6. **Run migrations and seed the database**
   
   php artisan migrate --seed
  

### Option 2: Docker Installation

1. **Clone the repository**
  
   git clone https://github.com/your-username/task-management.git
   cd task-management
 

2. **Set up environment file**
 
   cp .env.example .env
  

3. **Configure database settings for Docker in the .env file**
  
   DB_CONNECTION=mysql
   DB_HOST=db
   DB_PORT=3306
   DB_DATABASE=task_management
   DB_USERNAME=root
   DB_PASSWORD=root
   JWT_SECRET=your_jwt_secret_key
   JWT_EXPIRES_IN=60
 

4. **Build and start Docker containers**

   docker-compose up -d


5. **Install dependencies and set up the application**

   docker-compose exec app composer install
   docker-compose exec app php artisan key:generate
   docker-compose exec app php artisan migrate --seed


6. **Access the application**
   - API: [http://localhost:8000/api](http://localhost:8000/api)

API Endpoints :
 <img src="https://github.com/badorhassan/task-management-api/blob/main/route list.png" />

### Authentication
- `POST /api/auth/login` - User login
- `POST /api/auth/register` - User registration
- `GET /api/auth/me` - Get authenticated user
- `POST /api/auth/logout` - User logout
- `POST /api/auth/refresh` - Refresh JWT token

### Tasks
- `GET /api/tasks` - List all tasks (filterable)
- `POST /api/tasks` - Create a new task
- `GET /api/tasks/{id}` - Get a specific task
- `PUT /api/tasks/{id}` - Update a task
- `DELETE /api/tasks/{id}` - Delete a task
- `PATCH /api/tasks/{id}/status` - Update task status
- `GET /api/tasks/{id}/dependencies` - Get task dependencies

### Users
- `GET /api/users` - List all users (Admin only)
- `GET /api/users/{id}` - Get a specific user
- `GET /api/users/{id}/tasks` - Get tasks assigned to a user



License
The Laravel framework is open-sourced software licensed under the MIT license.