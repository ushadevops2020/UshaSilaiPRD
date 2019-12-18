<?php
/**
 * @file
 * Contains \Drupal\IMPORT_EXAMPLE\Form\ImportForm.
 */
namespace Drupal\sewing_import\Form;
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

class districtBulkImportForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'sewing_district_bulk_import';
  }
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $cancelRedirectURI = drupal_get_destination();
    $form['description'] = array(
      HASH_MARKUP => '<p>Use this form to upload a XLS file of District Data</p>',
      HASH_PREFIX      => '<div class="sample-file-download"><a target="_blank" href="/sites/default/files/import_file/sewing_district_data.xls"><i class="fa fa-download"></i> Download Template</a></div>',
    );
    $form['import_file'] = array(
      '#type' => 'managed_file',
      '#title' => t('Upload file here'),
      '#upload_location' => 'public://ImportSewingdistrict/',
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
    $import_file = $form_state->getValue('import_file');
    $file = File::load( $import_file[0] );
    $file->setPermanent();
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
        $operations[] = ['\Drupal\sewing_import\Services\districtsBulkImportContent::districtsAddImportContentItem', [$data]];        
      }
    }
    if(!empty($operations)) {      
      $batch = array(
        'title' => t('Importing Data...'),
        'operations' => $operations,
        'init_message' => t('Import is starting.'),
        'finished' => '\Drupal\sewing_import\Services\districtsBulkImportContent::districtsAddImportContentItemCallback',
      );
      batch_set($batch);
    }
    if(!empty($error)) {
      $error = array_map('current', $error);
      $this->importLog($error);
      foreach($error as $key=>$value) {
        drupal_set_message(t('Error: '.$value), 'error');
      }
    } 
    #

  }
  public function validateImportData($item, $row) {    
    $error = array();
    # Check Dealer Code Unique  

    if(empty($item[0])){
      $error['error'][$row] = $item[0].' - District Code field is empty. In excel file row number : '.$row;
    }elseif(!empty($item[0])){
      $locationQuery = \Drupal::entityQuery(NODE)
      ->condition(TYPE, 'manage_districts')
      ->condition('field_district_code', $item[0]);
      $districtID = $locationQuery->execute();
      if(!empty($districtID)){
      $error['error'][$row] = $item[0].' - District Code Already exist. In excel file row number : '.$row;
      }else{

        if(empty($item[1])){
          $error['error'][$row] = $item[0].' - District Name field is empty. In excel file row number : '.$row;
        }elseif(empty($item[3])){
          $error['error'][$row] = $item[0].' - State Code field is empty. In excel file row number : '.$row;
        }elseif(empty($item[4])){
          $error['error'][$row] = $item[0].' - Location Code field is empty. In excel file row number : '.$row;
        }
        elseif(!empty($item[3])){
          $stateQuery = \Drupal::entityQuery(NODE)
            ->condition(TYPE, 'manage_business_states')
            ->condition('field_business_state_code', $item[3]);
          $stateID = $stateQuery->execute();
          if(empty($stateID)){
            $error['error'][$row] = $item[3].' - State Code does not exist. In excel file row number : '.$row;
          }else{
            $nid = array_values($stateID);
            $stateData = Node::load($nid[0]);
            $item[2] = $nid[0];
            $item[3] = $stateData->field_country->target_id;

            if(!empty($item[4])){
              $locationQuery = \Drupal::entityQuery(NODE)
              ->condition(TYPE, 'manage_locations')
              ->condition('field_location_code', $item[4]);
              $locationID = $locationQuery->execute();
              if(empty($locationID)){
              $error['error'][$row] = $item[4].' - Location Code does not exist. In excel file row number : '.$row;
              }else{
              $nid = array_values($locationID);
              $locationData = Node::load($nid[0]);
              $item[4] = $nid[0];     
              }
            }
          }
        }          
      }
    }
    

    if(empty($error)) {
      $result = $item;
    } else {
      $result = $error;
    }    
    return $result;
  }
  #
  public function importLog($error) {
    date_default_timezone_set('Asia/Kolkata');
    $logfile = IMPORT_LOG_PATH."/import-sewing-district-log_".date("j_M_Y_h_i_s_a").".txt";
    $current = file_get_contents($file);
    foreach ($error as $key => $value) {
      $a = $key+1;
      $current .= $a.') '.$value.''.PHP_EOL;
    }
    file_put_contents($logfile, $current);
    $destination = IMPORT_LOG_PATH;
    $replace = "";
    file_unmanaged_move($logfile, $destination, $replace);
  }
  #
}