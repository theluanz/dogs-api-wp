<?php 
  function api_user_post($request) {
    $mail = sanitize_email($request['mail']);
    $username = sanitize_text_field($request['username']);
    $password = $request['password'];

    if(empty($mail) || empty($password) || empty($username)){
      $response = new WP_Error('error', "Dados incopletos", ['status' => 406]);
      return rest_ensure_response($response);
    }
    
    if(username_exists($mail)){
      $response = new WP_Error('error', "Email já cadastrado", ['status' => 406]);
      return rest_ensure_response($response);
    }

    if(username_exists($username)){
      $response = new WP_Error('error', "Nome de usuário já cadastrado", ['status' => 406]);
      return rest_ensure_response($response);
    }


    $response = wp_insert_user([
      'user_login' => $username,
      'user_email' => $mail,
      'user_pass' => $password,
      'role' => 'subscriber'
    ]);

    return rest_ensure_response($response);
  }

  function api_register_user_post() {
    register_rest_route( 'v1', 'user', [
      'methods' => WP_REST_Server::CREATABLE,
      'callback' => 'api_user_post',
    ]);
  }
  add_action('rest_api_init', 'api_register_user_post')
?>