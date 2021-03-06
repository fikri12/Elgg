<?php
/**
 * List files by type
 *
 * @package ElggFile
 */

$page_owner_guid = get_input('page_owner', null);

if ($page_owner_guid !== null) {
	$page_owner_guid = sanitise_int($page_owner_guid);
}

if ($page_owner_guid) {
	elgg_set_page_owner_guid($page_owner_guid);
}
$owner = elgg_get_page_owner_entity();

elgg_group_gatekeeper();
elgg_register_title_button('file', 'add', 'object', 'file');

// Get input
$md_type = 'simpletype';
// avoid reflected XSS attacks by only allowing alnum characters
$file_type = preg_replace('[\W]', '', get_input('tag'));
$listtype = get_input('listtype');
$friends = (bool) get_input('friends', false);

// breadcrumbs
elgg_push_breadcrumb(elgg_echo('file'), "file/all");
if ($owner) {
	if (elgg_instanceof($owner, 'user')) {
		elgg_push_breadcrumb($owner->getDisplayName(), "file/owner/$owner->username");
	} else {
		elgg_push_breadcrumb($owner->getDisplayName(), "file/group/$owner->guid/all");
	}
}
if ($friends && $owner) {
	elgg_push_breadcrumb(elgg_echo('friends'), "file/friends/$owner->username");
}
if ($file_type) {
	elgg_push_breadcrumb(elgg_echo("file:type:$file_type"));
}

// title
if (!$owner) {
	// world files
	$title = elgg_echo('all') . ' ' . elgg_echo("file:type:$file_type");
} else {
	$friend_string = $friends ? elgg_echo('file:title:friends') : '';
	$type_string = elgg_echo("file:type:$file_type");
	$title = elgg_echo('file:list:title', [$owner->getDisplayName(), $friend_string, $type_string]);
}

$sidebar = file_get_type_cloud($page_owner_guid, $friends);

$limit = elgg_get_config('default_limit');
if ($listtype == "gallery") {
	$limit = 12;
}

$params = [
	'type' => 'object',
	'subtype' => 'file',
	'limit' => $limit,
	'full_view' => false,
	'preload_owners' => true,
];

if ($owner instanceof ElggUser) {
	if ($friends) {
		$params['relationship'] = 'friend';
		$params['relationship_guid'] = $user->guid;
			$params['relationship_join_on'] = 'owner_guid';
	} else {
		$params['owner_guid'] = $page_owner_guid;
	}
} else {
	$params['container_guid'] = $page_owner_guid;
}

if ($file_type) {
	$params['metadata_name'] = $md_type;
	$params['metadata_value'] = $file_type;
}

$content = elgg_list_entities_from_relationship($params);

$body = elgg_view_layout('content', [
	'filter' => '',
	'content' => $content,
	'title' => $title,
	'sidebar' => $sidebar,
]);

echo elgg_view_page($title, $body);
