# Form_PHP
# 📌 Form Handling System with File Upload, Validation & Email (PHP + MySQL)

## 📖 Overview
This project is a complete **form handling system** built using **PHP and MySQL**.  
It supports:

- Input validation (email, phone, etc.)
- File upload handling
- Full CRUD operations
- Email sending using PHPMailer

The system ensures secure and structured data processing from user input to database storage and email notification.

---

## Features

### Form Validation
- Validates user inputs such as:
  - Email format
  - Phone number (10 digits)
- Prevents invalid data submission

---

### File Upload
- Upload files through form
- Stores files in `public/uploads/`
- Automatically creates directory if not present
- Generates unique file names using:
  - Timestamp
  - Random number

---

### CRUD Operations (MySQL)
- **Create** → Insert new form data  
- **Read** → Retrieve stored data  
- **Update** → Modify existing records  
- **Delete** → Remove records  

---

### Email Integration (PHPMailer)
- Sends email after form submission
- Uses SMTP configuration
- Can send:
  - Confirmation emails
  - Notifications

---

## Technologies Used

- PHP (Core PHP)
- MySQL
- PHPMailer
- HTML/CSS (Frontend)
- Apache (XAMPP)

---
## Workflow
User submits form
        ↓
Input validation (email, phone)
        ↓
File uploaded (temporary)
        ↓
move_uploaded_file() → permanent storage
        ↓
Data stored in MySQL
        ↓
Email sent using PHPMailer
