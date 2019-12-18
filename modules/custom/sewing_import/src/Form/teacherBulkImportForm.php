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

class teacherBulkImportForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'sewing_teacher_bulk_import';
  }
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $cancelRedirectURI = drupal_get_destination();
    $form['description'] = array(
      HASH_MARKUP => '<p>Use this form to upload a XLS file of Teacher Data</p>',
      HASH_PREFIX      => '<div class="sample-file-download"><a target="_blank" href="/sites/default/files/import_file/sewing_teacher_data.xls"><i class="fa fa-download"></i> Download Template</a></div>',
    );
    $form['import_file'] = array(
      '#type' => 'managed_file',
      '#title' => t('Upload file here'),
      '#upload_location' => 'public://ImportSewingTeacher/',
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
        $operations[] = ['\Drupal\sewing_import\Services\teacherBulkImportContent::teacherImportContentItem', [$data]];        
      }
    }
    if(!empty($operations)) {      
      $batch = array(
        'title' => t('Importing Data...'),
        'operations' => $operations,
        'init_message' => t('Import is starting.'),
        'finished' => '\Drupal\sewing_import\Services\teacherBulkImportContent::teacherImportContentItemCallback',
      );
      batch_set($batch);
    }
    if(!empty($error)) {
      $error = array_map('current', $error);
      //$this->importLog($error);
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
      $error['error'][$row] = $item[0].' - School Code is blank. In excel file row number : '.$row;
    }else{
      $schoolQuery = \Drupal::entityQuery(NODE)
        ->condition(TYPE, 'sewing_school')
        ->condition('field_sewing_school_code', $item[0]);
      $schoolID = $schoolQuery->execute();
      if(empty($schoolID)){
        $error['error'][$row] = $item[0].' - School Code does not exist. In excel file row number : '.$row;
      }else{
        $nid = array_values($schoolID);
        $item[0] = $nid[0];
      }
    }
	if(empty($item[1])){
      $error['error'][$row] = $item[1].' - Teacher Name is blank. In excel file row number : '.$row;
    }
    if(!empty($item[2])){
      $item[2] = array_search($item[2], GENDER_SEWING_TEACHER);
    }
	if(!empty($item[8])){
      $item[8] = $this->getDateFromExcelDate($item[8]);
    }
	if(!empty($item[10])){
      $item[10] = array_search($item[10], QUALIFICATION_SEWING_TEACHER);
    }
	if(!empty($item[12])){
      $item[12] = array_search($item[12], NO_YES_OPTIONS);
    }
	if(!empty($item[13])){
      $item[13] = $this->getDateFromExcelDate($item[13]);
    }
	if(!empty($item[15])){
      $item[15] = array_search($item[15], NO_YES_OPTIONS);
    }
	if(!empty($item[16])){
      $trainerQuery = \Drupal::entityQuery(NODE)
        ->condition(TYPE, 'trainer')
        ->condition('field_trainer_code', $item[16]);
      $trainerID = $trainerQuery->execute();
      if(empty($trainerID)){
        $error['error'][$row] = $item[16].' - Trainer Code does not exist. In excel file row number : '.$row;
      }else{
        $nid = array_values($trainerID);
        $item[16] = $nid[0];
      }
    }
	if(!empty($item[18])){
      $item[18] = $this->getDateFromExcelDate($item[18]);
    }
	if(!empty($item[19])){
      $item[19] = array_search($item[19], SKILL_LEVEL_SEWING_TEACHER);
    }
	if(!empty($item[20])){
      $item[20] = array_search($item[20], NO_YES_OPTIONS);
    }
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
  public function importLog($error) {
    date_default_timezone_set('Asia/Kolkata');
    $logfile = IMPORT_LOG_PATH."/import-sewing-teacher-log_".date("j_M_Y_h_i_s_a").".txt";
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