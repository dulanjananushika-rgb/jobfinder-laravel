# JobFinder Laravel Migration Notes

This Laravel rebuild replaces the legacy flat PHP pages with MVC routes, Eloquent models, validation, hashed database users, role middleware, CSRF-protected forms, migrations, and seed data.

## Demo Accounts

- Admin: `admin@jobfinder.test` / `Admin@12345`
- Employer: `employer@jobfinder.test` / `Employer@12345`
- Job seeker: `seeker@jobfinder.test` / `Seeker@12345`

## Standardized Roles

- `admin`
- `employer`
- `job_seeker`

The legacy `job-seeker` / `job_seeker` split is removed.

## Main Routes

- `/`
- `/jobs`
- `/my-applications`
- `/saved-jobs`
- `/employer/jobs`
- `/employer/applications`
- `/admin/users`
- `/admin/jobs`
- `/admin/applications`

## Data Model

- `users`
- `jobs`
- `job_applications`
- `saved_jobs`

Uploaded resumes and profile pictures are stored through Laravel's `public` disk and served via `public/storage`.

## Legacy Data

The original PHP files and `uploads` folder are left untouched in the parent directory. Existing MySQL data has not been imported automatically because no schema dump was present in the legacy project. To import production data, map the old columns into the Laravel tables above and normalize `user_type` values to `role`.
