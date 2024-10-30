<?php
/**
* Plugin Name: Create Child Theme
* Description: Helps you creates a new child theme quickly
* Author: webdeveloperphp6
* Version: 2.0.0
* Plugin URI: https://sitefreelancing.com/product/create-child-theme/
* Author URI: https://sitefreelancing.com/
* License: GPL2
* License URI:  http://www.gnu.org/licenses/gpl-2.0.html
**/
 

// add submenu page under Tools
function cct_menu() {
	global $starter_plugin_admin_page;
	$starter_plugin_admin_page = add_submenu_page ('tools.php', __('Create Child Theme', 'cct'), __('Create Child Theme', 'cct'), 'manage_options', 'CreateChildTheme', 'cctMainFunction');
}
add_action('admin_menu', 'cct_menu');


///////////////////////////////////////////////
// main function that generates the  admin page
function cctMainFunction () {
	
// check that the user has the required capability 
    if (!current_user_can('manage_options'))
    {
      wp_die( __('You do not have sufficient privileges to access this page. Sorry!') );
    }	
	
	///////////////////////////////////////
	// MAIN AMDIN CONTENT SECTION
	///////////////////////////////////////
	
	// display heading with icon WP style
	?>
    <div class="wrap">
    <div id="icon-index" class="icon32"><br></div>
    <h2>Child Theme Wizard</h2>
    
    <?php
    
	// if isset 
	if( isset($_POST[ 'hiddenfield' ]) && $_POST[ 'hiddenfield' ] == 'Y' && isset($_POST[ 'parent' ]) && isset($_POST[ 'include-gpl' ]) ) {
		
		// call function with sanitized values
		$newchildtheme = array(
		'parent'      => sanitize_text_field($_POST['parent']),
		'title'       => sanitize_text_field($_POST['title']),
		'description' => sanitize_text_field($_POST['description']),
		'url'         => sanitize_text_field($_POST['url']),
		'author'      => sanitize_text_field($_POST['author']),
		'author-url'  => sanitize_text_field($_POST['author-url']),
		'version'     => sanitize_text_field($_POST['version']),
		'include-gpl' => sanitize_text_field($_POST['include-gpl']),
		);
		
		// create child theme - let's see if this worked
		$status = cct_create_theme($newchildtheme);
		// cct_testing($newchildtheme);
		
		// Put a "settings updated" message on the screen 
		if ($status['alert'] == -1) {
			// panic - there was an error
			?>
            <div class="error">
		    <strong><p>Yikes - something went wrong:</p>
            <?php echo $status['message']; ?>
            </strong>
		    </div>
            <div><strong><p>Would you like to <a href="<?php get_admin_url('tools.php?page=CreateChildTheme'); ?>">try again?</a></p></strong></div>
    
            <?php
		} else {
			// everything went fine
			?>
			<div class="updated">
			 <strong>
             <?php echo $status['message']; ?>
             <p>
			 <?php _e('Your Child Theme was created successfully!', 'cct' ); ?>
			 </strong></p></div>
             <div>
             <p>Head over to <a href="<?php echo admin_url('themes.php'); ?>">Appearance - Themes</a> to activate it.</p>
             <p>Add your custom styles in <a href="<?php echo admin_url('theme-editor.php'); ?>">Appearance - Editor</a>.</p>
			</div>
			<?php 
			}
		} else {
	
		// display the new child theme dialogue 
		?>

    <p>This simple wizard will help you generate a new <a href="https://developer.wordpress.org/themes/advanced-topics/child-themes/" target="_blank">Child Theme</a> with just one click. </p>
    <p>Select which theme you want to use as a base, fill in the details and click &quot;Create Child Theme&quot;.</p>
    <hr>
    <form name="cctform" method="post" action="">
    <input type="hidden" name="hiddenfield" value="Y">
    
    
    <?php
	
	// fields for author and title
	// we can pre-populate some data too
	$current_user = wp_get_current_user();
	?>
    <table width="600" border="0">
      <tr>
        <td>Parent Theme</td>
        <td><?php
        // grab a list of all parent themes and iterate through them
		// $themes = wp_get_themes();
		$themes = cct_giveme_parents();
		$currentTheme = wp_get_theme();
		echo '<select name="parent">';
		foreach ($themes as $theme) {
			// if it's the current theme, make it selected
			if($currentTheme->get('Name') == $theme->get('Name')) {
				echo '<option value ="' . $theme->get_stylesheet() . '" selected>' . $theme->get('Name') . ' (currently active)</option>';
			} else {
				echo '<option value ="' . $theme->get_stylesheet() . '">' . $theme->get('Name') . '</option>';
			}
		}
		echo '</select>';
        ?></td>
        <td><em></em></td>
      </tr>
      <tr>
        <td>Title</td>
        <td><input type="text" name="title" value="" size="20"></td>
        <td><em>what's your new Child Theme called?</em></td>
      </tr>
      <tr>
        <td>Description</td>
        <td><input type="text" name="description" value="" size="20"></td>
        <td><em>a few notes about your Child Theme</em></td>
      </tr>
      <tr>
        <td>Child Theme URL</td>
        <td><input type="text" name="url" value="" size="20"></td>
        <td><em>does it have a website or release post?</em></td>
      </tr>
      <tr>
        <td>Author</td>
        <td><input type="text" name="author" value="<?php echo $current_user->display_name; ?>" size="20"></td>
        <td><em>that's you</em></td>
      </tr>
      <tr>
        <td>Author URL</td>
        <td><input type="text" name="author-url" value="<?php echo $current_user->user_url; ?>" size="20"></td>
        <td><em>that's your website</em></td>
      </tr>
      <tr>
        <td>Version</td>
        <td><input type="text" name="version" value="1.0" size="20"></td>
        <td><em>start with 1.0 and work your way up</em></td>
      </tr>
      <tr>
        <td>Include GPL License</td>
        <td>
        <select name="include-gpl">
        <option value="on">Yes Please!</option>
        <option value="off">No Thanks</option>
        </select>
        </td>
        <td><em></em></td>
      </tr>
    </table>
    
    <p class="submit">
    <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Create Child Theme') ?>" />
    </p>
    
    <?php // end of else
		}
		?>
        <br><hr><br>
        
    </div> <!-- end of main wrap -->
<p>Quickly creates Child theme with functions.php and style.css files readymade.</p>
<?php
} // end of main function

