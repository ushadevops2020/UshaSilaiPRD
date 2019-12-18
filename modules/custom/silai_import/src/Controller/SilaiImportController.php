<?php

namespace Drupal\silai_import\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Form\FormBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Database\Database;
use Drupal\file\Entity\File;
use Drupal\Core\Url;
/**
 * Defines HelloController class.
 */
class SilaiImportController extends ControllerBase {

  /**
   * Display the markup.
   *
   * @return array
   *   Return markup array.
   */
  protected $formBuilder;
  /**
   * Attached District Bulk Import Form
   *  The form builder.
   */
  public function districtsBulkImportForm() { 
    $form = \Drupal::formBuilder()->getForm('Drupal\silai_import\Form\districtsBulkImportForm');
    return $form;
  }
   /**
   * Attached District Bulk Import Form
   *  The form builder.
   */
  public function blocksBulkImportForm() { 
    $form = \Drupal::formBuilder()->getForm('Drupal\silai_import\Form\blocksBulkImportForm');
    return $form;
  }
  /**
   * Attached District Bulk Import Form
   *  The form builder.
   */
  public function villageBulkImportForm() { 
    $form = \Drupal::formBuilder()->getForm('Drupal\silai_import\Form\villageBulkImportForm');
    return $form;
  }

  /**
   * Attached NGO Bulk Import Form
   *  The form builder.
   */
  public function ngoBulkImportForm() { 
    $form = \Drupal::formBuilder()->getForm('Drupal\silai_import\Form\ngoBulkImportForm');
    return $form;
  }
  /**
   * Attached NGO Bulk Import Form
   *  The form builder.
   */
  public function nfaBulkImportForm() { 
    $form = \Drupal::formBuilder()->getForm('Drupal\silai_import\Form\nfaBulkImportForm');
    return $form;
  }

  /**
   * Attached Agreement Bulk Import Form
   *  The form builder.
   */
  public function agreementBulkImportForm() { 
    $form = \Drupal::formBuilder()->getForm('Drupal\silai_import\Form\agreementBulkImportForm');
    return $form;
  } 
  /**
   * Attached School Bulk Import Form
   *  The form builder.
   */
  public function schoolBulkImportForm() { 
    $form = \Drupal::formBuilder()->getForm('Drupal\silai_import\Form\schoolBulkImportForm');
    return $form;
  }     
  #
  public function schoolBulkUpdateForm() { 
    $form = \Drupal::formBuilder()->getForm('Drupal\silai_import\Form\schoolBulkUpdateForm');
    return $form;
  }   
  	#
  public function schoolSurveyBulkImportForm() { 
    $form = \Drupal::formBuilder()->getForm('Drupal\silai_import\Form\schoolSurveyBulkImportForm');
    return $form;
  } 
  #
}
