# P6_Snowtricks

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/5bbe46a5f41c456893df6564057633c6)](https://www.codacy.com/gh/boumebar/P6_Snowtricks/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=boumebar/P6_Snowtricks&amp;utm_campaign=Badge_Grade)

## Description

Creation of a community site about snowboard figures via Symfony.

## Installation

Clone or download the GitHub repository
```
    git clone https://github.com/boumebar/P6_Snowtricks
```
Install dependencies with composer:
```
    composer install
```

Configure the file .env
```
    DATABASE_URL=mysql:///db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7
```
Create the database
```
    php bin/console doctrine:database:create
```
Create database structure
```
    php bin/console doctrine:migrations:migrate
```
Install fixtures
```
    php bin/console doctrine:fixtures:load
```


## Congratulations the project is installed correctly, you can now start using :)