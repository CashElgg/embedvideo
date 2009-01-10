<?php

  /**
	 * embed video widget edit page 
	 */

?>
  <p>Youtube and Google videos are supported.</p>
  <p>
    <?php echo elgg_echo('embedvideo:url'); ?><br />
    <input onclick="this.select();" type="text" name="params[url]" value="<?php echo htmlentities($vars['entity']->url); ?>" class="input-text" />
  </p>
  
  <p>
    <?php echo elgg_echo('embedvideo:title'); ?><br />
    <textarea class="input-textarea" name="params[title]" style="height: 20px;" ><?php echo htmlentities($vars['entity']->title); ?></textarea>
  </p>  

  <p>
    <?php echo elgg_echo('embedvideo:comment'); ?> (html tags are allowed)<br />
    <textarea class="input-textarea" name="params[comment]" style="height: 100px;" ><?php echo htmlentities($vars['entity']->comment); ?></textarea>
  </p>  

