<?php
function api_user_get()
{
  $user = wp_get_current_user();

  if ($user->ID == 0 || $user->ID == null) {
    $response = new WP_Error('error', 'Usuário não possui permissão', ['status' => 401]);
  } else {
    $response = [
      'id' => $user->ID,
      'username' => $user->user_login,
      'name' => $user->display_name,
      'email' => $user->user_email,
    ];
  }
  return rest_ensure_response($response);
}

function api_register_user_get()
{
  register_rest_route('v1', 'user', [
    'methods' => WP_REST_Server::READABLE,
    'callback' => 'api_user_get',
  ]);
}
add_action('rest_api_init', 'api_register_user_get');
