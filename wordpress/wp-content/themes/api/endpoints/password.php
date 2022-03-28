<?php

function api_password_lost_post($request)
{
    $login = $request['login'];
    $url = $request['url'] ?: 'http://localhost:8080/';

    if (empty($login)) {
        $response = new WP_Error('error', 'Informe o e-mail ou login.', ['status' => 406]);
        return rest_ensure_response($response);
    }

    $user = get_user_by('email', $login);
    if (empty($user)) {
        $user = get_user_by('login', $login);
    }

    if (empty($user)) {
        $response = new WP_Error('error', 'Informe o e-mail ou login correto.', ['status' => 401]);
        return rest_ensure_response($response);
    }

    $user_login = $user->user_login;
    $user_email = $user->user_email;

    $key = get_password_reset_key($user);

    $message = "Utilize o link abaixo para resetar a sua senha: \r\n";
    $url = esc_url_raw($url . "/?key=$key&login=" . rawurlencode($user_login) . "\r\n");
    $body = $message . $url;

    wp_mail($user_email, 'Password Reset', $body);

    return rest_ensure_response($key);
}

function register_api_password_lost_post()
{
    register_rest_route('v1', 'password/lost/', [
        'methods' => WP_REST_Server::CREATABLE,
        'callback' => 'api_password_lost_post',
    ]);
}
add_action('rest_api_init', 'register_api_password_lost_post');


// Password RESET

function api_password_reset_post($request)
{
    $login = $request['login'];
    $password = $request['password'];
    $key = $request['key'];

    $user = get_user_by('login', $login);


    if (empty($user) || empty($password) || empty($key)) {
        $response = new WP_Error('error', 'Dados incompletos.', ['status' => 406]);
        return rest_ensure_response($response);
    }

    $check_key = check_password_reset_key($key, $login);

    if (is_wp_error($check_key)) {
        $response = new WP_Error('error', 'Token expirado.', ['status' => 401]);
        return rest_ensure_response($response);
    }
    reset_password($user, $password);

    return rest_ensure_response("Senha resetada");
}

function register_api_password_reset_post()
{
    register_rest_route('v1', 'password/reset/', [
        'methods' => WP_REST_Server::CREATABLE,
        'callback' => 'api_password_reset_post',
    ]);
}
add_action('rest_api_init', 'register_api_password_reset_post');
