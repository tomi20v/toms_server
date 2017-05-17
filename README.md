server
======

server api made with Symfony3, FOSRestBundle and Doctrine

## Notes

you may have to set the permissions in the var folder as per default symfony install

## Install

### dependencies

`composer install`
(requires composer)

### init database

init DB:
`bin/console doctrine:database:create`
`bin/console doctrine:schema:create`
(you might need to chmod the folder/DB file if running the server with a different user)

## Getting started, running (after install)

### running the server

`bin/console server:run`
will serve on localhost:8000 

### access the webapp
navigate to
`http://localhost:8000/app/`

## Testing

there is a `phpunit-5.7.phar` committed for convenience
`php phpunit-5.7.phar`
will run the tests.

