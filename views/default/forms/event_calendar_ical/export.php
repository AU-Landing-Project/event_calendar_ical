<?php

echo '<br>';
echo '<h3>' . elgg_echo('event_calendar_ical:export:settings') . '</h3>';

echo '<br>';


// export which calendar
echo elgg_echo('event_calendar_ical:export:type') . ' ';

$value = $vars['type'];
if ($value == 'group') {
  $value = $vars['group_guid'];
}

$options_values = array(
	'mine' => elgg_echo('event_calendar_ical:mine'),
	'friends' => elgg_echo('event_calendar_ical:friends'),
);

if (elgg_get_plugin_setting('site_calendar', 'event_calendar') != 'no') {
  $options_values['site'] = elgg_echo('event_calendar_ical:site');
}


$groups = elgg_get_logged_in_user_entity()->getGroups('', false);
  
if ($groups) {
  foreach ($groups as $group) {
	if (event_calendar_activated_for_group($group)) {
	  $options_values[$group->guid] = elgg_echo('group') . ': ' . $group->name;
    }
  }
}


echo elgg_view('input/dropdown', array(
	'name' => 'type',
	'value' => $value,
	'options_values' => $options_values
));


echo '<br><br>';

// determine default dates - start/end based on interval day/week/month
// start will be at 00:00 at the beginning of the day/week/month
// end will be at 23:59 at the end of day
// $date[0] = year, 1 = month, 2 = day
$date = explode('-', $vars['date']);
switch ($vars['interval']) {
  case 'day':
	$start_date = $end_date = $vars['date'];
	break;
  
  case 'week':
	// need to adjust start_date to be the beginning of the week
	$start_ts = strtotime($vars['date']);
	$start_ts -= date("w",$start_ts)*60*60*24;
	$end_ts = $start_ts + 6*60*60*24;
				
	$start_date = date('Y-m-d',$start_ts);
	$end_date = date('Y-m-d',$end_ts);
	break;
  
  case 'month':
  default:
	$start_date = $date[0] . '-' . $date[1] . '-1';
	$end_date = $date[0] . '-' . $date[1] . '-' . getLastDayOfMonth($date[1],$date[0]);
	break;
}

// start/end date
echo elgg_echo('event_calendar_ical:start_date') . "<br>";
echo elgg_view('input/date', array('name' => 'start_date', 'value' => $start_date, 'style' => 'width: 120px')) . "<br><br>";

echo elgg_echo('event_calendar_ical:end_date') . "<br>";
echo elgg_view('input/date', array('name' => 'end_date', 'value' => $end_date, 'style' => 'width: 120px;'));

echo '<br><br>';

echo elgg_view('input/submit', array('value' => elgg_echo('event_calendar_ical:export')));

/**
 * Useful functions from event_calendar
 * 
 * // returns TRUE if the given user can add an event to the given calendar
// if group_guid is 0, this is assumed to be the site calendar
event_calendar_can_add($group_guid=0,$user_guid=0)
 
getLastDayOfMonth($month,$year);
 
event_calendar_get_personal_events_for_user($user_guid,$limit)
 * 
 * event_calendar_activated_for_group($group)
 * 
 * event_calendar_get_events_between($start_date,$end_date,$is_count=FALSE,$limit=10,$offset=0,$container_guid=0,$region='-')
 * 
 * date_default_timezone_get()
 * 
 * // when searching for dates use this
 * strtotime($vars['date'] . " " . date_default_timezone_get())
 */