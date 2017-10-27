SEO Auto Image Tags
======================

Description
----------------------------------------------
Auto generate clean ALT tags for your images as they are uploaded. Automatically removes hyphens, periods and other characters to generate clean alt tag names.
SEO Auto Image Tags automatically updates the title and alt tag for every image uploaded with a clean Title Case version of the file name! Don't waste your time manually craeting SEO Friendly Tags, let SEO Auto Image Tags do the work for you!
You can run the database updater to create, update, or delete image tag data for all images in the media library. All with a lightweight and efficient clientside script to EVERY image is properly tagged.

Installation
------------
It's recommended to use the Gulp file for build the distribution or testing folder. Feel free to include and or suggest better weays to do it.
1. Clone or download the main folder.
2. Install required packages through NPM. Gulp has to be installed as a global dependency as well as a local. All the other packages can be installed locally.
3. You can create an FTP connection to automatically upload the builded files to a remote location (i.e. a Wordpress installation). Just create a file called ftp_connect.json and it should include the following attributes:
{
	"host": 		"your_host_ftp_addr",
	"user": 		"your_username",
	"password": 	"your_password",
	"parallel": 	1,
	"log":      	"gutil.log",
	"remote_path": "path/to/plugins/folder/seo-auto-image-tags/"
}
4. Run the gulp file
5. Copy the build folder (seo-auto-image-tags) into a Wordpress installation. Skip this step if you have set an FTP connection on step 3.
6. Go to admin panel to play with it.


Support
-------
This is a developer's portal for SEO Auto Image Tags and should not be used for user support. Please visit the
[support forums](https://wordpress.org/support/plugin/seo-auto-image-tags) for that purpose.

License
------------
[GPLv2](http://www.gnu.org/licenses/gpl-2.0.html)
