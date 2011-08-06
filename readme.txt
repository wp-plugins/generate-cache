=== Generate Cache ===
Contributors: Denis Buka
Donate link: http://www.denisbuka.ru
Tags: cache, generate, maintenance, construction, trigger, spawn
Requires at least: 3.2
Tested up to: 3.2
Stable tag: 0.1

Makes sure your entire website is fully cached at all times. Can be used when performing website maintenance/construction.

== Description ==

When your cache is emptied (say, upon a new post or comment publication, or if you delete it manually after website changes), the plugin loops through selected items (posts, categories, tags or pages) and makes sure you have them all freshly cached to be rapidly served to visitors.

You can also use this plugin when performing website construction, design or maintenance, so that only cached pages are served to visitors.

**How this works:** Let's say you've got a caching plugin installed on your blog (Hyper Cache, WP Super Cache, W3 Total Cache or what have you). When new content is added (posts, pages, comments, etc.) the cache is automatically deleted. But your entire blog will only be cached when somebody re-visits every single page. Generate Cache plugin keeps track of your cache directory and if its size drops below the value you specify, new cache for your entire blog will be automatically regenerated. You can choose which blog items should be taken into account: posts, categories, tags, pages and the home page.

**Benefits:**   

* Perform website maintenance/construction while serving cached pages to visitors.   
* Have your cache always prebuild for better search rankings, since page loading time is a significant SEO factor.   
* Quickly serve cached pages to visitors.   
* Never worry about prebuilding your cache manually.   

**Features overview:**   

* Keeps track of your cache folder size so that it never drops below a certain limit.   
* Tries to guess your cache folder location, or you can specify it manually.   
* Choose for which blog items (posts, categories, tags, pages) cache should be regenerated.   


Note: This is not a caching plugin! It is to be used only together with such.

Links: [Author's Site](http://www.denisbuka.ru)

== Installation ==

1. Unzip the archive and put the folder into your plugins folder (/wp-content/plugins/).
2. Activate the plugin from the Plugins admin menu.
3. Go to Settings -> Generate Cache to set some options.

== Frequently Asked Questions ==

= What kind of script actions are behind this plugin? =
When there is no cache for some page, a script is executed which determines the size of the cache directory and compares it to the limit you specify. If the limit is not reached, a background process is triggered getting the contents of every blog item (post, page, category, tag, home) so that new cache is generated. After all pages have been called, the process is terminated.

= How long will it take to generate cache for my entire site? =
All pages are called within 1-2 second intervals. So if your blog has 100 pages, its full cache will be generated in about 100-200 seconds.

== Upgrade Notice ==

== Screenshots ==

== Changelog ==

= 0.1 =
* Initial release
