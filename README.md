litkit
======

litkit is a tool to help you follow lots of literary blogs


## Details

In the midst of some technological turbulence, there remains a treasure trove of vibrant and enjoyable literary blogs on the Internet. You can still subscribe to them and join the conversation. This litkit can help. 

Using Silliman's Blogroll as a starting point, I've created an OPML file. This file is a big list of all the RSS feeds of all the blogs on Silliman's list. The file can be imported into your blog reader of choice. Since Google Reader isn't around anymore, I recommend Feedly (my current favorite) or Digg Reader (also very good).


## Usage

1. <a href="http://nocategories.net/opml-builder/_MASTER.opml">Download the OPML file</a>.
2. Import the OPML file into your reader application.
 a. import <a href="http://blog.feedly.com/2013/07/03/the-fix-to-the-missing-feeds-issue-is-here/">instructions for Feedly reader</a>
 b. import <a href="https://digg.zendesk.com/entries/21950935-I-have-an-OPML-file-How-do-I-import-it-to-Digg-Reader-">instructions for Digg reader</a>
 note: The file from step 1 is quite large. If the import fails, try to import <a href="http://nocategories.net/opml-builder/smaller-opml.zip">this collection of smaller files</a>, one at a time.
3. Enjoy more than 1300 literary blogs.

There is a PHP file in this repository that is useful for creating other OPML files, from other lists of blogs. This can be documented in more detail with later updates, but for now: create a .txt file that contains one blog URL per line, then feed that file to the PHP script, which will hunt each site for an RSS feed and create an OPML of the ones it finds.

## Credits

First, thanks to <a href="http://ronsilliman.blogspot.com/">Ron Silliman</a>, for amassing the collection of litblogs in the first place. Thanks to <a href="https://twitter.com/intent/user?screen_name=skinofstars">Kevin Carmody</a> for creating <a href="http://skinofstars.com/2010/03/php-script-rss-auto-discovery-opml-file/">the PHP script used to convert the list into OPML</a>. Thanks to <a href="https://twitter.com/myoung">Michael Young</a> for his prompt and helpful tips and fixes for Digg Reader.