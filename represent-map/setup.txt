
About RepresentMap
------------------

You can use RepresentMap to setup a website that visualizes your local startup 
community on a custom Google map.

This code first came from http://represent.la, our map of the Los Angeles
startup community. The response was extremely positive, so we decided to spread
the love and share the code with other startup communities. Let's visualize
the world's startups together!

RepresentMap was created by:
- Alex Benzer (http://www.twitter.com/abenzer)
- Tara Tiger Brown (http://www.twitter.com/tara)
- Sean Bonner (http://www.twitter.com/seanbonner)


Requirements
------------

- PHP5
- MySQL


Installation
------------

Setup should be super easy. Follow these steps:

1. Create a new MySQL database and user.
2. Use phpMyAdmin or another MySQL utility to run the newest "places" and "settings" SQL files in the /db directory.
3. Open /include/db_example.php with your text editor. Enter your MySQL credentials in there. Also, replace "letsgetmappy" with a new password for the admin panel. Rename the file to "db.php".
4. Upload all of the files to your server.
5. You'll probably want to comb through index.php with your favorite text editor and replace all the RepresentLA content
   (logo, "more info" text, Twitter/Facebook share buttons, etc.) with your own stuff.
6. Populate your database. We recommend seeding it with some existing data before opening it up to your local community.
   You can add markers by using the button on the map page, or by importing them with an SQL query. If you use an SQL
   query, note that you should leave the lat/long values blank when importing. Then, run geocode.php to automatically
   generate lat/long values for all your rows.
7. Once visitors to your site have submitted their own markers, point your browser to /admin to approve/reject them.
8. Challenge your newly-discovered neighbors to ping pong!


Startup Genome Integration (optional)
-------------------------------------

Startup Genome is a project that "enables local startup communities to collect, curate, and display
their city's data anyway they want." Integrating your map with Startup Genome will allow people to 
keep their profile updated over time and it's a great way to show the rest of the world what's 
happening in your startup community. There's also nice interface that lets you and other curators 
manage your map data.

If you want to pull your map data from Startup Genome, check out the settings in your db.php.
Complete instructions are provided there.

Learn more about Startup Genome here: http://www.startupgenome.com


EventBrite Integration (optional)
--------------------------------

You can automatically show local events in your community on your map! Just follow these steps:

1. Make sure you've configured your "db.php" with your Eventbrite API key and search parameters.
2. Use phpMyAdmin or another MySQL utility to run the newest "events" SQL file in the /db directory.
3. Run "events_get.php" in your browser anytime you want to get new events. By default, events
   should be displayed on your map up to 1 month before their start date.
4. If you want this script to run automatically (once a day would make sense), you can setup a
   chron on your server. If you don't know how to do that, ask your hosting provider.


License
-------

RepresentMap uses the Creative Commons Attribution-ShareAlike 2.0 Generic (CC BY-SA 2.0) License.
Your use of this code and all associated materials is bound by the terms of this license.
For full information, please see this website: http://creativecommons.org/licenses/by-sa/2.0/

Attribution: The "Based on RepresentLA" text and link must be left intact and visible
on your map unless you've been given explicit, written permission from any of the creators to
remove it.


Useful Links
------------

GitHub project: https://github.com/abenzer/represent-map
Represent.LA twitter: http://www.twitter.com/representla