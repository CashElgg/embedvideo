<?php
        
    if (!function_exists('videoembed_create_embed_object'))
    {
      echo 'error loading embedvideo library'; 
      return;
    }

    	 
    $video_url = $vars['entity']->url;
	  $video_comment = $vars['entity']->comment;
	  $video_title = $vars['entity']->videotitle;
	  
    if (include_once($CONFIG->path . "/vendors/kses/kses.php"))
    {                       
      $video_title = kses($video_title, $CONFIG->allowedtags, $CONFIG->allowedprotocols);
    }
    
    // the Elgg css is not giving us anything to work with so we need our own div class
    echo '<div class="videoembed_wrapper">';    
    
    echo '<div class="videoembed_title"><h3>' . $video_title . '</h3></div>';	 

    echo videoembed_create_embed_object($video_url, $vars['entity']->getGUID());
    
    // protect against inserting bad content
    if (include_once($CONFIG->path . "/vendors/kses/kses.php"))
    {                       
      $video_comment = kses($video_comment, $CONFIG->allowedtags, $CONFIG->allowedprotocols);
    }   
    
    echo '<div class="videoembed_comment">' . $video_comment . '</div>';
    echo '</div>';
?>
