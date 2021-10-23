# Symfony Blog

I started to get familiar with the `Symfony` framework. 
As a starting point, I decided to create a system for a simple website.

## Approximate plan

Implementation steps:
- Install the framework `Symfony` and start the server
- Separation front and admin, each with its own a skeleton template
- Make registration, authentication, authorization (separate form for front and admin)
- Users with roles: `user`, `admin`, `superadmin`

In the admin area:
- Admin dashboard
- User management
- Pages management
- Blog management

---

## Install
```
git clone git@github.com:amberlex78/sf-cms.git
cd sf-cms
cp .env.example .env
composer install
yarn install
yarn encore dev
symfony server:start -d
```

## Create DB container
If you don't have `MySQL` server installed, you can `docker up` the `MySQL` container.

Database connection see in `docker-compose.yml` and config in `.env` files.
### Up
Docker up: `docker-compose up -d` or:
```
make init
```
### Down
Docker down: `docker-compose down --remove-orphans` or:
```
make down
```
See all command in `Makefile` file.

## Init DB after container up
Fill tables: `doctrine:fixtures:load`
```
symfony console d:f:l
```

### Before loading fixtures again, you should drop and update schema
Drop tables `doctrine:schema:drop`
```
symfony console d:s:d --full-database --force
```
Update tables: `doctrine:schema:update`
```
symfony console d:s:u --force
```

## Access to site

Front:
```
https://127.0.0.1:8000
```

Admin:
```
https://127.0.0.1:8000/admin
```

Users:
```
user@example.com   - User
admin@example.com  - Admin
sadmin@example.com - Super Admin
```
Password: `password`
