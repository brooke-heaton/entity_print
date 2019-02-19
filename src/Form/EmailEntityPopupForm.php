<?php

namespace Drupal\entity_email\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Ajax\ReplaceCommand;

/**
 * Class EmailEntityPopupForm.
 *
 * @package Drupal\entity_email\Form
 */
class EmailEntityPopupForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'email_entity_popup_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state,$eid =null) {
    if(empty($eid)){
      return $form;
    }
    $form['#prefix'] = '<div id="email_entity_popup_form">';
    $form['#suffix'] = '</div>';

    $form['email_to'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Email:'),
      '#description' => $this->t('If adding multiple email addresses, separate each email address with a semicolon.'),
      '#maxlength' => 300,
      '#size' => 80,
    );

    $form['email_subject'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Subject:'),
      //'#description' => $this->t('Recipient email address'),
      '#maxlength' => 300,
      '#size' => 80,
    );

    $form['email_body'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Message:'),
      //'#description' => $this->t('Recipient email address'),
      '#cols' => 80,
    );

    $form['actions']['yes'] = [
      '#type' => 'submit',
      '#value' => 'Send',
      '#attributes' => [ 'class' => [ 'use-ajax', ], ],
      '#ajax' => [ 'callback' => [$this, 'submitEmailEntityPopupForm'],
                    'event' => 'click', ],
    ];
    $form_state->set('eid',$eid);
    $form['#attached']['library'][] = 'core/drupal.dialog.ajax';

    return $form;
  }
/*
 * AJAX form submit handler
 */
  public function submitEmailEntityPopupForm(array $form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $hasAddressError = false;
    $allEmail = $form_state->getValue('email_to');

    $arrEmail = explode(';',$allEmail);
    foreach ($arrEmail as $email){
      if (!filter_var(trim($email), FILTER_VALIDATE_EMAIL) === true) {
        $hasAddressError = true;
      }
    }

    if($hasAddressError){
      $msg = "<div role=\"contentinfo\" aria-label=\"Error message\" class=\"messages messages--error\">
                <div class=\"messages__content\" role=\"alert\">
                      <h2 class=\"visually-hidden\">Error message</h2>
                            Be sure to enter a valid email address. If entering multiple email addresses, be sure to use a semicolon to separate each email address.
                  </div>
                </div>";
      $element =[
        '#type'=> 'markup',
        '#markup'=> $msg,
        '#weight' => -10,
      ];

      array_unshift($form, $element);
      $response->addCommand(new ReplaceCommand('#email_entity_popup_form', $form));

    }else{
      $emailParam = array();
      $emailParam['to'] =  implode(',',$arrEmail);
      $subject = $form_state->getValue('email_subject');

      if($subject){
        $emailParam['subject'] = $subject;
      }

      $addlBody = $form_state->getValue('email_body');

      if($addlBody) {
        $emailParam['usermsg'] = $addlBody;
      }

      $res = $this->sendEntityEmail($emailParam,$form_state->get('eid'));
      if($res){
        $msg = t('Email sent to: :rcpt', array(':rcpt'=>$form_state->getValue('email_to')));
        $markup = "
        <div role=\"contentinfo\" aria-label=\"Status message\" class=\"messages messages--status\">
    <div class=\"messages__content\">
              <h2 class=\"visually-hidden\">Status message</h2>
                    {$msg}
          </div>
  </div>
        ";

        $response->addCommand(new OpenModalDialogCommand("Email Sent!", $markup , ['width' => 800]));
      }else{

        $msg = t('Unable to send Email  to: :rcpt', array(':rcpt'=>$form_state->getValue('email_to')));

        $markup ="
                  <div role=\"contentinfo\" aria-label=\"Error message\" class=\"messages messages--error\">
                  <div class=\"messages__content\" role=\"alert\">
                            <h2 class=\"visually-hidden\">Error message</h2>
                                  {$msg}
                        </div>
                  </div>
            ";

        $element =[
          '#type'=> 'markup',
          '#markup'=> $markup,
          '#weight' => -10,
        ];
        array_unshift($form, $element);
        $response->addCommand(new ReplaceCommand('#email_entity_popup_form', $form));
      }
    }
    return $response;
  }


  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

   // drupal_set_message(t('Please enter a valid email address.'), 'status', TRUE);
  }

  /**
   * {@inheritdoc}
   */

  public function submitForm(array &$form, FormStateInterface $form_state) {
    //$er = $form_state->get('email_error');
    //$hasAddressError = ($er == true) ? $form_state->get('email_error'): false;
    /*$hasAddressError = false;
    $allEmail = $form_state->getValue('email_to');

    $arrEmail = explode(',',$allEmail);
    foreach ($arrEmail as $email){
      if (!filter_var(trim($email), FILTER_VALIDATE_EMAIL) === true) {
        $hasAddressError = true;
      }
    }

    if($hasAddressError){
      drupal_set_message(t('Please enter a valid email address.'), 'error', TRUE);
    }else{
      $emailParam = array();
      $emailParam['to'] =  $form_state->getValue('email_to');
      $subject = $form_state->getValue('email_subject');

      if($subject){
        $emailParam['subject'] = $subject;
      }

      $addlBody = $form_state->getValue('email_body');

      if($addlBody) {
        $emailParam['usermsg'] = $addlBody;
      }

      $res = $this->sendEntityEmail($emailParam,$form_state->get('eid'));
      if($res){
        drupal_set_message(t('Email sent to: :rcpt', array(':rcpt'=>$form_state->getValue('email_to'))), 'status', TRUE);
      }
      else {
        drupal_set_message(t('Unable to send Email  to: :rcpt', array(':rcpt'=>$form_state->getValue('email_to'))), 'error', TRUE);

      }

    }

    $form_state->setRedirect('entity.node.canonical',array('node'=>$form_state->get('eid')));*/

  }

  private function sendEntityEmail($emailParam, $entityID){
    try{
      $output = null;
      $message = null;

      // Get a node storage object.
      $node_storage = \Drupal::entityTypeManager()->getStorage('node');

     // Load a single node.
      $entity = $node_storage->load($entityID);

      if(empty($entity)){
        $message = "Unable to get Entity Content";
        throw new \Exception($message);
      }


      $mailManager = \Drupal::service('plugin.manager.mail');

      $module = 'entity_email';
      $key = 'entity_email';
      $to = $emailParam['to'];

      $build['entity'] = \Drupal::entityTypeManager()->getViewBuilder($entity->getEntityTypeId())->view($entity, 'emailable');

      if(isset($emailParam['usermsg'])) {
        $build['userMessage'] = array('#markup'=> $emailParam['usermsg'] . "<br /><br />" ,'#weight'=>-10);
      }

      $renderer = \Drupal::service('renderer');
      $markup = $renderer->render($build);

      $params['message'] = $markup;

      if(isset($emailParam['subject'])){
        $params['subject'] = $emailParam['subject'];
      }
      else{
        $params['subject'] = "A NASWA state template has been emailed to you: " .$entity->title->value;
      }

      $langcode = \Drupal::currentUser()->getPreferredLangcode();
      $send = true;

      $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);
      if ($result['result'] === true) {
        $output = $result['result'];
        $message = "Entity Email sent to: " . $to;
        \Drupal::logger('entity_email')->info($message);
      }
      else{
        $message = "Unable to sent email. Please check email config";
        throw new \Exception($message);
      }

    }catch(\Exception $ex) {
      $output = null;
      \Drupal::logger('entity_email')->error($message);
      watchdog_exception(__FUNCTION__, $ex);
    }
    return $output;
  }

}
