# Twilio Chat Application

This is a PHP-based chat application that integrates with Twilio for sending and receiving SMS, and Pusher for real-time notifications. The application now includes Docker support for easier setup and deployment.

## Prerequisites

Before you begin, ensure you have the following installed on your local machine:

- Docker
- Docker Compose

## Setup Instructions

### Step 1: Clone the Repository

Clone this repository to your local machine using the following command:

```
git clone https://github.com/2way-twilio-chat.git

cd 2way-twiili-chat
```


### Step 2: Configure the Environment

- Copy the .env-example file to a new file named .env:
- cp .env-example .env

```
DB_HOST=""
DB_NAME=""
DB_USER=""
DB_PASS=""

PUSHER_APP_ID="xxxxxx"
PUSHER_KEY="xxxxxxxxxxxxxx"
PUSHER_SECRET="xxxxxxxxxxxxxx"
PUSHER_CLUSTER="xxx"

TWILIO_ACCOUNT_SID="xxxxxxxxxxxxxxxxxxxxxxxxxxxx"
TWILIO_AUTH_TOKEN="xxxxxxxxxxxxxxxxxxxxxxxxxxxx"
TWILIO_NUMBER="+xxxxxxxxxxxxxx"
```

### Step 3: Twilio Webhook Setup

Add following URL in your Twilio Number Incoming Message Webhook
```
http://[DomainName]/src/api/twillioInbound.php
```
### Step 4: Build and Run the Application with Docker

Run the application using Docker by executing the following command:

```
docker-compose up --build
```

Access the application with following credentials

```
Email : admin@test.com
Password : VahcDGNpBZMe
```
### Application Structure

- `includes/`: Contains reusable frontend components (header, footer, sidebar, etc.).
- `public/`: Publicly accessible files like CSS, images, and JavaScript.
- `src/`: Core application logic, including API endpoints, configuration, and database management.
    - `API/`: Handles API requests and responses.
    - `Config/`: Configuration files and helper functions.
    - `DB/`: Database management and migration scripts.
- `vendor/`: Composer dependencies.
- `terraform/`: Infrastructure as code (optional, if applicable).
- `.env`: Environment configuration file.
- `.env-example`: Template for the environment configuration.
- `docker-compose.yml`: Docker Compose setup.
- `Dockerfile`: Docker image build instructions.
- `entrypoint.sh`: Startup script for Docker containers.


### Usage

- Login: 
Navigate to login.php to log in.

- Chat: 
Once logged in, you can start a conversation, send and receive messages.


### Troubleshooting

Ensure that your .env file is correctly configured.

Check if the database tables are created and populated.

Make sure all dependencies are installed by running composer install.

