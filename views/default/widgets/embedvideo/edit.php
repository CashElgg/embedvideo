<?php

?>
	<p>
		<?php echo elgg_echo('embedvideo:url'); ?><br />
		<?php echo elgg_view('input/text', array(
									  'internalname' => 'params[url]',
								    'value' => $vars['entity']->url,
													) ); ?>
	</p>
	<p>
		<?php echo elgg_echo('embedvideo:comment'); ?><br />
		<?php echo elgg_view('input/longtext', array(
									  'internalname' => 'params[comment]',
								    'value' => $vars['entity']->comment,
													) ); ?>
  </p>	
