<?php
/*
 * Plugin Name:   cbnet Favicon
 * Plugin URI:    http://www.chipbennett.net/wordpress/plugins/cbnet-favicon/
 * Description:   Easily add favicon to your blog . (Note: this plugin is a fork of the MaxBlogPress Favicon plugin, with registration/activiation functionality removed.) Adjust settings <a href="options-general.php?page=cbnet-favicon/cbnet-favicon.php">here</a>.
 * Version:       2.1.1
 * Author:        chipbennett
 * Author URI:    http://www.chipbennett.net/
 *
 * License:       GNU General Public License, v2 (or newer)
 * License URI:  http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program was modified from MaxBlogPress Favicon plugin, version 2.0.9, 
 * Copyright (C) 2007 www.maxblogpress.com, released under the GNU General Public License.
 */
 
define('cbnetFAVICON_NAME', 'cbnet Favicon');	// Name of the Plugin
define('cbnetFAVICON_VERSION', '2.1.1');				// Current version of the Plugin
$cbnet_abspath = str_replace("\\","/",ABSPATH);       // required for Windows
define('cbnet_ABSPATH', $cbnet_abspath);

/**
 * cbnetFavIcon - cbnet Favicon Class
 * Holds all the necessary functions and variables
 */
class cbnetFavIcon 
{
    /**
     * cbnet Favicon plugin path
     * @var string
     */
	var $cbnetfavicon_path = '';
	
    /**
     * cbnet Favicon plugin's icon directory absolute path
     * @var string
     */
	var $cbnetfavicon_fullpath = '';
	
    /**
     * cbnet Favicon plugin's icon directory full path
     * @var string
     */
	var $cbnetfavicon_dir_path = '';
	
    /**
     * cbnet Favicon image
     * @var string
     */
	var $cbnetfavicon = '';

	/**
	 * Constructor. Adds cbnet Favicon plugin's actions/filters.
	 * @access public
	 */
	function cbnetFavIcon() { 
		global $wp_version;
		$this->cbnetfavicon_path     = preg_replace('/^.*wp-content[\\\\\/]plugins[\\\\\/]/', '', __FILE__);
		$this->cbnetfavicon_path     = str_replace('\\','/',$this->cbnetfavicon_path);
		$this->siteurl             = get_bloginfo('wpurl');
		$this->siteurl             = (strpos($this->siteurl,'http://') === false) ? get_bloginfo('siteurl') : $this->siteurl;
		$this->cbnet_fullpath        = $this->siteurl.'/wp-content/plugins/'.substr($this->cbnetfavicon_path,0,strrpos($this->cbnetfavicon_path,'/')).'/';
		$this->cbnetfavicon_fullpath = $this->cbnet_fullpath.'icons/';
		$curr_path  		       = str_replace("\\", "/", __FILE__);
		$this->cbnetfavicon_dir_path = substr($curr_path, 0, strrpos($curr_path,'/')).'/icons/';
		$this->img_how             = '<img src="'.$this->cbnet_fullpath.'images/how.gif" border="0" align="absmiddle">';
		$this->img_comment         = '<img src="'.$this->cbnet_fullpath.'images/comment.gif" border="0" align="absmiddle">';

	    add_action('activate_'.$this->cbnetfavicon_path, array(&$this, 'cbnetfaviconActivate'));
		add_action('admin_menu', array(&$this, 'cbnetfaviconAddMenu'));
		$this->cbnet_activate = get_option('cbnet_activate');
		$this->cbnetfavicon = get_option('cbnet_favicon');
		$this->cbnet_code_inj_mode = get_option('cbnet_code_inj_mode');
		if ( $this->cbnetfavicon != '' ) {
			if ( $wp_version < 2.1 ) {
				add_action('wp_head', array(&$this, 'cbnetfaviconAdd'), 99);
			} else {
				if ( $this->cbnet_code_inj_mode != 1 ) {
					add_filter('get_header', array(&$this, 'cbnetfaviconAddStart'), 99);
				}
				add_action('wp_head', array(&$this, 'cbnetfaviconAddStart'), 99);
				add_filter('get_footer', array(&$this, 'cbnetfaviconAddEnd'), 99);
			}
			add_action('template_redirect', array(&$this, 'cbnetfaviconRedirect'));
			add_action('admin_head', array(&$this, 'cbnetfaviconAdd'));
			add_action('rss2_head', array(&$this, 'cbnetfaviconAddToRSS2Feed'));
			add_action('atom_head', array(&$this, 'cbnetfaviconAddToAtomFeed'));
		}		
	}
	
