<?php

  $statement = $vars['statement'];
  $performed_by = $statement->getSubject();

  $url = "<a href=\"{$performed_by->getURL()}\">{$performed_by->name}</a>";
  $string = sprintf(elgg_echo("embedvideo:river:created"), $url);
  
  echo $string; 
?>
