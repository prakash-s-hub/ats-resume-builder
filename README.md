# 📄 CareerCraft — ATS-Friendly Resume Builder

A streamlined, single-file PHP resume builder and live preview application designed to help job seekers create clean, professional, and Applicant Tracking System (ATS) optimized resumes in minutes.

---

## ✨ Features

- **Multiple Professional Templates:** Choose from 4 distinct layouts (*Harvard, McKinsey, Google, Stanford*).
- **Real-Time Interactive Builder:** Add, edit, or delete personal info, work experience, education, and skills seamlessly using background AJAX requests.
- **Live Live Document Preview:** Instant visual feedback as you update your resume details.
- **Custom Styling Options:** Customize accent colors and typography fonts (*Inter, Lato, Merriweather*) on the fly.
- **Print & PDF Export:** Optimized print stylesheets that hide the dashboard UI and export a clean, paper-ready resume document.
- **Saved Resumes Dashboard:** Easily manage and switch between multiple saved resumes.

---

## 🛠️ Tech Stack

- **Backend:** PHP (Vanilla, MySQLi, AJAX router inside a single file)
- **Database:** MySQL / MariaDB (Relational structure with cascading deletes)
- **Frontend:** HTML5, CSS3 (CSS Grid, Flexbox, Custom Variables)
- **JavaScript:** Fetch API for asynchronous state updates
- **Libraries & CDNs:** FontAwesome 6, Google Fonts (*Inter, Merriweather, Lato, DM Sans*)

---

## 🗂️ Project Structure

This project is packaged as a **single-file PHP application** for simple deployment and portability:

```text
naveen_resume/
│
└── index.php    # Handles routing, database interactions, AJAX endpoints, builder UI, and document preview