	/**
	 * Called when plugin is activated. Adds option value to the options table.
	 * @access public
	 */
	function cbnetfaviconActivate() {
		add_option('cbnet_favicon', '', 'cbnet Favicon plugin option', 'no');
		return true;
	}
	
	/**
	 * Returns the correct favicon path
	 * @access public
	 */
	function cbnetfaviconRedirect() {
		global $wpdb;
		if ( is_404() && strpos($_SERVER['REQUEST_URI'],'favicon.ico') !== false ) {
			$favicon_path = get_option('cbnet_favicon');
			header( "Location: $favicon_path" );
		}
	}
	
	/**
	 * Returns the type/extension of image
	 * @access public
	 */
	function cbnetfaviconType($faviconpath) {
		if ( preg_match("/\.gif$/i", $faviconpath) )     return 'gif';
		if ( preg_match("/\.ico$/i", $faviconpath) ) 	 return 'x-icon';
		if ( preg_match("/\.jp[e]?g$/i", $faviconpath) ) return 'jpg';
		if ( preg_match("/\.png$/i", $faviconpath) )	 return 'png';
		else return '';
	}
	
	/**
	 * Adds favicon to the rss2 feed
	 * @access public
	 */
	function cbnetfaviconAddToRSS2Feed() {
		$cbnet_rss  = '<image>'."\n";
		$cbnet_rss .= '<link>'.get_bloginfo_rss('url').'</link>'."\n";
		$cbnet_rss .= '<url>'.$this->cbnetfavicon.'</url>'."\n";
		$cbnet_rss .= '<title>'.get_bloginfo_rss('name').'</title>'."\n";
		$cbnet_rss .= '</image>'."\n";
		echo $cbnet_rss;
	}
	
	/**
	 * Adds favicon to the atom feed
	 * @access public
	 */
	function cbnetfaviconAddToAtomFeed() {
		$cbnet_atom  = '<icon>'.$this->cbnetfavicon.'</icon>'."\n";
		$cbnet_atom .= '<logo>'.$this->cbnetfavicon.'</logo>'."\n";
		echo $cbnet_atom;
	}
	
	/**
	 * Adds favicon
	 * @access public
	 */
	function cbnetfaviconAdd() {
		$favicon_type = $this->cbnetfaviconType($this->cbnetfavicon);
		if ( trim($favicon_type) != '' ) {
			$cbnet_favicon = '<link rel="shortcut icon" href="'.$this->cbnetfavicon.'" type="image/'.$favicon_type.'" />';
		} else {
			$cbnet_favicon = '<link rel="shortcut icon" href="'.$this->cbnetfavicon.'" />';
		}
		return $cbnet_favicon;	
	}
	
	/**
	 * Start Output Buffer
	 * @access public
	 */
	function cbnetfaviconAddStart() {
		if ( $this->cbnet_header_executed != 1 ) {
			$this->cbnet_header_executed = 1;
			if ( $this->cbnet_code_inj_mode != 1 ) {
				ob_start();
			} else {
				$cbnet_favicon = $this->cbnetfaviconAdd(1);
				echo $cbnet_favicon;
			}
		}
	}
	
