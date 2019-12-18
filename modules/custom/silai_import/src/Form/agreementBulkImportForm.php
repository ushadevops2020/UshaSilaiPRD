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

class agreementBulkImportForm extends FormBase {
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
      HASH_PREFIX => '<div class="sample-file-download"><a target="_blank" href="/sites/default/files/import_file/silai_agreement_data.xls"><i class="fa fa-download"></i> Download Template</a></div>',
    );
    $form['import_file'] = array(
      '#type' => 'managed_file',
      '#title' => t('Upload file here'),
      '#upload_location' => 'public://importagreement/',
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
        $operations[] = ['\Drupal\silai_import\Services\agreementBulkImportContent::agreementAddImportContentItem', [$data]];
      }
    }

    if(!empty($operations)) {
      $batch = array(
        'title' => t('Importing Data...'),
        'operations' => $operations,
        'init_message' => t('Import is starting.'),
        'finished' => '\Drupal\silai_import\Services\agreementBulkImportContent::agreementAddImportContentItemCallback',
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
  }
  public function validateImportData($item, $row) {
    $error = array();
    if(!empty($item[1])){
      $excelDate = $item[1];
      $item[1] = $this->getDateFromExcelDate($excelDate);
    }
    if(empty($item[3])){
      $error['error'][$row] = $item[3].' - NGO Code field is blank. In excel file row number : '.$row;
    }else{
      $ngoQuery = \Drupal::entityQuery(NODE)
        ->condition(TYPE, 'ngo')
        ->condition('field_ngo_code', $item[3]);
      $ngoID = $ngoQuery->execute();
      if(empty($ngoID)){
        $error['error'][$row] = $item[3].' - NGO Code is not exist. In excel file row number : '.$row;
      }else{
        $nid = array_values($ngoID);
        $item[3] = $nid[0];
      }
    }
    if(empty($item[4])){
      $error['error'][$row] = $item[4].' - NFA Number field is blank. In excel file row number : '.$row;
    }else{
      $nfaQuery = \Drupal::entityQuery(NODE)
        ->condition(TYPE, 'nfa')
        ->condition('title', $item[4]);
      $nfaID = $nfaQuery->execute();
      if(empty($nfaID)){
        $error['error'][$row] = $item[4].' - NFA Number is not exist. In excel file row number : '.$row;
      }else{
        $nid = array_values($nfaID);
        $item[4] = $nid[0];
      }
    }
    if(!preg_match('/^[0-9\b]*$/', $item[5])) {
      $item[5] = 0;
    }
    if(!empty($item[8])){
      $excelDate = $item[8];
      $item[8] = $this->getDateFromExcelDate($excelDate);
    }else{
      $error['error'][$row] = $item[8].' - Contract Valid From is blank. In excel file row number : '.$row;
    }
    if(!empty($item[9])){
      $excelDate = $item[9];
      $item[9] = $this->getDateFromExcelDate($excelDate);
    }else{
      $error['error'][$row] = $item[9].' - CONTRACT VALID - Till is blank. In excel file row number : '.$row;
    }
    if(empty($item[11])){
      $error['error'][$row] = $item[11].' - School Type is blank. In excel file row number : '.$row;
    }
    if(empty($error)) {
      $result = $item;
      
    } else {
      $result = $error;
    }
    return $result;
  }

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
    $logfile = IMPORT_LOG_PATH."/import-agreement-log_".date("j_M_Y_h_i_s_a").".txt";
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