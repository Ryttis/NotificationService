# NotificationService

## Table of Contents

1. [Project Overview](#project-overview)
2. [Setup and Installation](#setup-and-installation)
3. [Configuration](#configuration)
4. [Usage](#usage)
5. [Testing](#testing)
6. [Development](#development)
7. [Contributing](#contributing)
8. [License](#license)

## Project Overview

**NotificationService** is a robust notification system designed to handle sending notifications through various channels including SMS, email, and push notifications. It integrates with Symfony Messenger to handle retry mechanisms and delayed message dispatching.

### Features

- Send notifications via SMS, email, push, and Facebook Messenger
- Retry failed messages
- Delay message dispatching
- Integration with Twilio for SMS, AWS SES, Sendgrid, and SMTP for email

## Setup and Installation

### Prerequisites

- PHP 7.4 or higher
- Composer
- Docker (for containerized environment)
- Twilio Account (for SMS)
- AWS SES/Sendgrid/SMTP account (for email)

### Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/yourusername/NotificationService.git
   cd NotificationService