	/**
	 * Adds favicon. Gets content from output buffer and displays
	 * @access public
	 */
	function cbnetfaviconAddEnd() {
		if ( $this->cbnet_code_inj_mode != 1 ) {
			$cbnet_output  = ob_get_contents();
			ob_end_clean();
			$cbnet_favicon = $this->cbnetfaviconAdd(1);
			$cbnet_output = str_replace("</head>", "\n $cbnet_favicon \n </head>", $cbnet_output);
			echo $cbnet_output;
		} 
	}

	/**
	 * Adds "cbnet Favicon" link to admin Options menu
	 * @access public 
	 */
	function cbnetfaviconAddMenu() {
		add_options_page('cbnet Favicon', 'cbnet Favicon', 'manage_options', $this->cbnetfavicon_path, array(&$this, 'cbnetfaviconOptionsPg'));
	}
	
	/**
	 * Creates favicon directory to upload icons
	 * @access public 
	 */
	function cbnetfaviconMakeDir($cbnet_dir) {
		$cbnet_upload_path = cbnet_ABSPATH.'wp-content/'.$cbnet_dir;
		if ( is_admin() && !is_dir($cbnet_upload_path) ) {
			@mkdir($cbnet_upload_path);
		}
		return $cbnet_upload_path;
	}
	
	/**
	 * Page Header
	 */
	function cbnetfiHeader() {
		echo '<h2>'.cbnetFAVICON_NAME.' '.cbnetFAVICON_VERSION.'</h2>';
	}
	
	/**
	 * Page Footer
	 */
	function cbnetfiFooter() {
		echo '<p style="text-align:center;margin-top:3em;"><strong>'.cbnetFAVICON_NAME.' '.cbnetFAVICON_VERSION.' by <a href="http://www.chipbennett.net/" target="_blank" >Chip Bennett</a></strong></p>';
	}
	
