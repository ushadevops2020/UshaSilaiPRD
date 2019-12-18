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

class revenueBulkImportForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'sewing_revenue_bulk_import';
  }
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $cancelRedirectURI = drupal_get_destination();
    $form['description'] = array(
      HASH_MARKUP => '<p>Use this form to upload a XLS file of Data</p>',
      HASH_PREFIX      => '<div class="sample-file-download"><a target="_blank" href="/sites/default/files/import_file/revenue_fee.xls"><i class="fa fa-download"></i> Download Template</a></div>',
    );
    $form['import_file'] = array(
      '#type' => 'managed_file',
      '#title' => t('Upload file here'),
      '#upload_location' => 'public://ImportRevenue/',
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
        $operations[] = ['\Drupal\sewing_import\Services\revenueBulkImportContent::revenueBulkImportContentItem', [$data]];
      }
    }
    if(!empty($operations)) {
      $batch = array(
        'title' => t('Importing Data...'),
        'operations' => $operations,
        'init_message' => t('Import is starting.'),
        'finished' => '\Drupal\sewing_import\Services\revenueBulkImportContent::revenueBulkImportContentItemCallback',
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
      $error['error'][$row] = $item[0].' - Receipt Number is blank. In excel file row number : '.$row;
    }
    if(!empty($item[1])){
      $item[1] =  strtotime($this->getDateFromExcelDate($item[1]));
    }
    if(empty($item[2])){
      $error['error'][$row] = $item[2].' - School Code is blank. In excel file row number : '.$row;
    }else{
      $schoolQuery = \Drupal::entityQuery(NODE)
        ->condition(TYPE, 'sewing_school')
        ->condition('field_sewing_school_code', $item[2]);
      $schoolID = $schoolQuery->execute();
      if(empty($schoolID)){
        $error['error'][$row] = $item[2].' - School Code does not exist. In excel file row number : '.$row;
      }else{
        $nid = array_values($schoolID);
        $item[2] = $nid[0];
      }
    }
    if(empty($item[3])){
      $error['error'][$row] = $item[3].' - Payment Type is blank. In excel file row number : '.$row;
    }else{
      $item[3] = trim($item[3]);
    }
    if(!empty($item[5])){
      $admissionQuery = \Drupal::entityQuery(NODE)
        ->condition(TYPE, 'manage_sewing_students')
        ->condition('field_student_admission_no', $item[5]);
      $studentID = $admissionQuery->execute();
      if(empty($studentID)){
        $error['error'][$row] = $item[5].' - Admission Number does not exist. In excel file row number : '.$row;
      }else{
        $nid = array_values($studentID);
        $item[5] = $nid[0];
      }
    }
    if($item[6]!= ''){
      if(!is_numeric($item[6])){
        $error['error'][$row] = $item[6].' - Total Fee is not valid. In excel file row number : '.$row;
      }
    }
    if($item[7]!= ''){
      if(!is_numeric($item[7])){
        $error['error'][$row] = $item[7].' - Net Amount (Payble to UIL) is not valid. In excel file row number : '.$row;
      }
    }
    if($item[8]!= ''){
      if(!is_numeric($item[8])){
        $error['error'][$row] = $item[8].' - Service Tax amount is not valid. In excel file row number : '.$row;
      }
    }
    if(empty($item[9])){
        $error['error'][$row] = $item[9].' - Payment Mode field is blank. In excel file row number : '.$row;
    }else{
      $item[9] = REVENUE_BULK_PAYMENT_MODE[trim($item[9])];
      if(empty($item[9])){
        $error['error'][$row] = $item[9].' - Payment Mode text is wrong. In excel file row number : '.$row;
      }
    }
    if(!empty($item[11])){
      if(!is_numeric($item[11])){
        $error['error'][$row] = $item[11].' - Cheque amount is not valid. In excel file row number : '.$row;
      }
    }
    if(!empty($item[12])){
      $item[12] =  strtotime($this->getDateFromExcelDate($item[12]));
    }
    if(!empty($item[13])){
      $item[13] =  strtotime(date('H:i', $item[13] * 86400));
    }
    if(!empty($item[21])){
      $item[21] =  strtotime($this->getDateFromExcelDate($item[21]));
    }
    if(!empty($item[22])){
      $item[22] =  strtotime(date('H:i', $item[22] * 86400));
    }
    //print_r($item);
    //die;
    if(empty($error)) {
      $result = $item;
    }else {
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

  public function getyearMonthExcelDate($excelDate){
    $excel_date = $excelDate; 
    $unix_date = ($excel_date - 25569) * 86400;
    $excel_date = 25569 + ($unix_date / 86400);
    $unix_date = ($excel_date - 25569) * 86400;
    return gmdate("Y-m", $unix_date);
  }

  function generateDateToFYear($Year, $Month){
    if($Month <=3){
      $lastYear = $Year-1;
      $fYear = $lastYear.'-'.$Year;
    }else if($Month >= 4){
      $nextYear = $Year+1;
      $fYear = $Year.'-'.$nextYear;
    }
    return $fYear;
  }
  
  #
  public function importLog($error) {
    date_default_timezone_set('Asia/Kolkata');
    $logfile = IMPORT_LOG_PATH."/import-sewing_revenue-log_".date("j_M_Y_h_i_s_a").".txt";
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