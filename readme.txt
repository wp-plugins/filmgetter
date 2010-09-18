=== FilmGetter ===
Contributors: confact
Tags: movie, poster, imdb, TMDb, film, plot, rating
Requires at least: 2.1
Tested up to: 3.0
Stable tag: 4.3

FilmGetter uses tags to show information like Poster, plot, rating, release date, TMDb and imdb urls.

== Description ==

Filmgetter gets the information from TMDb about a movie you have choosen. Adding it to the database and you can now use it's information with using the tag [film] or [imdb] tag. It will write out the information about the movie you want.


== Installation ==

1. Upload the 'filmgetter' directory to the `/wp-content/plugins/` directory - make sure to have the plugin directory named with lowcase or the stylesheet won't work.
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Paste in the TMDb-API number in the FilmGetter admin page.
4. Add movies via the Add Movie section on the admin page.*
5. Use the [film]movie name[/film] to show the filminfo, or use [imdb]movie name[/imdb] to show just imdb-link

* No need for that in version 0.1.2-

== Screenshots ==

No screenshots for now.

== Changelog ==

= 0.1.3.1 =
* Fixed the style in the admin.

= 0.1.3 =
* Fixed Stylesheet, style.css is the file you want to edit, to change the style, the design of the film-info.
* Fixed the style, it won't go over other things in the design now.
* Fixed grammar, spellings.

= 0.1.2 =
* Fixed so if the trailer url is empty, the url will be #.
* Fixed so if the imdb url is empty, the url will be #.
* Added Feature: Add movies in the admin page from IMDB and TMDb ids.
* Added Beta Feature: Adding movies automatically, in this beta you need to be specific on the movie names, no trailers will be added either.

= 0.1.1 =
* Fixed database error (the youtube URL was too short).
* Added feature: Remove movie.
* Added License text. (Damn me!)
* Fixed some spellings.

= 0.1 =
* Added [film] tag.
* Added [imdb] tag.
* Added Admin section.
* Added option to edit TMDb-API pass in admin.
* Added feature add movie in admin section.

== Upgrade Notice ==

= 0.1.3 =
Make sure to have the plugin in a directory, named filmgetter, with lowcase. Or the stylesheet won't work.

= 0.1.2 =
no database errors fixed in this, no need to update, if you have already update in 0.1.1

= 0.1.1 =
Fixed a error in the database (see more info in the changelog), fixed a function to fix it, just upload the new version and go in to the admin page and click on the button in the Update plugin section.

= 0.1 =
The first version out. no need to upgrade ;)