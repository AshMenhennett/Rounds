# Rounds

This is a repository for a data entry application to track a sport team's stats, built with Laravel and Vue.

## Highlight
- Import and export of crucial club data, including teams, players and rounds.
- Fully featured Admin Dashboard

## Functionality
- ```User```s (coaches) can register and join ```Teams```s and manage said ```Team```'s ```Round``` (match) data.
- Each ```User``` has a role- either ```coach``` or ```admin```.
- An ```admin``` ```User``` has complete control over coaches, ```Team```s, ```Player```s and ```Round```s and is also considered a coach.
- An ```admin``` ```User``` can import mass data and export crucial club data.
- A coach may leave a ```Team```, but may not delete it.
- An ```admin``` ```User``` may not delete a ```Team``` while it contains players.
- A ```Player``` may not be deleted if it has played in a ```Round``` (match).
- For more insight into the models and their relations, read the comments in ```App\Player```, ```App\Round```, ```App\Team``` and ```App\User```.
- The coach FAQ will provide insight into some of the naming conventions used throughout the app (i.e. rounds, temporary players): ```views/faq.blade.php```

## Installation & Configuration
If you would like to install this project, treat it as you would any other Laravel application:
- Clone the repo.
- Install dependencies: ```composer install``` (also, ```npm install```, if you need).
- Configure environment variables- ```.env``` (see below).
- Generate application key: ```php artisan key:generate```.
- Run Laravel migrations: ```php artisan migrate```.

Make sure you configure these environment variables:
- ```APP_URL``` : the url of the application. This variable is used for linking to the application in emails.
- ```APP_NAME```: the human readable name of the application. This variable is used for refering to the application via emails and the welcome page. It is also used in the navbar as the application branding.
- ```MAIL_FROM_ADDRESS``` and ```MAIL_FROM_NAME```: the 'from' email address and name. This is used for sending out emails.
- ```ADMIN_SUPPORT_EMAIL_ADDRESS```: the email address of admin, whom you wish users to contact for administrative purposes (support).
- ```COPY_NAME```: the copyright name in the footer.
- ```COPY_URL```: the copyright link in the footer.

Further steps:
- Set the ```QUEUE_DRIVER``` environment variable to ```database```.
- Set the ```APP_ENV``` environment variable to ```production``` when the app is on a live sever, to force HTTPS connections on all routes.
- Run ```php artisan queue:work``` to allow jobs, queued mail and event broadcasting to function.

## Commands
There are a few commands for use with this application:
- When the 'to-be' Administrator registers for an account, it is necessary to execute the ```alter:role``` command via command line, after they have registered, passing in the id of the ```User``` and the role (i.e. ```admin```). Eg. ```php artisan alter:role 1 admin```.
- To remind coaches that they have not fullfilled their duties (haven't filled in round data by the end of the day specified by either the ```default_date``` in ```rounds``` or ```date``` in ```round_team```), you can setup a Cron job to run daily (at an appropriate time, by which a round will have been completed by i.e. 5pm), executing this command ```php artisan send:roundReminder```.
- The local storage of excel spreadsheets will have to be cleaned every now and then. To do so, execute the following command ```php artisan clear:imports```.
- The local storage of compiled blade views can be cleaned, if you wish. To do so, execute the following command ```php artisan clear:compViews```.

## Additional Packages
- [Laravel Excel](https://github.com/Maatwebsite/Laravel-Excel)
- [Laravel Fractal](https://github.com/spatie/laravel-fractal)

## Additional Modules
- [FileSaver.js](https://github.com/eligrey/FileSaver.js/)
- [Moment.js](https://github.com/moment/moment/)

## Other
*Please do not use the associated legal views, if they still exist in this repository (terms.blade.php and privacy.blade.php). Use at your own peril.*
*Also, do not use the images included in ```public/img```.*

## License
[MIT](https://s3-ap-southeast-2.amazonaws.com/ashleymenhennett/LICENSE)
