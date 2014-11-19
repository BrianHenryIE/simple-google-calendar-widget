
Updated November 2014 to support Google OAuth 2.0 -- now requires PHP 5.3

To install:

Upload the unzipped plugin folder to your WordPress wp-content/plugins directory

Upload the Google API PHP Client into this plugin's directory as a directory called google-api-php-client
https://github.com/google/google-api-php-client/archive/master.zip

To configure:

Log into: https://console.developers.google.com/project/

Click Create Project, call it something nice like MyWordpressCalendar.

Under APIs & auth turn on Calendar API.

Under Credentials, click Create new Client ID and select Service account.

Upload your .p12 file to the plugin directory.


Add the widget to a sidebar and enter fill in all the fields with the information from your API dashboard


Share the calendar with the service account email address


