<?php

namespace Drupal\silai\Controller;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Access\AccessResult; 
use Drupal\Core\Form\FormInterface;

class BulkImportController extends ControllerBase {
  /**
   * Display the markup.
   *
   * @return array
   */
  public function learnerBulkImport(Request $request) {

    $form = \Drupal::formBuilder()->getForm('Drupal\silai\Form\learnerBulkImportForm');
    
    return $form;
  }
  /**
   * Display the markup.
   *
   * @return array
   */
  public function dealersBulkImport(Request $request) {

    $form = \Drupal::formBuilder()->getForm('Drupal\silai\Form\dealersBulkImportForm');
    
    return $form;
  }
  
}