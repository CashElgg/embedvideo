<?php
/**
 * Embedded Video Plugin
 *
 * @author Cash Costello
 * @license GPL2
 */

elgg_register_event_handler('init', 'system', 'embedvideo_init');

function embedvideo_init() {

	elgg_register_library('embedvideo', elgg_get_plugins_path() . 'embedvideo/lib/embedvideo.php');
	elgg_load_library('embedvideo');

	elgg_register_widget_type('embedvideo', elgg_echo('embedvideo:widget'), elgg_echo('embedvideo:description'), 'profile', true);

	elgg_extend_view('css/elgg','embedvideo/css');

	elgg_register_event_handler('all', 'all', 'embedvideo_log_listener');
}

/**
 * Get the HTML for displaying a video on the front page
 * 
 * @return string
 */
function embedvideo_frontpage() {
	$url   = elgg_get_plugin_setting('front_url', 'embedvideo');
	$width = elgg_get_plugin_setting('front_width', 'embedvideo');

	if (!isset($width) || !is_numeric($width) || $width < 0) {
		$width = 452;
	}

	return videoembed_create_embed_object($url, 0, $width);
}

/**
 * Add a river entry for new videos
 *
 * @param string     $event
 * @param string     $type
 * @param ElggEntity $object
 * @return bool
 */
function embedvideo_log_listener($event, $type, $object) {

	static $catch_double;

	if ($event === 'update' && $object instanceof Loggable && $object->getClassName() === 'ElggWidget') {
		if ($object->handler == 'embedvideo') {
			// only log when url has been changed
			if (isset($object->url) && $object->url_hash != md5($object->url)) {
				if (!isset($catch_double)) {
					elgg_delete_river(array('object_guid' => $object->guid));
					add_to_river('river/object/widget/embedvideo/update', 'embedvideo', elgg_get_logged_in_user_guid(), $object->guid);
				}

				$catch_double = true;
			}
		}
	}

	return true;
}
