[logo]:https://naps.rit.edu/logo.svg

# Atlas

![Logo][logo]

[![Build Status](https://travis-ci.org/ritstudentgovernment/atlas.svg?branch=master)](https://travis-ci.org/ritstudentgovernment/atlas)
[![StyleCI](https://github.styleci.io/repos/127938992/shield)](https://github.styleci.io/repos/127938992)
[![Maintainability](https://api.codeclimate.com/v1/badges/8c51b9c33513e17d478c/maintainability)](https://codeclimate.com/github/ritstudentgovernment/Atlas/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/8c51b9c33513e17d478c/test_coverage)](https://codeclimate.com/github/ritstudentgovernment/Atlas/test_coverage)

Atlas is a service that allows students to post locations around campus they like to rest! 

Now featuring more than just napping locations, students may find spots to recharge with food, coffee, or energy drinks.

Administrators can add and modify **Categories** of spots with their own Icons, configure them with different **Descriptors**, add quantifying **Types**, and set colors with different **Classification** levels.

## Configuration

This guide will help you configure your own instance of Atlas.

#### Prerequisites
* PHP 7.1+
* Composer
* npm
* Postgres


#### Installation:
1. Clone this repo
2. Configure the **.env** file to match your system's configuration
3. Run **composer install**, **php artisan migrate**, and **php artisan db:seed**
4. Run **php artisan jwt:secret**
5. Run **npm install** and **npm run dev**
6. Run **php artisan serve** to spin up the web server
7. Run **php artisan horizon &** in a new terminal tab to run the queue worker
8. Visit 127.0.0.1:8000 in your browser to verify everything is working properly
9. Configure authentication as outlined below

#### Authentication (SAML2)

The default application is setup to support [https://samltest.id](https://samltest.id) out of the box for local development testing.
In order to use this service you just have to upload the metadata for this application from your installation.

The default admin user when seeded in development is **Sheldon Cooper** from samltest.

The metadata is found at the '/saml2/metadata' route.

Save that file as XML and name it something unique, then [upload it](https://samltest.id/upload.php) to the samltest service.

Once that is done basic SAML auth should be configured.

When you're ready to move to a production environment you will need to modify this configuration. This is done by editing the .env
file attributes beginning with the 'SAML2' prefix and the 'config/saml2_settings.php file.'

\****Note***\* The SAMLController is not setup for SLO. If you would like to implement that you must do so yourself. Right now
the application only logs you out of itself, it does not contact the SAML IdP. (The [library](https://github.com/aacotroneo/laravel-saml2) being used does allow for this however)
Once you implement the SAMLController SLO code you should be all set, the 'Saml2LogoutEventListener' has already been provided.