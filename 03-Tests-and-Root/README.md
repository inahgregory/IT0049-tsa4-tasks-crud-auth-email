# Tasks for Today Full CRUD Authentication and Email

IT0049 Technical Summative Assessment 4 by Inah Angela Gregorio, BSITCST - AC32.

This project extends TSA3 with validated task creation/editing, archiving through is_archived, Laravel Breeze authentication for management actions, and a completion email generated when an existing task changes to done.

## Requirements

PHP 8.2+, Composer 2, PDO SQLite, and the standard Laravel PHP extensions. Laravel 12 matches the installed PHP 8.2 environment. The active Blade pages use public/css/app.css, so no npm build is required.

Breeze v2.4.2 has been installed and its Blade scaffolding generated. Auth controllers, LoginRequest, routes, and tests are included. Registration/profile field mapping uses the existing username and full_name schema. Auth views have been styled to match TSA3. The optional generated Tailwind/Vite source remains, but active pages do not require its assets.

## First setup

From PowerShell inside the project:

```powershell
composer install
if (!(Test-Path .env)) { Copy-Item .env.example .env }
php artisan key:generate
if (!(Test-Path database/database.sqlite)) { New-Item -ItemType File database/database.sqlite }
php artisan migrate --seed
php artisan serve --host=127.0.0.1 --port=8004
```

Open http://127.0.0.1:8004. Generate an application key only during first setup. On later runs start only the server. Keep that window open; Ctrl+C stops it. XAMPP MySQL and PHP GD are not needed for this SQLite-based project.

Register your own account at /register using a unique username, full name, lowercase email, and a password of at least 8 characters. Login uses EMAIL, not username. There is no shared preset password. The seeded owner profile is a fictional classroom record with a random hashed password.

## Public and protected pages

| URL | Access | Behavior |
| --- | --- | --- |
| / | Public | Active tasks dated today |
| /tasks | Public | All active tasks ordered by date, then id |
| /profile | Public | Original owner profile |
| /about | Public | Application and developer information |
| /tasks/create | Authenticated | New-task form |
| POST /tasks | Authenticated | Validate and save a task |
| /tasks/{task}/edit | Authenticated | Prefilled edit form |
| PUT/PATCH /tasks/{task} | Authenticated | Validate, update, and notify on a done transition |
| DELETE /tasks/{task} | Authenticated | Set is_archived to true |
| /account | Authenticated | Current user's private profile/password settings |
| /dashboard | Authenticated | Signed-in version of the Welcome page |

The public owner Profile and the signed-in user's Account settings are separate. Registration can add login users; the public Profile continues to display the original owner.

Guests can browse without seeing management controls. The auth middleware also protects direct management requests. Forms use CSRF tokens and method overrides. Breeze's password hashing, session regeneration, logout invalidation, login throttling, password reset, and password-update behavior are retained.

## Task validation and archiving

TaskRequest requires title (maximum 255 characters), status (pending, in_progress, or done), and task_date (YYYY-MM-DD). Forms use old() and error messages to preserve valid input after a failed submission.

The archive action requires a confirmation checkbox and updates is_archived; it does not physically delete the row. Both public lists use Task::active() to exclude archived records. Archived records cannot be edited or updated through their old URLs. No restore or archive-history page is required by this activity.

The new migrations add is_archived, normalize TSA3's completed status to done, and add password/remember-token/email-verification fields, a unique user email index, and password_reset_tokens. Use a fresh database for this standalone copy. Before upgrading a separate existing TSA3 database, reconcile duplicate emails.

The seeder creates 8 sample tasks across yesterday, today, and tomorrow and one owner profile on first setup. Repeating it updates its sample IDs and preserves existing passwords and archive flags. To retain your own edited sample titles/dates/statuses, do not reseed unnecessarily. Today uses APP_TIMEZONE=Asia/Manila (UTC+08:00).

## Completion email behavior

TaskController::update re-reads the task inside a transaction and compares the persisted old status with the validated new status. Only a non-done to done transition calls:

Mail::to(config('tasks.completion_recipient'))->send(new TaskCompleted($current));

TaskCompleted is a Mailable. Its Blade view includes the task title, ID, scheduled date, and done status. Sending is synchronous; no queue worker is needed.

