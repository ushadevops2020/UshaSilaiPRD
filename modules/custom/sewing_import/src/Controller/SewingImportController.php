<?php

namespace Drupal\sewing_import\Controller;

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
class SewingImportController extends ControllerBase {
    /**
   * Display the markup.
   *
   * @return array
   *   Return markup array.
   */
  protected $formBuilder;
  /**
   * Attached School Bulk Import Form
   *  The form builder.
   */
  public function schoolBulkImportForm() { 
    $form = \Drupal::formBuilder()->getForm('Drupal\sewing_import\Form\schoolBulkImportForm');
    return $form;
  }
  /**
   * Attached District Bulk Import Form
   *  The form builder.
   */
  public function districtBulkImportForm() { 
    $form = \Drupal::formBuilder()->getForm('Drupal\sewing_import\Form\districtBulkImportForm');
    return $form;
  }

  /**
   * Attached Town Bulk Import Form
   *  The form builder.
   */
  public function townBulkImportForm() { 
    $form = \Drupal::formBuilder()->getForm('Drupal\sewing_import\Form\townBulkImportForm');
    return $form;
  }

   /**
   * Attached Student Bulk Import Form
   *  The form builder.
   */
  public function studentBulkImportForm() { 
    $form = \Drupal::formBuilder()->getForm('Drupal\sewing_import\Form\studentBulkImportForm');
    return $form;
  }

   /**
   * Attached Fee Configuration Bulk Import Form
   *  The form builder.
   */
  public function feeconfigBulkImportForm() { 
    $form = \Drupal::formBuilder()->getForm('Drupal\sewing_import\Form\feeconfigBulkImportForm');
    return $form;
  }

  /**
   * Attached Course Bulk Import Form
   *  The form builder.
   */
  public function courseBulkImportForm() { 
    $form = \Drupal::formBuilder()->getForm('Drupal\sewing_import\Form\courseBulkImportForm');
    return $form;
  }

  /**
   * Attached Dealers Bulk Import Form
   *  The form builder.
   */
  public function dealersBulkImportForm() { 
    $form = \Drupal::formBuilder()->getForm('Drupal\sewing_import\Form\dealersBulkImportForm');
    return $form;
  }

  /**
   * Attached Activity Bulk Import Form
   *  The form builder.
   */
  public function activityBulkImportForm() { 
    $form = \Drupal::formBuilder()->getForm('Drupal\sewing_import\Form\activityBulkImportForm');
    return $form;
  }
  /**
   * Attached Student Bulk Update Form
   *  The form builder.
   */
  public function studentBulkResultUpdateForm() {  
    $form = \Drupal::formBuilder()->getForm('Drupal\sewing_import\Form\studentBulkResultUpdateForm');
    return $form;
  }
  
  /**
   * Attached Student Bulk Update Form
   *  The form builder.
   */
  public function revenueBulkImportForm() { 
    $form = \Drupal::formBuilder()->getForm('Drupal\sewing_import\Form\revenueBulkImportForm');
    return $form;
  }
  /**
   * Attached Student Bulk Update Form
   *  The form builder.
   */
  public function schoolFeeBulkUpdateForm() { 
    $form = \Drupal::formBuilder()->getForm('Drupal\sewing_import\Form\schoolFeeBulkUpdateForm');
    return $form;
  }
  /**
   * Attached teacher Bulk Upload Form
   *  The form builder.
   */
  public function teacherBulkImportForm() { 
    $form = \Drupal::formBuilder()->getForm('Drupal\sewing_import\Form\teacherBulkImportForm');
    return $form;
  }
  /**
   * Attached Add Bulk Student Form
   *  The form builder.
   */
  public function addBulkStudentForm() { 
    $form = \Drupal::formBuilder()->getForm('Drupal\sewing_import\Form\addBulkStudentForm');
    return $form;
  }
  /**
   * Attached Add Bulk Student Form
   *  The form builder.
   */
  public function studentBulkAdmissionNoUpdate() { 
    $form = \Drupal::formBuilder()->getForm('Drupal\sewing_import\Form\studentBulkAdmissionNoUpdate');
    return $form;
  }
  #
  /**
   * Attached Add Bulk Student Form
   *  The form builder.
   */
  public function studentBulkAdmissionSumQuery() { 
    $form = \Drupal::formBuilder()->getForm('Drupal\sewing_import\Form\studentBulkAdmissionSumQuery');
    return $form;
  }
  /**
   * Attached Bulk Certificate issued Form
   *  The form builder.
   */
  public function studentBulkCertificateIssued() { 
    $form = \Drupal::formBuilder()->getForm('Drupal\sewing_import\Form\studentBulkCertificateIssued');
    return $form;
  }
  #
}
