#! /usr/bin/python3
import sqlite3
import bcrypt
from datetime import datetime

# Connect to the existing SQLite database (users.db)
def connect_db():
    conn = sqlite3.connect('/var/www/pactest/admin/etc/users.db')  # Path to your existing database
    return conn

# Function to create a new user
def create_user(fullname, username, password):
    conn = connect_db()
    cursor = conn.cursor()

    # Check if username already exists
    cursor.execute('SELECT username FROM users WHERE username = ?', (username,))
    if cursor.fetchone():
        print(f"Error: Username '{username}' already exists.")
        conn.close()
        return

    # Hash the password using bcrypt
    hashed_password = bcrypt.hashpw(password.encode('utf-8'), bcrypt.gensalt())

    try:
        # Insert new user into the database with role 'user'
        cursor.execute('''
            INSERT INTO users (fullname, username, password, role, created, last_edited, edited_by)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ''', (
            fullname,
            username,
            hashed_password.decode('utf-8'),
            'user',  # Set role to 'user'
            datetime.now(),  # Created timestamp
            datetime.now(),  # Last edited timestamp
            None  # No 'edited_by' on initial user creation
        ))
        conn.commit()
        print(f"User '{username}' created successfully!")
    except Exception as e:
        print(f"Error creating user: {e}")
    finally:
        conn.close()

# Function to ask for user input and create a new user
def ask_user_details():
    fullname = input("Enter full name: ")
    username = input("Enter username: ")

    # Prompt for password twice to confirm
    password = input("Enter password: ")
    password_confirm = input("Confirm password: ")

    if password != password_confirm:
        print("Error: Passwords do not match!")
        return

    # Create user
    create_user(fullname, username, password)

# Main function to run the script
if __name__ == "__main__":
    ask_user_details()
