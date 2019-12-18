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

class addBulkStudentForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'sewing_add_bulk_student_form';
  }
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $cancelRedirectURI = drupal_get_destination();
    $form['description'] = array(
      HASH_MARKUP => '<p>Use this form to upload a XLS file of Data</p>',
      HASH_PREFIX      => '<div class="sample-file-download"><a target="_blank" href="/sites/default/files/import_file/add_new_student_templete.xls"><i class="fa fa-download"></i> Download Template</a></div>',
    );
    $form['import_file'] = array(
      '#type' => 'managed_file',
      '#title' => t('Upload file here'),
      '#upload_location' => 'public://AddSewingStudentBulk/',
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
        $operations[] = ['\Drupal\sewing_import\Services\addBulkStudentContent::studentAddImportContentItem', [$data]];
      }
    }
    if(!empty($operations)) {
      $batch = array(
        'title' => t('Importing Data...'),
        'operations' => $operations,
        'init_message' => t('Import is starting.'),
        'finished' => '\Drupal\sewing_import\Services\addBulkStudentContent::studentAddImportContentItemCallback',
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
        $schoolData = Node::load($nid[0]);
        $item[0] = $nid[0];
        //$item[51] = $schoolData->field_town_city->target_id;
        //$item[52] = $schoolData->field_district->target_id;
        $item['locationNid'] = $schoolData->field_location->target_id; 
        $item['schoolName'] = $schoolData->title->value;
        $item['schoolTypeNid'] = $schoolData->field_sewing_school_type->target_id;
      }
    }
	if(!empty($item[1])){
		if(in_array($item[1], ADD_STUDENT_BULK_IMPORT_SALUTATION)){
		  $item[1] = array_search($item[1], ADD_STUDENT_BULK_IMPORT_SALUTATION);
		}else{
		  $error['error'][$row] = $item[1].' - Salutation is not valid in row number . '.$row.'.';
		}
	}
	if(!empty($item[5])){
		if(in_array($item[5], ADD_STUDENT_BULK_IMPORT_GENDER)){
		  $item[5] = array_search($item[5], ADD_STUDENT_BULK_IMPORT_GENDER);
		}else{
		  $error['error'][$row] = $item[5].' - Gender is not valid in row number . '.$row.'.';
		}
	}
	if(!empty($item[6])){
      $item[6] = $this->getDateFromExcelDate($item[6]);
    }
	if(!empty($item[7])){
		if(in_array($item[7], ADD_STUDENT_BULK_IMPORT_QUALIFICATION)){
		  $item[7] = array_search($item[7], ADD_STUDENT_BULK_IMPORT_QUALIFICATION);
		}else{
		  $error['error'][$row] = $item[7].' - Qualification is not valid in row number . '.$row.'.';
		}
	}
	if(empty($item[10])){
      $error['error'][$row] = $item[10].' - State Code is blank. In excel file row number : '.$row;
    }else{
      $stateQuery = \Drupal::entityQuery(NODE)
        ->condition(TYPE, 'manage_business_states')
        ->condition('field_business_state_code', $item[10]);
      $state_id = $stateQuery->execute();
      if(empty($state_id)){
        $error['error'][$row] = $item[10].' - State Code does not exist. In excel file row number : '.$row;
      }else{
        $nid = array_values($state_id);
        $item[10] = $nid[0];        
      }
    }
	if(!empty($item[15])){
      $item[15] = $this->getDateFromExcelDate($item[15]);
    }
	/* if(!empty($item[16])){
      $item[16] = $this->getDateFromExcelDate($item[16]);
    } */
    if(empty($item[16])){
      $error['error'][$row] = $item[16].' - Course Code is blank. In excel file row number : '.$row;
    }else{
      $courseQuery = \Drupal::entityQuery(NODE)
        ->condition(TYPE, 'course_master')
        ->condition('field_course_code', $item[16]);
      $course_id = $courseQuery->execute();
      if(empty($course_id)){
        $error['error'][$row] = $item[16].' - Course Code does not exist. In excel file row number : '.$row;
      }else{
        $nid = array_values($course_id);
        $courseData = Node::load($nid[0]);
        $item[16] = $nid[0];        
        $item['courseCodeName'] = $courseData->title->value;
        $durationNid =  $courseData->field_course_duration->target_id;
		$courseDurationData = Node::load($durationNid);
		$masterDataService = \Drupal::service('sewing.master_data');
		
		$courseDuration = ($courseDurationData->field_duration->value == 1) ? $courseDurationData->field_duration->value.' Month' : $courseDurationData->field_duration->value.' Months';
		
		//$item['courseDurationInDigit'] = $courseDurationData->field_duration->value;
		$item['courseDuration'] = $courseDuration;
		$gradeData = $masterDataService->getGradeMasterData($item[0]);
		$paymentToUILPercent = $gradeData['field_payable_to_uil'];
		$courseMasterDataFee = $courseData->field_course_fee->value;
		$paymentToUILFee = ($paymentToUILPercent/100)*$courseMasterDataFee;
		$item['paymentToUILPercent'] = $paymentToUILPercent;
		$item['courseFee'] = $courseMasterDataFee;
		$item['feeDue'] = $paymentToUILFee;
		$item['feeRecived'] = 0;
		$item['outstandingfee'] = $paymentToUILFee;
		$item['admissionDate'] = date('Y-m-d');
		$item['courseCompleteDate'] = date('Y-m-d', strtotime("+".$courseDurationData->field_duration->value." months", strtotime($item[15])));
      }
    }
    /* if(!empty($item[18])){
      $item[18] = $this->getDateFromExcelDate($item[18]);
    } */
	if(!empty($item[17])){
		if(in_array($item[17], ADD_STUDENT_BULK_IMPORT_EXISTING_SEWING_MACHINE_BRANDS)){
		  $item[17] = array_search($item[17], ADD_STUDENT_BULK_IMPORT_EXISTING_SEWING_MACHINE_BRANDS);
		}else{
		  $error['error'][$row] = $item[17].' - "Existing Sewing Machine Brand" is not valid in row number . '.$row.'.';
		}
	}
	if(!empty($item[19])){
		if(in_array($item[19], ADD_STUDENT_BULK_IMPORT_YES_NO_OPTION)){
		  $item[19] = array_search($item[19], ADD_STUDENT_BULK_IMPORT_YES_NO_OPTION);
		}else{
		  $error['error'][$row] = $item[19].' - "Want to buy New" is not valid in row number . '.$row.'.';
		}
	}
	if(!empty($item[20])){
		if(in_array($item[20], ADD_STUDENT_BULK_IMPORT_MODEL_MAKE)){
		  $item[20] = array_search($item[20], ADD_STUDENT_BULK_IMPORT_MODEL_MAKE);
		}else{
		  $error['error'][$row] = $item[20].' - "Model Make" is not valid in row number . '.$row.'.';
		}
	}
	if(!empty($item[21])){
		if(in_array($item[21], ADD_STUDENT_BULK_IMPORT_TIME_TO_BUY)){
		  $item[21] = array_search($item[21], ADD_STUDENT_BULK_IMPORT_TIME_TO_BUY);
		}else{
		  $error['error'][$row] = $item[21].' - "Time to Buy" is not valid in row number . '.$row.'.';
		}
	}
	if(!empty($item[22])){
		if(in_array($item[22], ADD_STUDENT_BULK_IMPORT_FUTURE_PLAN)){
		  $item[22] = array_search($item[22], ADD_STUDENT_BULK_IMPORT_FUTURE_PLAN);
		}else{
		  $error['error'][$row] = $item[22].' - "Future Plan after Course" is not valid in row number . '.$row.'.';
		}
	}
	
    /* print_r($item);
	die; */
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