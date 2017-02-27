#Rounds

This is a repository for a data entry application to track a sport team's stats, built with Laravel and Vue.

##Highlights
- Import and export of crucial club data, including teams, players and rounds.
- ..

##Commands
There are a few commands for use with this application:
- When the 'to-be' Administrator registers for an account, it is necessary to execute the ```alter:role``` command via command line, after they have registered, passing in the id of the ```User``` and the role (i.e. ```admin```). Eg. ```php artisan alter:role 1 admin```.
- The local storage of excel spreadsheets will have to be cleaned every now and then. To do so, execute the following command ```php artisan clear:imports```.

##Functionality
- ```User```s can register and create ```Topic```s and ```Post```s.
- ```User```s may subscribe to any ```Topic```s and may report any ```Topic``` or ```Post``` for moderation.
- Users can @mention other users in ```Post```s.
- Users can send and receive messages from other users in realtime, via the internal messaging system.
- Owners of ```Post```s or eleveated ```User```s may modify or delete ```Post```s.
- Only ```admin``` and ```moderator``` accounts may delete ```Topic```s.
- Users can manage their own profile changing to their password and avatar image.
- All subscribed ```User```s recieve emails, via a triggered event when a ```Topic``` that they are subscribed to has a ```Post``` added to it.
- Moderators are alerted via email when content is reported. Moderators can easily manage these reports in the Moderator Dashboard.
- Other expected events are raised, check out ```App\Events``` and ```App\Listeners``` for further insight. See ```App\Providers\EventServiceProvider```'s ```$listen``` property for the association of ```Event```s and ```Listener```s.

##Installation & Configuration
If you would like to install this project, treat it as you would any other Laravel application:
- Clone the repo.
- Install dependencies: ```composer install``` and ```npm install``` (required to install ```pusher-js``` and ```laravel-echo```).
- Configure environment variables- ```.env``` (see below).
- Generate application key: ```php artisan key:generate```.
- Run Laravel migrations: ```php artisan migrate```.

Make sure you configure these environment variables:
- ```APP_URL``` : the url of the application. This variable is used for linking to the application in emails.
- ```APP_NAME```: the human readable name of the application. This variable is used for refering to the application via emails. It is also used in the navbar as the application branding.
- ```MAIL_FROM_EMAIL``` and ```MAIL_FROM_NAME```: the 'from' email address and name. This is used for sending out emails.
- ```S3_KEY```, ```S3_SECRET```, ```S3_REGION```, ```S3_BUCKET_NAME``` and ```S3_IMG_BUCKET_URL```: the conncetion to Amazon S3 variables. These values are used for the avatar uploading facility built in to the application.
- ```PUSHER_APP_ID```, ```PUSHER_KEY``` and ```PUSHER_SECRET```: the connection configuration for the ```pusher``` broadcast driver.

Further steps:
- Set the ```QUEUE_DRIVER``` environment variable to ```database```.
- Set the ```BROADCAST_DRIVER``` environment variable to the broadcast driver to be used. Set this to ```pusher``` if you wish to use the pusher API with ```Laravel Echo```.
- Set the ```APP_ENV``` environment variable to ```production``` when the app is on a live sever, to force HTTPS connections on all routes.
- Configure your Amazon S3 bucket with a policy that will allow the application to upload avatars to it.
- Configure the ```Laravel Echo``` instance in ```resources/assets/js/bootstrap.js```, starting line 41.
- Run ```php artisan queue:work``` to allow jobs, queued mail and event broadcasting to function.




##Routes
![Routes](https://cloud.githubusercontent.com/assets/9494635/23349180/b3931c98-fd00-11e6-9ae4-547c46c7444e.png)
Thanks to [Pretty Routes](https://github.com/garygreen/pretty-routes)

##Additional Packages
- [Laravel Excel](https://github.com/Maatwebsite/Laravel-Excel)
- [Laravel Fractal](https://github.com/spatie/laravel-fractal)

##Additional Modules
- [FileSaver.js](https://github.com/eligrey/FileSaver.js/)
- [Moment.js](https://github.com/moment/moment/)

##License
[MIT](https://s3-ap-southeast-2.amazonaws.com/ashleymenhennett/LICENSE)
