<?php
gatekeeper();

$type = get_input('type', 'personal');
$group_guid = get_input('group_guid', false);
$date = get_input('date', date('Y-n-j'));
$interval = get_input('interval', 'month');

elgg_set_page_owner_guid(elgg_get_logged_in_user_guid());

if ($type == 'group') {
  $group = get_entity($group_guid);
  // make sure group exists, has calendars enabled, and global group calendars are enabled
  if (!elgg_instanceof($group, 'group')
		  || $group->event_calendar_enable == 'no'
		  || elgg_get_plugin_setting('group_calendar', 'event_calendar') == 'no'
		  ) {
	forward('', '404');
  }
  
  elgg_set_page_owner_guid($group->getGUID());
  
  elgg_push_breadcrumb($group->name, $group->getURL());
  elgg_push_breadcrumb(elgg_echo('item:object:event_calendar'), elgg_get_site_url() . 'event_calendar/group/' . $group->getGUID());
}
elseif ($type == 'site') {
  elgg_push_breadcrumb(elgg_echo('item:object:event_calendar'), elgg_get_site_url() . 'event_calendar/list/all');
}
else {
  elgg_push_breadcrumb(elgg_echo('item:object:event_calendar'), elgg_get_site_url() . "event_calendar/list/{$date}/{$interval}/{$type}");
}

elgg_push_breadcrumb(elgg_echo('event_calendar_ical:export'));

$title = elgg_echo('event_calendar_ical:title');

$content = elgg_view_form('event_calendar_ical/export', array(), array(
	'type' => $type,
	'group_guid' => $group_guid,
	'date' => $date,
	'interval' => $interval
));

$layout = elgg_view_layout('content', array(
	'title' => $title,
	'filter' => elgg_view('event_calendar_ical/tabs', array('filter_type' => 'export')),
	'content' => $content
));

echo elgg_view_page($title, $layout);