# recruitment-task1

# How to Run Patient Portal Application

## Quick Start (Recommended)

### 1. Start the Application
```bash
docker-compose up -d
```

### 2. Setup Database
```bash
# Run migrations
docker-compose exec app php artisan migrate

# Import patient data
docker-compose exec app php artisan import:patients results.csv
```

### 3. Access the Application
- **Frontend**: http://localhost:5173
- **Backend API**: http://localhost:8000/api

## Test Credentials

Use these credentials to log in (Polish characters are fully supported):

| Username | Password (Birth Date) |
|----------|----------------------|
| PiotrKowalski | 1983-04-12 |
| AnnaJabłońska | 2002-12-12 |
| AndrzejKowalski | 2020-01-31 |
| BożenaWiśniewska | 2021-11-21 |

**Note**: Copy and paste the usernames exactly as shown above to ensure Polish characters (ł, ń, ś, ż) are handled correctly.

## Stop the Application

```bash
docker-compose down
```

## Alternative: Local Development

### Backend
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
php artisan migrate
php artisan import:patients results.csv
php artisan serve
```

### Frontend
```bash
cd frontend
npm install
npm run dev
```

## Troubleshooting

- If frontend port 5173 is busy: `docker-compose restart frontend`
- If backend port 8000 is busy: `docker-compose restart app`
- Check logs: `docker-compose logs [service-name]`