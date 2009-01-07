<?php

  /**
   * Embedded Video Plugin
   * 
   */
  
  function embedvideo_init() {
  
    add_widget_type('embedvideo', elgg_echo('embedvideo:widget'), elgg_echo('embedvideo:description'), 'profile', true);
      
  }
    
  register_elgg_event_handler('init','system','embedvideo_init');
?>
