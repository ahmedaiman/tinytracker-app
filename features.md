# TinyTracker – Application Features & Test Coverage

*Generated: 2025-07-03*

---

## 1. Core Domain Models

| Model | Key Attributes (fillable) | Relationships | Behaviour / Business Logic |
|-------|---------------------------|---------------|-----------------------------|
| **User** | name, email, password, role | hasMany children, randomChecks | Role handling (`guardian`, `doctor`, `admin`), 2FA, API tokens |
| **Child** | user_id, name, date_of_birth, gender, notes, current_profile_photo_path | belongsTo user; hasMany appointments, appointmentReminders, randomChecks, meals, snacks, basalDoses, photos, notes | Computed `age`, `age_in_months`, profile-photo URL, scopes (`forUser`, `upcomingAppointments`, `abnormalReadings`, `recentNotes`) |
| **Appointment** | child_id, user_id, doctor_id, title, description, start_time, end_time, location, type, status, recurrence_rule, metadata | belongsTo child, user (guardian), doctor; hasMany reminders, instances | Recurrence engine, scopes (`upcoming`, `past`, `today`, `between`), status helpers (`confirm`, `complete`, `cancel`), next-occurrence calculator |
| **AppointmentReminder** | appointment_id, type, recipient_type, recipient_contact, message, scheduled_at, status, attempts | belongsTo appointment | Status helpers, scopes (`pending`, `due`, `sent`, `failed`), next scheduled retrieval |
| **BasalDose** | child_id, user_id, insulin_type_id, units, injection_date, notes, is_manual_entry, is_correction_dose | belongsTo child, user, insulinType | Scopes (`forChild`, `forDate`, `correctionDoses`), computed helpers (dose with units, formatted date, days since injection) |
| **Meal** | child_id, user_id, meal_type, meal_time, pre_bg, post_bg, post_meal_bg_time, insulin_type_id, insulin_units, insulin_injected_at, food_desc, carbs_grams, sugars_grams, status, is_override, notes | belongsTo child, user, insulinType | Scopes (`forChild`, `forDate`, `ofType`, `completed`), status helpers, post-meal BG time difference |
| **Snack** | child_id, user_id, snack_time, food_description, carbs_grams, sugars_grams, pre_snack_bg, post_snack_bg, post_snack_bg_time, insulin_type_id, insulin_units, insulin_injected_at, notes | belongsTo child, user, insulinType | Scopes (`forChild`, `forDate`), `hasBloodGlucoseData`, `hasInsulinData`, post-snack BG time difference |
| **RandomCheck** | child_id, user_id, check_time, bg_value, insulin_type_id, insulin_units, insulin_injected_at, context, notes, is_manual_entry, is_high_alert, is_low_alert | belongsTo child, user, insulinType | Scopes (`forChild`, `forDate`, `highAlerts`, `lowAlerts`), context options, target-range check |
| **Note** | child_id, user_id, title, content, type, noted_at, is_important, tags, related_data | belongsTo child, user | Scopes (`ofType`, `important`, `forChild`, `dateRange`), tagging, excerpt attribute |
| **InsulinType** | name, description, onset, peak, duration, is_active | hasMany basalDoses, meals, snacks, randomChecks | Active/inactive scopes, SoftDeletes |
| **Photo** | child_id, user_id, file_path, thumbnail_path, taken_at, caption, file_size, mime_type | belongsTo child, user | URL accessors, thumbnail accessors, formatted size, SoftDeletes |

<sub>*Recurrence fields: `recurrence_pattern`, `recurrence_interval`, `recurrence_days`, `recurrence_end_date`, `recurrence_parent_id`, `recurrence_exdates`*</sub>

---

## 2. Routes

| File | Route | Middleware | Purpose |
|------|-------|------------|---------|
| `routes/web.php` | GET `/` | guest | Welcome (Inertia) |
|  | GET `/dashboard` | auth:sanctum, verified | User dashboard |
|  | Jetstream auth routes | guest / auth | Registration (disabled), Login, Email verification, Password reset, Password confirm, 2FA, Profile, API tokens, Browser sessions, Account deletion |
| `routes/api.php` | GET `/api/user` | auth:sanctum | Current user for SPA/mobile |
| `routes/console.php` | `artisan inspire` | CLI only | Inspirational quote |

