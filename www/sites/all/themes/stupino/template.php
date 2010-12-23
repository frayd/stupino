<?php
function stupino_node_submitted($node) {
  return t('Автор: !username',
    array(
      '!username' => theme('username', $node),
    ));
}

function stupino_comment_submitted($comment) {
  return theme('username', $comment).', '.format_date($comment->timestamp, 'small');
}

function stupino_preprocess_page(&$variables) {
  $variables['rss_twit'] = l(theme('image', drupal_get_path('theme', 'stupino') . '/img/acoffee_rss.png', t('RSS Feed'), t('RSS Feed')), 'rss.xml', array('html' => TRUE));;
}

function stupino_preprocess_node(&$variables) {
  $node_date = 
    '<div class="create-d">'. date('d', $variables['node']->created) .'</div>'.
    '<div class="create-m">'. date('M', $variables['node']->created) .'</div>'.
    '<div class="create-y">'. date('Y', $variables['node']->created) .'</div>';
  $variables['node_create_date'] = $node_date;
}