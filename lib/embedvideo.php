<?php

  /**
   * Embed Video Library
   * Functions to parse flash video urls and create the flash embed object
   * 
   * Current video sites supported:
   *
   * youtube
   * google
   * vimeo
   * metacafe
   * veoh
   *
   * todo
   * ------------
   * clean up display related code and move to css for easier skinning
   * look into creating embed code that validates as xhtml
   * 
   */


  /**
   * Public API for library
   *
   * @param string $url either the url or embed code
   * @param integer $guid unique identifier of the widget
   * @param integer $videowidth override the admin set default width
   * @return string html video div with object embed code or error message
   */
  function videoembed_create_embed_object($url, $guid, $videowidth=0)
  {
    
    if (!isset($url))
    {
      return '<p><b>' . elgg_echo('embedvideo:novideo') . '</b></p>';
    }
    
    if (strpos($url, 'youtube.com') != false)
    {
      return videoembed_youtube_handler($url, $guid, $videowidth);
    }
    else if (strpos($url, 'video.google.com') != false)
    {
      return videoembed_google_handler($url, $guid, $videowidth);
    }
    else if (strpos($url, 'vimeo.com') != false)
    {
      return videoembed_vimeo_handler($url, $guid, $videowidth);
    }
    else if (strpos($url, 'metacafe.com') != false)
    {
      return videoembed_metacafe_handler($url, $guid, $videowidth);
    }    
    else if (strpos($url, 'veoh.com') != false)
    {
      return videoembed_veoh_handler($url, $guid, $videowidth);
    }    
    else if (strpos($url, 'viddler.com') != false)
    {
      return '<p><b>not handling viddler.com videos yet</b></p>';
    }    
    else if (strpos($url, 'dailymotion.com') != false)
    {
      return videoembed_dm_handler($url, $guid, $videowidth);
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
    
  /**
   * generic css insert
   *
   * @param integer $guid unique identifier of the widget
   * @param integer/string $width 
   * @param integer/string $height 
   * @return string style code for video div
   */
  function videoembed_add_css($guid, $width, $height)
  {    
    $videocss = "
      <style type=\"text/css\">
        #embedvideo{$guid} { 
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
  
  /**
   * generic <object> creator
   *
   * @param string $type 
   * @param string $url 
   * @param integer $guid unique identifier of the widget
   * @param integer/string $width 
   * @param integer/string $height    
   * @return string <object> code
   */
  function videoembed_add_object($type, $url, $guid, $width, $height)
  {
    $videodiv = "<div id=\"embedvideo{$guid}\">";
    
    // could move these into an array and use sprintf
    switch ($type) 
    {
      case 'youtube':
        $videodiv .= "<object width=\"$width\" height=\"$height\"><param name=\"movie\" value=\"http://{$url}&hl=en&fs=1\"></param><param name=\"allowFullScreen\" value=\"true\"></param><embed src=\"http://{$url}&hl=en&fs=1\" type=\"application/x-shockwave-flash\" allowfullscreen=\"true\" width=\"$width\" height=\"$height\"></embed></object>";
        break;
      case 'google':
        $videodiv .= "<embed id=\"VideoPlayback\" src=\"http://video.google.com/googleplayer.swf?docid={$url}&hl=en&fs=true\" style=\"width:{$width}px;height:{$height}px\" allowFullScreen=\"true\" allowScriptAccess=\"always\" type=\"application/x-shockwave-flash\"> </embed>";
        break;
      case 'vimeo':
        $videodiv .= "<object width=\"$width\" height=\"$height\"><param name=\"allowfullscreen\" value=\"true\" /><param name=\"allowscriptaccess\" value=\"always\" /><param name=\"movie\" value=\"http://vimeo.com/moogaloop.swf?clip_id={$url}&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1\" /><embed src=\"http://vimeo.com/moogaloop.swf?clip_id={$url}&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1\" type=\"application/x-shockwave-flash\" allowfullscreen=\"true\" allowscriptaccess=\"always\" width=\"$width\" height=\"$height\"></embed></object>";
        break;
      case 'metacafe':
        $videodiv .= "<embed src=\"http://www.metacafe.com/fplayer/{$url}.swf\" width=\"$width\" height=\"$height\" wmode=\"transparent\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" type=\"application/x-shockwave-flash\"></embed>";
        break;
      case 'veoh':
        $videodiv .= "<embed src=\"http://www.veoh.com/veohplayer.swf?permalinkId={$url}&player=videodetailsembedded&videoAutoPlay=0\" allowFullScreen=\"true\" width=\"$width\" height=\"$height\" bgcolor=\"#FFFFFF\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\"></embed>";
        break;       
    }
              
    $videodiv .= "</div>";

    return $videodiv;
  }
  
  /**
   * calculate the video width and size
   *
   * @param $width 
   * @param $height 
   * @param $toolbar_height
   */
  function videoembed_calc_size(&$width, &$height, $aspect_ratio, $toolbar_height)
  {
    // set video width and height
    if (!$width)
      $width = get_plugin_setting('videowidth', 'embedvideo');

    // make sure width is a number and greater than zero
    if (!isset($width) || !is_numeric($width) || $width < 0)
      $width = 284;

    $height = round($width / $aspect_ratio) + $toolbar_height;  
  }
  
  /**
   * main youtube interface
   *
   * @param string $url 
   * @param integer $guid unique identifier of the widget
   * @param integer $videowidth  optional override of admin set width
   * @return string css style, video div, and flash <object>
   */
  function videoembed_youtube_handler($url, $guid, $videowidth)
  {
    // this extracts the core part of the url needed for embeding
    $videourl = videoembed_youtube_parse_url($url);
    if (!isset($videourl))
    {
      return '<p><b>' . sprintf(elgg_echo('embedvideo:parseerror'), 'youtube') . '</b></p>';  
    }
    
    videoembed_calc_size($videowidth, $videoheight, 425/320, 25);
                    
    // add css inline for now
    $embed_object = videoembed_add_css($guid, $videowidth, $videoheight);
  
    $embed_object .= videoembed_add_object('youtube', $videourl, $guid, $videowidth, $videoheight);
    
    return $embed_object;  
  }
  
  /**
   * parse youtube url
   *
   * @param string $url 
   * @return string subdomain.youtube.com/v/hash
   */
  function videoembed_youtube_parse_url($url)
  {    
    // separate parsing embed url
    if (strpos($url, 'embed') != false)
    {
      return videoembed_youtube_parse_embed($url);
    }
    
    if (strpos($url, 'feature=hd') != false)
    {
      // this is high def with a different aspect ratio
    }
    
    // This provides some security against inserting bad content.
    // Divides url into http://, www or localization, domain name, path.
    if (!preg_match('/(http:\/\/)([a-zA-Z]{2,3}\.)(youtube\.com\/)(.*)/', $url, $matches))
    {
      //echo "malformed youtube url";
      return;    
    }
    
    $domain = $matches[2] . $matches[3];
    $path = $matches[4]; 
   
    // it is possible to get urls like this http://jp.youtube.com/watch?feature=rec-HM-r2&v=hbCm1q45uQk 

    // forces rest of url to start with "watch?v=", followed by hash, and rest of options start with &    
    if (!preg_match('/^(watch\?v=)([a-zA-Z0-9_-]*)(&.*)?$/',$path, $matches))
    {
      //echo "bad hash";
      return;        
    }
    
    $hash = $matches[2];
    
    
    return $domain . 'v/' . $hash;
  }


  /**
   * parse youtube embed code
   *
   * @param string $url 
   * @return string subdomain.youtube.com/v/hash
   */
  // how about other languages??
  function videoembed_youtube_parse_embed($url)
  {
    if (strpos($url, 'width="480"') != false)
    {
      // this is high def with a different aspect ratio
    }

    if (!preg_match('/(value=")(http:\/\/)([a-zA-Z]{2,3}\.)(youtube\.com\/)(v\/)([a-zA-Z0-9_-]*)(&hl=[a-zA-Z]{2})(.*")/', $url, $matches))
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
  
  
  /**
   * main google interface
   *
   * @param string $url 
   * @param integer $guid unique identifier of the widget
   * @param integer $videowidth  optional override of admin set width
   * @return string css style, video div, and flash <object>
   */
  function videoembed_google_handler($url, $guid, $videowidth)
  {
    // this extracts the core part of the url needed for embeding
    $videourl = videoembed_google_parse_url($url);
    if (!isset($videourl))
    {
      return '<p><b>' . sprintf(elgg_echo('embedvideo:parseerror'), 'google') . '</b></p>';  
    }
    
    videoembed_calc_size($videowidth, $videoheight, 400/300, 27);
                
    // add css inline for now
    $embed_object = videoembed_add_css($guid, $videowidth, $videoheight);
  
    $embed_object .= videoembed_add_object('google', $videourl, $guid, $videowidth, $videoheight);
    
    return $embed_object;   
  }

  /**
   * parse google url
   *
   * @param string $url 
   * @return string hash
   */
  function videoembed_google_parse_url($url)
  {        
    // separate parsing embed url
    if (strpos($url, 'embed') != false)
    {
      return videoembed_google_parse_embed($url);
    }
        
    if (!preg_match('/(http:\/\/)(video\.google\.com\/videoplay)(.*)/', $url, $matches))
    {
      //echo "malformed google url";
      return;    
    }
    
    $path = $matches[3];
    //echo $path; 
   
    // forces rest of url to start with "?docid=", followed by hash, and rest of options start with &    
    if (!preg_match('/^(\?docid=)([0-9-]*)(&.*)?$/',$path, $matches))
    {
      //echo "bad hash";
      return;        
    }
    
    $hash = $matches[2];
    //echo $hash;
    
    return $hash;
  }


  /**
   * parse google embed code
   *
   * @param string $url 
   * @return string hash
   */
  function videoembed_google_parse_embed($url)
  {

    if (!preg_match('/(src=")(http:\/\/video\.google\.com\/googleplayer\.swf\?docid=)([0-9-]*)(&hl=[a-zA-Z]{2})(.*")/', $url, $matches))
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

  /**
   * main vimeo interface
   *
   * @param string $url 
   * @param integer $guid unique identifier of the widget
   * @param integer $videowidth  optional override of admin set width
   * @return string css style, video div, and flash <object>
   */
  function videoembed_vimeo_handler($url, $guid, $videowidth)
  {
    // this extracts the core part of the url needed for embeding
    $videourl = videoembed_vimeo_parse_url($url);
    if (!isset($videourl))
    {
      return '<p><b>' . sprintf(elgg_echo('embedvideo:parseerror'), 'vimeo') . '</b></p>';  
    }
    
    // aspect ratio changes based on video - need to investigate
    videoembed_calc_size($videowidth, $videoheight, 400/300, 0);
                    
    // add css inline for now
    $embed_object = videoembed_add_css($guid, $videowidth, $videoheight);
  
    $embed_object .= videoembed_add_object('vimeo', $videourl, $guid, $videowidth, $videoheight);
    
    return $embed_object;   
  }

  /**
   * parse vimeo url
   *
   * @param string $url 
   * @return string hash
   */
  function videoembed_vimeo_parse_url($url)
  {        
    // separate parsing embed url
    if (strpos($url, 'embed') != false)
    {
      return videoembed_vimeo_parse_embed($url);
    }
        
    if (strpos($url, 'groups') != false)
    {
      if (!preg_match('/(http:\/\/)(www\.)?(vimeo\.com\/groups)(.*)(\/videos\/)(.*)/', $url, $matches))
      {
        //echo "malformed vimeo group url";
        return;    
      }
          
      $hash = $matches[6];
    }
    else
    {
      if (!preg_match('/(http:\/\/)(www.)?(vimeo.com\/)(.*)/', $url, $matches))
      {
        //echo "malformed vimeo url";
        return;    
      }
          
      $hash = $matches[4];
    }
        
    //echo $hash; 
       
    return $hash;
  }

  /**
   * parse vimeo embed code
   *
   * @param string $url 
   * @return string hash
   */
  function videoembed_vimeo_parse_embed($url)
  {
    if (!preg_match('/(value="http:\/\/vimeo\.com\/moogaloop\.swf\?clip_id=)([0-9-]*)(&)(.*" \/)/', $url, $matches))
    {
      //echo "malformed embed vimeo url";
      return;    
    }

    $hash   = $matches[2];
    //echo $hash;
            
    return $hash;  
  }
  
  /**
   * main metacafe interface
   *
   * @param string $url 
   * @param integer $guid unique identifier of the widget
   * @param integer $videowidth  optional override of admin set width
   * @return string css style, video div, and flash <object>
   */
  function videoembed_metacafe_handler($url, $guid, $videowidth)
  {
    // this extracts the core part of the url needed for embeding
    $videourl = videoembed_metacafe_parse_url($url);
    if (!isset($videourl))
    {
      return '<p><b>' . sprintf(elgg_echo('embedvideo:parseerror'), 'metacafe') . '</b></p>';  
    }
    
    videoembed_calc_size($videowidth, $videoheight, 400/295, 40);
                    
    // add css inline for now
    $embed_object = videoembed_add_css($guid, $videowidth, $videoheight);
  
    $embed_object .= videoembed_add_object('metacafe', $videourl, $guid, $videowidth, $videoheight);
    
    return $embed_object;   
  }

  /**
   * parse metacafe url
   *
   * @param string $url 
   * @return string hash
   */
  function videoembed_metacafe_parse_url($url)
  {        
    // separate parsing embed url
    if (strpos($url, 'embed') != false)
    {
      return videoembed_metacafe_parse_embed($url);
    }
    
    if (!preg_match('/(http:\/\/)(www\.)?(metacafe\.com\/watch\/)([0-9]*)(\/[0-9a-zA-Z_-]*)(\/)/', $url, $matches))
    {
      //echo "malformed metacafe group url";
      return;    
    }
          
    $hash = $matches[4] . $matches[5];
        
    //echo $hash; 
       
    return $hash;
  }

  /**
   * parse metacafe embed code
   *
   * @param string $url 
   * @return string hash
   */
  function videoembed_metacafe_parse_embed($url)
  {
    if (!preg_match('/(src="http:\/\/)(www\.)?(metacafe\.com\/fplayer\/)([0-9]*)(\/[0-9a-zA-Z_-]*)(.swf)/', $url, $matches))
    {
      //echo "malformed embed metacafe url";
      return;    
    }

    $hash   = $matches[4] . $matches[5];
    //echo $hash;
            
    return $hash;  
  }

  /**
   * main veoh interface
   *
   * @param string $url 
   * @param integer $guid unique identifier of the widget
   * @param integer $videowidth  optional override of admin set width
   * @return string css style, video div, and flash <object>
   */
  function videoembed_veoh_handler($url, $guid, $videowidth)
  {
    // this extracts the core part of the url needed for embeding
    $videourl = videoembed_veoh_parse_url($url);
    if (!isset($videourl))
    {
      return '<p><b>' . sprintf(elgg_echo('embedvideo:parseerror'), 'veoh') . '</b></p>';  
    }
    
    videoembed_calc_size($videowidth, $videoheight, 410/311, 30);
                    
    // add css inline for now
    $embed_object = videoembed_add_css($guid, $videowidth, $videoheight);
  
    $embed_object .= videoembed_add_object('veoh', $videourl, $guid, $videowidth, $videoheight);
    
    return $embed_object;   
  }

  /**
   * parse veoh url
   *
   * @param string $url 
   * @return string hash
   */
  function videoembed_veoh_parse_url($url)
  {        
    // separate parsing embed url
    if (strpos($url, 'embed') != false)
    {
      return videoembed_veoh_parse_embed($url);
    }
    
    if (!preg_match('/(http:\/\/www\.veoh\.com\/videos\/)([0-9a-zA-Z]*)/', $url, $matches))
    {
      //echo "malformed veoh url";
      return;    
    }
          
    $hash = $matches[2];
        
    //echo $hash; 
       
    return $hash;
  }

  /**
   * parse veoh embed code
   *
   * @param string $url 
   * @return string hash
   */
  function videoembed_veoh_parse_embed($url)
  {
    if (!preg_match('/(src="http:\/\/)(www.)?(veoh\.com\/veohplayer.swf\?permalinkId=)([a-zA-Z0-9]*)/', $url, $matches))
    {
      //echo "malformed embed veoh url";
      return;    
    }

    $hash   = $matches[4];
    //echo $hash;
            
    return $hash;  
  } 

  /**
   * main dm interface
   *
   * @param string $url 
   * @param integer $guid unique identifier of the widget
   * @param integer $videowidth  optional override of admin set width
   * @return string css style, video div, and flash <object>
   */
  function videoembed_dm_handler($url, $guid, $videowidth)
  {
    // this extracts the core part of the url needed for embeding
    $videourl = videoembed_dm_parse_url($url);
    if (!isset($videourl))
    {
      return '<p><b>' . sprintf(elgg_echo('embedvideo:parseerror'), 'daily motion') . '</b></p>';  
    }
    
    videoembed_calc_size($videowidth, $videoheight, 410/311, 30);
                    
    // add css inline for now
    $embed_object = videoembed_add_css($guid, $videowidth, $videoheight);
  
    $embed_object .= videoembed_add_object('dm', $videourl, $guid, $videowidth, $videoheight);
    
    return $embed_object;   
  }

  /**
   * parse dm url
   *
   * @param string $url 
   * @return string hash
   */
  function videoembed_dm_parse_url($url)
  {        
    // separate parsing embed url
    if (strpos($url, 'embed') != false)
    {
      return videoembed_dm_parse_embed($url);
    }
    
    if (!preg_match('/(http:\/\/www\.veoh\.com\/videos\/)([0-9a-zA-Z]*)/', $url, $matches))
    {
      //echo "malformed daily motion url";
      return;    
    }
          
    $hash = $matches[2];
        
    //echo $hash; 
       
    return $hash;
  }

  /**
   * parse dm embed code
   *
   * @param string $url 
   * @return string hash
   */
  function videoembed_dm_parse_embed($url)
  {
    if (!preg_match('/(src="http:\/\/)(www.)?(veoh\.com\/veohplayer.swf\?permalinkId=)([a-zA-Z0-9]*)/', $url, $matches))
    {
      //echo "malformed embed daily motion url";
      return;    
    }

    $hash   = $matches[4];
    //echo $hash;
            
    return $hash;  
  } 

           
?>