No REST resources are yet exposed; controllers can be added safely.

---

## 3. Database Schema (Migrations)

Chronological snapshot (high-level):

1. Core Jetstream tables: `users`, cache & jobs, tokens.
2. **Children** table (FK → users).
3. Add `role` enum to users.
4. **Photos**, **InsulinTypes**, **Meals**, **Snacks**, **RandomChecks**, **BasalDoses**, **Notes**, **AuditLog**.
5. Fix-up migrations: `fix_migration_discrepancies`, `remove_injection_site_from_basal_doses`.
6. Additional schema updates: `add_is_manual_entry_to_basal_doses`, `add_user_id_to_photos`, `add_unique_constraint_to_insulin_types_name`.
7. Appointments & AppointmentReminders initial creation and subsequent `add_*` migrations (e.g. for recurrence, alerts, notes).

All tables use timestamps and soft-deletes where relevant.

---

## 4. Automated Test Coverage

### A. Model Feature Tests (`tests/Feature/Models`)
* `AppointmentTest` – 14 scenarios (CRUD, scopes, recurrence, state changes).
* `AppointmentReminderTest` – 12 scenarios (status mutations, scopes, next scheduled).
* `ChildTest` – create, relationship, age calculation.
* `BasalDoseTest` – feature tests (CRUD, scopes, accessors, date formatting).
* `MealTest` – feature tests (CRUD, scopes, status, post-meal BG time).
* `SnackTest` – feature tests (CRUD, scopes, blood glucose & insulin data, time difference).
* `RandomCheckTest` – feature tests (CRUD, scopes, context, target-range).
* `NoteTest` – feature tests (CRUD, scopes, tagging, excerpt).
* `InsulinTypeTest` – feature tests (CRUD, validation, unique, scopes, SoftDeletes).
* `PhotoTest` – feature tests (CRUD, relationships, accessors, file handling, SoftDeletes).

### B. Jetstream/Auth Feature Tests
* `RegistrationTest` – skipped (registration disabled).
* `AuthenticationTest`
* `PasswordConfirmationTest`
* `PasswordResetTest`
* `ProfileInformationTest`
* `TwoFactorAuthenticationSettingsTest`
* `ApiTokenPermissionsTest`
* `CreateApiTokenTest`
* `DeleteApiTokenTest`
* `BrowserSessionsTest`
* `DeleteAccountTest`
* `EmailVerificationTest`
* `UpdatePasswordTest`

### C. Example Tests
Laravel defaults – smoke tests.

Overall test suite validates domain entities plus security/session flows.

---

## 5. End-User Functionalities

1. **Family Management** – Guardians manage Child profiles with photos.
2. **Medical Tracking** – Appointments (incl. recurring) + automatic reminders; logging of insulin doses, meals, snacks, random BG checks, notes, photos.
3. **Alerting** – Hypo/hyper glucose alerts; reminder dispatch via email/SMS/Telegram/push.
4. **Dashboards** – Inertia + Tailwind screens showing upcoming events & recent data.
5. **Role-based Access** – Guardian vs Doctor vs Admin.
6. **API Access** – Sanctum tokens for mobile apps.
7. **Audit & Soft Deletes** – Historical tracking, safe deletions.
8. **Security** – 2-FA, session management, password policies.

---

## 6. QA / Future Extensions

* Add HTTP/controller tests once endpoints exist.
* Expand coverage for nutrition & random-check models.
* Load/perf tests for reminder generation.
* E2E browser tests for SPA flows.

### Planned Enhancements (from `requirements.md`)

* Support **multiple children** per guardian view layer.
* **Direct Bluetooth glucometer import**.
* Seven-day PDF export with chart (3 series) reflecting filter.

---

> **Need more detail?** Ping @maintainers in Slack or open an issue.
