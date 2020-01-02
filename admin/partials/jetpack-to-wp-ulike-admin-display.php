<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       lincolnlemos.com
 * @since      1.0.0
 *
 * @package    Jetpack_To_Wp_Ulike
 * @subpackage Jetpack_To_Wp_Ulike/admin/partials
 */

global $wpdb;

function search_user_by_info($fullname) {
  $args = [
    'search' => '*'.esc_attr( $fullname ).'*',
  ];
  // Create the WP_User_Query object
  $wp_user_query = new WP_User_Query($args);

  // Get the results
  $authors = $wp_user_query->get_results();
  
  // Check for results
  if (!empty($authors)) {
      return $authors[0];
  } else {
      return false;
  }

}


function get_user_by_jetpack_login($login) {
  $users = get_users(['meta_key' => 'jetpack_user_login', 'meta_value' => $login]);
  return count($users) > 0 ? $users[0] : false;

}
function get_post_by_name( $post_name, $post_type = 'post' )
{
    $post_ids = get_posts(array
    (
        'post_name'   => $post_name,
        'post_type'   => $post_type,
        'numberposts' => 1,
    ));

    return array_shift( $post_ids );
}

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) die;

  $options = get_option( $this->plugin_name );

  $is_ready_to_import = 
    (isset($options['url_xml']) && $options['url_xml']) &&
    (isset($options['url_wp_dot_com']) && $options['url_wp_dot_com']);

  $is_importing = isset($_GET['importing']) && $_GET['importing'] == 1 ? true : false;
  $is_syncing_users = isset($_GET['sync-users']) && $_GET['sync-users'] == 1 ? true : false;
  $is_syncing_likes = isset($_GET['sync-likes']) && $_GET['sync-likes'] == 1 ? true : false;

  // if (isset($_POST)) {
  //   echo '<pre>'.print_r($_POST['users'],1). '</pre>';
  //   die();
  // }
?>

<div class="wrap">

<h2><?php esc_attr_e('Jetpack to WP Ulike', $this->plugin_name ); ?></h2>

