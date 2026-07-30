# CLAUDE.md

# SIMASET Kenco

## Project Goal

An internal web system for recording and auditing the assets of
PT. Kenco Manufactur Indonesia. Every asset carries a QR label; scanning it opens
the record, and stock opname is performed from there.

Named SIMASET Kenco — Sistem Manajemen Aset. It was called "STO Asset Inventory"
until stock opname turned out to be one module among several, with maintenance,
transfer and disposal ahead of it. Do not name the system after a feature.

The application must prioritize:

* Simplicity
* Performance
* Maintainability
* Scalability
* Professional UI
* Responsive layout
* Enterprise architecture

This is an internal business application, **not** a marketing website.

---

# General Rules

Always prioritize:

* Clean code
* Readable code
* Modular architecture
* Reusable components
* Consistent naming
* Performance
* Security

Avoid unnecessary complexity.

---

# Tech Stack

Backend

* Laravel
* PHP 8.3+

Frontend

* Blade
* Vanilla JavaScript
* Hand-written CSS driven by design tokens — no CSS framework

There is no build step. `public/css/tokens.css` holds the tokens and the
`@font-face` rules; `public/css/app.css` holds everything else and is linked
after it. Both are served straight from `public/`, fingerprinted by
`@assetUrl` — see `FONTS.md` before touching either font file.

Database

* MySQL

Package

* Laravel Breeze
* Simple QR Code

Exports are CSV, written with `fputcsv` straight to a streamed response. There
is no Laravel Excel and no PDF package: nothing has needed xlsx or PDF output
yet, and neither should be added on spec.

---

# UI Philosophy

The interface must feel like a modern ERP system.

Characteristics:

* Professional
* Minimal
* Clean
* Functional
* Data-focused
* Easy to navigate

The interface should never look like a landing page.

---

# Theme

Primary Color

Dark Navy

`#163A5F`

Primary Hover

`#1E4A73`

Primary Active

`#0F2D4A`

Background

`#F5F7FA`

Surface

`#FFFFFF`

Border

`#D9E2EC`

Primary Text

`#1F2937`

Secondary Text

`#6B7280`

Success

`#16A34A`

Warning

`#F59E0B`

Danger

`#DC2626`

---

# Language

The interface is Indonesian. Labels, headings, buttons, empty states, flash
messages and CSV headers are written in Indonesian directly in the templates —
there is no translation layer for our own copy and none is wanted.

Framework messages come from `lang/id` (`validation.php`, `auth.php`,
`passwords.php`) and need `APP_LOCALE=id`. That value lives in `.env`, which the
deploy never rewrites, so changing it means editing the host by hand and
rebuilding the config cache — see DEPLOYMENT.md.

Only the validation rules this app uses are translated; the rest fall back to
English, which is correct for a message nobody has had reason to phrase yet.
Field names belong in the `attributes` map so a failure names the label the user
sees rather than a database column.

Kept in English on purpose:

* Role names — identifiers from the access matrix, not copy
* Loanwords Indonesian offices already use: Dashboard, Status, Brand, Model,
  PIC, Filter, Reset, Export, Stock Opname, Maintenance, Asset

The company name is `config('app.company')`, the system name is
`config('app.name')`. Never type either into a template.

---

# Typography

Font

Inter

Font Weight

400

500

600

700

Do not use decorative fonts.

---

# Visual Style

Use

* Flat Design
* Simple Cards
* Soft Border
* Soft Shadow only when necessary
* Rounded Corner (8px)
* Consistent spacing
* Professional icons

Do NOT use

* Glassmorphism
* Heavy Shadow
* Neon Color
* Blur Effect
* Fancy Animation
* Floating Cards
* Gradient Background
* Transparent Panel
* Colorful Dashboard
* Over-designed UI

---

# Layout

Desktop Layout

Top Navbar

↓

Left Sidebar

↓

Main Content

↓

Footer

Sidebar

* Fixed
* Collapsible

Navbar

Contains

* Page Title
* Search
* Notification
* User Profile

Content

* Breadcrumb
* Page Header
* Action Button
* Card
* Table
* Form

---

# Sidebar Menu

Dashboard

Master Data

* Asset
* Category
* Department
* Location
* Brand

Transactions

* Stock Opname
* Maintenance
* Asset Transfer

Reports

Settings

Users

Profile

Maintenance, Asset Transfer, Reports and Settings are placeholders — rendered
with a `soon` badge and no route. Keep them visible so the shape of the system
is legible, but do not link them until the module exists.

---

# Dashboard

Dashboard should display:

* Total Assets
* Active Assets
* Maintenance Assets
* Missing Assets
* STO Progress
* Assets by Department
* Assets by Category
* Recent Activities

Avoid unnecessary charts.

Prioritize useful information.

---

# Asset Module

