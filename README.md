# Job Board API

A RESTful API for a job board platform built with Laravel.

Postman doc: https://documenter.getpostman.com/view/15626596/2sB2qZE2bN

## Features

- Separate authentication for companies and candidates
- Job posting and management for companies
- Job application system for candidates
- Queue processing for file uploads
- Caching for public job listings
- Comprehensive API documentation

## Setup

1. Clone the repository:
```bash
git clone https://github.com/iamprincesly/job-board-api.git
cd job-board-api
```

2. Install dependencies:
```
composer install
```

3. Copy env
``` 
cp .env.example .env
``` 

4. Geneate app key
```
php artisan key:generate
```

5. Run app refresh
```
php artisan app:refresh
```

6. Run queue worker
```
php artisan queue:work
```

7. Run the server
``` 
php artisan serve
```
