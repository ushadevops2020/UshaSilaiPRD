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

class studentBulkImportForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'sewing_student_bulk_import';
  }
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $cancelRedirectURI = drupal_get_destination();
    $form['description'] = array(
      HASH_MARKUP => '<p>Use this form to upload a XLS file of Data</p>',
      HASH_PREFIX      => '<div class="sample-file-download"><a target="_blank" href="/sites/default/files/import_file/sewing_student_data.xls"><i class="fa fa-download"></i> Download Template</a></div>',
    );
    $form['import_file'] = array(
      '#type' => 'managed_file',
      '#title' => t('Upload file here'),
      '#upload_location' => 'public://ImportSewingStudent/',
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
        $operations[] = ['\Drupal\sewing_import\Services\studentBulkImportContent::studentAddImportContentItem', [$data]];
      }
    }
    if(!empty($operations)) {
      $batch = array(
        'title' => t('Importing Data...'),
        'operations' => $operations,
        'init_message' => t('Import is starting.'),
        'finished' => '\Drupal\sewing_import\Services\studentBulkImportContent::studentAddImportContentItemCallback',
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

    if(empty($item[6])){
      $error['error'][$row] = $item[6].' - School Code is blank. In excel file row number : '.$row;
    }else{
      $schoolQuery = \Drupal::entityQuery(NODE)
        ->condition(TYPE, 'sewing_school')
        ->condition('field_sewing_school_code', $item[6]);
      $schoolID = $schoolQuery->execute();
      if(empty($schoolID)){
        $error['error'][$row] = $item[6].' - School Code does not exist. In excel file row number : '.$row;
      }else{
        $nid = array_values($schoolID);
        $schoolData = Node::load($nid[0]);
        $item[6] = $nid[0];
        $item[51] = $schoolData->field_town_city->target_id;
        $item[52] = $schoolData->field_district->target_id;
        $item[53] = $schoolData->field_location->target_id; 
        $item[54] = $schoolData->title->value;
        $item[55] = $schoolData->field_sewing_school_type->target_id;
      }
    }

    if(!empty($item[8]) && $item[8] != 'NA'){
      $item[8] = $this->getDateFromExcelDate($item[8]);
    }else{
      $item[8] ='';
    }
    if(empty($item[9])){
      $error['error'][$row] = 'Admission Number is blank. In excel file row number : '.$row;
    }
    if(empty($item[10])){
      $error['error'][$row] = 'Student Name is blank. In excel file row number : '.$row;
    }
    if(empty($item[11])){
      $error['error'][$row] = 'Father Name is blank. In excel file row number : '.$row;
    }
    if(!empty($item[12])){
      $item[12] = array_search($item[12], GENDER_SEWING_STUDENT);
    }
    if(!empty($item[13]) && $item[13] != 'NA'){
      $item[13] = $this->getDateFromExcelDate($item[13]);
    }else{
      $item[13] ='';
    }
    if(!empty($item[14])){
      $item[14] = array_search($item[14], MARITAL_STATUS_SEWING_STUDENT);
    }
    if(!empty($item[18]) && strlen($item[18]) >7) {
      $error['error'][$row] = $item[18].' - Pincode length Should be less than 8. In excel file row number : '.$row;
    }
    if(!empty($item[19]) && (strlen($item[19]) < 7 || strlen($item[19]) > 12)){
      $error['error'][$row] = $item[19].' - Mobile Number length Should be in between 7 to 11. In excel file row number : '.$row;
    } 
    if(!empty($item[24])){
      $item[24] = array_search($item[24], COURSE_TYPE_SEWING_STUDENT);
    }
    if(empty($item[25])){
      $error['error'][$row] = $item[25].' - Course Code is blank. In excel file row number : '.$row;
    }else{
      $courseQuery = \Drupal::entityQuery(NODE)
        ->condition(TYPE, 'course_master')
        ->condition('field_course_code', $item[25]);
      $course_id = $courseQuery->execute();
      if(empty($course_id)){
        $error['error'][$row] = $item[25].' - Course Code does not exist. In excel file row number : '.$row;
      }else{
        $nid = array_values($course_id);
        $courseData = Node::load($nid[0]);
        $item[25] = $nid[0];        
        $item[56] = $courseData->title->value;
        $item[57] = $courseData->field_course_duration->target_id;
      }
    }
    if(!empty($item[27]) && $item[27] != 'NA'){
      $item[27] = $this->getDateFromExcelDate($item[27]);
    }else{
      $item[27] ='';
    }
    if(!empty($item[32])){
      $item[32] = array_search($item[32], EXAM_APPEAR_SEWING_STUDENT);
    }
    if(!empty($item[33])){
      $item[33] = array_search($item[33], EXAM_RESULT_SEWING_STUDENT);
    }
    if(!empty($item[34]) && $item[34] != 'NA'){
      $item[34] = $this->getDateFromExcelDate($item[34]);
    }else{
      $item[34] ='';
    }
    if(!empty($item[35])){
      $item[35] = array_search($item[35], GRADES_SEWING_STUDENT);
    }
    if(!empty($item[37])){
      $item[37] = array_search($item[37], CERTIFICATION_ISSUED_SEWING_STUDENT);
    }
    if(!empty($item[39])){
      $item[39] = array_search($item[39], EXISTING_SEWING_MACHINE_BRANDS_SEWING_SCHOOL);
    }
    if(!empty($item[41])){
      $item[41] = array_search($item[41], WANT_TO_BUYNEW_SEWING_STUDENT);
    }
    if(!empty($item[42])){
      $item[42] = array_search($item[42], MODELMAKE_SEWING_STUDENT);
    }
    if(!empty($item[45])){
      $item[45] = array_search($item[45], FUTURE_PLANCOURSES_SEWING_STUDENT);
    }
    if(!empty($item[47])){
      $item[47] = array_search($item[47], STATUS_ONROLL_SEWING_STUDENT);
    }
    if(!empty($item[48])){
      $item[48] = array_search($item[48], EXIT_CODE_SEWING_SCHOOL);
    }
    if(empty($item[50]) && !empty($item[8])){
      $yearMonth = $this->getyearMonthExcelDate($item[8]);
      $yearMonthArray = explode('-', $yearMonth);
      $year = $yearMonthArray[0];
      $month = $yearMonthArray[1];
      $fy = $this->generateDateToFYear($year, $month);
      $item[50] = array_search($fy, FINANCIAL_YEAR_SEWING_SCHOOL_NEW);
      //$item[50] = array_search($item[50], FINANCIAL_YEAR_SEWING_STUDENT);
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
    $logfile = IMPORT_LOG_PATH."/import-sewing_student-log_".date("j_M_Y_h_i_s_a").".txt";
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