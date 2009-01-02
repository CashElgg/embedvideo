<?php

?>
  <p>
    <?php echo elgg_echo('embedvideo:url'); ?><br />
    <input type="text" name="params[url]" value="<?php echo htmlentities($vars['entity']->url); ?>" class="input-text" />
  </p>
  
  <p>
    <?php echo elgg_echo('embedvideo:comment'); ?><br />
    <textarea class="input-textarea" name="params[comment]" ><?php echo htmlentities($vars['entity']->comment); ?></textarea>
  </p>  
