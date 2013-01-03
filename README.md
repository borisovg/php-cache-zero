php-cache-zero
==============

This class provides a simple caching system for PHP pages. It was inspired by [Cache\_Lite](https://pear.php.net/package/Cache_Lite).

Compatibility
-------------

PHP 5 only (tested with 5.4.4).

Usage Examples
--------------

Please see files in the 'examples' directory.

Constructor Options
-------------------

The constructor takes an associative array with options as key value pairs.

**string cacheDir (default: 'cache')**

Path to directory that will contain the cache files. This must be within $\_SERVER['DOCUMENT\_ROOT']. Do not add slash at the beginning or the end.

**bool caching (default: true)**

Enable / disable caching (e.g. for debugging, or error handling)

**bool fileLocking (default: true)**

Use flock() before writitng to cache file. Note that this may not work so well of you are writing to an NFS export.

**int hashedDirectoryLevel (default: 0)**

Create nested subdirectories for cache files based on the first n characters of id. Useful if you envisage creating a massive number of files on a filesystem that doesn't like that sort of thing.

**int lifeTime (default: 3600)**

Lifetime of cache in seconds. Note that this only affects the validity of the cache - stale files are not deleted for performance reasons.

**int modeFile (default: 0600)**

File mode used by chmod().

**int modeDir (default: 0700)**

Directory mode used by mkdir().

**bool exitOnError (default: true)**

Configures the behaviour of the error handler. If set to false, it prints an error message and disables caching but does not terminate the script.

**bool protectID (default: true)**

Hash the id with md5 before use. If set to false, characters that are not alpha-numeric or [-\_?&=.] will be removed.

**bool protectGroup (default: false)**

Hash the group with md5 before use. If set to false, characters that are not alpha-numeric or [-\_?&=.] will be removed.

**bool verify	(default: true)**

Verify integrity of cache file. A checksum is added at the top of the file on write and is used to verify the data on read.

**string verifyMethod	(default: 'crc32')**

Method for producing the checksum. Acceptable values are 'crc32', 'md5', or 'strlen'.

Public Methods
--------------

**CacheZero::get(string $id, string $group = 'default', boolean $expiredOK = false)**

Get cache content, if available and valid.

* $id - cache object ID (e.g. $\_SERVER['REQUEST\_URI'])
* $group - optional group
* $expiredOK - optional, if set to true will return cache content even if expired

**CacheZero::save(string $data, string $id, string $group = 'default')**

Save content to cache.

* $data - data to go in the cache
* $id - cache object ID (e.g. $\_SERVER['REQUEST\_URI'])
* $group - optional group

**CacheZero::start(string $id, string $group = 'default')**

Start capture of output buffer (e.g. echo).

* $id - cache object ID (e.g. $\_SERVER['REQUEST\_URI'])
* $group - optional group

**CacheZero::finish()**

Finish capture of output buffer, save to cache file and print buffer contents.
