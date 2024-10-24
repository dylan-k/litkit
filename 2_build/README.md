
blogroll-roller
===============================================================================

This PHP script helps you grab all the RSS feeds from a list of blogs, so you can quickly subscribe to them, without having to do a lot of looky-looky clicky-clicky.

The script will output all the RSS feeds it finds into one OPML file, which you can import into your favorite feed reader to add all the blogs to your favorite feed reader.

Some examples of well-designed, modern RSS reader apps include [inoReader](https://www.inoreader.com/blog/2014/05/opml-subscriptions.html) and [Readwise Reader](https://docs.readwise.io/reader/docs/faqs/adding-new-content#how-do-i-upload-an-opml-file-to-import-all-my-rss-feeds-from-my-existing-rss-feed-reader-such-as-feedly-inoreader-reeder-etc), both of which can import an OPML list of feeds for subscribing.


Instructions for Use
--------------------------------------------------------------------------------

1. make a text file named `URLlist.txt`
2. fill the text file the the URLS of blogs you'd like to follow, one URL per line. for example:

```txt
https://example.com
https://example.net
https://example.org
```

3. put `URLlist.txt` and `blogroll-roller.php` in the same directory on your server (i.e. `~/blogroll-roller/`)
4. `cd` to that directory and run the command `php blogroll-roller.php` to make it go.
5. profit!

If you have any questions about this, please use the Github Issues and I'll try to help you out. I'm not the original author of this, but I'll do my best.



Changelog
================================================================================


2024-10-24
--------------------------------------------------------------------------------

### Added

  - Add error handling for failed content fetching.
  - Trim URLs to remove unnecessary line breaks and whitespace.
  - Skip processing for empty lines in the URL list.
  - Convert URLs to use `https://` as needed

### Changed

  - Script renamed to `blogroll-roller.php`.
  - Script refactored:
    - **PSR Standards**: The code should follow PSR-12 standards.
    - **Error Handling**: Improved error handling with exceptions.
    - **Type Declarations**: Added scalar type declarations and return types.
    - **Code Documentation**: Added PHPDoc comments for better documentation.
  - Only fetch the `<head>` from URLs
  - Switch OPML file encoding to UTF-8 for broader compatibility.
  - Change file mode to "w" to overwrite the output file each time.
  - Extracted repeated code into functions
  - Added configuration constants
  - Updated README


2013-11-25
--------------------------------------------------------------------------------

Project forked from [@skinofstars](https://github.com/skinofstars) work: [PHP Script for RSS auto-discovery and OPML file generation](https://web.archive.org/web/20200802141531/http://skinofstars.com/2010/03/php-script-rss-auto-discovery-opml-file).



License
===============================================================================

You may freely use, modify, and distribute this code, provided that any derivative works also comply with the [GNU General Public License v3.0](http://www.gnu.org/copyleft/gpl.html). For more details, see the [LICENSE](LICENSE) file.

This project is forked from [@skinofstars](https://github.com/skinofstars) work: [PHP Script for RSS auto-discovery and OPML file generation](https://web.archive.org/web/20200802141531/http://skinofstars.com/2010/03/php-script-rss-auto-discovery-opml-file).
