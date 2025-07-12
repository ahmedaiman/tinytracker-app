# TinyTracker Dashboard Components

## Quick Actions

### Global Quick Actions
- **Add New Entry** (Floating Action Button)
  - Log Meal
  - Log Snack
  - Log Blood Glucose
  - Record Insulin Dose
  - Add Note
  - Schedule Appointment

### Appointment Actions
- Confirm Appointment
- Reschedule Appointment
- Cancel Appointment
- Add to Calendar
- Set Reminder

### Meal/Snack Actions
- Log Pre-meal BG
- Log Post-meal BG
- Log Insulin Dose

### Blood Glucose Actions
- Log Reading
- Add Context (before meal, after meal, before bed, etc.)
- Flag as High/Low
- Add Notes

## Dashboard Cards

### 1. Today's Overview Card
- Upcoming appointments
- Meals/snacks due
- Next insulin dose
- Recent BG readings
- Quick action buttons

### 2. Blood Glucose Tracker Card
- Current BG reading
- Trend indicator (↑/↓)
- Time since last reading
- Quick log button
- 24h graph view toggle

### 3. Meal & Insulin Summary Card
- Meals logged today
- Total carbs/sugars
- Insulin administered
- Next meal time
- Quick add meal button

### 4. Appointments Card
- Next appointment (today/tomorrow)
- Doctor's name
- Time until next appointment
- Quick actions (call, directions, reschedule)
- View all appointments link

### 5. Alerts & Reminders Card
- Upcoming medication reminders
- Unacknowledged alerts
- System notifications
- Snooze/Dismiss actions

### 6. Recent Activity Card
- Last 5 logged entries (meals, BG, notes)
- Time since last entry
- Quick access to details
- Filter by activity type

### 7. Child Profile Card (Multiple Children)
- Current child's photo
- Age
- Last logged BG
- Quick stats (meals today, etc.)
- Switch child button

### 8. Insulin On Board (IOB) Card
- Active insulin amount
- Time until clear
- Last dose time
- Type of insulin
- Graph of insulin activity

### 9. Weekly Summary Card
- BG averages (day/night)
- Meal patterns
- Insulin totals
- Appointment summary
- Expand to detailed view

### 10. Quick Notes Card
- Recent notes (last 3)
- Quick add button
- Important flags
- View all notes link

## Implementation Notes
- Cards should be draggable/reorderable
- Each card should have a settings/configure option
- Responsive design for mobile/tablet/desktop
- Role-based visibility (Guardian/Doctor/Admin)
- Light/dark mode support
- Data refresh indicators

## Accessibility Features
- Keyboard navigation
- Screen reader support
- High contrast mode
- Adjustable text sizes
