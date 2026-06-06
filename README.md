# 🔐 User Authentication System (PHP + MySQL + Redis)


A simple full-stack authentication system built using PHP, MySQL, and Redis.  

## 🚀 Features

- 📝 User Registration (with password hashing)
- 🔑 Secure Login (password_verify)
- 🔐 Token-based Authentication
- ⚡ Redis for session storage
- 💾 MySQL for persistent user data


## 🔄 How It Works

1. User registers → data stored in MySQL (password hashed)
2. User logs in → credentials verified
3. Server generates a **token**
4. Token stored in **Redis** (mapped to user ID)
5. Token sent to frontend and stored in **localStorage**
6. Profile page sends token → backend verifies via Redis → returns user data

## ⚙️ Setup

1. Install XAMPP and start Apache & MySQL  
2. Create a database named `intern_users` and a `users` table  
3. Install and start Redis server  
4. Enable Redis extension in PHP (`php.ini`)  
5. Place the project in `htdocs` and run via localhost 

## 📸 Screenshots

### Register Page
<p align="center">
  <img src="assets/Screenshots/Register page.jpeg" width="100%" />
</p>

### Login Page
<p align="center">
  <img src="assets/Screenshots/Login page.jpeg" width="100%" />
</p>

### Profile Page
<p align="center">
  <img src="assets/Screenshots/Profile page.jpeg" width="100%" />
</p>

---

