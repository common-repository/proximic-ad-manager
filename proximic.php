<?php
/*
Plugin Name: Proximic Ad Manager
PLugin URI: http://www.proximic.com/publishers/plugins.php#wordpress
Description: Publish your Proximic ad units into your Wordpress blog and increase your online revenue! (<a href='edit.php?page=proximic_ad_options'>Manage your Proximic Ads</a>). 
Author: Proximic
Version: 1.0
Author URI: http://www.proximic.com/
*/

@define("PROXIMIC_AD_VERSION", "1.0");

@define('PROXIMIC_AD_DIRPATH', strrchr(dirname(__FILE__),'/') . "/");

@define('PROXIMIC_AD_OPTION_NAME', "proximic_ad_optionarr");

@define('PROXIMIC_AD_HOME', "http://www.proximic.com/");

@define('PROXIMIC_AD_OPTION_PAGE', "proximic_ad_options");
@define('PROXIMIC_AD_SETTINGS_PAGE', "proximic_ad_settings");


 class proximicAdClass{
 	function ProximicPanel()
 	{
 		// check ad name for only allowed characters
 		function valid_kw_chars($text)
 		{
 			if( preg_match("/[^a-zA-Z0-9_]/",$text) ){
 				return false;
 			}
 			return true;
 		}
 		
 		//functions to handle ads
 		// delete an ad
 		function AdDel( &$options, $adname, $save_options=true, &$isEnable )
 		{
 		 $isDefault = false;
 		 $isEnable = false;
 			$newAdArray = array();
 			$lastKey = NULL;
 			//find and delete kw
 			foreach( $options['ads'] as $key => $vals ){
 				if( $key == $adname ){
 				 $isEnable = $vals['enabled'];
 					if( $options['default'] == $adname){
 					 $isDefault = true;
 						$options['default'] = NULL;
 					}
 				}else{
 				 if ($vals != NULL){
 					 $newAdArray[$key] = $vals;
 					 $lastKey = $key;
 					}
 				}
 			}
 			
 			if( $options['default'] == NULL ){
 					$options['default'] = $lastKey; 
 			}
 			
 			$options['ads'] = $newAdArray;
 			if( $save_options )
 				update_option(PROXIMIC_AD_OPTION_NAME, $options);
 			
 			return $isDefault;
 		}
 		
 		// add or update an ad 
 		function AdNew( &$options, $adname, $adcode, $desc, $enabled, $isDefault, $save_options=TRUE )
 		{
 		 if( isset($adname) && $adname != '' && valid_kw_chars($adname)
  			&& isset($adcode) && $adcode != '' ){
  			
  			if (isset($enabled) && $enabled == ''){
  			 $enable = false;
  			}else{
  			 $enable = true;
  			}
  			
  			if(!isset($isDefault) || $isDefault == ''){
  			 $isDefault = false;
  			}else{
  			 $isDefault = true;
  			}
  			
  			if ($isDefault || ! isset($options['default']) || $options['default'] == '' ){
  			 $options['default'] = $adname;
  			} 			
  					
 		  $options['ads'][$adname] = array('adcode' => $adcode, 'desc' => $desc, 'enabled' => $enable);
 		 
 			 if( $save_options ){
 				 update_option(PROXIMIC_AD_OPTION_NAME, $options);
 				}
 				return true;
 			}else{
 			 return false;
 			}
 		}
 		
 		/*
  	 Create default options
  	*/
  	function Proximic_DefaultOption()
  	{
  		$options = array();
  		$options['version'] = PROXIMIC_AD_VERSION; 
  		$options['next_update_check'] = time(); // when to check for update to plugin next.
  		$options['default'] = NULL;		// always have to check against NULL for default.
  		$options['ads'] = array();
  		add_option(PROXIMIC_AD_OPTION_NAME, $options, 'Options for Proximic Ad from '.PROXIMIC_AD_HOME);
  		return $options;
  	}
 		
 		//functions to display ad
 		function ProximicAd_Header($options, $submit_msgs, &$showinfo){
 		 $firstAd = false;
 		 if ((!isset($options)) || (!isset($options['ads'])) || count($options['ads']) == 0){
 		  $firstAd = true;
 		 }
    echo "<div class='wrap'>";
 		 echo "<h2>Proximic Ad Manager</h2>"; 		 
 		 
 		 
 		 $infoclass = "proximic_info";
 		 $haserror = false;
  		foreach ($submit_msgs as $msg) {
  		 if ($msg == "err"){
  		  $infoclass = "proximic_errinfo proximic_info";
  		  $haserror = true;
  		  continue;
  		 }
  			echo '<div class="'.$infoclass.'">';
  			echo $msg;
  			echo '</p></div>';	
  			$infoclass = "proximic_info";
  		}
  		
  		if (!$firstAd){  
  		 if ($haserror||isset($_GET["fn"])){
  		  $showinfo = false; // not show footer info
  		 }else{
  		  $showinfo = true;
  		 }
  		 echo "<ul class='subsubsub'>";
 		  echo "<li><a ".($haserror||isset($_GET["fn"])?"":"class='current'")." href='".makeActionUrl()."'>Manage Ads</a> | </li>";
 		  echo "<li><a ".(((!isset($_GET["name"]))&&isset($_GET["fn"])&&$_GET["fn"]=='edit')?"class='current'":"")." href='".makeActionUrl()."&fn=edit'>Create Ads</a></li>";
  		 echo "</ul>"; 		 
  		 if (!isset($_GET["fn"])){
 		   echo "<div style='clear: both;'><h3>My Proximic Ad Units</h3>\n";
      echo "<p>Manage your list of Proximic ad units below:</p></div>\n";
     }
 		 }
 		}
 		
 		function ProximicAd_Footer($showinfo){
 		 if ($showinfo){
 		  echo "<br /><h3>Instructions</h3>";
  		 echo "<p>This plugin allows you to create as many Proximic ad units as you need, and manage your list of ad units from this page.<br />";
     echo "For each Proximic ad unit, copy the corresponding code tag and insert it in the HTML version of your blog post or template; it will automatically be replaced by the actual Proximic ad code.<br />";
     echo "This way, you can test different format or color customizations without having to edit the WordPress code or templates, or change all the posts manually.<br /></p>";
     echo "<p>You can specify a default Proximic ad unit and call it with the same code tag:<br />";
     echo "<ul class='proximic_info_list'><li> &lt;!--proximic--&gt; for all your blog posts or pages</li>";
     echo "<li> &lt;?php proximicAd(); ?&gt; for all your templates</li></ul></p>";
 
     echo "<p>For all other non-default Proximic ad unit, you will need to insert the name you specified for this ad unit, example:<br />";
     echo "<ul class='proximic_info_list'><li> &lt;!--proximic#my_ad_unit--&gt; for blog posts or pages</li>";
     echo "<li> &lt;?php proximicAd(\"my_ad_unit\"); ?&gt; for templates</li></ul></p>";
 
     echo "<p>For each Proximic ad unit, you can apply the following actions:<br />";
     echo "<ul class='proximic_info_list'><li> disable/enable: Disabling an ad unit will comment the corresponding code tag and hide the ad unit from your pages. When disabled, the ad unit row has a grayed background. By enabling it, the code tag will be uncommented and displayed again on your pages.</li>";
     echo "<li> make default: As described above, the designated default ad unit can be called from the same simple code tag. When specified as default, the ad unit shows (default) aside its title.</li>";
     echo "<li> delete: delete your ad unit. Note: this will only delete the ad unit from the list, all the corresponding code tags inserted in your pages will remain in the code but will no longer publish the ad unit.</li>";
     echo "<li> edit: edit your ad unit. You can edit the description and the Proximic ad code for a different style or color customizations.</li></ul></p>";
 		 }
 		 echo "</div><!-- wrap -->";
 		}
 		
 		function makeActionUrl($withpage=true){
 		 return $_SERVER[PHP_SELF] . ($withpage?('?page=' . PROXIMIC_AD_OPTION_PAGE):"");
 		}
 		
 		function makeForm($name, $action, $btntext, $method='POST', $confirm=false){
 		 $formstr = "<form action='".makeActionUrl($method=='POST')."' method='".$method."'";
 		 if ($confirm){
 		  $formstr .= " onsubmit='return (confirm(\"Are you sure?\"))'";
 		 }
 		 $formstr .= ">\n";
 		 if ($name != ""){
 		  $formstr .= " <input type='hidden' name='name' value='".$name."' />\n";
 		 }
 		 if (!($method=='POST')){
 		  $formstr .= " <input type='hidden' name='page' value='".PROXIMIC_AD_OPTION_PAGE."' />\n";
 		 }
 		 $formstr .= " <input type='hidden' name='fn' value='".$action."' />\n";
 		 $formstr .= " <input class='proximic_btnlink' type='submit' value='".$btntext."' />\n";
 		 $formstr .= "</form>\n";
 		 
 		 return $formstr;
 		}
 		
   function ProximicAd_AdTable($options=NULL, $highlight=NULL)
	  {
		  $tablestr = '<table border="0" width="95%" class="widefat">';

		  if( !isset($options) ) {
			  $tablestr .=  '<tr><td>Internal Error: missing $options</td></tr>';
		  } else {
		   $tablestr .= "\n<thead><tr><th>Name</th><th style='width: 120px;'>Description</th><th>Code for Templates</th><th>Code for Posts/Pages</th><th>Status</th><th style='width: 320px;'>Actions</th></tr></thead>\n<tbody>\n";
		   $altclass = 'proximic_evenrow';
		   
		   foreach( $options['ads'] as $key => $vals ){
		    if ($key == $highlight){
 					 $tablestr .= "<tr class=\"".($vals['enabled']? $altclass."high":"proximic_disabledhigh") ."\">\n";
 					}else{
 					 $tablestr .= "<tr class=\"$altclass". ($vals['enabled']?"":" proximic_disabled") ."\">\n";
 					}
 					
 					$tablestr .= "<td>$key";
 					if( $options['default'] == $key )
  					$tablestr .= ' (default)';
 					$tablestr .= "</td>\n";
 					
 					$tablestr .= '<td style="font-size:.9em;">'.$vals['desc'] . "</td>\n";
 					
 					$tablestr .= "<td>&lt;?php proximicAd(";
  				if( $options['default'] != $key )
  					$tablestr .= '"' . $key . '"';
  				$tablestr .= "); ?&gt;</td>\n";
  
  				$tablestr .= "<td>&lt;!--proximic";
  				if( $options['default'] != $key )
  					$tablestr .= '#' . $key;
  				$tablestr .= "--&gt;</td>\n";
  				
  				$tablestr .= '<td style="font-size:.9em;">'.($vals['enabled']?"enabled":"disabled") . "</td>\n";
  				
  				$tablestr .= '<td style="font-size:.9em;" align="center">';
  				$tablestr .= makeForm($key, $vals['enabled']?"disable":"enable", $vals['enabled']?"disable":"enable");
  				$tablestr .= makeForm($key, "default", "make default");
  				$tablestr .= makeForm($key, "delete", "delete", "POST", true);
  				$tablestr .= makeForm($key, "edit", "edit", "GET");
  				$tablestr .= '</td>' ."\n";
  				// on/off checkbox
  				$tablestr .= '</tr>' ."\n";
  				$altclass = ($altclass == 'proximic_evenrow' ? 'proximic_oddrow' : 'proximic_evenrow');
  			}
			 }
			 $tablestr .= "</tbody>\n</table>\n";
			 
			 return $tablestr;
			}
			
			function ProximicAd_AdForm($options=NULL, $adname=NULL, $vals=NULL)
	  {
	   if( !isset($options) ) {
			  return 'Internal Error: missing $options';
		  }
		  
		  $editmode = false;
		  if ( isset($adname) && $adname != "" && isset($options['ads'][$adname])){
		   $editmode = true;
		  }
		  
		  if ( !isset($vals) ){
		   if ($editmode){
		    $vals = $options['ads'][$adname];
		   }else{
		    $vals = array(	'adcode' => '',	'desc' => '', 'enabled' => true,	'make_default' => false);
		   }
		  }else{
		   $adname = $vals["name"];
		   if ( isset($adname) && $adname != "" && isset($options['ads'][$adname])){
		    $editmode = true;
		   }
		  }
		  
		  $adcode = htmlentities(stripslashes($vals['adcode']) , ENT_COMPAT);
		  $desc = htmlentities(stripslashes($vals['desc']), ENT_COMPAT);
		  
		  $formstr .= '<form action="'.makeActionUrl().'" name="proximicform" method="post">';
		  $formstr .= '<input type="hidden" value="edit" name="fn" />';
		  if ($editmode){
		   $formstr .= '<input type="hidden" value="'.$adname.'" name="oldname" />';
		  }
		  
		  $formstr .= '<div id="poststuff" class="proximic_ad_form">';

		  $formstr .= '<div class="postbox"><h3>Step 1) Name your ad unit</h3><div class="inside">';
		  $formstr .= '<table><tr><td>Name:</td><td>';
		  if ($editmode){
		   $formstr .= $adname;
		   $formstr .= '<input type="hidden" name="name" value="'.$adname.'" />';
		  }else{
		   $formstr .= '<input onblur="proximic_changename(this)" type="text" size="32" name="name" value="'.$adname.'" />';
		   $formstr .= '</td></tr>';
		   $formstr .= '<tr><td colspan="2"><i>Spaces in the title will be replaced by underscores. Example: "my ad unit" will become: "my_ad_unit"</i>';
		  }
		  $formstr .= '</td></tr><tr><td>Description (optional):</td><td><textarea name="desc" rows="6" cols="30">'.$desc.'</textarea>';
		  $formstr .= '</td></tr></table></div></div>';

		  $formstr .= '<div class="postbox"><h3>Step 2) Paste your Proximic ad code</h3><div class="inside">';
		  $formstr .= '<p>From your Publisher Program, go to "Manage Ads" page, create your Proximic ad unit and copy/paste your ad unit code into the text box below:</p><textarea name="adcode" rows="10" cols="30">'.$adcode.'</textarea></div></div>';

		  $formstr .= '<div class="postbox"><h3>Step 3) Publish your ad unit</h3><div class="inside">';
		  $formstr .= '<p><b>Into Blog Posts or Pages:</b></p>';
		  $formstr .= '<p>To publish your Proximic ad unit into your blog posts or pages, simply add the following code tag:</p>';
		  $formstr .= '<p>&nbsp;&nbsp;&nbsp;&lt;!--proximic#<span class="proximic_name">'. (( isset($adname) && $adname != "" )? $adname:"name") .'</span>--&gt;</p>';
		  $formstr .= '<p>wherever you want the ads to appear.</p>';
		  $formstr .= '<p>&nbsp;</p>';
		  $formstr .= '<p><b>Into Templates:</b></p>';
		  $formstr .= '<p>To publish your Proximic ad unit into the header, the sidebars, or any other template of your Wordpress theme files, simply add the following code tag:</p>';
		  $formstr .= '<p>&nbsp;&nbsp;&nbsp;&lt;?php proximicAd("<span class="proximic_name">'. (( isset($adname) && $adname != "" )? $adname:"name") .'</span>"); ?&gt;</p>';
		  $formstr .= '<p>wherever you want the ads to appear.</p></div></div>';

		  $formstr .= '</div>';
		  $formstr .= '<p class="submit" style="width: 450px;text-align:right"><input type="submit" value="Save" style="font-weight: bold;"/></p>';
		  $formstr .= '</form>';
		  return $formstr;
		 }
		
 		
 		function ProximicAd_Body($options=NULL, $highlight=NULL, $newform_values=NULL){
 		 $firstAd = false;
 		 if ((!isset($options)) || (!isset($options['ads'])) || count($options['ads']) == 0){
 		  $firstAd = true;
 		 }
 		 
 		 echo "<div class='proximic_warp'>"; 
 		 if ($firstAd  || isset($newform_values) || (isset($_GET["fn"]) && $_GET["fn"] == "edit")){
 		  if (isset($_GET["name"])&&($_GET["name"]!="")){
 		   echo "<h3>Edit Ad (".$_GET["name"].")</h3>";
 		  }else{
 		   echo "<h3>Create a New Ad</h3>";
 		  }
 		  echo (ProximicAd_AdForm($options, $_GET["name"], $newform_values));
 		 }else{
 		  echo (ProximicAd_AdTable($options, $highlight)); 		  
 		 }
 		 echo "</div><!-- proximic_warp -->"; 
 		}
 		
 		
 		// function to handle request
 		function handleRequest( &$options, &$submit_msgs, &$highlight, &$newform_values )
  	{
  	 global $_POST, $_GET;
  	 
  	 $fn = ($_POST['fn']);//?($_POST['fn']):($_GET['fn']);
  	 
  		if ( isset($fn) ) {
  			if (get_magic_quotes_gpc()) {
  				$_GET	= array_map('stripslashes', $_GET);
  				$_POST	= array_map('stripslashes', $_POST);
  				$_COOKIE= array_map('stripslashes', $_COOKIE);
  			}
  			if( $fn == 'edit' ){
  			 $name = isset($_POST['name'])?str_replace(" ", "_", $_POST['name']):"";
  			 $isEnable = true;
  			 $isDefault = false;
  			 if (isset($_POST['oldname'])){
  			  if (isset($options["ads"][$_POST['oldname']])){
  			   $isEnable = $options["ads"][$_POST['oldname']]['enabled'];
  			  }
  			 }
  				if (AdNew( $options, $name, $_POST['adcode'], $_POST['desc'], $isEnable, $isDefault)){
  				 $highlight = $name;
  				 if (isset($_POST['oldname'])){
 					  $submit_msgs[] = 'Proximic Ad (' . $name . ') Edited';
 					 }else{
 					  $submit_msgs[] = 'New Proximic Ad (' . $name . ') Added';
 					 }
 					 $newform_values = NULL;
 					}else{
 					 $submit_msgs[] = "err";
 					 $submit_msgs[] = 'Error Ad Name or Ad Code, please check your input';
 						$newform_values = array();
 						$newform_values['name'] = $name;
 						$newform_values['adcode'] = $_POST['adcode'];
 						$newform_values['desc'] = $_POST['desc'];
 						$newform_values['make_default'] = $_POST['make_default'];
 					}
 					
 				}elseif( $fn == 'all' ){
  				// handle all on/off first
  				$enableAll = (isset($_POST['all_on']) && $_POST['all_on'] == '1');
  				$submit_msgs[] = 'All Proximic Ads are ' . ($enableAll? "enabled" : "disabled");
  				
  				// do indivdidual entries now
  				foreach($options['ads'] as $adname => $val ){
 						$options['ads'][$adname]['enabled'] = $enableAll;
  				}
  				
  				update_option(PROXIMIC_AD_OPTION_NAME, $options);
  				
  			}elseif(isset($_POST['name']) && isset($options['ads'][$_POST['name']]) ){
  			 //handle single enable/disable/make default
  			 $adname = $_POST['name'];
  			 
  			 $highlight = $_POST['name'];
  			 
  				if ( $fn == 'enable' || $fn == 'disable' ){
  				 $enable = ($fn == 'enable')? true:false;
  				 
  				 $submit_msgs[] = 'Proximic Ad (' . $_POST['name'] . ') is ' . ($enable? "enabled" : "disabled");
  				 
  				 $options['ads'][$adname]['enabled'] = $enable; 				 
  				}
  				
  				if ( $fn == 'default' ){
  				 $options['default'] = $adname;
  				 $submit_msgs[] = 'Proximic Ad (' . $_POST['name'] . ') is set to default';
  				} 				
  				
  				if ( $fn == 'delete' ){
  				 AdDel($options, $adname, true, $isenable);
  				}
  				
  				update_option(PROXIMIC_AD_OPTION_NAME, $options);
  			}else{
  			 $submit_msgs[] = "err";
  				$submit_msgs[] = "Unknown function:  $fn";
  			}
  		}
  	}
 		
 
 
 		// place to pass msgs back to user about state of form submission
 		$highlight = "";
 		$submit_msgs = array();
 
 		$action_url = $_SERVER[PHP_SELF] . '?page=' . basename(__FILE__) . "&amp;#new_ad";
 
 		// Create option in options database if not there already:
 		$options = get_option(PROXIMIC_AD_OPTION_NAME);
 		if( !$options){
 			$options = Proximic_DefaultOption();
 		}
 
   $newform_values = NULL;
   //handle request now
   handleRequest($options, $submit_msgs, $highlight, $newform_values);
 		
 		$showinfo = false; //weather or not show info in footer
 		ProximicAd_Header($options, $submit_msgs, $showinfo);
 		
 		ProximicAd_Body($options, $highlight, $newform_values);
 		
 		ProximicAd_Footer($showinfo);
 
 		echo "\n</div>";
 	}
 	
 	//page in setting menu
 	function ProximicSettingPage(){
?>
<h1>Proximic Ad Manager - Settings</h1>

<p>The Proximic Ad Manager plugin is an essential tool for monetizing your blog with the Proximic ad units.<br /></p>

<p>Within a few steps, you can publish Proximic ads on your Wordpress blog and start earning money today. Proximic ads have high contextual relevancy and will increase your online revenue!<br /></p>

<h2>Getting Started</h2>
<p>1/ Setup your ad units on the Proximic Publisher Program</p>
<p>To use the Proximic Ad Manager plugin, you must be registered and approved through the Proximic Publisher Program. If you do not have a Proximic publisher account yet, please <a href="https://partners.proximic.com/users/signup" target="_blank">register here</a> first.<br />
There, you can create ad units with format and colors matching you Wordpress blog and preview the contextually matching ads resulting from a URL query of your choice.<br />
Once you are satisfied with your ad unit customization, copy the Proximic ad code provided.<br /></p>
 
<p>2/ Manage and publish your ad units on your Wordpress account</p>
<p>Once you have installed and activated your Proximic Ad Manager plugin, a new link will appear under the "Posts" section of your Wordpress Admin page:<br />
<a href="edit.php?page=<?php echo(PROXIMIC_AD_OPTION_PAGE); ?>">"Proximic Ads"</a>.<br />
Click on this link and create your first Proximic ad unit by following the steps 1, 2 and 3:</p>

    <h3> Step 1: Name your ad unit</h3>

<p>This name will be used for calling the ad unit from your blog posts or templates. You can also add a description that will appear in your list of ad units.</p>

    <h3> Step 2: Paste your Proximic ad code</h3>

<p>Paste here the Proximic ad code you have previously copied from the Proximic Publisher Program.</p>

    <h3> Step 3: Publish your ad unit</h3>

<p>This gives you the corresponding code tags to use in your blog posts or template for publishing your Proximic ad units.<br /></p>

<p>For templates, you can also go to Appearance > Widgets from your Wordpress Admin page and add the newly created Proximic widgets into your available template modules (header, sidebar...).</p>
<?php
 	}
 	
 	//insert ad into page
  function ProximicInsertAd($data) {
 		global	$doing_rss; 	/* will be true if getting RSS feed */
 	
 		$EDITING_PAGE = false;
 		$PLACEHOLDER = '<span style="background-color:#99CC00;border:1px solid #0000CC;padding:3px 8px 3px 8px;font-weight:bold;color:#111;">&lt;!--@@--&gt;</span>';
 		$PLACEHOLDER_DISABLED = '<span style="background-color:#99CC00;border:1px solid #0000CC;padding:3px 8px 3px 8px;font-weight:normal;font-style:italic;color:#C00;">&lt;!--@@--&gt;</span>';
 	
 		$options = get_option(PROXIMIC_AD_OPTION_NAME);
 		// NO ad IN FEEDS!
 		if($doing_rss){
 			return $data;
 		}
 		if( strstr($_SERVER['PHP_SELF'], 'post.php') ){
 			// user is editing a page or post, show placeholders, not real ads
 			$EDITING_PAGE = true;
 		}
 		
 		// set up some variables we need
 		$patts = array();
 		$subs = array();
 		$default = $options['default'];

 		//-- fill in stuff to search for ($patts) and substition blocks ($subs)
 		
 		foreach( $options['ads'] as $key => $vals ){
 			if( $key == $default ){
 				$patts[] = "<!--proximic-->";
 				$patts[] = "&lt;!&#8211;proximic&#8211;&gt;";
 				$subs[] = ($vals['enabled'] ? stripslashes($vals['adcode']) : "<!-- Default Ad: $key DISABLED-->\n");
 				$subs[] = ($vals['enabled'] ? stripslashes($vals['adcode']) : "<!-- Default Ad: $key DISABLED-->\n");
 				if($EDITING_PAGE){
 				 $subs[ sizeof($subs)-2] = str_replace('@@', 'proximic', ($vals['enabled'] ? $PLACEHOLDER : $PLACEHOLDER_DISABLED));
 				 $subs[ sizeof($subs)-1] = str_replace('@@', 'proximic', ($vals['enabled'] ? $PLACEHOLDER : $PLACEHOLDER_DISABLED));
 				}
 			}
 			$patts[] = "<!--proximic#" . $key . "-->";
 			$patts[] = "&lt;!&#8211;proximic#" . $key . "&#8211;&gt;";
 			$subs[] = ($vals['enabled'] ? stripslashes($vals['adcode']) : "<!-- $key DISABLED-->");
 			$subs[] = ($vals['enabled'] ? stripslashes($vals['adcode']) : "<!-- $key DISABLED-->");
 			if($EDITING_PAGE){
 			 $subs[ sizeof($subs)-2] = str_replace('@@', 'proximic#'.$key, ($vals['enabled'] ? $PLACEHOLDER : $PLACEHOLDER_DISABLED));
 			 $subs[ sizeof($subs)-1] = str_replace('@@', 'proximic#'.$key, ($vals['enabled'] ? $PLACEHOLDER : $PLACEHOLDER_DISABLED));
 			}
 		}
			return str_replace($patts, $subs, $data);
 
 	} // function ProximicInsertAd(...)
 	
  //add css and js file at admin header
  function add_admin_cssjs()
  {
   if ($_GET['page'] == PROXIMIC_AD_OPTION_PAGE){
 			echo '<link type="text/css" rel="stylesheet" href="'. get_bloginfo('wpurl') .'/wp-content/plugins';
 			echo ((PROXIMIC_AD_DIRPATH == "/plugins/")?"/":PROXIMIC_AD_DIRPATH) . 'proximic.css" />';
 			echo '<script src="'. get_bloginfo('wpurl') .'/wp-content/plugins';
 			echo ((PROXIMIC_AD_DIRPATH == "/plugins/")?"/":PROXIMIC_AD_DIRPATH) .'proximic.js" /></script>';
 		}
  }
 	
  // creates options page button under Options menu in WP-admin
 	function add_proximic_setting_menu()
 	{
 	 if (function_exists('add_options_page')) {
 	   add_options_page('Proximic Ad Manager', 'Proximic Ad Manager', 8, PROXIMIC_AD_SETTINGS_PAGE, array('proximicAdClass', 'ProximicSettingPage'));
 	   add_submenu_page('edit.php',"Proximic Ad Manager", "Proximic Ads", 8, PROXIMIC_AD_OPTION_PAGE, array('proximicAdClass','ProximicPanel'));
 	 } 	 
 	}
 	
 	// This is the function that outputs adsensem widget.
	 function widget($args,$n='') {
	  // $args is an array of strings that help widgets to conform to
	  // the active theme: before_widget, before_title, after_widget,
	  // and after_title are the array keys. Default tags: li and h2.
	  extract($args); //nb. $name comes out of this, hence the use of $n

   $options = get_option(PROXIMIC_AD_OPTION_NAME);
	  //If name not passed in (Sidebar Modules), extract from the widget-id (WordPress Widgets)
	  if($n==''){ $n=substr($args['widget_id'],9); } //Chop off beginning proximic- bit
	  $vals = $options['ads'][$n];

   echo $before_widget;
   echo (($vals['enabled'] ? stripslashes($vals['adcode']) : ""));
   echo $after_widget;
	 }
  
	 function widget_control($name)
		{
			echo '<label for="adsensem-'. $name .'-title" >Please Edit Preference of this ad in Posts -> Proximic Ads</label>';
  }
 	
 	// register widget for sidebar
 	function register_widget($name,$args){
			if(function_exists('wp_register_sidebar_widget')){
				wp_register_sidebar_widget('proximic-' . $name, "Ad#$name", array('proximicAdClass','widget'),$args,$name);
				wp_register_widget_control('proximic-' . $name, "Ad#$name", array('proximicAdClass','widget_control'), $args,$name); 
			} else if (function_exists('register_sidebar_module') ){
				register_sidebar_module('Ad #' . $name, 'proximic_sbm_widget', 'proximic-' . $name, $args );
				register_sidebar_module_control('Ad #' . $name, array('proximicAdClass','widget_control'), 'proximic-' . $name);
			}			
		}
 	
 	// init widget for sidebar
 	function init_widgets()
		{
			if (function_exists('wp_register_sidebar_widget') || function_exists('register_sidebar_module') ){
			 $options = get_option(PROXIMIC_AD_OPTION_NAME);
				/* Loop through available ads and generate widget one at a time */
				if(is_array($options['ads'])){
					foreach($options['ads'] as $key => $vals){
					 $args = array('name' => $key, 'height' => 80, 'width' => 300);
					 proximicAdClass::register_widget($key,$args);
					}
				}
   }
		}
 }
 	
	if( function_exists('add_action') ){
	 add_action('admin_head', array('proximicAdClass','add_admin_cssjs') );
	 add_action('admin_menu', array('proximicAdClass','add_proximic_setting_menu'));	 
	 add_action('widgets_init',  array('proximicAdClass','init_widgets'), 1);	 
 }

 if( function_exists('add_action') ){
  add_filter('the_content', array('proximicAdClass','ProximicInsertAd')); 
 }
 
 function proximicAd($name=NULL){
  global	$doing_rss; 	/* will be true if getting RSS feed */
	
		$options = get_option(PROXIMIC_AD_OPTION_NAME);
		// NO ad IN FEEDS!
		if($doing_rss){
			return;
		}
		$name = (isset($name)&&$name!="")? $name : $options['default'];
		
		foreach( $options['ads'] as $key => $vals ){
			if( $key == $name ){
				echo (($vals['enabled'] ? stripslashes($vals['adcode']) : ""));
				return;
			}
		}
		return;
 }
	
?>