	/**
	 * Displays the page content for "cbnet Favicon" Options submenu
	 * Carries out all the operations in Options page
	 * @access public 
	 */
	function cbnetfaviconOptionsPg() {
		global $wpdb;
		$this->cbnetfavicon_request = $_REQUEST['cbnetfavicon'];
		
			if ( $this->cbnetfavicon_request['save_more'] ) {
				$cbnet_code_inj_mode = $this->cbnetfavicon_request['cbnet_code_inj_mode'];
				update_option('cbnet_code_inj_mode', $cbnet_code_inj_mode);
				echo '<div id="message" class="updated fade"><p><strong>Options Saved Successfully.</strong></p></div>';
			} else if ( $this->cbnetfavicon_request['save'] ) {
				$success = '';
				$cbnet_dir = 'cbnet-favicon';
				$cbnet_valid_file = array("image/x-icon", "image/png", "image/jpeg", "image/pjpeg", "image/gif", "image/bmp");
				if ( $this->cbnetfavicon_request['link_type'] == 3 ) { // Upload from URL
					$cbnet_upload_path = $this->cbnetfaviconMakeDir($cbnet_dir);
					$cbnet_src_url     = trim($this->cbnetfavicon_request['favicon_upload_2']);
					$cbnet_src_info    = pathinfo($cbnet_src_url);
					$cbnet_src_file    = $cbnet_src_info['basename'];
					$cbnet_src_ext     = $cbnet_src_info['extension'];
					if ( $cbnet_src_ext == 'ico' || $cbnet_src_ext == 'jpg' || $cbnet_src_ext == 'gif' || $cbnet_src_ext == 'png' || $cbnet_src_ext == 'bmp' ) {
						$cbnet_dest_url    = $cbnet_upload_path.'/'.$cbnet_src_file;
						$cbnet_favicon_url = $this->siteurl.'/wp-content/'.$cbnet_dir.'/'.$cbnet_src_file; 
						if ( ini_get('allow_url_fopen') ) {
							@copy($cbnet_src_url, $cbnet_dest_url);
							$success = 1;
						} else if ( extension_loaded('curl') ) {
							$ch = curl_init();
							curl_setopt($ch, CURLOPT_URL, $cbnet_src_url);
							curl_setopt($ch, CURLOPT_HEADER, false);
							curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							set_time_limit(300); // 5 minutes for PHP
							curl_setopt($ch, CURLOPT_TIMEOUT, 300); // 5 minutes for CURL
							$cbnet_outfile = fopen($cbnet_dest_url, 'wb');
							curl_setopt($ch, CURLOPT_FILE, $cbnet_outfile);
							curl_exec($ch);
							fclose($cbnet_outfile);
							curl_close($ch); 	
							$success = 1;
						} else {
							$cbnet_msg = "Favicon couldn't be uploaded from URL. 'URL file-access' and/or 'CURL' are/is disabled in your server.";
						}
					} else {
						$cbnet_msg = 'Favicon couldn\'t be uploaded from URL. Invalid file type';
					}
					if ( $success == 1 ) {
						$this->cbnetfavicon = $cbnet_favicon_url;
						$cbnet_msg = 'Favicon uploaded from URL and activated.';
					} else {
						$cbnet_msg = 'Favicon couldn\'t be uploaded from URL.';
					}
				} else if ( $this->cbnetfavicon_request['link_type'] == 2 ) { // Upload from local computer
					$cbnet_upload_path   = $this->cbnetfaviconMakeDir($cbnet_dir);
					$upload_1_name     = $_FILES['favicon_upload_1']['name'];
					$upload_1_type     = $_FILES['favicon_upload_1']['type'];
					$upload_1_size     = $_FILES['favicon_upload_1']['size'];
					$upload_1_tmp_name = $_FILES['favicon_upload_1']['tmp_name'];
					$cbnet_favicon_path  = $cbnet_upload_path.'/'.$upload_1_name;
					$cbnet_favicon_url   = $this->siteurl.'/wp-content/'.$cbnet_dir.'/'.$upload_1_name; 
					if ( in_array($upload_1_type,$cbnet_valid_file) ) {
						if ( move_uploaded_file($upload_1_tmp_name, $cbnet_favicon_path) ) {
							$cbnet_msg = 'Favicon uploaded from local computer and activated.';
						} else {
							$cbnet_msg = 'Favicon couldn\'t be uploaded from local computer.';
						}
					} else {
						$cbnet_msg = 'Favicon couldn\'t be uploaded from local computer. Invalid file type.';
					}
					$this->cbnetfavicon = $cbnet_favicon_url;
				} else { // Use own link
					$this->cbnetfavicon = $this->cbnetfavicon_request['favicon'];
					$cbnet_msg = 'Favicon activated.';
				}
				update_option("cbnet_favicon", $this->cbnetfavicon);
				echo '<div id="message" class="updated fade"><p><strong>'. __($cbnet_msg, 'cbnetfavicon') .'</strong></p></div>';
			}
		
			// create an array of icons
			$icons_array = array();
			if ( $dir = opendir($this->cbnetfavicon_dir_path) ) {
				while ( ($file = readdir($dir)) !== false ) {
					if ( $file != "." && $file != ".." ) {
						$file_info = pathinfo($file);
						if ( $file_info['extension'] == 'ico' )
							$icons_array[] = $file;
					}
				}
				closedir($dir);
			}
			if ( trim($this->cbnetfavicon) == '' ) {
				$cbnetfavicon_curr_img = $this->cbnetfavicon_fullpath.'1x1.gif';
			} else {
				$cbnetfavicon_curr_img = $this->cbnetfavicon;
			}
			if ( strpos($_SERVER['HTTP_USER_AGENT'],'MSIE') === false ) { 
				$icon_span_style = 'style="padding:4px 5px 6px 5px;background-color:#dddddd;"';
				$icon_style = '';
			} else {
				$icon_span_style = 'style="background-color:#dddddd;"';
				$icon_style = 'style="padding:5px 4px 4px 4px;"';
			}
			$cbnet_code_inj_mode = get_option('cbnet_code_inj_mode');
			if ( $cbnet_code_inj_mode == 1 ) $cbnet_code_inj_mode_1_chk = 'checked';
			else $cbnet_code_inj_mode_2_chk = 'checked';
			?>
			<script type="text/javascript"><!--
			function selectIcon(icon) {
				document.getElementById('cbnetfavicon_curr').value   = icon.src;
				document.getElementById('cbnetfavicon_curr_img').src = icon.src;
			}
			function bgColorAlter(cell, onfocus) {
				if ( onfocus == 1 ) {
					cell.style.backgroundColor = '#dddddd';
				} else {
					cell.style.backgroundColor = '#f1f1f1';
				}
			}
			function cbnetSwitchType(curr) {
				var cbnet_type_1  = document.getElementById('cbnet_type_1');
				var cbnet_type_2  = document.getElementById('cbnet_type_2');
				var cbnet_type_3  = document.getElementById('cbnet_type_3');
				var cbnet_icon    = document.getElementById('cbnet_icon');
				var cbnet_icon_td = document.getElementById('cbnet_icon_td');
				if ( curr == 1 ) {
				    cbnet_icon_td.style.width  = '6%';
					cbnet_icon.style.display   = 'block';
					cbnet_type_1.style.display = 'block';
					cbnet_type_2.style.display = 'none';
					cbnet_type_3.style.display = 'none';
				} else if ( curr == 2 ) {
				    cbnet_icon_td.style.width  = '1%';
					cbnet_icon.style.display   = 'none';
					cbnet_type_1.style.display = 'none';
					cbnet_type_2.style.display = 'block';
					cbnet_type_3.style.display = 'none';
				} else if ( curr == 3 ) {
				    cbnet_icon_td.style.width  = '1%';
					cbnet_icon.style.display   = 'none';
					cbnet_type_1.style.display = 'none';
					cbnet_type_2.style.display = 'none';
					cbnet_type_3.style.display = 'block';
				}
			}
			function cbnetShowHide(Div, Img) {
				var divCtrl = document.getElementById(Div);
				var theImg = document.getElementById(Img);
				if ( divCtrl.style == '' || divCtrl.style.display == 'none' ) {
					divCtrl.style.display = 'block';
					theImg.src = '<?php echo $this->cbnet_fullpath;?>images/minus.gif';
				} else if ( divCtrl.style != '' || divCtrl.style.display == 'block' ) {
					divCtrl.style.display = 'none';
					theImg.src = '<?php echo $this->cbnet_fullpath;?>images/plus.gif';
				}
			}//--></script>
			<div class="wrap">
			 <?php $this->cbnetfiHeader(); ?>
			 <form method="post" action="" enctype="multipart/form-data">
			 <p>
			 <table border="0" width="100%" cellpadding="1" cellspacing="1" style="background-color:#ffffff;">
			  <tr>
			   <td>&nbsp;</td>
			   <td>
			   <input type="radio" name="cbnetfavicon[link_type]" id="link_type_1" value="1" <?php echo 'checked';?> onclick="cbnetSwitchType(1)" /> Favicon Link &nbsp;    
			   <input type="radio" name="cbnetfavicon[link_type]" id="link_type_2" value="2" onclick="cbnetSwitchType(2)" /> Upload Favicon From My Computer &nbsp;     
			   <input type="radio" name="cbnetfavicon[link_type]" id="link_type_3" value="3" onclick="cbnetSwitchType(3)" /> Upload From URL &nbsp; 
			   </td>
			  </tr>
			  <tr>
			   <td width="105"><strong><?php _e('Favorite Icon', 'cbnetfavicon'); ?>: </strong></td>
			   <td>
			    <table width="100%" border="0">
				 <tr>
				  <td width="1%" id="cbnet_type_1" style="display:block">
			      <input type="text" name="cbnetfavicon[favicon]" id="cbnetfavicon_curr" value="<?php echo $this->cbnetfavicon;?>" size="45">
			      </td>
				  <td width="1%" id="cbnet_type_2" style="display:none">
				  <input type="file" name="favicon_upload_1" id="favicon_upload_1" value="" size="25">
				  </td>
				  <td width="1%" id="cbnet_type_3" style="display:none">
			      <input type="text" name="cbnetfavicon[favicon_upload_2]" id="favicon_upload_2" value="" size="45">
				  </td>
				  <td id="cbnet_icon_td" width="6%">
			      <span id="cbnet_icon" <?php echo $icon_span_style;?>><img src="<?php echo $cbnetfavicon_curr_img;?>" id="cbnetfavicon_curr_img" width="16" height="16" border="0" align="absmiddle" <?php echo $icon_style;?> /></span>
				  </td>
				  <td width="91%">
				  <input type="submit" name="cbnetfavicon[save]" value="<?php _e('Save', 'cbnetfavicon'); ?>" class="button" />
				  </td>
                 </tr>
				</table>
			   </td>
			  </tr>
			 </table>
			 </p>
			 <p>
			 <strong><?php _e('Choose the favorite icon you want to use', 'cbnetfavicon'); ?>: </strong>
			 <table border="0" width="300" cellpadding="8" cellspacing="3" bgcolor="#ffffff">
			 <?php 
			 $i = 0;
			 foreach ( $icons_array as $icon ) { 
				if ( $i == 0 ) print '<tr>';
				else if ( $i%10 == 0 ) print '</tr><tr>';
				$i++;
				print '<td style="background-color:#f1f1f1; width:50px; height:16px; text-align:center; padding:8px;" onmouseover="bgColorAlter(this,1);" onmouseout="bgColorAlter(this,0);"><img src="'.$this->cbnetfavicon_fullpath.$icon.'" onclick="selectIcon(this);" style="cursor:hand;cursor:pointer;border:0"></td>';
			 } 
			 ?>
			 </table>
			 </p>
			 <p>
			 <strong><?php _e('Not happy with the above icons? You can get more free icons from these websites', 'cbnetfavicon'); ?>: </strong><br />
			 <a href="http://www.vistaicons.com/" target="_blank">Vista Icons</a><br />
			 <a href="http://www.famfamfam.com/" target="_blank">FAMFAMFAM</a><br />
			 <a href="http://www.free-icons.co.uk/" target="_blank">Free Icons</a><br />
			 <a href="http://www.iconarchive.com/" target="_blank">Icon Archive</a><br />
			 </p>
			 <span style="font-size:14px;font-weight:bold;"><a onclick="cbnetShowHide('cbnet_adv_opt','cbnet_adv_opt_img');" style="cursor:hand;cursor:pointer"><img src="<?php echo $this->cbnet_fullpath;?>images/plus.gif" id="cbnet_adv_opt_img" border="0" /><strong>Advanced Options (optional)</strong></a></span>
			 <div id="cbnet_adv_opt" style="display:none">
			 <table border="0" width="60%" cellspacing="2" cellpadding="5" style="border:1px solid #dddddd; background-color:#f1f1f1; padding:0;">
			  <tr>
			   <td style="background-color:#f1f1f1" width="45%"><strong>Plugin code injecting mode:</strong></td>
			   <td style="background-color:#f1f1f1">
			   <input type="radio" name="cbnetfavicon[cbnet_code_inj_mode]" value="1" <?php echo $cbnet_code_inj_mode_1_chk;?> /> wp_head() &nbsp;&nbsp;&nbsp;&nbsp;
			   <input type="radio" name="cbnetfavicon[cbnet_code_inj_mode]" value="2" <?php echo $cbnet_code_inj_mode_2_chk;?> /> Buffer Caching</td>
			  </tr>
			  <tr>
			   <td style="background-color:#ffffff" colspan="3"><input type="submit" class="button" name="cbnetfavicon[save_more]" value="Save" /></td>
			  </tr>
			 </table>
			 </div>
			 </form>
			 <?php $this->cbnetfiFooter(); ?>
			</div>
			<?php
		}
	
		
} // Eof Class

$cbnetFavIcon = new cbnetFavIcon();
?>