server
======

server api made with Symfony3, FOSRestBundle and Doctrine

## Notes

I've been using PHP 5.6 (with phpunit 5.7)

I haven't used the build-in `Form` validation

you may have to set the permissions in the var folder as per symfony install

## Install

### dependencies

`composer install`
(requires composer)

### init database

create folder '~/var/data' (a .keep could be added here and in .gitignore)
`mkdir var/data`
init DB:
`bin/console doctrine:database:create`
`bin/console doctrine:schema:create`
(you might need to chmod the folder/DB file if running the server with a different user)

## Getting started, running (after install)

`bin/console server:run`
will serve on localhost:8000 

## Testing

there is a `phpunit-5.7.phar` committed for convenience
`php phpunit-5.7.phar`
will run the tests.

