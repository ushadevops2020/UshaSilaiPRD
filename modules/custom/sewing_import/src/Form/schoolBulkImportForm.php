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

class schoolBulkImportForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'sewing_school_bulk_import';
  }
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $cancelRedirectURI = drupal_get_destination();
    $form['description'] = array(
      HASH_MARKUP => '<p>Use this form to upload a XLS file of Data</p>',
      HASH_PREFIX      => '<div class="sample-file-download"><a target="_blank" href="/sites/default/files/import_file/sewing_school_data.xls"><i class="fa fa-download"></i> Download Template</a></div>',
    );
    $form['import_file'] = array(
      '#type' => 'managed_file',
      '#title' => t('Upload file here'),
      '#upload_location' => 'public://ImportSewingSchool/',
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
        $operations[] = ['\Drupal\sewing_import\Services\schoolBulkImportContent::schoolAddImportContentItem', [$data]];
      }
    }
    if(!empty($operations)) {
      $batch = array(
        'title' => t('Importing Data...'),
        'operations' => $operations,
        'init_message' => t('Import is starting.'),
        'finished' => '\Drupal\sewing_import\Services\schoolBulkImportContent::schoolAddImportContentItemCallback',
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
      $yearMonth = $this->getyearMonthExcelDate($item[28]);
      $yearMonthArray = explode('-', $yearMonth);
      $year = $yearMonthArray[0];
      $month = $yearMonthArray[1];
      $fy = $this->generateDateToFYear($year, $month);
      $item[0] = array_search($fy, FINANCIAL_YEAR_SEWING_SCHOOL_NEW);
      //$item[0] = array_search($item[0], FINANCIAL_YEAR_SEWING_SCHOOL);
    }
    if(empty($item[1])){
      $error['error'][$row] = $item[1].' - Town Code is blank. In excel file row number : '.$row;
    }else{
      $townQuery = \Drupal::entityQuery(NODE)
        ->condition(TYPE, 'manage_towns')
        ->condition('field_town_code', $item[1]);
      $townID = $townQuery->execute();
      if(empty($townID)){
        $error['error'][$row] = $item[1].' - Town Code does not exist. In excel file row number : '.$row;
      }else{
        $nid = array_values($townID);
        $townData = Node::load($nid[0]);
        $item[1] = $nid[0];
        $item[53] = $townData->field_country->target_id;
        $item[54] = $townData->field_district->target_id;        
        $item[55] = $townData->field_location->target_id;
        $item[56] = $townData->field_business_state->target_id;
      }
    }
    if(empty($item[6])){
      $error['error'][$row] = $item[6].' - School Type is blank. In excel file row number : '.$row;
    }else{
      $schooltypeQuery = \Drupal::entityQuery(NODE)
        ->condition(TYPE, 'school_type_master')
        ->condition('field_school_type_code', $item[6]);
      $schoolTypeID = $schooltypeQuery->execute();
      if(empty($schoolTypeID)){
        $error['error'][$row] = $item[6].' - School Type does not exist. In excel file row number : '.$row;
      }else{
        $nid = array_values($schoolTypeID);
        $item[6] = $nid[0];        
      }
    }
    if(empty($item[7])){
      $error['error'][$row] = $item[7].' - School Code is empty. In excel file row number : '.$row;
    }else{
      $schoolcodeQuery = \Drupal::entityQuery(NODE)
        ->condition(TYPE, 'sewing_school')
        ->condition('field_sewing_school_code', $item[7]);
      $schoolCode = $schoolcodeQuery->execute();
      if(!empty($schoolCode)){
        $error['error'][$row] = $item[7].' - School Code Already exist. In excel file row number : '.$row;
      }
    }
    if(empty($item[8])){
      $error['error'][$row] = $item[8].' - School Name is empty. In excel file row number : '.$row;
    }
    if(empty($item[9])){
      $error['error'][$row] = $item[9].' - Grade is empty. In excel file row number : '.$row;
    }else{
      $GradeQuery = \Drupal::entityQuery(NODE)
        ->condition(TYPE, 'grades')
        ->condition('title', $item[9]);
      $gradeCode = $GradeQuery->execute();
      if(empty($gradeCode)){
        $error['error'][$row] = $item[9].' - Grade Code does not exist. In excel file row number : '.$row;
      }else{
        $nid = array_values($gradeCode);
        $item[9] = $nid[0];        
      }
    }
    $item[57] = $item[7];
    $item[58] = $item[7];
    if(!empty($item[13]) && strlen($item[13]) >7) {
      $error['error'][$row] = $item[13].' - Pincode length Should be less than 8. In excel file row number : '.$row;
    }
    // if(empty($item[14])){
    //   $item[14] = 1111111111;
    // }elseif(!empty($item[14]) && strlen($item[14]) != 10){
    //   $error['error'][$row] = $item[14].' - Mobile Number length Should be equals to 10. In excel file row number : '.$row;
    // }
    if(empty($item[16])){
      $item[16] = $item[7].'@usha.com';
    }
    if(!empty($item[17]) && strlen($item[17]) >4) {
      $error['error'][$row] = $item[17].' - Area In Sqft length Should be less than 5. In excel file row number : '.$row;
    }
    if(!empty($item[18])){
      $item[18] = array_search($item[18], AREA_RANGE_SEWING_SCHOOL);
    }
    if(!empty($item[19]) && $item[19] != 'NA'){
      $item[19] = $this->getDateFromExcelDate($item[19]);
    }else{
      $item[19] ='';
    }
    if(!empty($item[21]) && $item[21] != 'NA'){
      $item[21] = $this->getDateFromExcelDate($item[21]);
    }else{
      $item[21] ='';
    }
    if(!empty($item[23]) && strlen($item[23]) != 10) {
      $error['error'][$row] = $item[23].' - PAN No. length Should be equals to 10. In excel file row number : '.$row;
    }
    if(!empty($item[24])){
      $item[24] = array_search($item[24], AREA_OF_OPERATION_SEWING_SCHOOL);
    }
    if(!empty($item[28]) && $item[28] != 'NA'){
      $item[28] = $this->getDateFromExcelDate($item[28]);
    }else{
      $item[28] ='';
    }
    if(!empty($item[29]) && $item[29] != 'NA'){
      $item[29] = $this->getDateFromExcelDate($item[29]);
    }else{
      $item[29] ='';
    }

    if(!empty($item[30])){
      $SewingDealerQuery = \Drupal::entityQuery(NODE)
          ->condition(TYPE, 'dealer')
          ->condition('field_dealer_code', $item[30]);
      $DealerCode = $SewingDealerQuery->execute();
      if(empty($DealerCode)){
        $item[30] = '';
      }else{
        $nid = array_values($DealerCode);
        $item[30] = $nid[0];        
      }
    }
    if(!empty($item[33])){
      if($item[33] == 'Approved') {
        $item[33] = 1;
      } elseif($item[33] == 'Terminated') {
        $item[33] = 3;
      } elseif($item[33] == 'Created') {
        $item[33] = 0;
      } 
    }    
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
    $logfile = IMPORT_LOG_PATH."/import-sewing_school-log_".date("j_M_Y_h_i_s_a").".txt";
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