# Food Donation Platform (Core PHP + MySQL)

A complete NGO-oriented food donation platform built with **Core PHP**, **PDO**, **TailwindCSS**, and **vanilla JavaScript**.

## Features
- User registration/login with password hashing.
- Role-based dashboards (`admin`, `user`, `orphanage`).
- Donor donation submission flow.
- Admin orphanage assignment with nearest orphanage suggestion (based on coordinates).
- Orphanage accept/reject actions with one-way status transitions.
- Email notifications to:
  - `balaabineshh0@gmail.com`
  - `balaabinesh88@gmail.com`
  - donor confirmation/status updates
- REST-like JSON API endpoints with consistent response shape.

## Project Structure
```text
/config
  app.php
/core
  BaseModel.php
  Controller.php
  Csrf.php
  Database.php
  Mailer.php
  Session.php
/models
  User.php
  Donation.php
/controllers
  AuthController.php
  DashboardController.php
  DonationController.php
  ApiController.php
/views
  /layouts
    header.php
    footer.php
  /auth
    login.php
    register.php
  /dashboard
    admin.php
    user.php
    orphanage.php
  home.php
/api
  bootstrap.php
  users.php
  donations.php
  orphanage-actions.php
/assets
  /js
    validation.js
  /css
    app.css
/storage
  mail.log
index.php
router.php
.htaccess
schema.sql
README.md
```

## Setup
1. Create database and tables:
   ```bash
   mysql -u root -p < schema.sql
   ```
2. Configure environment (optional defaults exist):
   - `APP_URL`
   - `DB_HOST`
   - `DB_PORT`
   - `DB_NAME`
   - `DB_USER`
   - `DB_PASS`
   - `MAIL_FROM`
   - `APP_BASE_PATH` (optional override; if omitted, app auto-detects subfolder from `SCRIPT_NAME`)

3. Ensure `storage/mail.log` is writable:
   ```bash
   touch storage/mail.log
   chmod 664 storage/mail.log
   ```

## Run
```bash
php -S 0.0.0.0:8000 router.php
```

Then open `http://localhost:8000` (or your configured base path).

## Seed Admin
- Email: `admin@ngo.local`
- Password: `admin12345`

## Roles and Workflows
### Donor (`user`)
1. Register/login as `user`.
2. Submit donation (title, description, quantity, pickup address, optional lat/lng).
3. Receives submission confirmation email.

### Admin (`admin`)
1. Login and open dashboard.
2. Review pending donations.
3. See suggested nearest orphanage.
4. Assign donation to suggested/manual orphanage.
5. Assigned orphanage gets email notification.

### Orphanage (`orphanage`)
1. Login and view assigned donations.
2. Accept or reject pending items.
3. Once finalized, action buttons are disabled.

## Status and Validation Rules
- Donation statuses: `pending`, `accepted`, `rejected`.
- Valid transition: **only** `pending -> accepted|rejected`.
- Final states are immutable (enforced in backend even if UI is bypassed).

## API Endpoints
All responses use:
```json
{
  "success": true,
  "data": {},
  "error": null
}
```

### Users API
- `GET /api/users`
- `POST /api/users`
- `PUT /api/users`
- `PATCH /api/users`
- `DELETE /api/users`

Example:
```bash
curl -X POST http://localhost:8000/api/users \
  -H "Content-Type: application/json" \
  -d '{"name":"Test","email":"test@ngo.local","password":"password123","role":"user"}'
```

### Donations API
- `GET /api/donations`
- `POST /api/donations`
- `PUT /api/donations`
- `PATCH /api/donations`
- `DELETE /api/donations`

Example:
```bash
curl -X PATCH http://localhost:8000/api/donations \
  -H "Content-Type: application/json" \
  -d '{"id":1,"status":"accepted"}'
```

### Orphanage Action API
- `POST /api/orphanage/actions`

Example:
```bash
curl -X POST http://localhost:8000/api/orphanage/actions \
  -H "Content-Type: application/json" \
  -d '{"donation_id":1,"action":"accept"}'
```

## Security Notes
- Passwords hashed with `password_hash` and verified with `password_verify`.
- CSRF token required for all state-changing web form actions.
- PDO prepared statements used throughout.
- Output escaped with `htmlspecialchars` in views.
- Server-side role protection enforced.
- Session IDs regenerated on login.

## Email Fallback
If `mail()` fails, email payloads are appended to `storage/mail.log`.


## Mail Templates
The app sends separate formatted emails for each role-based event:
- Admins: **New Donation Submitted** with donor and pickup details.
- Donors: **Thanks for Donating** confirmation email.
- Orphanages: **Donation Assigned for You** after admin assignment.
- Donors also receive **Donation Status Updated** when orphanage accepts/rejects.
