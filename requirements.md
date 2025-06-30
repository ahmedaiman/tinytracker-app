System Requirements Specification — “TinyTrack” Pediatric T1D Logbook v3.0
1 Purpose
Single-patient web application for guardians to capture, validate, store, and report all data required to manage a child with type-1 diabetes. System enforces meal windows, insulin logging, appointment tracking, photo archiving, and sends time-based Telegram bot notifications.

2 Scope
Single-tenant deployment. Stack: Laravel 12, PHP 8.3, MySQL 8, Redis, Vue 3 + Inertia, Tailwind CSS, Docker/Sail. Accessible via any ES 2019-capable desktop or mobile browser.

3 Definitions
Term	Definition
BG	Blood glucose in mg/dL
Meal Windows	Breakfast 07:00–08:59, Lunch 11:00–15:59, Dinner 18:00–20:59
Rapid Insulin	Meal-time insulin (e.g., Novorapid)
Basal Insulin	Daily long-acting insulin (e.g., Lantus)

4 User Classes
Role	Privileges
Guardian	Full CRUD, exports, Telegram configuration
Doctor	Read-only dashboards and exports
Admin	User management, system settings, insulin catalog maintenance

5 Operating Environment
Linux host or WSL2. Reverse-proxy TLS termination (nginx/Traefik). Telegram Bot API reachability. Optional SMTP for password reset.

6 Assumptions
One child profile. Local timezone UTC+05:00.

7 Functional Requirements
7.1 Child Profile
Fields: name, date_of_birth, gender, notes.

Passport photo upload ≤ 2 MB JPG/PNG; system stores original + 150×150 thumbnail.

Dashboard shows age in years-months-days and latest photo.

7.2 Meal Logging
System auto-assigns meal_type based on meal_time within windows.

Required sequence per meal:

Pre BG → record status Open.

Rapid insulin units 0–20 and injection timestamp.

Food description. Optional carbs (g) and sugars (g).

Post BG entered ≥ 120 min after meal_time → status Closed.

One record per meal_type per calendar date.

Entries outside windows require override_flag “other_meal”; still enforce one record per date per type.

7.3 Snack Logging
Timestamp, food description required.

Optional BG (pre, post).

7.4 Random BG Check
Timestamp and BG mandatory.

Optional note.

7.5 Insulin Catalog
Table insulin_types(id, name, rapid_flag BOOLEAN).

Seed rows: Novorapid (rapid_flag 1), Lantus (rapid_flag 0).

Admin may add future brands.

7.6 Basal Insulin Logging
One entry per date. Fields: insulin_type_id (must be basal), units 0–30, injected_at timestamp.

7.7 Notes Journal
Free-text note ≤ 500 chars tied to calendar date; multiple per day.

7.8 Appointment Scheduling
Fields: doctor_type, doctor_name, location, start_at, note.

Creates two queued Telegram messages: −24 h, −2 h.

7.9 Telegram Notification Channel
Settings: bot_token, chat_id.

Laravel Notifications → Telegram via HTTPS POST https://api.telegram.org/bot{token}/sendMessage.

Failures logged; no email fallback.

7.10 Reporting
Filter parameters: date range, record type, meal_type, insulin_type.

Line chart: datetime vs BG with series Pre-meal, Post-meal, Random (Chart.js).

Table: timestamp, category, meal_type, food, BGs, carbs, sugars, insulin units, notes.

Export current filter to PDF and XLSX.

7.11 Security
Email/password + optional TOTP.

Sanctum token auth for REST API.

Rate-limited login.

All traffic via HTTPS.

7.12 Audit and Backup
Soft deletes on all data tables.

audit_log(table_name, record_id, action, diff_json, user_id, created_at).

Nightly encrypted MySQL dump retained 30 days.

8 Data Model
Table	Key Columns	Notes
children	id, name, dob, gender, notes	single child
photos	id, child_id, file_path, taken_at	original + thumb
insulin_types	id, name, rapid_flag	seed Novorapid, Lantus
meals	id, child_id, meal_time, meal_type ENUM(breakfast,lunch,dinner,other), pre_bg, novorapid_units, novorapid_injected_at, post_bg, food_desc, carbs, sugars, status ENUM(open,closed), override_flag BOOLEAN	unique (child_id, date(meal_time), meal_type)
snacks	id, child_id, snack_time, food_desc, pre_bg, post_bg	
random_checks	id, child_id, check_time, bg_value, note	
basal_doses	id, child_id, dose_date DATE UNIQUE, insulin_type_id, units, injected_at	
notes	id, child_id, note_text, created_at	
appointments	id, child_id, doctor_type, doctor_name, location, start_at, note	
users	id, name, email, password_hash, role ENUM(guardian,doctor,admin)	
audit_log	id, table_name, record_id, action, diff_json, user_id, created_at	

9 Business Rules
BG value range 20–600.

novorapid_units 0–20; basal units 0–30.

Post BG accepted only if timestamp ≥ 120 min after meal_time.

Meal cannot close without novorapid_units and post BG.

New meal cannot open while previous main-meal record status ≠ Closed.

One basal_dose per date.

Appointment reminders fire via Telegram exactly −24 h and −2 h using queued jobs respecting UTC+05:00 conversion.

10 Non-Functional Requirements
Category	Requirement
Performance	95-th percentile page render ≤ 300 ms
Availability	≥ 99 % monthly uptime
Scalability	Retain ≥ 10 years of daily data without schema change
Security	OWASP Top-10 compliance, host-level disk encryption optional
Maintainability	PSR-12, 80 % automated test coverage
Portability	docker-compose; no host PHP/Node required
Privacy	GDPR/PDPA export and erase endpoints

11 External Interfaces
Telegram Bot API for notifications.

REST API v1 protected by Sanctum.

SMTP (optional) for password reset only.

12 Constraints
ES 2019 browser required.

Image upload ≤ 2 MB.

All timestamps stored UTC, converted to UTC+05:00 in UI.

13 Acceptance Criteria
Profile creation displays age accurate to the day.

Breakfast logged at 07:15 with pre BG 140 → record Open; lunch creation blocked.

Novorapid 3 U logged; post BG 09:20 → breakfast Closed; lunch allowed.

Dinner attempted at 17:45 rejected; override_flag required.

Basal Lantus 6 U recorded once; second attempt same date rejected.

Appointment 2025-07-05 10:00 triggers Telegram messages at 2025-07-04 10:00 and 2025-07-05 08:00; logs show HTTP 200.

Seven-day PDF export reflects filter, line chart shows three series.

14 Future Extensions
Support multiple children.

Direct Bluetooth glucometer import.

Native mobile app with push notifications.