<?php if (!$is_importing): ?>

  <p>Please, make sure that your site is accessible via API at Wordpress.com. You can test it accessing the URL below</p>
  <p>https://public-api.wordpress.com/rest/v1/sites/<b>YOUR_SITE_ID_AT_WORDPRESS_DOT_COM</b>/posts/<b>ANY_POST_ID_FROM_YOUR_WEBSITE_AT_WORDPRESS_DOT_COM</b>/likes</p>

  <form method="post" name="<?php echo $this->plugin_name; ?>" action="options.php">
    <?php        
      settings_fields($this->plugin_name);
      do_settings_sections($this->plugin_name);
    ?>

    <!-- XML URL -->
    <?php $field_name = 'url_xml' ?>
    <fieldset>
        <p>
          <strong><?php esc_attr_e( 'XML URL', $this->plugin_name ); ?></strong>
          <small>Please, use the same XML that you used to import the content from WordPress.com. It's important that you didn't change any post slug.</small>
        </p>
        <legend class="screen-reader-text">
          <span><?php esc_attr_e( 'XML URL', $this->plugin_name ); ?></span>
        </legend>
        <input 
          type="text" 
          class="<?php echo $field_name ?>" 
          id="<?php echo $this->plugin_name . '-'. $field_name ?>"
          name="<?php echo $this->plugin_name . '['. $field_name . ']' ?>"
          value="<?php if( ! empty( $options[$field_name] ) ) echo $options[$field_name]; ?>"
        />
    </fieldset>
    
    <!-- WP.COM URL -->
    <?php $field_name = 'url_wp_dot_com' ?>
    <fieldset>
        <p>
          <strong><?php esc_attr_e( 'WordPress.com URL', $this->plugin_name ); ?></strong>
          <small>Please, paste only the domain withtout the protocol. Ex: yoursite.home.blog</small>
        </p>
        <legend class="screen-reader-text">
          <span><?php esc_attr_e( 'WordPress.com URL', $this->plugin_name ); ?></span>
        </legend>
        <input 
          type="text" 
          class="<?php echo $field_name ?>" 
          id="<?php echo $this->plugin_name . '-'. $field_name ?>"
          name="<?php echo $this->plugin_name . '['. $field_name . ']' ?>"
          value="<?php if( ! empty( $options[$field_name] ) ) echo $options[$field_name]; ?>"
        />
    </fieldset>

    <?php submit_button( __( 'Submit URLS', $this->plugin_name ), 'primary','submit', TRUE ); ?>
  </form>
    
    <?php if ($is_ready_to_import): ?>
      <form action="/wp-admin/tools.php">
        <input type="hidden" value="1" name="importing">
        <input type="hidden" value="<?php echo $this->plugin_name ?>" name="page">
        <?php submit_button( __( 'Import Likes', $this->plugin_name ), 'primary','submit', TRUE ); ?>
      </form>
    <?php endif; ?>
  
  <?php else: // $is_importing ?> 
    <h2>Importing content from WordPress.com (<?php echo $options['url_wp_dot_com'] ?>)</h2>

    <?php 
      $xml = simplexml_load_file($options['url_xml']);

      // Get the POSTS
      if ($xml) {
        foreach ($xml->channel->item as $item) {
          
          $wp = $item->children('http://wordpress.org/export/1.2/');          
          
          if ($wp->post_type != 'post') continue;
          if ($wp->status != 'publish') continue;          
          
          $posts[] = array(
            "title"      => (string)$item->title,
            "post_id"    => (string)$wp->post_id,
            "slug"    => (string)$wp->post_name,
          );
        }

        echo '<p>There is '. count($posts) .' to be sync.</p>';
      }
      
      // Get all users that has likes
      if ($posts) {
        
        $users = [];
        $synced_users = [];
        
        foreach ($posts as $key => $post) {
          
          $wp_post = get_post_by_name($post['slug']);
          if (!$wp_post) {
            echo '<p>The post ' . $post['title'] . ' was not found in WordPress. Have you changed the slug?</p>';
            die();
          }

          $request = wp_remote_get( 'https://public-api.wordpress.com/rest/v1/sites/'.$options['url_wp_dot_com'].'/posts/'.$post['post_id'].'/likes' );
          if( is_wp_error( $request ) ) {
            echo '<pre>'.print_r($request,1). '</pre>';
            die();
            return false; // Bail early
          }

          $body = wp_remote_retrieve_body( $request );
          $data = json_decode( $body );                            

          if ($data->found > 0) {

            $likes = $data->likes;
            // Store the likes 
            $posts[$key]['likes'] = $likes;
            
            foreach ($likes as $like ) {

              if ($is_syncing_likes) {
                
                $user = get_user_by_jetpack_login($like->login);
                if ($user) {
                  $wpdb->insert(
                    $wpdb->prefix . 'ulike',
                    array(
                      'post_id'     => $wp_post->ID,
                      'date_time' => current_time( 'mysql' ),                  
                      'user_id'   => $user->ID,
                      'status'    => 'like'
                    ),
                    array( '%d', '%s', '%s', '%s', '%s' )
                  );  
                } else {
                  echo '<p>None user registered with login '. $like->login .'</p>';
                  // die();
                }
                                

              } else {

                $users[$like->login] = [
                  'full_name' => $like->first_name . ' '. $like->last_name,
                  'first_name' => $like->first_name,
                  'last_name' => $like->last_name,
                  'login' => $like->login
                ];              
              }

            }

          }
          
        } // foreach $posts as $post
        
        if (count($users) > 0) {
          
          echo '<form action="tools.php?page='. $this->plugin_name .'&sync-users=1" method="POST" >';
            foreach ($users as $u ) {

              $user = get_user_by_jetpack_login($u['login']);              
              $email = count($users) > 0 ? $user->data->user_email : '';

              echo  '<p>',
                      '<label for="">'.$u['first_name'].' '.$u['last_name'].' - '.$u['login'].'</label><br /> ',
                      '<input type="email" placeholder="email" name="newUsers['.$u['login'].']" value="'.$email.'" />',
                    '</p> <br /> ';
            }
            echo '<input type="submit" value="submit" class="button button-primary" />';
          echo '</form>';
                    
        } // count $users > 0        

      }         

    ?>
  <?php endif; // if (!$is_importing): 
    
  if ($is_syncing_users) {

    $users = $_POST['newUsers'];

    $is_ready_to_sync_likes = true;
    
    if ($users) {
      foreach ($users as $login => $email) {

        $user = get_user_by('email', $email);
        
        if ($user) {          
          update_usermeta($user->ID, 'jetpack_user_login', $login);
        } else {
          $is_ready_to_sync_likes = false;
          echo '<p>There is no user with email ' . $email . '</p> <br />';
        }
      }
    }
    
    // if ($is_ready_to_sync_likes) {
      echo '<a href="tools.php?page='. $this->plugin_name .'&importing=1&sync-likes=1" class="button button-primary">Sync Likes</a>';
    // }
  }
  
  ?>


</div>

