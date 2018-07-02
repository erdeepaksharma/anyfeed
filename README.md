### OpenCart Any Feed PRO ###

##### Supported OpenCart Versions #####
* v2.0.x
* v1.5.x

##### Demo #####
http://demo.hostjars.com/opencart/admin/index.php?route=feed/any_feed_pro

##### Installation Service #####
If you have purchased our installation service with this module, please contact us providing ftp access details for the server you would like the module installed on. You can also send through a CSV file for verification. Any CSV files verified prior to purchase will carry a guarantee to work.

We will then take care of the installation for you.

##### Installation Instructions #####
[Knowledge Base: How to install a module](https://hostjars.zendesk.com/hc/en-us/articles/203573549-How-Do-I-Install-my-Module-)

Step 1) Upload the Any Feed PRO files to your OpenCart installation.
Upload the admin and system folders found in your .zip's to your OpenCart store, these folders should be merged with your existing admin and system folders. No files should be overwritten.
(To check if your files merged correctly look in your admin/controller/feed folder for a file called any_feed_pro.php)

* In MijoShop, this location will be in your /components/com_mijoshop/opencart/ folder

Step 2) Navigate to Extensions > Feeds and find Any Feed PRO in the list, press the "Install" button.

Step 3) Add User Permissions for Any Feed PRO
Go to System > Users > User Groups and select your admin user. Edit your usergroup to have both access and modify permissions on feed/any_feed_pro.

Optional Step 1) To add Any Feed Pro to the Tools menu:
Go to Extensions > Extension Installer and "Upload" the afp_install.ocmod.xml file.
Next go to Extensions > Modifications and press the "Refresh" button to refresh your modification cache

Optional Step 2) Increase the max_execution_time in your OpenCart installation admin/php.ini. The default
setting is 30 seconds. This limit can be exceeded depending on the size of the feed and amount of
products exported; so it is best to increase the max_execution_time before exporting your large feed.

Congratulations, installation is complete :)

Thanks for purchasing a HostJars Extension!

##### Support #####
http://helpdesk.hostjars.com

##### Changelog #####
15/09/2015
[New Fields]
* Viewed
* Date Added
* Date Available
* Sort Order
* Meta Title
* Points
* Related Products

[Bug Fixes]
* Exporting options no longer causes any CSV alignment issues
* Additional Images field now exports correctly
* Filter field now exports correctly
* Bug fix for Cron Feed Cache

14/08/2015

* Clickable titles for renaming
* Creating a feed was tweaked
* Validation on Feed names was improved

29/05/2015

* Added More Fields
* Fixed rule processing bug

21/07/2015

* Fixed string to number conversions when dealing with XML
* Export to CSV/XML with graceful error handling
* CDATA now turns off when required
* Decoding Categories for invalid HTML


27/07/2015

* Forces clean XML root tag
* Cleaned input of subcategories
* Bug fix for Custom name mapping


Date Created 13/04/2015