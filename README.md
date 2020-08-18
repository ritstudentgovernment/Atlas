[logo]:https://atlas.rit.edu/storage/logo.jpg

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
2. Create a new Postgres database table for your local Atlas instance 
3. Configure the **.env** file to match your system's configuration
4. Run **composer install**, **php artisan migrate**, and **php artisan db:seed**
5. Run **php artisan jwt:secret**
6. Run **npm install** and **npm run dev**
7. Run **php artisan serve** to spin up the web server
8. Run **php artisan horizon &** in a new terminal tab to run the queue worker
9. Visit 127.0.0.1:8000 in your browser to verify everything is working properly
10. Configure authentication as outlined below

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

## Local Development Process

This process will help ensure proper version control methods are maintained, and help make development go smoothly.

#### Running Atlas Locally

1. First, ensure you've installed the app with the instructions found above
2. Start the development server and resources with the following commands
    1. Run the development web server: **php artisan serve &**
    2. Run the Queue worker: **php artisan horizon &**
    3. Ensure NPM rebuilds the application with changes to JS files: **npm run watch &**
    4. In a new tab, run **./vendor/bin/phpunit-watcher watch** to have PHP tests run when changes to PHP files are detected
3. As in the initial install, Atlas should now be accessible at 127.0.0.1:8000

##### Mac Users:

Laravel provides a utility called [Valet](https://laravel.com/docs/5.8/valet) which automatically runs the development server for you in the background, and forwards it to a url like **https://atlas.test/**.

Follow the instructions [here](https://laravel.com/docs/5.8/valet#installation) if you would like to install it for yourself, to save yourself some time in the development process.

#### VCS Process (internal SG process)

1. Locally checkout the **develop** branch on your machine
2. Make a new branch for whichever ticket you are working on
    1. An example name is **feature/ATLAS-67/pusher-implementation**
    2. the format is **\[feature, bug, or refactor\]/\[JIRA-Ticket-ID\]/\[Human Readable Name\]**
3. Do all work for the ticket in that branch. When you are done developing the feature and associated tests, create a detailed Pull Request into the **Develop** branch
    1. The PR should contain a brief summary of the ticket / issue, how you implemented it, and anything a reviewer should be cognisant of in the review process
    2. Assign three reviewers to the PR; a Senior Dev, and two Junior Devs.
    3. The PR can be merged when 2 reviews have been submitted.
4. Merge the PR. Upon successful test completion, the code will automatically be deployed to the development server for widespread team testing.
5. Once the change has been thoroughly tested on the development server, create a summary PR to merge the changes into master
    1. A Senior Dev will then merge the changes into Master
    2. Upon a successful Travis build, the code will be auto-deployed to the production server.
    
#### VCS Process (external contributions)

1. Fork this repo
2. Follow steps 1-3 from the internal process from above
    1. *Note:* you do not have to follow branch naming guidelines, or PR guidelines for your local development.
3. When you get the code into your forked **Develop** branch, and you wish to contribute it to the main repo, make a PR from your **develop** branch to ours.
    1. This time, please be sure to follow step 3.1 from above.
4. Our development team will review your PR and merge it in if:
    1. New code has been properly tested and documented
    2. The code introduces well thought out features or bug fixes
    3. The build passes and does not lower code coverage or introduce code smells 
5. Upon successful testing of the feature or bugfix, the changes will be deployed to the production environment

#### Stopping the development server

1. Run the command **php artisan horizon:terminate** to stop the queue workers
2. Close all open terminal tabs, terminating their processes