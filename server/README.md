# AIML AcademicHub API

## Setup

1. Install MongoDB and create `server/.env` from `.env.example`.
2. Run `npm install` inside `server`.
3. Run `npm run dev`.

The API is available at `http://localhost:5000/api`. Every protected route expects `Authorization: Bearer <jwt>`.

## REST endpoints

| Method | Endpoint | Access |
| --- | --- | --- |
| POST | `/auth/login` | Public |
| POST | `/auth/register` | Public/bootstrap |
| GET/PUT | `/faculty/me` | Faculty |
| POST | `/faculty/me/photo` | Faculty |
| GET | `/faculty` | Admin/HOD |
| GET | `/faculty/dashboard` | Authenticated |
| GET/POST/PUT/DELETE | `/courses` | Read: authenticated; write: Admin/HOD |
| GET/POST/PUT/DELETE | `/mentoring` | Faculty-owned records |