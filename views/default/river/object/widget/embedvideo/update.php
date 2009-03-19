<?php
  $performed_by = get_entity($vars['item']->subject_guid);
  $widget       = get_entity($vars['item']->object_guid);
  
  $url = "<a href=\"{$performed_by->getURL()}\">{$performed_by->name}</a>";
  $string = sprintf(elgg_echo("embedvideo:river:updated"), $url);
  if (isset($widget->videotitle))
    $string .= ': ' . $widget->videotitle;
    
  echo $string; 
?>
