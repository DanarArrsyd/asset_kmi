# CLAUDE.md

# STO Asset Inventory System

## Project Goal

Build a professional web-based Asset Inventory & Stock Take (STO) system for internal company use.

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
* Bootstrap 5
* Vanilla JavaScript

Database

* MySQL

Package

* Laravel Breeze
* Simple QR Code
* Laravel DomPDF
* Laravel Excel

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

https://domain.com/asset/AST000001

Never store asset information directly inside the QR Code.

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

Enforce via Laravel Policy per model (Asset, Category, Department, Location, Brand). Never check role inline in Blade/Controller — always through Policy/Gate.

Protect every route.

---

# Testing

Use Pest.

Required coverage

* Feature test per Controller action (CRUD, auth, authorization)
* Unit test per Service Class
* Policy test per role x action matrix

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