Asset List

Create Asset

Edit Asset

Delete Asset

Asset Detail

Upload Photo

Generate QR

Print QR

Download QR

---

# QR Code

QR Code stores only a unique URL.

Example

https://domain.com/asset/AST-KMI-0001

Never store asset information directly inside the QR Code.

That URL is reachable without a session, because a label nobody can scan is a
label nobody uses. `AssetController::show` decides what comes back: staff who
may see the record get the full detail page, everyone else gets a summary with
the asset number, name, category, status, condition and the date of the last
stock take. Location, PIC, purchase date, specification and photo stay behind
the login — a sticker on a rack can be photographed by any visitor walking past.

The route is throttled and carries `noindex`, since asset numbers are sequential
and therefore trivially enumerable.

---

# QR Flow

Create Asset

↓

Save

↓

Generate QR

↓

Preview

↓

Download

↓

Print

↓

Attach to Asset

---

# Stock Opname Flow

Login

↓

Scan QR

↓

Open Asset Detail

↓

Verify Asset

↓

Update Condition

↓

Take Photo (Optional)

↓

Save STO

↓

History Updated

---

# Asset Detail

Display

Asset Photo

Asset Number

Asset Name

Category

Brand

Model

Specification

Department

Location

PIC

Status

Condition

Purchase Date

History

QR Preview

Actions

Start STO

Print QR

Download QR

Edit Asset

---

# Tables

Every table must have

Search

Filter

Pagination

Sorting

Export

Responsive

Sticky Header if needed

---

# Forms

Use consistent spacing.

Group related fields.

Show validation clearly.

Required fields must be marked.

---

# Buttons

Primary

Dark Navy

Secondary

Light Gray

Danger

Red

Success

Green

Avoid too many button colors.

---

# Icons

Use Bootstrap Icons only.

Keep icon usage consistent.

The font is subsetted to the glyphs actually referenced, so a `bi-` class that
is not listed in `app.css` renders a blank space with no error. Adding an icon
means rebuilding the subset — `FONTS.md` has the steps, and `IconCoverageTest`
fails if you forget.

---

# Cards

Cards should be simple.

No gradients.

No transparency.

No excessive padding.

---

# Authentication

Use Laravel Breeze.

Role Based Access

Super Admin — full access, all modules, user management, settings.

Admin — manage master data, transactions, reports. No user/role management.

Auditor — read all asset data, run/update Stock Opname, no edit master data.

Department — read/edit own department's assets only, request transfer/maintenance.

User — read-only own department assets, no edit.

Enforce via Laravel Policy per model (Asset, Category, Department, Location,
Brand, StockOpname, User). Never check role inline in Blade/Controller — always
through Policy/Gate.

Brand, Category, Department and Location share the `ManagesMasterData` trait, so
their matrix is defined in one place.

Two rules that are easy to break by accident: the last Super Admin cannot be
deleted, from the user list or from their own profile — ask
`User::isLastSuperAdmin()`, never re-implement the count. And role names
(Super Admin, Admin, Auditor, Department, User) stay in English, because they are
identifiers from this matrix rather than interface copy.

Protect every route.

---

# Testing

Use Pest.

Required coverage

* Feature test per Controller action (CRUD, auth, authorization)
* Test per Service Class, covering what only that service decides
* Policy test per role x action matrix

`tests/Unit` stays framework-free, so service tests live in
`tests/Feature/Services` — both services talk to the database and the disk, and
a framework-free test of them would assert nothing.

Run tests before merge. No PR without passing tests.

---

# File Upload

Asset Photo

* Store on local disk (`storage/app/public/assets`), symlinked via `php artisan storage:link`
* Allowed mime: jpg, jpeg, png, webp
* Max size: 2MB
* Validate via Form Request (`mimes`, `max`, `dimensions`)
* Filename: hash-based, never trust original filename

QR Code

* Generate via Simple QR Code package, format PNG, stored on generate, regenerate only if asset number changes

---

# Coding Standard

Use

Resource Controller

Form Request

Policy

Service Class

Route Model Binding

Eloquent Relationship

Laravel Validation

Avoid

Business Logic inside Controller

Duplicated Code

Long Functions

Magic Numbers

Hardcoded Values

---

# Performance

Always use

Pagination

Eager Loading

Database Index

Lazy Loading when appropriate

Cache only when necessary

---

# Security

Validate all inputs.

Sanitize uploaded files.

Use CSRF Protection.

Use Authorization.

Never trust client-side validation.

---

# Future Ready

The architecture must allow future modules without major refactoring.

Possible future modules

* Borrowing
* Calibration
* Preventive Maintenance
* Disposal
* Asset Audit
* Notifications
* Email Reminder
* Multi Branch
* REST API
* Mobile Application

Always build with scalability in mind.
