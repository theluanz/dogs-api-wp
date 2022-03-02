<?php 
remove_action('rest_api_init', 'create_initial_rest_routes', 99);
$dirbase = get_template_directory();

function change_api() { 
  return 'api'; 
} 

add_filter( 'rest_url_prefix', 'change_api');
?>