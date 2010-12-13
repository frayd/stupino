<?php
// $Id: contact.author-pane.inc,v 1.1.2.1.2.5 2010/08/10 02:32:40 michellec Exp $

/**
 * @file
 *   This file provides a preprocess function on behalf of the contact module.
 */

/**
 * Implementation of hook_preprocess_author_pane().
 */
function contact_preprocess_author_pane(&$variables) {
  // Check if this preprocess needs to be run given who's calling it.
  if (!author_pane_run_preprocess('contact', $variables['caller'])) {
    return;
  }

  global $user;
  $account = $variables['account'];
  $account_id = $account->uid;

  // If the account has the contact form on, and the account isn't the viewing
  // user, and neither the account nor the viewer are anonymous.
  if ((!empty($account->contact)) && ($account_id != $user->uid) && ($user->uid != 0) && ($account_id != 0)) {
    $variables['contact'] = l(t('Email'), "user/$account_id/contact", array('attributes' => array('class' => 'author-email-icon'), 'html' => TRUE));
  }
}

/**
 * Implementation of hook_author_pane_allow_preprocess_disable().
 */
function contact_author_pane_allow_preprocess_disable() {
  return array('contact' => 'Contact');
}
