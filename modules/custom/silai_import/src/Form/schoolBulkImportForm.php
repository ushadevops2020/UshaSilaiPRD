<?php
/**
 * @file
 * Contains \Drupal\IMPORT_EXAMPLE\Form\ImportForm.
 */
namespace Drupal\silai_import\Form; 
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

class schoolBulkImportForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'silai_village_bulk_import';
  }
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $cancelRedirectURI = drupal_get_destination();
    $form['description'] = array(
      HASH_MARKUP => '<p>Use this form to upload a XLS file of Data</p>',
      HASH_PREFIX => '<div class="sample-file-download"><a target="_blank" href="/sites/default/files/import_file/silai_school_data.xls"><i class="fa fa-download"></i> Download Template</a></div>',
    );
    $form['import_file'] = array(
      '#type' => 'managed_file',
      '#title' => t('Upload file here'),
      '#upload_location' => 'public://importschool/',
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
        $finalData = ['\Drupal\silai_import\Services\schoolBulkImportContent::schoolAddImportContentItem', [$data]];
        if(!in_array($data[3], $duplicateData)) {
          $operations[] = $finalData;
        } else {
          $error['error'][] = $data[3].' - School Code is already used in row number '.$row.'.';
        }
        $duplicateData[] = $data[3];
      }
    }
    if(!empty($operations)) {
      $batch = array(
        'title' => t('Importing Data...'),
        'operations' => $operations,
        'init_message' => t('Import is starting.'),
        'finished' => '\Drupal\silai_import\Services\schoolBulkImportContent::schoolAddImportContentItemCallback',
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
    if(empty($item[0])){
      $error['error'][$row] = $item[0].' - School Type is blank. In excel file row number : '.$row;
    }else{
      $item[0] = array_search($item[0], BULK_SCHOOL_TYPE_DATA);
    }
    if(!empty($item[1])){
      $item[1] = array_search($item[1], BULK_FISCAL_YEAR_DATA_FOR_SCHOOL);
    }
    if(empty($item[2])){
      $error['error'][$row] = $item[2].' - Village Code is blank. In excel file row number : '.$row;
    }else{
      $villageQuery = \Drupal::entityQuery(NODE)
        ->condition(TYPE, 'silai_villages')
        ->condition('field_silai_village_code', $item[2]);
      $villageID = $villageQuery->execute();
      if(empty($villageID)){
        $error['error'][$row] = $item[2].' - Village Code is not exist. In excel file row number : '.$row;
      }else{
        $nid = array_values($villageID);
        $villageData = Node::load($nid[0]);
        $item[2] = $nid[0];
        $item[20] = $villageData->field_silai_block->target_id;
        $item[21] = $villageData->field_silai_district->target_id;
        $districtData = Node::load( $item[21]);
        $item[22] = $districtData->field_silai_business_state->target_id;
        $item[23] = $districtData->field_silai_location->target_id;
      }
    }
    if(empty($item[3])){
      $error['error'][$row] = $item[3].' - School Code is not exist. In excel file row number : '.$row;
    }
    $item[6] = $item[3];
    $item[7] = 'qwerty';
    $item[8] = $item[3].'@usha.com';
    if(empty($item[9])){
      $item[9] = 1111111111;
    }
    if(empty($item[10])){
      $error['error'][$row] = $item[10].' - Ngo Code is blank. In excel file row number : '.$row;
    }else{
      $ngoQuery = \Drupal::entityQuery(NODE)
        ->condition(TYPE, 'ngo')
        ->condition('field_ngo_code', $item[10]);
      $ngoID = $ngoQuery->execute();
      if(empty($ngoID)){
        $error['error'][$row] = $item[10].' - Ngo Code is not exist. In excel file row number : '.$row;
      }else{
         $nid = array_values($ngoID);
         $item[10] = $nid[0];
      }
    }
    if($item[0] == 'Satellite Silai'){
      $item[11] = $item[11];
    }

    $item[15] = $this->getDateFromExcelDate($item[15]);
    $item[16] = $this->getDateFromExcelDate($item[16]);
    $item[17] = array_search($item[17], NO_YES_OPTIONS);
    if(empty($error)) {
      $result = $item;
      
    } else {
      $result = $error;
    }
    return $result;
  }
  #
  public function getDateFromExcelDate($excelDate){
    $excel_date = $excelDate; 
    $unix_date = ($excel_date - 25569) * 86400;
    $excel_date = 25569 + ($unix_date / 86400);
    $unix_date = ($excel_date - 25569) * 86400;
    return gmdate("Y-m-d", $unix_date);
  }
  #
  public function importLog($error) {
    date_default_timezone_set('Asia/Kolkata');
    $logfile = IMPORT_LOG_PATH."/import-village-log_".date("j_M_Y_h_i_s_a").".txt";
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