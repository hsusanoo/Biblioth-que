# Biblioth-que

## Overview
School library management system, main features:
- Managing books in stock with the ability to import and/or export data
from/to excel sheets.
- Browsing books on the front-end side with multiple filters.
- Admin dashboard displaying an overview on the data matching the admin’s
needs.
- Managing other employees accounts with admin access.

## Build

You need to have Node.js and NPM installed.
 - Run ``composer install`` and ``npm install``
 - This app uses mysql for the database, create an empty database with whatever name you want.
 - Go edit ``.env`` file, replace ``DB_CREDENTIALS`` with ``mysql://USERNAME:PASSWORD@127.0.0.1:3306/DB_NAME``, replace ``MAIL_HERE`` as stated in the comment above depending on the service you want to use.
 - Generate database tables from php entities by running ``php bin/console doctrine:schema:update --force``
 - Generate dummy data by running ``php bin/console doctrine:fixtures:load``, admin credentials are "admin@gmail.com" with the password "admin", this can be changed in ``src\DataFixtures\AppFixtures``
 - Run app : ``npm run build && symfony serve``

## Screenshots

![Dashboard](screenshots/Dashboard.png)

![Edit_book](screenshots/Edit_book.png)

![Responsables](screenshots/Responsables.png)

![Inventory](screenshots/Inventory.png)

![Export](screenshots/Export.png)

![Schema](screenshots/schema.png)





