<?php

/**
 *
  require_once(DRUPAL_ROOT . '/modules/custom/phpdebug/send_mail.php');
  _run_send_mail();
 */

use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;

function _run_send_mail() {
  // All system mails need to specify the module and template key (mirrored
  // from hook_mail()) that the message they want to send comes from.
  $module = 'mailinfo';
  $key = 'mailinfo_demo_message';

  // Specify 'to' and 'from' addresses.
  $to = 'myemail@example.com';
  $from = 'webwindsor@gmail.com';

  // "params" loads in additional context for email content completion in
  // hook_mail(). In this case, we want to pass in the values the user entered
  // into the form, which include the message body in $form_values['message'].
  $params['message'] = 'some message 2';

  // MailManager::mail();

  // The language of the e-mail. This will one of three values:
  // - $account->getPreferredLangcode(): Used for sending mail to a particular
  //   website user, so that the mail appears in their preferred language.
  // - \Drupal::currentUser()->getPreferredLangcode(): Used when sending a
  //   mail back to the user currently viewing the site. This will send it in
  //   the language they're currently using.
  // - \Drupal::languageManager()->getDefaultLanguage()->getId(): Used when
  //   sending mail to a pre-existing, 'neutral' address, such as the system
  //   e-mail address, or when you're unsure of the language preferences of
  //   the intended recipient.
  //
  // Since in our case, we are sending a message to a random e-mail address
  // that is not necessarily tied to a user account, we will use the site's
  // default language.

  // $language_code = 'en';
  $language_code = \Drupal::languageManager()->getDefaultLanguage()->getId();

  // Whether or not to automatically send the mail when we call mail() on the
  // mail manager. This defaults to TRUE, and is normally what you want unless
  // you need to do additional processing before the mail manager sends the
  // message.
  $send_now = TRUE;
  // Send the mail, and check for success. Note that this does not guarantee
  // message delivery; only that there were no PHP-related issues encountered
  // while sending.

  $mailManager = \Drupal::service('plugin.manager.mail');
  $result = $mailManager->mail($module, $key, $to, $language_code, $params, $from, $send_now);
  $params['message'] = 'some message from update';
  $message = \Drupal::service('plugin.manager.mail')->mail('update', 'status_notify', $to, $language_code, $params);

  kint($result);
  if ($result['result'] == TRUE) {
    drupal_set_message(t('Your message has been sent.'));
  }
  else {
    drupal_set_message(t('There was a problem sending your message and it was not sent.'), 'error');
  }
}
