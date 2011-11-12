<?php
/**
 * Embed video settings
 */

if (!isset($vars['entity']->videowidth)) {
	$vars['entity']->videowidth = 290;
}

if (!isset($vars['entity']->front_width)) {
	$vars['entity']->front_width = 452;
}

echo '<h4>Widget Settings</h4>';
echo '<div>';
echo elgg_echo('embedvideo:width') . ': ';
echo elgg_view('input/text', array(
	'name' => 'params[videowidth]',
	'value' => $vars['entity']->videowidth,
	'class' => ' ',
));
echo '</div>';

echo '<h4 class="mtl">Front Page Settings</h4>';
echo '<div>';
echo elgg_echo('embedvideo:url') . ': ';
echo elgg_view('input/text', array(
	'name' => 'params[front_url]',
	'value' => $vars['entity']->front_url,
	'onclick' => 'this.select();',
));
echo '</div>';

echo '<div>';
echo elgg_echo('embedvideo:width') . ': ';
echo elgg_view('input/text', array(
	'name' => 'params[front_width]',
	'value' => $vars['entity']->front_width,
	'class' => ' ',
));
echo '</div>';
