<?php

$type = get_input('type', 'personal');
$group_guid = (int) get_input('group_guid', false);
$date = get_input('date', date('Y-n-j'));
$interval = get_input('interval', 'month');

$urlmod = "?method=ical&type={$type}&date={$date}&interval={$interval}";
if ($type == 'group' && $group_guid !== false) {
  $urlmod .= "&group_guid={$group_guid}";
}

echo elgg_view('navigation/tabs', array(
	'tabs' => array(
		array(
			'text' => elgg_echo('event_calendar_ical:export'),
			'href' => elgg_get_site_url() . 'event_calendar/ical/export' . $urlmod,
			'selected' => ($vars['filter_type'] == 'export')
		),
		array(
			'text' => elgg_echo('event_calendar_ical:import'),
			'href' => elgg_get_site_url() . 'event_calendar/ical/import' . $urlmod,
			'selected' => ($vars['filter_type'] == 'import')
		)
	)
));