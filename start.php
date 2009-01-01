<?php

  /**
   * Embedded Video Widget
   * 
   */
  
  function embedvideo_init() {
  
    add_widget_type('embedvideo', 'Embed Video', elgg_echo('embedvideo:description'), 'profile', true);
      
  }
    
  register_elgg_event_handler('init','system','embedvideo_init');
?>
