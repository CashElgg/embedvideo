<?php
/**
 * Embed video widget view
 */

$video_url = $vars['entity']->url;
$video_comment = $vars['entity']->comment;
$video_title = $vars['entity']->videotitle;

echo '<h3 class="center mbs">' . $video_title . '</h3>';

echo videoembed_create_embed_object($video_url, $vars['entity']->getGUID()); 

echo elgg_view('output/longtext', array(
	'value' => $video_comment,
));