- pending or in_progress to done generates one notification.
- Saving an already-done task, including title/date changes, generates no repeat.
- Creating a task initially as done generates no notification; the requirement concerns updates.
- Reopening a task and completing it again is a new transition and generates another notification.
- Archiving a task does not generate a completion email.
- A mail transport exception rolls back the update and displays an error for retry.

This is a synchronous classroom implementation, not a transactional email outbox. External SMTP delivery cannot be made atomic with a database commit. The required local verification uses the log transport.

## Safe local mail configuration

The example environment uses:

```dotenv
MAIL_MAILER=log
MAIL_LOG_CHANNEL=task_mail
MAIL_FROM_ADDRESS=hello@example.com
TASK_COMPLETION_RECIPIENT=tasks-review@example.com
```

Messages are recorded in storage/logs/task-mail.log. No real inbox is contacted. TASK_COMPLETION_RECIPIENT supplies the recipient address; config/tasks.php exposes it to the controller. The dedicated log channel is configured in config/logging.php.

To test: create a pending task named Email proof task, edit it to done, then inspect the mail log. Save the same task again while it remains done and confirm the subject count stays unchanged. POWERSHELL-GUIDE.txt contains exact commands.

For a safe test SMTP service instead, configure MAIL_MAILER=smtp and the service's MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD, encryption/scheme settings, and a configured recipient through environment secrets. Do not commit credentials. Run php artisan config:clear after changing local mail settings. Configure production mail on the host if actual delivery is wanted.

Breeze password-reset messages also use the selected mail transport. Do not include reset links or tokens in screenshots or GitHub uploads.

## Verification

```powershell
php artisan migrate:status
php artisan route:list --path=tasks -v
php artisan tasks:summary
php artisan test
```

The summary shows stored rows, their archive flags, active tasks, and archived counts. Tests use an isolated in-memory database and fake or log mail. They verify public/guest access, task CRUD, preserved input, archiving without deletion, exclusion of archives, status transitions, no duplicate completion mail, an actual rendered log email, mail-failure rollback, and Breeze authentication/password flows.

## GitHub upload

Suggested repository: it0049-tsa4-tasks-crud-auth-email

Upload the CONTENTS of TSA4-GitHub-Upload, including .gitignore and .env.example, with artisan and composer.json at the repository root. Include raw source, migrations, seeders, tests, composer.lock, README, and storage/bootstrap placeholders. Migrations and seeders satisfy the database submission requirement; no export is needed.

Exclude .env, secrets, vendor, node_modules, .runtime, local SQLite files, logs, caches, sessions, and password-reset tokens. The prepared upload folder excludes these. The GitHub browser uploader does not apply .gitignore exclusions to dragged files.

To avoid uploading too many files at once, the identical source is also organized in TSA4-GitHub-Batches:
1. Upload the CONTENTS of 01-Core and commit.
2. Upload the CONTENTS of 02-Views-and-Storage to the same repository and commit.
3. Upload the CONTENTS of 03-Tests-and-Root to the same repository and commit.

Do not upload the numbered batch folders themselves. The resulting repository must contain normal app, resources, routes, tests, and root files.

## Hosting and submission

Use a PHP 8.2+ host with Composer and SQLite or MySQL. GitHub Pages does not execute Laravel.

Point the document root to public. Install with composer install --no-dev --optimize-autoloader. The generated auth code works without the Breeze development package in production; do not rerun breeze:install.

Set APP_ENV=production, APP_DEBUG=false, a newly generated APP_KEY, APP_URL to the real HTTPS URL, APP_TIMEZONE=Asia/Manila, SESSION_SECURE_COOKIE=true for HTTPS, database configuration, and mail configuration through the host's secrets. Keep Laravel's default HttpOnly and SameSite protections. Provision persistent SQLite storage or a MySQL service and writable storage/bootstrap/cache directories. Use file sessions/cache and sync queue settings unless configured otherwise.

For the assessment, retain MAIL_MAILER=log and make task-mail.log accessible through the host's private console for verification. Do not expose it through public routes. For a test SMTP service, put its credentials in host secrets. Run php artisan migrate --seed --force on a fresh database and php artisan optimize.

The optional Dockerfile has not been built or tested here. It needs host secrets and persistent writable storage. Run migrations through the host's console. Hosting has not been provisioned.

Submit a real GitHub repository URL and verified hosted HTTPS application URL. Localhost is for documentation only.

## References

- https://laravel.com/docs/12.x/mail
- https://laravel.com/docs/12.x/database#database-transactions
- https://github.com/laravel/breeze
