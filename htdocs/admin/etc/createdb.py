#! /usr/bin/python3

import sqlite3
import bcrypt
import datetime

# Path to the SQLite database in the etc directory
DB_PATH = "users.db"

# Function to hash a password
def hash_password(password):
    return bcrypt.hashpw(password.encode('utf-8'), bcrypt.gensalt()).decode('utf-8')

# Create the initial database and the users table
def create_database():
    conn = sqlite3.connect(DB_PATH)
    cursor = conn.cursor()

    # Create the users table
    cursor.execute('''
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            fullname TEXT NOT NULL,
            role TEXT CHECK(role IN ('user', 'admin')) NOT NULL,
            created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            last_edited TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            edited_by TEXT,
            password TEXT NOT NULL
        )
    ''')

    # Check if the initial admin user already exists
    cursor.execute("SELECT * FROM users WHERE username = 'admin'")
    admin_exists = cursor.fetchone()

    if not admin_exists:
        # Hash the initial admin password
        hashed_password = hash_password("admin")

        # Insert the initial admin user into the users table
        cursor.execute('''
            INSERT INTO users (username, fullname, role, created, last_edited, edited_by, password)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ''', ("admin", "Administrator", "admin", datetime.datetime.now(), datetime.datetime.now(), None, hashed_password))

        conn.commit()
        print("Initial admin user created.")
    else:
        print("Admin user already exists.")

    conn.close()

# Run the function to create the database and the admin user
if __name__ == "__main__":
    create_database()
