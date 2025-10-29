# PC Zone Monorepo

This repository contains:
- `frontend/` – Web UI
- `backend/`  – API service
- `pc_zone.sql` – Database schema and seed

## Quickstart

### Frontend
```bash
cd frontend
npm install
npm run dev
```

### Backend
```bash
cd backend
# If PHP/Composer:
composer install
# Or if Node:
npm install && npm start
```

### Database
- Create a database `pc_zone` and import `pc_zone.sql`.

## Deployment
- Create a `.env` for each service. Do NOT commit secrets.
- Add CI (GitHub Actions) if you like.
