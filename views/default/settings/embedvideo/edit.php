<?php
	$videowidth = $vars['entity']->videowidth;
	if (!$videowidth) $videowidth = '284';
?>
<p>
	<?php 
  
  echo elgg_echo('embedvideo:width');
	echo ': ';
	echo elgg_view('input/text', array(
									  'internalname' => 'params[videowidth]',
									  'value' => $videowidth,
									  'class' => ' ',
													) );
	?>
</p>
