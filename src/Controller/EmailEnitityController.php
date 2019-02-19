<?php

namespace Drupal\entity_email\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Ajax\AjaxResponse; use Drupal\Core\Ajax\OpenModalDialogCommand;



/**
 * Class EmailEnitityController.
 *
 * @package Drupal\entity_email\Controller
 */
class EmailEnitityController extends ControllerBase {
  protected $formBuilder;

  /**
   * The ModalFormExampleController constructor.
   *
   * @param \Drupal\Core\Form\FormBuilder $formBuilder
   * The form builder.
   */
  public function __construct(FormBuilder $formBuilder) {
    $this->formBuilder = $formBuilder;
  }


  public static function create(ContainerInterface $container) {
    return new static( $container->get('form_builder') );
  }
  /**
   * Emailentity.
   *
   * @return string
   *   Return Hello string.
   */
  public function emailEntity($eid) {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: emailEntity with parameter(s): $eid'),
    ];
  }

  public function getEntityPopupForm($eid = null){
    $response = new AjaxResponse();
    $modal_form = $this->formBuilder->getForm('Drupal\entity_email\Form\EmailEntityPopupForm',$eid);
    $response->addCommand(new OpenModalDialogCommand('Send Email To', $modal_form, ['width' => '800']));
    return $response;
  }

}
