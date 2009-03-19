<?php
  $performed_by = get_entity($vars['item']->subject_guid);

  $url = "<a href=\"{$performed_by->getURL()}\">{$performed_by->name}</a>";
  $string = sprintf(elgg_echo("embedvideo:river:updated"), $url);
  
  echo $string; 
?>
