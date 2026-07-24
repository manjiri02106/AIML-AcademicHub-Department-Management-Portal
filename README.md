# AIML AcademicHub

AIML AcademicHub now includes a production-oriented React + Express + MongoDB Faculty Module. The original PHP pages remain available under `frontend/` for reference, while the new application lives in `client/` and `server/`.

## Run the new application

Prerequisites: Node.js 18+, MongoDB, and npm.

```powershell
cd server
Copy-Item .env.example .env
npm install
npm run seed
npm run dev
```

In a second terminal:

```powershell
cd client
npm install
npm run dev
```

Open `http://localhost:5173`. Demo login after seeding: `faculty@aiml.edu` / `password`.

## Application structure

```text
server/src/
  config/       MongoDB connection
  models/       User, Faculty, CourseAllocation, MentorRecord
  controllers/ REST use cases
  routes/       Auth, faculty, courses, mentoring, dashboard
  middleware/   JWT authorization, validation, error handling
client/src/
  components/   Layout and reusable UI states
  context/      JWT auth state
  pages/        Login, Dashboard, Profile, Courses, Mentoring
  services/     Axios API functions
```

The API contract and endpoint matrix are documented in [server/README.md](server/README.md).# AIML-AcademicHub-Department-Management-Portal