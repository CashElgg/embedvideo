<?php
        
    if (!@include(dirname(dirname(dirname(dirname(dirname(__FILE__))))) . "/lib/embedvideo.php"))
    {
      echo 'error loading embedvideo library'; 
      return;
    }

    	 
    $video_url = $vars['entity']->url;
	  $video_comment = $vars['entity']->comment;
	  
    echo videoembed_create_embed_object($video_url, $vars['entity']->getGUID());
    
    // protect against inserting bad content
    if (@include_once($CONFIG->path . "/vendors/kses/kses.php"))
    {                       
      $video_comment = kses($video_comment, $CONFIG->allowedtags, $CONFIG->allowedprotocols);
    }   
    
    echo $video_comment;	 
?>
