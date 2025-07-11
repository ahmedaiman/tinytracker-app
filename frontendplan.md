# Frontend Implementation Plan

This document outlines all frontend requirements and implementation details for TinyTracker, based on `features.md`. It adheres strictly to the existing Inertia + Tailwind theme.

---

## 1. Tech Stack & Tools

- Inertia.js + Vue 3 (default Jetstream stack)
- Tailwind CSS for styling
- Chart.js (via vue-chartjs) or ApexCharts for data visualization
- Flatpickr or Pikaday for date/time pickers
- Heroicons for icons
- Laravel Jetstream components for modals, forms, alerts
- Cypress for E2E testing
- Vue Test Utils + Jest for component unit tests

---

## 2. Layout & Navigation

- **Main Layout**: Navbar, sidebar (mobile drawer), footer
- **Navbar**: App logo, page title, user menu (profile, settings, logout)
- **Sidebar**: Links to Dashboard, Children, Appointments, Reminders, Basal Doses, Meals, Snacks, Random Checks, Notes, Photos, Settings
- **Responsive**: Collapse sidebar on mobile; use top menu

---

## 3. Pages & Views

### 3.1 Authentication & Profile
- Login, Forgot Password, Email Verification, Reset Password (Jetstream)
- Profile Settings, Two-Factor, API Tokens, Browser Sessions

### 3.2 Dashboard
- **Summary Cards**: # Children, Upcoming Appointments, Today’s BG Checks, Pending Reminders
- **Charts**:
  - Blood Glucose Trend (line chart)
  - Insulin Dosage by Type (bar chart)
  - Carbohydrates Intake (donut chart)
- **Recent Activity**: Table feed of latest Meals, Snacks, Random Checks
- **Alerts**: List of high/low BG alerts

### 3.3 Children
- **List View**: Table of children with actions (View, Edit, Delete)
- **Modals**: Create/Edit Child form (name, DOB, gender, photo upload)
- **Profile Page**:
  - Header with photo, name, age
  - **Tabs**: Appointments | Basal Doses | Meals | Snacks | Random Checks | Notes | Photos

### 3.4 Appointments & Reminders
- **Appointments List**: Filters (upcoming, past), table with pagination
- **Modal**: Create/Edit Appointment (fields: title, description, start/end, recurrence rule, type, status)
- **Calendar View** (optional): month/week view
- **Reminders**: Nested under Appointment detail, list and Create Reminder modal

### 3.5 Basal Doses, Meals, Snacks, Random Checks
- **Shared Pattern**:
  - Table view with filters (date, child)
  - Create/Edit modal forms (date/time picker, units, BG values, notes)
  - Scopes: child selection dropdown
  - Display computed fields (e.g., post-meal time diff)

### 3.6 Notes
- **List**: Table with tags, is_important filter
- **Modal**: Create/Edit Note with markdown editor or textarea + preview

### 3.7 Photos
- **Gallery**: Grid of thumbnails
- **Modal**: Upload Photo (file picker), caption, taken_at
- **Lightbox**: View full image
- Soft-delete and restore actions

### 3.8 Settings & Admin
- **Role Management** (Admins only): assign guardian/doctor
- **Global Settings**: alerts configuration, notification channels

---

## 4. Components

- **BaseLayout.vue**: nav + sidebar
- **DataTable.vue**: generic table with sorting, pagination
- **Modal.vue**: wrapper for Jetstream modal
- **FormInput.vue**: text, number, date, time inputs
- **SelectDropdown.vue**
- **FileUpload.vue**: photo picker + preview
- **ChartLine.vue**, **ChartBar.vue**, **ChartDonut.vue**
- **SummaryCard.vue**
- **AlertToast.vue**

---

## 5. State & Data Flow

- Use Inertia props for server data
- Local reactive state for form inputs
- Validation errors via Inertia form helper

---

## 6. UX & Accessibility

- Consistent theme: Tailwind color palette and typography
- Keyboard & screen-reader support on modals and forms
- Responsive breakpoints: mobile-first

---

## 7. Testing Strategy

- **Component Tests**: Jest + Vue Test Utils for critical components (DataTable, Modal, Chart)
- **E2E Tests**: Cypress covering key flows:
  - User login & dashboard load
  - Create/Edit/Delete flows for Children, Appointments, Doses, etc.
  - Chart rendering and data accuracy
  - Photo upload & gallery

---

## 8. Directory Structure

```
resources/
  js/
    Pages/
      Dashboard.vue
      Children/
      Appointments/
      BasalDoses/
      Meals/
      Snacks/
      RandomChecks/
      Notes/
      Photos/
      Settings.vue
    Components/
      DataTable.vue
      Modal.vue
      ChartLine.vue
      …
    Layouts/
      BaseLayout.vue
  css/
    app.css (Tailwind)
```

---

> All implementations must adhere to the existing theme; no breaking CSS or component overrides. Use existing Jetstream and Tailwind conventions for consistency.
