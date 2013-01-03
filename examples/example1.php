<?php

/*
 * This example demonstrates caching by capturing the screen
 * buffer, as if you were using a template.
 *
 * Note: you will need to create a cache directory before
 * using this code (path: $_SERVER['DOCUMENT_ROOT'].'/cache').
*/

require '../CacheZero.php';

$options = array (
	'lifeTime' => 20,
	'verifyMethod' => 'md5'
);
$cache = new CacheZero($options);

$data = $cache->get('example1', 'examples');
if ($data) {
	$data = str_replace('NOT ', '', $data);
	echo $data;

} else {
	$cache->start('example1', 'examples');
	// in practice this will probably be pulled in with 'require'
?>
<html>
	<head>
		<title>Example 1 | CacheZero</title>
	</head>
	<body>
		<h1>Example 1</h1>
		<p>This data is <b>NOT FROM CACHE</b>. Please refesh the page.</p>
	</body>
</html>
<?php
	$cache->finish();
}

?>
