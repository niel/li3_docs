<?php

/**
 * Initialize code index.
 */
use lithium\aop\Filters;
use lithium\core\Libraries;
use li3_docs\extensions\docs\Code;

$filter = function ($params, $next) {
	$indexPath = Libraries::get(true, 'path') . '/resources/docs.index.json';

	if (file_exists($indexPath) && is_readable($indexPath)) {
		Code::index((array)json_decode(file_get_contents($indexPath), true));
	}
	$result = $next($params);

	if (($index = Code::index()) && is_array($index) && is_writable(dirname($indexPath))) {
		file_put_contents($indexPath, json_encode($index));
	}

	return $result;
};

Filters::apply('lithium\action\Dispatcher', 'run', $filter);
Filters::apply('lithium\console\Dispatcher', 'run', $filter);

/**
 * Setup default options:
 *
 * - `'index'` _array|void_: Allows to restrict indexing to provided set of libraries.
 *   By default all libraries registered in the application are indexed.
 * - `'categories'` _array|void_: Allows manually provide a set of category names. By
 *    default categories are extracted from all indexed libraries.
 */
Libraries::add('li3_docs', array('bootstrap' => false) + Libraries::get('li3_docs') + array(
	'url' => '/docs',
	'index' => null,
	'categories' => null
));

?>
