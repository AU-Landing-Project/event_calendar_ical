<?php

/**
 * set some inputs to generate ical links
 * and reroute some things on event_calendar pagehandler
 */
function ec_ical_event_calendar_router($hook, $type, $return, $params) {
  switch($return['segments'][0]) {
	case 'list':
	  if ($return['segments'][3] == 'mine') {
		set_input('ical_calendar_type', 'personal');
	  }
	  elseif ($return['segments'][3] == 'friends') {
		set_input('ical_calendar_type', 'friends');
	  }
	  else {
		if (elgg_get_plugin_setting('site_calendar', 'event_calendar') != 'no') {
		  set_input('ical_calendar_type', 'site');
		}
		else {
		  set_input('ical_calendar_type', 'site');
		}
	  }
	  set_input('ical_date', $return['segments'][1]);
	  set_input('ical_interval', $return['segments'][2]);
	  break;
	  
	case 'group':
		if ($return['segments'][4] == 'mine') {
		  set_input('ical_calendar_type', 'personal');
		}
		elseif ($return['segments'][4] == 'friends') {
		  set_input('ical_calendar_type', 'friends');
		}
		else {
		  set_input('ical_calendar_type', 'group');
		  set_input('ical_group_guid', $return['segments'][1]);
		}
		set_input('ical_date', $return['segments'][2]);
		set_input('ical_interval', $return['segments'][3]);
	  break;
	  
	case 'ical':
		elgg_load_library('elgg:event_calendar');
	  
		if ($return['segments'][1] == 'export') {
		  if (include(elgg_get_plugins_path() . 'event_calendar_ical/pages/export.php')) {
			return true;
		  }
		}
		
		if ($return['segments'][1] == 'import') {
		  if (include(elgg_get_plugins_path() . 'event_calendar_ical/pages/import.php')) {
			return true;
		  }
		}
	  break;
  }
}

/**
 * replace ical extras link with our own
 */
function ec_ical_extras_menu($hook, $type, $return, $params) {
  if (!empty($return)) {
	foreach ($return as $key => $item) {
	  if ($item->getName() == 'ical') {
		$calendar_type = get_input('ical_calendar_type', false);
		$date = get_input('ical_date', false);
		$interval = get_input('ical_interval', false);
		$group_guid = get_input('ical_group_guid', false);
		
		// it's our link, lets modify it
		$text = elgg_view('output/img', array('src' => 'mod/event_calendar/images/ics.png'));
		$url = elgg_get_site_url() . 'event_calendar/ical/export?method=ical';
		
		if ($calendar_type) {
		  $url .= "&type={$calendar_type}";
		}
		
		if ($date) {
		  $url .= "&date={$date}";
		}
		
		if ($interval) {
		  $url .= "&interval={$interval}";
		}
		
		if ($group_guid !== false) {
		  $url .= "&group_guid={$group_guid}";
		}
		
		$ical = new ElggMenuItem('ical', $text, $url);
		$ical->setTooltip(elgg_echo('event_calendar_ical:tooltip'));
		$ical->setPriority($item->getPriority());
		
		// replace original with our own
		$return[$key] = $ical;
	  }
	}
  }
  return $return;
}