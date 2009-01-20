<?php

  /**
   * Embedded Video Plugin
   * 
   */
  
  function embedvideo_init() {
    global $CONFIG;
      
    @include_once $CONFIG->pluginspath . 'embedvideo/lib/embedvideo.php';

    add_widget_type('embedvideo', elgg_echo('embedvideo:widget'), elgg_echo('embedvideo:description'), 'profile', true);
    
    extend_view('css','embedvideo/css');  
  }
  
  function embedvideo_frontpage() {
    $url   = get_plugin_setting('front_url', 'embedvideo');
    $width = get_plugin_setting('front_width', 'embedvideo');
    
    if (!isset($width) || !is_numeric($width) || $width < 0)
      $width = 400; // if bad, set default to 400
    
    return videoembed_create_embed_object($url, 0, $width);
  }
  
  // head off the default log listener and only log
  function embedvideo_log_listener($event, $object_type, $object) {
    
    if ($object instanceof Loggable && $object->getClassName() == 'ElggWidget')
    {
      if ($object->handler == 'embedvideo')
      {
        // only log when url has been changed
        if (!isset($object->url) || $object->url_hash == md5($object->url))
          return false;
      } 
    }
    
    return true;
  }
    
  register_elgg_event_handler('init','system','embedvideo_init');
  
  // filter widget updates before system log
  register_elgg_event_handler('all','all','embedvideo_log_listener', 390);
?>
