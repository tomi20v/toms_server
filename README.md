server
======

server api made with Symfony3, FOSRestBundle and Doctrine

## Notes

I've been using PHP 5.6 (with phpunit 5.7)

I haven't used the build-in `Form` validation

you may have to set the permissions in the var folder as per symfony install

the list action will return the 'content' field as 'resource #x'. this should be filtered

the upload will not check if the file is a pdf, this would be a must to do on the server side as well

I'd move some logic from the controller to a separate service, eg. where it makes calls to Doctrine. the controller should be slimmer.

## Install

### dependencies

`composer install`
(requries composer)

### init database

create folder '~/var/data'
`mkdir var/data`
init DB:
`bin/console doctrine:database:create`
(you might need to chmod the folder/DB file if running the server with a different user)

## Getting started, running (after install)

`bin/console server:run`
will serve on localhost:8000 

## Testing

there is a `phpunit-5.7.phar` committed for convenience
`php phpunit-5.7.phar`
will run the tests.

