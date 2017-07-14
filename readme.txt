=== Plugin Name ===
Contributors: sephiroth74
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=XKSFZCW7WJ6FS
Tags: flash, flickr, widget, slideshow, photostream, gallery
Requires at least: 2.9.2
Tested up to: 2.9.2
Stable tag: 0.4

Flash simple slideshow widget to display user's photostream

== Description ==

This widget is useful if you want to display a simple flickr slideshow ( with a fade effect between images ) of all your public photostream. 
What you can configure:

*	Delay between images
*	Image custom frame ( size and color )
*	Image shadow
*	Size of the image to show
*	Size of the flash object
*	Include or not the swfobject.js

A few notes about the widget panel configuration screen:

*   "Username" is the flickr username to be used to display the public photostream
*   "Slideshow Timer" is the time between each image ( in milliseconds )
*   "Api Key" is the flickr api key needed. create a new one here: http://www.flickr.com/services/apps/create/apply
*   "Frame Color" is the color of the image frame
*   "Image Type" is the flickr provided image size to be loaded. See http://www.flickr.com/services/api/misc.urls.html
*	"Use Frame". If checked display a 5px frame border around the loaded image ( color is customizable using "Frame Color" ).
*	"Use Shadow". If checked create a shadow for each loaded image.
*	"Flash object width" and "Flash object height" are the dimensions of the flash object. You can use percent values ( eg 100% ) or absolute values ( eg 350px ). Even if the flash object size is small than the flickr image loaded, the flash will resize the loaded image in order to fit its contents.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the folder flash-flickr-slideshow to the `/wp-content/plugins/` directory
2. Create your own Flickr API key http://www.flickr.com/services/apps/create/apply
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Place the widget `Flash Flickr Photostream` in one of your sidebar items
5. Configure the general options form the sidebar option menu under "Flash Flickr Slideshow"

== Frequently Asked Questions ==

== Screenshots ==

1. Widget configuration panel
2. Widget in action on a dark theme background

== Changelog ==

= 0.4 =
* upgraded to swfobject 2.2
* in the plugin option page added the possibility to include or not the swfobject.js file

= 0.3 =
* Added custom image frame size
* Correct a bug with the link of the swfobject.js

= 0.2 =
* Added "flash object width" and "flash object height" in the admin area.
* Added listeners to io errors in the flash swf.

== Upgrade Notice ==

== Arbitrary section ==

== A brief Markdown Example ==
