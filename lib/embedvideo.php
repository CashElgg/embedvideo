<?php

  function videoembed_create_embed_object($url)
  {
    
    if (!isset($url))
    {
      return '<p><b>' . elgg_echo('embedvideo:novideo') . '</b></p>';
    }
    
    if (strpos($url, 'youtube.com') != false)
    {
      return videoembed_handle_youtube($url);
    }
    else if (strpos($url, 'video.google.com') != false)
    {
      return videoembed_handle_google($url);
    }
    else if (strpos($url, 'vimeo.com') != false)
    {
      return '<p><b>not handling vimeo videos yet</b></p>';
    }
    else if (strpos($url, 'dailymotion.com') != false)
    {
      return '<p><b>not handling dailymotion.com videos yet</b></p>';
    }    
    else if (strpos($url, 'veoh.com') != false)
    {
      return '<p><b>not handling veoh.com videos yet</b></p>';
    }    
    else if (strpos($url, 'viddler.com') != false)
    {
      return '<p><b>not handling viddler.com videos yet</b></p>';
    }    
    else if (strpos($url, 'metacafe.com') != false)
    {
      return '<p><b>not handling metacafe.com videos yet</b></p>';
    }        
    else if (strpos($url, 'blip.tv') != false)
    {
      return '<p><b>not handling blip.tv videos yet</b></p>';
    }
    else
    {
      return '<p><b>' . elgg_echo('embedvideo:unrecognized') . '</b></p>';
    }
  }
    
  // to support more than one video we need to add unique identifier to id name
  function videoembed_add_css($width, $height)
  {    
    $videocss = "
      <style type=\"text/css\">
        #embedvideo { 
          height: {$height}px;
          width: {$width}px; 
          padding:0; 
          margin:0 0 10px 0; 
          overflow: hidden;
          align: center;
        }
      </style>";

    return $videocss;
  }
  
  function videoembed_add_object($type, $url, $width, $height)
  {
    // could move these into an array
    switch ($type) 
    {
      case 'youtube':
        $videodiv = "
          <div id=\"embedvideo\">
            <object width=\"$width\" height=\"$height\"><param name=\"movie\" value=\"http://{$url}&hl=en&fs=1\"></param><param name=\"allowFullScreen\" value=\"true\"></param><embed src=\"http://{$url}&hl=en&fs=1\" type=\"application/x-shockwave-flash\" allowfullscreen=\"true\" width=\"$width\" height=\"$height\"></embed></object>
          </div>";
        break;
      case 'google':
        $videodiv = "
          <div id=\"embedvideo\">
            <embed id=\"VideoPlayback\" src=\"http://video.google.com/googleplayer.swf?docid={$url}&hl=en&fs=true\" style=\"width:{$width}px;height:{$height}px\" allowFullScreen=\"true\" allowScriptAccess=\"always\" type=\"application/x-shockwave-flash\"> </embed>
          </div>";
        break;       
    }
              
    return $videodiv;
  }
  
  function videoembed_handle_youtube($url)
  {
    // this extracts the core part of the url needed for embeding
    $videourl = videoembed_parse_youtube_url($url);
    if (!isset($videourl))
    {
      return '<p><b>' . sprintf(elgg_echo('embedvideo:parseerror'), 'youtube') . '</b></p>';  
    }
    
    // set video width and height
    $videowidth = get_plugin_setting('videowidth','embedvideo');
    // make sure width is a number and greater than zero
    if (!isset($videowidth) || !is_numeric($videowidth) || $videowidth < 0)
      $videowidth = 284;
    // 25 is the height of the control bar which doesn't scale
    $videoheight = round(319 * $videowidth / 425) + 25;
                
    // add css inline for now
    $embed_object = videoembed_add_css($videowidth, $videoheight);
  
    $embed_object .= videoembed_add_object('youtube', $videourl, $videowidth, $videoheight);
    
    return $embed_object;  
  }
  
  function videoembed_parse_youtube_url($url)
  {    
    // separate parsing embed url
    if (strpos($url, 'embed') != false)
    {
      return parse_youtube_embed_url($url);
    }
    
    if (strpos($url, 'feature=hd') != false)
    {
      // this is high def with a different aspect ratio
    }
    
    // This provides some security against inserting bad content.
    // Divides url into http://, www or localization, domain name, path.
    if (!preg_match('/(http:\/\/)([a-zA-Z]{2,3}\.)(youtube.com\/)(.*)/', $url, $matches))
    {
      //echo "malformed youtube url";
      return;    
    }
    
    $domain = $matches[2] . $matches[3];
    $path = $matches[4]; 
   
    // it is possible to get urls like this http://jp.youtube.com/watch?feature=rec-HM-r2&v=hbCm1q45uQk 

    // forces rest of url to start with "watch?v=", followed by hash, and rest of options start with &    
    if (!preg_match('/^(watch\?v=)([a-zA-Z0-9_]*)(&.*)?$/',$path, $matches))
    {
      //echo "bad hash";
      return;        
    }
    
    $hash = $matches[2];
    
    
    return $domain . 'v/' . $hash;
  }


  // how about other languages??
  function parse_youtube_embed_url($url)
  {
    if (strpos($url, 'width="480"') != false)
    {
      // this is high def with a different aspect ratio
    }

    if (!preg_match('/(value=")(http:\/\/)([a-zA-Z]{2,3}\.)(youtube.com\/)(v\/)([a-zA-Z0-9_]*)(&hl=[a-zA-Z]{2})(.*")/', $url, $matches))
    {
      //echo "malformed embed youtube url";
      return;    
    }

    $domain = $matches[3] . $matches[4];
    $hash   = $matches[6];
    
    // need to pull out language here
    //echo $matches[7];
        
    return $domain . 'v/' . $hash;  
  }
  
  
  function videoembed_handle_google($url)
  {
    // this extracts the core part of the url needed for embeding
    $videourl = videoembed_parse_google_url($url);
    if (!isset($videourl))
    {
      return '<p><b>' . sprintf(elgg_echo('embedvideo:parseerror'), 'google') . '</b></p>';  
    }
    
    // set video width and height
    $videowidth = get_plugin_setting('videowidth','embedvideo');
    // make sure width is a number and greater than zero
    if (!isset($videowidth) || !is_numeric($videowidth) || $videowidth < 0)
      $videowidth = 284;
    // 27 is the height of the control bar which doesn't scale
    $videoheight = round(299 * $videowidth / 400) + 27;
                
    // add css inline for now
    $embed_object = videoembed_add_css($videowidth, $videoheight);
  
    $embed_object .= videoembed_add_object('google', $videourl, $videowidth, $videoheight);
    
    return $embed_object;   
  }

  function videoembed_parse_google_url($url)
  {        
    // separate parsing embed url
    if (strpos($url, 'embed') != false)
    {
      return parse_google_embed_url($url);
    }
        
    if (!preg_match('/(http:\/\/)(video.google.com\/videoplay)(.*)/', $url, $matches))
    {
      //echo "malformed google url";
      return;    
    }
    
    $path = $matches[3];
    //echo $path; 
   
    // forces rest of url to start with "?docid=", followed by hash, and rest of options start with &    
    if (!preg_match('/^(\?docid=)([0-9]*)(&.*)?$/',$path, $matches))
    {
      //echo "bad hash";
      return;        
    }
    
    $hash = $matches[2];
    //echo $hash;
    
    return $hash;
  }


  function parse_google_embed_url($url)
  {

    if (!preg_match('/(src=")(http:\/\/video.google.com\/googleplayer.swf\?docid=)([0-9]*)(&hl=[a-zA-Z]{2})(.*")/', $url, $matches))
    {
      //echo "malformed embed google url";
      return;    
    }

    $hash   = $matches[3];
    //echo $hash;
    
    // need to pull out language here
    //echo $matches[4];
        
    return $hash;  
  }
      
?>