/* 
 * this function creates the new directory and file
 */
 
function cct_create_theme($childtheme) {
	
	// add messages to this variable
	$status = array();
	$status['alert'] = 0;
	
	// create the directory
	$directory = get_theme_root() . '/' . sanitize_file_name($childtheme['title']);
	// echo '<p>The child theme directory will be created at ' . $directory;
	if (!mkdir($directory)) {
		$status['message'] = "<p>Could not create directory. Does it exist already?</p>";
		$status['alert'] = -1;
		return $status;
	} else {
		$status['message'] = "<p>Directory created successfully.</p>";
	}
	
	// create style.css in our directory
	$filename = $directory . '/' . 'style.css';
	$handle = fopen($filename, 'w');
	
	// add meta data 
	$data = "/* \n Theme Name:   " . $childtheme['title'];
	$data = $data . "\n Theme URI:    " . $childtheme['url'];
	$data = $data . "\n Description:  " . $childtheme['description'];
	$data = $data . "\n Author:       " . $childtheme['author'];
	$data = $data . "\n Author URI:   " . $childtheme['author-url'];
	$data = $data . "\n Template:     " . $childtheme['parent'];
	$data = $data . "\n Version:      " . $childtheme['version'];
	
	// insert GPL License Terms
	if ($childtheme['include-gpl'] == 'on') {
		$data = $data . "\n License:      GNU General Public License v2 or later";
	    $data = $data . "\n License URI:  http://www.gnu.org/licenses/gpl-2.0.html";
	}
	
	// adding the call to the parent style sheet in CSS is no longer best practice
	// $data = $data . "\n*/\n\n" . '@import url("../' . $childtheme['parent'] . '/style.css");';
	$data = $data . "\n\n /* == Add your own styles below this line ==";
	$data = $data . "\n--------------------------------------------*/\n\n";

	// write data and close the file
	if (fwrite($handle, $data) == false) {
		$status['message'] = $status['message'] . "<p>There was a problem writing data to style.css.</p>";
		$status['alert'] = -1;
		return $status;
	} else {
		$status['message'] = $status['message'] . "<p>Writing data to style.css...</p>";
	}
	fclose($handle);
	
	// create functions.php
	$filename = $directory . '/' . 'functions.php';
	$handle = fopen($filename, 'w');
	
	// add some meta data 
	$data = "<?php /*\n\n  This file is part of a child theme called " . $childtheme['title'] . ".";
	$data = $data . "\n  Functions in this file will be loaded before the parent theme's functions.";
	$data = $data . "\n  For more information, please read";
	$data = $data . "\n  https://developer.wordpress.org/themes/advanced-topics/child-themes/";
	$data = $data . "\n\n*/";
	
	// add call to enqueue parent style sheet via functions.php
	$data = $data . "\n\n// this code loads the parent's stylesheet (leave it in place unless you know what you're doing)";
	$data = $data . "\n\nfunction your_theme_enqueue_styles() {";
	
	// @since 1.4: define $parent_style
	$data = $data . "\n\n    " . '$parent_style' . " = 'parent-style';";
	
	$data = $data . "\n\n    wp_enqueue_style( " . '$parent_style' . ", \n";
	$data = $data . "      get_template_directory_uri() . '/style.css'); \n";
	
	$data = $data . "\n    wp_enqueue_style( 'child-style', \n";
	$data = $data . "      get_stylesheet_directory_uri() . '/style.css', \n";
	$data = $data . "      array(". '$parent_style' ."), \n";
	$data = $data . "      wp_get_theme()->get('Version') \n";
	$data = $data . "    );\n}";
	$data = $data . "\n\nadd_action('wp_enqueue_scripts', 'your_theme_enqueue_styles');";
	
	$data = $data . "\n\n/*  Add your own functions below this line.";
	$data = $data . "\n    ======================================== */ \n\n";
	
	// write data and close the file
	if (fwrite($handle, $data) == false) {
		$status['message'] = $status['message'] . "<p>There was a problem writing data to functions.php.</p>";
		$status['alert'] = -1;
		return $status;
	} else {
		$status['message'] = $status['message'] . "<p>Writing data to functions.php...</p>";
	}
	fclose($handle);
	
	// create a nice screenshot 
	$thumbnail_status = cct_make_thumbnail($directory);
	$status['message'] = $status['message'] . $thumbnail_status;
	
	return $status;
}

// return an array only of parent themes
function cct_giveme_parents() {
	
	// first we'll grab all installed themes
	$allThemes = wp_get_themes();
	$parentThemes = array();
	
	// next we'll add all parents and return the result
	foreach ($allThemes as $theme) {
		
		if ($theme->parent() == false) {
			$parentThemes[] = $theme;
		}
	}
	
	return $parentThemes;
}

// create a new thumbnail
function cct_make_thumbnail($childpath) {
	
	// for now we'll just copy an existing image
	$thumbnail = plugins_url('images/screenshot.png', __FILE__);
	$destination = $childpath . '/screenshot.png';
	if (!copy($thumbnail, $destination)) {
		return "<p>Could not copy thumbnail image :-(</p>";
	} else {
		return "<p>Copied thumbnail file over - looking good!</p>";
	}
}

// quick tests go here
function cct_testing($childtheme) {
	
	$directory = get_theme_root() . '/' . sanitize_file_name($childtheme['title']);
	cct_make_thumbnail($directory);
}

?>
