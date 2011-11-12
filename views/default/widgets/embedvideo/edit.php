<?php
/**
 * Embed video widget edit page
 */

$guid = $vars['entity']->guid;

echo elgg_view('input/hidden', array(
	'name' => 'params[url_hash]',
	'value' => md5($vars['entity']->url),
));

echo '<div>';
echo elgg_view('output/url', array(
	'text' => 'Supported sites',
	'href' => "#embedvideo-sites-$guid",
	'rel' => 'toggle',
));
echo "<div class=\"hidden mas pas elgg-border-plain\" id=\"embedvideo-sites-$guid\">";
echo elgg_echo('embedvideo:sites');
echo '</div>';
echo '</div>';

echo '<div>';
echo elgg_echo('embedvideo:url');
echo elgg_view('input/text', array(
	'name' => 'params[url]',
	'value' => $vars['entity']->url,
	'onclick' => 'this.select();',
));
echo '</div>';

echo '<div>';
echo elgg_echo('embedvideo:title');
echo elgg_view('input/text', array(
	'name' => 'params[videotitle]',
	'value' => $vars['entity']->videotitle,
));
echo '</div>';

echo '<div>';
echo elgg_echo('embedvideo:comment') . ' ' . elgg_echo('embedvideo:tags_instruct');
echo elgg_view('input/plaintext', array(
	'name' => 'params[comment]',
	'value' => $vars['entity']->comment,
	'style' => 'height: 6em;',
));
echo '</div>';
