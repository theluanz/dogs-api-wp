<?php

function api_photo_get($request)
{
  $post = get_post($request['id']);

  if (!isset($post) || empty($post)) {
    $response = new WP_Error('error', "Post não encontrado", ['status' => 404]);
  }


  $photo = photo_data($post);

  $photo['views'] = (int) $photo['views'] + 1;
  update_post_meta($post->ID, 'views', $photo['views']);

  $comments = get_comments([
    'post_id' => $post->ID,
    'order' => 'ASC'
  ]);

  $response = [
    'photo' => $photo,
    'comments' => $comments
  ];

  return rest_ensure_response($response);
}



function register_api_photo_get()
{
  register_rest_route('v1', 'photo/(?P<id>[0-9]+)', [
    'methods' => WP_REST_Server::READABLE,
    'callback' => 'api_photo_get',
  ]);
}
add_action('rest_api_init', 'register_api_photo_get');



function photo_data($post)
{
  $post_meta = get_post_meta($post->ID);
  $src = wp_get_attachment_image_src($post_meta['img'][0], 'large')[0];
  $user = get_userdata($post->post_author);
  $total_comments = get_comments_number($post->ID);
  return [
    'id' => $post->ID,
    'author' => $user->user_login,
    'title' => $post->post_title,
    'date' => $post->post_date,
    'src' => $src,
    'weight' => $post_meta['weight'][0],
    'age' => $post_meta['age'][0],
    'views' => $post_meta['views'][0],
    'total_comments' => $total_comments
  ];
}
