=== Generate Cache ===
Contributors: Denis Buka
Donate link: http://www.denisbuka.ru
Tags: cache, generate, trigger, spawn
Requires at least: 3.2
Tested up to: 3.2
Stable tag: 0.1

Use only together with caching plugins.

When your cache is emptied (say, upon a new post or comment publication, or if you delete it manually after website changes), the plugin loops through selected items (posts, categories, tags or pages) and makes sure you have them all freshly cached to be rapidly served to visitors.

== Description ==

**How this works:**

Let's say you've got a caching plugin installed on your blog (Hyper Cache, WP Super Cache, W3 Total Cache or what have you). When new content is added (posts, pages, comments, etc.) the cache is automatically deleted. But your entire blog will only be cached when somebody re-visits every single page. Generate Cache plugin keeps track of your cache directory and if its size drops below the value you specify, new cache for your entire blog will be automatically regenerated. You can choose which blog items should be taken into account: posts, categories, tags, pages and the home page.

**Features overview:**

* Keeps track of your cache folder size so that it never drops below a certain limit.   
* Tries to guess your cache folder location, or you can specify it manually.   
* Choose for which blog items (posts, categories, tags, pages) cache should be regenerated.   

Links: [Author's Site](http://www.denisbuka.ru)

== Installation ==

1. Unzip the archive and put the folder into your plugins folder (/wp-content/plugins/).
2. Activate the plugin from the Plugins admin menu.
3. Go to Settings -> Generate Cache to set some options.

== Frequently Asked Questions ==

None so far.

== Upgrade Notice ==

== Screenshots ==

== Changelog ==

= 0.1 =
* Initial release
