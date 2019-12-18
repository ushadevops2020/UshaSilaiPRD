<?php
/**
 * @file
 * Contains \Drupal\IMPORT_EXAMPLE\Form\ImportForm.
 */
namespace Drupal\silai\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\file\Entity\File;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Ajax\RedirectCommand;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Drupal\Core\Extension;
use Drupal\Core\Database\Database;

class dealersBulkImportForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'silai_learner_bulk_import';
  }
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $cancelRedirectURI = drupal_get_destination();
    $form['description'] = array(
      HASH_MARKUP => '<p>Use this form to upload a XLS file of Data</p>',
      HASH_PREFIX      => '<div class="sample-file-download"><a target="_blank" href="/sites/default/files/import_file/silai_dealer_data.xls"><i class="fa fa-download"></i> Download Template</a></div>',
    );
    $form['import_file'] = array(
      '#type' => 'managed_file',
      '#title' => t('Upload file here'),
      '#upload_location' => 'public://importdealers/',
      '#default_value' => '',
      HASH_REQUIRED => TRUE,
      "#upload_validators"  => array("file_validate_extensions" => array("xls")),
      '#states' => array(
        'visible' => array(
          ':input[name="File_type"]' => array('value' => t('Upload Your File')),
        ),
      ),
    );
    $form['actions']['#type'] = 'actions';

    $form[ACTIONS]['cancel'] = array(
      HASH_TYPE => 'button',
      HASH_VALUE => t('Cancel'),
      HASH_WEIGHT => -1,
      HASH_ATTRIBUTES => array('onClick' => 'window.location.href = "'.$cancelRedirectURI['destination'].'"; event.preventDefault();'),
      );
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Upload'),
      '#button_type' => 'primary',
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $error = array();
/* Fetch the array of the file stored temporarily in database */
    $import_file = $form_state->getValue('import_file');
    /* Load the object of the file by it's fid */
    $file = File::load( $import_file[0] );
    /* Set the status flag permanent of the file object */
    $file->setPermanent();
    /* Save the file in database */
    $file->save();

    $inputFileType = IOFactory::identify($file->getFileUri()); 
    $objReader   = IOFactory::createReader($inputFileType);
    $objPHPExcel = $objReader->load($file->getFileUri());
    $sheet = $objPHPExcel->getSheet(0); 
    $highestRow = $sheet->getHighestRow(); 
    $highestColumn = $sheet->getHighestColumn();
    for ($row = 2; $row <= $highestRow; $row++) {
      $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
      $data = $this->validateImportData($rowData[0], $row);
      if(!empty($data['error'])) {
        $error[] = $data['error'];
      }else{
        $operations[] = ['\Drupal\silai\Services\dealersBulkImportContent::dealersAddImportContentItem', [$data]];
      }
    }
    if(!empty($operations)) {
      $batch = array(
        'title' => t('Importing Data...'),
        'operations' => $operations,
        'init_message' => t('Import is starting.'),
        'finished' => '\Drupal\silai\Services\dealersBulkImportContent::dealersAddImportContentItemCallback',
      );
      batch_set($batch);
    }
    if(!empty($error)) {
      $error = array_map('current', $error);
      foreach($error as $key=>$value) {
        drupal_set_message(t('Error: '.$value), 'error');
      }
    } 
    #
  }
  public function validateImportData($item, $row) {
    $error = array();
    # Check Dealer Code Unique
    $dealerQuery = \Drupal::entityQuery(NODE)
        ->condition(TYPE, 'silai_dealer')
        ->condition('field_silai_dealer_code', $item[1]);
    $dealerCodeCheck = $dealerQuery->execute();
    if(empty($dealerCodeCheck)){
      # Get District and state.
      $query = \Drupal::entityQuery(NODE)
          ->condition(TYPE, 'silai_district')
          ->condition('field_silai_district_code', $item[3]);
      $destrictCodeCheck = $query->execute();
      if(empty($destrictCodeCheck)) {
          $error['error'][$row] = $item[3].' - District Code is not exist. In Row Number : '.$row;
      } else {
        foreach ($destrictCodeCheck as $destrictNid) {
          $item[3] = $destrictNid;
          $node = Node::load($destrictNid);
          $stateNid = $node->field_silai_business_state->target_id;
          $item[10] = $stateNid;
        }
      }
      #town code Nid
      if(!empty($item[4])){
        $query = \Drupal::entityQuery(NODE)
            ->condition(TYPE, 'silai_town')
            ->condition('field_silai_town_code', $item[4]);
          $townCodeCheck = $query->execute();
        if(empty($townCodeCheck)) {
            $error['error'][$row] = $item[4].' - Town Code is not exist. In Row Noumber : '.$row;
        } else {
          foreach ($townCodeCheck as $townNid) {
            $item[4] = $townNid;
          }
        }
      }
    }else{
      $error['error'][$row] = $item[1].' - Dealer Code is already exist. In Row Number : '.$row;
    }
    if(empty($error)) {
      $result = $item;
    } else {
      $result = $error;
    }
    return $result;
  }
}