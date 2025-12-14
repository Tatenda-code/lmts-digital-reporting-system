# LMTS Digital Reporting System

## Overview

The **LMTS Digital Reporting System** is a production-ready web application designed and developed to replace a previously paper-based daily LMTS reporting process for a fiber Company based in South Africa.

The system enables users to submit daily operational data online, which is securely stored in a centralized database for fast retrieval, auditing, and reporting.

This project represents a real-world software solution that is actively used and handles thousands of records in a live environment.

---

## Problem Statement

The original LMTS workflow relied on manual paper forms for daily submissions. This approach resulted in:

* Delayed data submission and processing
* Risk of data loss and human error
* No centralized or searchable records
* Difficult reporting and auditing

---

## Solution

I designed and implemented a fully digital reporting system that:

* Replaces paper forms with online submissions
* Stores data in a structured relational database
* Improves speed, accuracy, and accountability
* Supports scalability as data volume grows

---

## System Features

* Secure online daily report submission
* Input validation on client and server side
* Centralized MySQL database
* Efficient handling of thousands of records
* Mobile-friendly user interface
* Structured backend logic for maintainability

---

## Technology Stack

### Backend

* PHP
* MySQL

### Frontend

* HTML5
* CSS3 (Tailwind CSS)
* JavaScript

### Tools

* Git & GitHub

---

## Database Design

The database was designed with scalability and data integrity in mind, using:

* Proper relational structure
* Indexed fields for faster querying
* Separation of concerns between data entities

*Sensitive production data and credentials are intentionally excluded from this repository.*

---

## Security Considerations

* No database credentials stored in the repository
* Sensitive files excluded using `.gitignore`
* Server-side validation of submitted data
* Repository contains structure and logic only

---

## Project Status

* ✔ Live and in daily use
* ✔ Actively handling thousands of entries
* ✔ Maintained and improved as requirements evolve

---

## Repository Structure

```
/ (root)
├── index.html           # Main landing page
├── form.html            # Daily report submission form
├── build.html           # Fiber link builds form
├── selection.html       # Page for selecting form 
├── gp_maintenance.html  # Fiber maintenance form for Gauteng Province
├── script.js            # Frontend JavaScript
├── submit.php           # Handles form submission
├── gp_submit.php        # Handles maintenance form submission for Gauteng Province
├── buildSubmit.php      # Handles build form submission
├── admin.php            # Admin dashboard
├── gp_admin.php         # Maintenance admin interface for Gauteng Province
├── buildAdmin.php       # Fiber link builds Admin interface
└── config.example.php   # Example config file (real credentials excluded)
```

---

## Author

**Tatenda Koki**
GitHub: [https://github.com/Tatenda-code](https://github.com/Tatenda-code)

---

*This repository demonstrates practical backend development, database design, and real-world problem solving using web technologies.*
