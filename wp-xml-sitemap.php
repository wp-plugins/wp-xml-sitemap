<?php
/*
Plugin Name: WP XML Sitemap 
Plugin URI: http://vivacityinfotech.com/
Description: WPSitemap.Xml  is an XML file that lists the URLs for a site. It allows webmasters to include additional information about each URL: when it was last updated, how often it changes, and how important it is in relation to other URLs in the site. This allows search engines to crawl the site more intelligently.
Version: 1.0
Author URI: http://vivacityinfotech.com/
Requires at least: 3.8
License: GNU
  ---------------------------------------------------------------------------------------------
  Copyright 2014  vivacityinfotech.jaipur  (email : vivacityinfotech.jaipur@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
		if (!defined('ABSPATH')) die("Aren't you supposed to come here via WP-Admin?");

		if ( ! defined( 'WP_CONTENT_URL' ) )
 			 define( 'WP_CONTENT_URL', get_bloginfo( 'wpurl' ) . '/wp-content' );
		if ( ! defined( 'WP_CONTENT_DIR' ) )
			define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
		if ( ! defined( 'WP_PLUGIN_URL' ) )
			define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
		if ( ! defined( 'WP_PLUGIN_DIR' ) )
			define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );

// setting in plugin page
	function sm_settings_link($links) { 
  $settings_link = '<a href="options-general.php?page=wp-xml-sitemap.php">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
 	}
 
		$plugin = plugin_basename(__FILE__); 
		add_filter("plugin_action_links_$plugin", 'sm_settings_link' );
		add_action('admin_enqueue_scripts','enqueue_scripts');
		add_action( 'wp_enqueue_scripts', 'enqueue_scripts' );
	function enqueue_scripts()
	{
	
		wp_enqueue_style( 'my_stylesheet', plugins_url( 'css/style.css', __FILE__ ) );	
	
	}

function sm_setting_link() {
  		if (function_exists('add_options_page')) {
    		$sm_page = add_menu_page('WP XML Sitemap ', 'WP XML Sitemap ', 'administrator', basename(__FILE__), 'sm_setting_page');
  		}
	}
	
	

	function wp_sitemap_text() {
    
    	global $wpdb;
    	if (get_option('home_priority')) { $sm_frontpage = get_option('home_priority'); } else { $sm_frontpage = 0.5;}
    	if (get_option('sitexml_option')) { $sm_frontpage_frequency = get_option('sitexml_option'); } else { $sm_frontpage_frequency = 'weekly';}
    	if (get_option('general_priority')) { $pages_priority = get_option('general_priority'); } else { $pages_priority = 0.5;}
    	if (get_option('sm_generaloption')) { $pages_frequency = get_option('sm_generaloption'); } else { $pages_frequency = 'weekly';}
    	if (get_option('sm_frequency')) { $sm_frequency = get_option('sm_frequency');} else { $sm_frequency = "Disable"; }
    	if (!get_option('sm_Category')) { $sm_Category = 'NotInclude';} else {	$sm_Category = get_option('sm_Category'); }
    	if (!get_option('sm_tags')) { $sm_tags = 'NotInclude';} else {	$sm_tags = get_option('sm_tags'); }
    	if (!get_option('sm_last_change')) { $sm_last_change = 'Disable';} else {	$sm_last_change = get_option('sm_last_change'); }

    	$sm_xml_text =  '<?xml version="1.0" encoding="UTF-8"?>'."\n";
    	$sm_xml_text .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

    	$posts = $wpdb->prefix . "posts";
    	$query = "SELECT year(post_modified) AS y, month(post_modified) AS m, day(post_modified) AS d, ID,post_title, post_modified,post_name, post_type, post_parent FROM $posts WHERE post_status = 'publish' AND (post_type = 'page' OR post_type = 'post') ORDER BY post_date DESC";
    	$get_values = $wpdb->get_results($query); 
     
    	foreach ($get_values as $get_value) {
    
    		$permalink = utf8_encode($get_value->post_name);
    		$type = $get_value->post_type;
    		$sm_date = $get_value->y."-";
    
    		if ($get_value->m < 10) {
	  			$sm_date .= "0".$get_value->m."-";
    
    		}
    
    		else {
	 			 $sm_date .= $get_value->m."-";
    
   		 }
    		if ($get_value->d < 10) {
	 			 $sm_date .= "0".$get_value->d;
    		}
    
   		else {
	 		 	$sm_date .= $get_value->d;
    		}
    
	  		$id = $get_value->ID;
	  		$url = get_permalink($id);

    		$sm_xml_text .= "
    		 <url>
	  		<loc>".$url."</loc>";
	  		if ($sm_last_change == 'Enable') {
	       $sm_xml_text .= "<lastmod>".$sm_date."</lastmod>";
	 		 }

	  		if ($sm_frequency == 'Enable') {
	       $sm_xml_text .= "<changefreq>".$pages_frequency."</changefreq>
	       <priority>".$pages_priority."</priority>";
	  		}
    		 $sm_xml_text .= "</url>\n";
    
 		 }

    		if ($sm_Category == 'Include') {

	  			$terms = $wpdb->prefix . "terms";
	  			$taxonomy = $wpdb->prefix . "term_taxonomy";
	  			$query = "SELECT $terms.term_id, $taxonomy.taxonomy FROM $terms, $taxonomy WHERE ($terms.term_id = $taxonomy.term_id AND $taxonomy.taxonomy = 'category') ";
	  			$Categories = $wpdb->get_results($query); 

	  			$sm_date = date('Y-m-d');
	  
	  			foreach ($Categories as $Category) {
	        
	       	 $sm_xml_text .= "
				 <url>
					<loc>".get_category_link( $Category->term_id )."</loc>
					<lastmod>".$sm_date."</lastmod>";

					if ($sm_frequency == 'Enable') {
					     $sm_xml_text .= "<changefreq>".$pages_frequency."</changefreq>
					     <priority>".$pages_priority."</priority>";
					}
		    			$sm_xml_text .= "</url>\n";
	  
	  			}
    
    		}

    		if ($sm_tags == 'Include') {

	  			$terms = $wpdb->prefix . "terms";
	  			$taxonomy = $wpdb->prefix . "term_taxonomy";
	 			$query = "SELECT $terms.term_id, $taxonomy.taxonomy FROM $terms, $taxonomy WHERE ($terms.term_id = $taxonomy.term_id AND $taxonomy.taxonomy = 'post_tag') ";
	  			$smtags = $wpdb->get_results($query); 

	  			$sm_date = date('Y-m-d');
	  
	  			foreach ($smtags as $smtag) {
	        
	       	 $sm_xml_text .= "
				   <url>
					<loc>".get_tag_link( $smtag->term_id )."</loc>
					<lastmod>".$sm_date."</lastmod>";
				
						if ($sm_frequency == 'Enable') {
					     $sm_xml_text .= "<changefreq>".$pages_frequency."</changefreq>
					     <priority>".$pages_priority."</priority>";
						}
		   			 $sm_xml_text .= "</url>\n";
	  
	  				}
    
   			 }

    		 $sm_xml_text .= '</urlset>'."\n";
   		 return $sm_xml_text;

	}

	function created_sitemap_xml() {
     
		$docname = "wpsitemap.xml";
     
		if (get_option('xmlfile_path') == "1") { $file_handler = fopen(WP_PLUGIN_DIR.'/wp-xml-sitemap/'.$docname, "w+");  } 

		elseif (get_option('xmlfile_path') == "2") { $file_handler = fopen(ABSPATH.$docname, "w+"); }
		else {	$file_handler = fopen(WP_PLUGIN_DIR.'/wp-xml-sitemap/'.$docname, "w+");   }
     
     
      if (!$file_handler) {
	      die;
      }
     
     	else {
     		$text = wp_sitemap_text();
     		fwrite($file_handler, $text);
     		fclose($file_handler);
     	}
	}

	function sm_setting_page() { 

		if (get_option('xmlfile_path') == "1") { $path = WP_PLUGIN_URL.'/wp-xml-sitemap/wpsitemap.xml';  } 

		elseif (get_option('xmlfile_path') == "2") { $path = get_option( 'siteurl' ).'/wpsitemap.xml'; }
		else {	$path = WP_PLUGIN_URL.'/wp-xml-sitemap/wpsitemap.xml';  }


	?>
  		<h2 >Wp XML Sitemap </h2>
   	<h3 style="color: #0849b5; margin-bottom: 0;">WP XML Sitemap</h3>
     	<p style="margin:0 0 1em 0;">
      <br />
      <strong>The XML sitemap is automatically regenerated when you publish or delete a new post/page.</strong>
      </p>
  		<form method="post" action="options.php">
     <?php wp_nonce_field('update-options'); ?>
		<p id="flip" style="font-weight:bold; cursor:pointer;"> Attributes </span></p>

	<div class="wrapper" >    
 		<table width="50%">
	  		<tr>
	       <td width="80%"> If you want to change the  <strong>Frontpage</strong> Option <strong>then you can set enable </strong><br/> <strong> by default disabled.</strong></td>
	       <td>
		    <?php
			 if (!get_option('sm_frequency')) { $sm_frequency = 'Disable';}
			 else {	$sm_frequency = get_option('sm_frequency'); }
		    ?>
		    <select name="sm_frequency" id="sitexml_option" type="text" value="<?php echo $sm_frequency ?>" />
		    <option value="Disable" <?php if($sm_frequency=="Disable") {echo 'selected';}?>>disable</option>
		    <option value="Enable" <?php if($sm_frequency=="Enable") {echo 'selected';}?>>enable</option>
		    </select>
	       </td>
	 	 	</tr>
     	</table>
    <div >
	  	<table width="50%">
	  		<tr>
	       <p style="font-weight:bold;">Frontpage Option</p>
	       <th width="150">Priority</th>
	       <td width="100">
		    <select name="home_priority" id="home_priority" type="text" value="<?php echo get_option('home_priority'); ?>" />
		    <?php for ($i=0; $i<1.05; $i+=0.1) {
			 echo "<option value='".$i."' ";
			 if (get_option('home_priority')==$i) {
			      echo ' selected';
			 } 
			 
			 echo ">";
			 if($i==0) { echo "0.".$i;} 
			 elseif($i==1.0) { echo $i.'0';} 
			 else {echo $i;}
			 echo "</option>";
		    } 
		    ?>
		    </select>
	       </td>
	       <th width="150">Frequency</th>
	       <td width="100">
		    <select name="sitexml_option" id="sitexml_option" type="text" value="<?php echo get_option('sitexml_option'); ?>" />
		    <option value="always" <?php if(get_option('sitexml_option')=="always") {echo 'selected';}?>>always</option>
		    <option value="hourly" <?php if(get_option('sitexml_option')=="hourly") {echo 'selected';}?>>hourly</option>
		    <option value="weekly" <?php if(get_option('sitexml_option')=="weekly") {echo 'selected';}?>>weekly</option>
		    <option value="monthly" <?php if(get_option('sitexml_option')=="monthly") {echo 'selected';}?>>monhtly</option>
		    <option value="yearly" <?php if(get_option('sitexml_option')=="yearly") {echo 'selected';}?>>yearly</option>
		    <option value="never"  <?php if(get_option('sitexml_option')=="never") {echo 'selected';}?>>never</option>
		    </select>
		    </td>
	  		</tr>
	  	</table>
	   <table width="50%">
	  		<tr>
	  		<p style="font-weight:bold;">Other Option</p>
	  
	       <th width="150">Priority</th>
	       <td width="100">
	       <select name="general_priority" id="general_priority" type="text" value="<?php echo get_option('general_priority'); ?>" />
		    <?php for ($i=0; $i<1.05; $i+=0.1) {
			 echo "<option value='".$i."' ";
			 if (get_option('general_priority')==$i) {
			      echo ' selected';
			 } 
			 
			 echo ">";
			 if($i==0) { echo "0.".$i;} 
			 elseif($i==1.0) { echo $i.'0';} 
			 else {echo $i;}
			 echo "</option>";
		    } 
		    ?>
		    </select>
	       </td>
	       
	       <th width="150">Frequency</th>
	       <td width="100">
		    <select name="sm_generaloption" id="sm_generaloption" type="text" value="<?php echo get_option('sm_generaloption'); ?>" />
		    <option value="always" <?php if(get_option('sm_generaloption')=='always') {echo 'selected';}?>>always</option>
		    <option value="hourly" <?php if(get_option('sm_generaloption')=='hourly') {echo 'selected';}?>>hourly</option>
		    <option value="weekly" <?php if(get_option('sm_generaloption')=='weekly') {echo 'selected';}?>>weekly</option>
		    <option value="monthly" <?php if(get_option('sm_generaloption')=='monthly') {echo 'selected';}?>>monthly</option>
		    <option value="yearly" <?php if(get_option('sm_generaloption')=='yearly') {echo 'selected';}?>>yearly</option>
		    <option value="never" <?php if(get_option('sm_generaloption')=='never') {echo 'selected';}?>>never</option>
		    </select>
	       </td>
	  		</tr>
	 	 </table>
     </div> 
  
     <div style="margin: 20px 0 0 0;">
    
     
     <table>
     <tr>
     <td>Store your Xml file here :</td>
     <td>
     <select name="xmlfile_path" id="xmlfile_path" type="text" value="<?php echo get_option('xmlfile_path'); ?>" />
     <option value="1" <?php if (get_option('xmlfile_path') == "1") { echo "selected"; } ?> > Plugin's folder</option>
     <option value="2" <?php if (get_option('xmlfile_path') == "2") { echo "selected"; } ?> >Website root folder</option>
     </select>
     </td>
     </tr>
     </table></div> 
  <h3 style="color: #0849b5; margin-bottom: 0;">Categories and Tags</h3>
     <p style="margin:0 0 1em 0;">
	  Include the categories and tags into your generated wpsitemap.xml.
     </p>
     <p style="font-weight:bold;"><span>Categories: &nbsp;&nbsp;
	  <?php
	       if (!get_option('sm_Category')) { $sm_Category = 'NotInclude';}
	       else {	$sm_Category = get_option('sm_Category'); }
	  ?>
	  <select name="sm_Category" id="sm_Category" type="text" value="<?php echo $sm_tags ?>" />
	  <option value="NotInclude" <?php if($sm_Category=="NotInclude") {echo 'selected';}?>>NO</option>
	  <option value="Include" <?php if($sm_Category=="Include") {echo 'selected';}?>>Yes</option>
	  </select></span>
    
     <span style="font-weight:bold; margin-left:25px">Tags: &nbsp;&nbsp;
	  <?php
	       if (!get_option('sm_tags')) { $sm_tags = 'NotInclude';}
	       else {	$sm_tags = get_option('sm_tags'); }
	  ?>
	  <select name="sm_tags" id="sm_tags" type="text" value="<?php echo $sm_tags ?>" />
	  <option value="NotInclude" <?php if($sm_tags=="NotInclude") {echo 'selected';}?>>No</option>
	  <option value="Include" <?php if($sm_tags=="Include") {echo 'selected';}?>>Yes</option>
	  </select></span>
     </p>
 
     <p style="font-weight:bold;">Last changed in Website </p>
     <table width="50%">
	  <tr>
	       <td width="80%"> If you want to include recent changes in the website to   <strong>sitemap xml </strong>  <strong>then you can set enable </strong><br/> <strong> by default disabled.</strong></td>
	       <td>
		    <?php
			 if (!get_option('sm_last_change')) { $sm_last_change = 'Disable';}
			 else {	$sm_last_change = get_option('sm_last_change'); }
		    ?>
		    <select name="sm_last_change" id="sitexml_option" type="text" value="<?php echo $sm_last_change ?>" />
		    <option value="Disable" <?php if($sm_last_change=="Disable") {echo 'selected';}?>>disable</option>
		    <option value="Enable" <?php if($sm_last_change=="Enable") {echo 'selected';}?>>enable</option>
		    </select>
	       </td>
	  </tr>
	
     </table>
      
   
     <input type="hidden" name="action" value="update" />
     <input type="hidden" name="page_options" value="home_priority,general_priority,sitexml_option,sm_generaloption,xmlfile_path,sm_frequency, sm_Category, sm_tags,sm_last_change" />

     <p style="margin-top: 20px;">
     <input type="submit" value="<?php _e('Save Changes'); ?>" / id="submit">
     </p>
     </div>
	<?php
	created_sitemap_xml();
	}


		if ( is_admin() ){
     	add_action('admin_menu', 'sm_setting_link');
		}

	function install_sm () {

     	created_sitemap_xml();
	}
	register_activation_hook(WP_PLUGIN_DIR.'/wp-xml-sitemap/wp-xml-sitemap.php','install_sm');
	add_action ( 'activate_plugin', 'created_sitemap_xml' );
	add_action ( 'publish_post', 'created_sitemap_xml' );
	add_action ( 'publish_page', 'created_sitemap_xml' );
	add_action ( 'trashed_post', 'created_sitemap_xml' );
	?>
