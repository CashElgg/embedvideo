<?php

  /**
   * Embedded Video Plugin
   * 
   */
  
  function embedvideo_init() {
  
    add_widget_type('embedvideo', elgg_echo('embedvideo:widget'), elgg_echo('embedvideo:description'), 'profile', true);
      
  }
  
  // head off the default log listener and only log
  function embedvideo_log_listener($event, $object_type, $object) {
    
    if ($object instanceof Loggable && $object->getClassName() == 'ElggWidget')
    {
      if ($object->handler == 'embedvideo')
      {
        // only log when url has been changed
        if ($object->old_url === $object->url)
          return false;
      } 
    }
    
    return true;
  }
    
  register_elgg_event_handler('init','system','embedvideo_init');
  
  // filter widget updates before system log
  register_elgg_event_handler('all','all','embedvideo_log_listener', 390);
?>
