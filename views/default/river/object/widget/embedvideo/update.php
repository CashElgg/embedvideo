<?php
/**
 * Video embed widget
 */

$subject = $vars['item']->getSubjectEntity();
$widget = $vars['item']->getObjectEntity();

$subject_link = elgg_view('output/url', array(
	'href' => $subject->getURL(),
	'text' => $subject->name,
	'class' => 'elgg-river-subject',
	'is_trusted' => true,
));

$string = elgg_echo('river:embedvideo:object:widget', array($subject_link));

if (isset($widget->videotitle)) {
	$title = '<em>' . $widget->videotitle . '</em>';
	$string .= ' ' . elgg_echo('embedvideo:river:title', array($title));
}

echo elgg_view('river/elements/layout', array(
	'item' => $vars['item'],
	'summary' => $string,
));
