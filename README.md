# SnowTricks

## Description

SnowTricks is a collaborative site aimed at raising public awareness about the sport and assisting in the learning of tricks.

## Sommaire

- [Description](#description)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Documentation](#documentation)

## Installation

1. Clone the repository:
    ```bash
    git clone https://github.com/meline-p/snowtricks.git
    ```

2. Navigate to the project directory:
    ```bash
    cd repository
    ```

3. Install dependencies with Composer:
    ```bash
    composer install
    ```

4. Configure the environment by copying the .env file:
    ```bash
    cp .env .env.local
    ```

5. Update the database:
    ```bash
    php bin/console doctrine:migrations:migrate
    ```

6. (Optional) Load test data:
    ```bash
    php bin/console doctrine:fixtures:load
    ```

7. Install MailDev with npm:
    MailDev installation : https://github.com/maildev/maildev
    ```bash
    npm install -g maildev
    ```

## Configuration

- `DATABASE_URL` : Database connection URL.
- `MAILER_DSN` : Mail service URL.

Ensure these parameters are configured in the .env.local file.


## Usage

### Terminal 1 : Launch the website
Start the local server:
    ```bash
    symfony serve -d
    ```

Access the application via your browser at http://localhost:8000.

### Terminal 2 : Launch MailDev

```bash
maildev
```

Access the MailDev interface at http://localhost:1080/.


## Documentation

To generate documentation, use phpDocumentor.
You can download the latest PHAR file from https://phpdoc.org/phpDocumentor.phar and put it at the root of the project.
Execute this command:

```bsh
php phpDocumentor.phar run -d ./src -t docs/
```

Access the generated documentation in the docs/index.html directory. 
Launch Go Live on Visual Studio Code and access the online documentation in the docs directory.