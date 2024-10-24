litkit
======

litkit is a tool to help you follow lots of literary blogs


Details
--------------------------------------------------------------------------------

In the midst of some technological turbulence, there remains a treasure trove of vibrant and enjoyable literary blogs on the Internet. You can still subscribe to them and join the conversation. This litkit can help.

Using Silliman's Blogroll as a starting point, I've created an OPML file. This file is a big list of all the RSS feeds of all the blogs on Silliman's list. The file can be imported into your blog reader of choice. Since Google Reader isn't around anymore, I recommend Feedly (my current favorite) or Digg Reader (also very good).


Usage
--------------------------------------------------------------------------------

1. Download the OPML file `_LITBLOGS.opml`
2. Import the OPML file into your reader application. (Some examples of well-designed, modern RSS reader apps include [inoReader](https://www.inoreader.com/blog/2014/05/opml-subscriptions.html) and [Readwise Reader](https://docs.readwise.io/reader/docs/faqs/adding-new-content#how-do-i-upload-an-opml-file-to-import-all-my-rss-feeds-from-my-existing-rss-feed-reader-such-as-feedly-inoreader-reeder-etc), both of which can import an OPML list of feeds for subscribing.)
3. Enjoy more than 1300 literary blogs.

The PHP script in this repository, `blogroll-roller.php` is useful for creating other OPML files, from other lists of blogs. This is documented in more detail in `./2_build/README.MD`.


Credits
--------------------------------------------------------------------------------

First, thanks to <a href="https://ronsilliman.blogspot.com/">Ron Silliman</a>, for amassing the collection of litblogs in the first place. Thanks to <a href="https://twitter.com/intent/user?screen_name=skinofstars">Kevin Carmody</a> for creating <a href="https://skinofstars.com/2010/03/php-script-rss-auto-discovery-opml-file/">the PHP script used to convert the list into OPML</a>. Thanks to <a href="https://twitter.com/myoung">Michael Young</a> for his prompt and helpful tips and fixes for Digg Reader.
