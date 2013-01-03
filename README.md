php-cache-zero
==============

This class provides a simple caching system for PHP pages. It was inspired by [Cache_Lite](https://pear.php.net/package/Cache_Lite).

Compatibility
-------------

PHP 5 only (tested with 5.4.4).

Public Methods
--------------

CacheZero::get(string $id, string $group = 'default', boolean $expiredOK = false)

Get cache content, if available and valid.

* $id - cache object ID (e.g. $_SERVER['REQUEST_URI'])
* $group - optional group
* $expiredOK - optional, set to "true" to return cache content even if expired

CacheZero::save(string $data, string $id, string $group = 'default')

Save content to cache.

* $data - data to go in the cache
* $id - cache object ID (e.g. $_SERVER['REQUEST_URI'])
* $group - optional group

CacheZero::start(string $id, string $group = 'default')

Start capture of output buffer (e.g. echo).

* $id - cache object ID (e.g. $_SERVER['REQUEST_URI'])
* $group - optional group

CacheZero::finish()

Finish capture of output buffer, save to file and print buffer contents.

Constructor Options
-------------------

TODO
