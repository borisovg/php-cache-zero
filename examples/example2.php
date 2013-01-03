<?php

/*
 * This example demonstrates caching by saving the contents
 * of a variable before it is printed out.
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

$data = $cache->get('example2', 'examples');
if ($data) {
	$data = str_replace('NOT ', '', $data);
	echo $data;

} else {
	$data = <<<HTML
<html>
	<head>
		<title>Example 2 | CacheZero</title>
	</head>
	<body>
		<h1>Example 2</h1>
		<p>This data is <b>NOT FROM CACHE</b>. Please refesh the page.</p>
	</body>
</html>
HTML;
	$cache->save($data, 'example2', 'examples');
	echo $data;
}

?>
