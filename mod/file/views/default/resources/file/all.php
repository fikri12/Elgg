<?php
/**
 * All files
 *
 * @package ElggFile
 */

elgg_register_title_button('file', 'add', 'object', 'file');

$title = elgg_echo('file:all');

$content = elgg_list_entities([
	'type' => 'object',
	'subtype' => 'file',
	'full_view' => false,
	'no_results' => elgg_echo("file:none"),
	'preload_owners' => true,
	'preload_containers' => true,
	'distinct' => false,
]);

$sidebar = file_get_type_cloud();
$sidebar .= elgg_view('file/sidebar');

$body = elgg_view_layout('content', [
	'filter_context' => 'all',
	'content' => $content,
	'title' => $title,
	'sidebar' => $sidebar,
]);

echo elgg_view_page($title, $body);
