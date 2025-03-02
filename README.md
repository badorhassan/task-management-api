## written by Badria Elsayed

## A RESTful API for a Task Management System

A RESTful API for managing tasks with user authentication, role-based access control, and task dependency management


### Berif :

- **JWT Authentication for stateless API authentication**
- **Spatie Permission for role-based access control**
- **Task dependencies with validation (a task cannot be completed until all its dependencies are completed)**
- **Filtering tasks by status, due date range, and assigned user**
- **Docker containerization for easy setup and deployment**
- **API Resources for consistent API responses**
- **Custom exception handling for API errors**
- **Middleware for role-based route protection**
- **Database seeders for initial data population**
- **API documentation with Swagger/OpenAPI (optional)**

## Tools : 

- **Docker** 
- **Composer** 
- **Laravel 10**   
- **PHP/8.2.12**
- **JWT**
- **Spatie**
- **Postman**  

### DataBase ERD :

## Installation Locally : 
: 
- ## Clone the repository
- ## composer install
- ## cp .env.example .env
- ## Configure database settings in the .env file:
- **DB_CONNECTION=mysql**
- **DB_HOST=127.0.0.1**
- **DB_PORT=3306**
- **DB_DATABASE=task-management**
- **DB_USERNAME=root**
- **DB_PASSWORD=you_password if exists**
- **JWT_SECRET=your_jwt_secret_key**
- **JWT_EXPIRES_IN=60**
- ## php artisan key:generate
- ## php artisan migrate --seed


#### Installation Via Docker:

- **DB_CONNECTION=mysql**
- **DB_HOST=127.0.0.1**
- **DB_PORT=3306**
- **DB_DATABASE=task-management**
- **DB_USERNAME=root**
- **DB_PASSWORD=you_password if exists**
- **JWT_SECRET=your_jwt_secret_key**
- **docker-compose up -d**
- **docker-compose exec app composer install**
- **docker-compose exec app php artisan migrate --seed**

## API Endpoints :

  GET|HEAD  / ................................................................ 
  POST      _ignition/execute-solution ignition.executeSolution › Spatie\Lara… 
  GET|HEAD  _ignition/health-check ignition.healthCheck › Spatie\LaravelIgnit… 
  POST      _ignition/update-config ignition.updateConfig › Spatie\LaravelIgn… 
  POST      api/auth/login .................. login › API\AuthController@login 
  POST      api/auth/logout ........................ API\AuthController@logout 
  GET|HEAD  api/auth/me ................................ API\AuthController@me 
  POST      api/auth/refresh ...................... API\AuthController@refresh 
  POST      api/register ......................... API\AuthController@register 
  GET|HEAD  api/tasks ............................... API\TaskController@index 
  POST      api/tasks ............................... API\TaskController@store 
  GET|HEAD  api/tasks/{id} ........................... API\TaskController@show 
  PUT       api/tasks/{id} ......................... API\TaskController@update 
  DELETE    api/tasks/{id} ........................ API\TaskController@destroy 
  POST      api/tasks/{id}/dependencies ..... API\TaskController@addDependency 
  DELETE    api/tasks/{id}/dependencies .. API\TaskController@removeDependency 
  GET|HEAD  sanctum/csrf-cookie sanctum.csrf-cookie › Laravel\Sanctum › CsrfC… 



## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
