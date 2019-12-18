<?php
/**
 * @file
 * Contains \Drupal\IMPORT_EXAMPLE\Form\ImportForm.
 */
namespace Drupal\mis\Form;
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
use Drupal\user\Entity\User;

class MonthlyBulkImportForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'mis_import_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['description'] = array(
      HASH_MARKUP => '<p>Use this form to upload a XLS file of Data</p>',
      HASH_PREFIX      => '<div class="sample-file-download"><a target="_blank" href="/sites/default/files/import_file/monthly_quarterly_mis_update_sample_file_v.2.xls"><i class="fa fa-download"></i> Download Template</a></div>',
    );
    $form['import_type'] = [
      HASH_TYPE => SELECTFIELD,
      HASH_TITLE => t('MIS Type'),
      HASH_OPTIONS => array('Monthly', 'Quarterly'),
      HASH_REQUIRED => TRUE
    ];
    $form['import_file'] = array(
      HASH_TYPE => 'managed_file',
      HASH_TITLE => t('Upload file here'),
      HASH_UPLOAD_LOCATION => 'public://importmis/',
      HASH_DEFAULT_VALUE => '',
      HASH_REQUIRED => TRUE,
      HASH_UPLOAD_VALIDATORS  => array("file_validate_extensions" => array("xls")),
      '#states' => array(
        'visible' => array(
          ':input[name="File_type"]' => array('value' => t('Upload Your File')),
        ),
      ),
    );

    $form['actions']['#type'] = 'actions';

    $form['actions']['submit'] = array(
      HASH_TYPE => 'submit',
      HASH_VALUE => $this->t('Upload'),
      HASH_BUTTON_TYPE => 'primary'
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $error = array();
    /* Fetch the array of the file stored temporarily in database */
    $importFile = $form_state->getValue('import_file');
    $importType = $form_state->getValue('import_type');
    /* Load the object of the file by it's fid */
    $file = File::load( $importFile[0] );
    /* Set the status flag permanent of the file object */
    $file->setPermanent();
    /* Save the file in database */
    $file->save();
    $inputFileType = IOFactory::identify($file->getFileUri());
    $objReader   = IOFactory::createReader($inputFileType);
    $objPHPExcel = $objReader->load($file->getFileUri());
    $sheet = $objPHPExcel->getSheet($importType); 
    $highestRow = $sheet->getHighestRow(); 
    $highestColumn = $sheet->getHighestColumn();
    $duplicateFYr = $map =  array();
    $duplicateM = array();
    for ($row = 2; $row <= $highestRow; $row++) {
      //  Read a row of data into an array
      $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
      // print_r($rowData[0]);die;
      $data = $this->validateImportData($rowData[0], $row, $importType);
      
      if(!empty($data['error'])) {
        $error['error'][] = $data['error'];
      } else {
        $map[$data[0]][$data[2]] = $data[1];
        if(!in_array($map[$data[0]][$data[2]], $duplicateFYr[$data[0]][$data[2]])) {
          $finalData = ['\Drupal\mis\Services\addImportContent::addImportContentItem', [$data]];
          $operations[] = $finalData;
        } else {
          $date = date("m/d/Y", $data[0]);
          $error['error'][] = [$data[0].'--'.$data[2].'--'.$data[1].' - Data already exists on same fiscal year/Month in row number '.$row.'.'];
        }
        $duplicateFYr[$data[0]][$data[2]][] = $data[1];
        $duplicateM[] = $data[1];
        
      } 
      
    }
    if(!empty($operations)) {
      $batch = array(
        'title' => t('Importing Data...'),
        'operations' => $operations,
        'init_message' => t('Import is starting.'),
        'finished' => '\Drupal\mis\Services\addImportContent::addImportContentItemCallback',
      );
      batch_set($batch);
    }
    if(!empty($error)) {
      $error = array_values(current($error));
      foreach($error as $value) {
        foreach ($value as $row => $data) {
          drupal_set_message(t('Error '.$data), 'error');
        }
        
      }
    } 
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    /* Fetch the array of the file stored temporarily in database */
    $import_file = $form_state->getValue('import_file');
    // echo $importType = $form_state->getValue('import_type');die;
    // if (empty($importType)) {
    //   $form_state->setErrorByName('error', $this->t('Please select MIS Type.'));
    // }
    if (empty($import_file)) {
      $form_state->setErrorByName('error', $this->t('Please upload file.'));
    }


  }
 
  public function validateImportData($item, $row, $type) {

    $error = array();
    $fiscalYr = $item[0];
    $fiscalMonth = $item[1];
    $schoolCode = $item[2];
    $conn = Database::getConnection();
    $current_user = \Drupal::currentUser();
    $roles = $current_user->getRoles();
    $user = User::load($current_user->id());
    $misDataService = \Drupal::service('silai_mis.monthly_quarterly_list');
    if(count($item) <= 0) {
      $error['error'][] = 'Please upload valid file.';
    }
    if(in_array(ROLE_SILAI_PC, $roles)){
      $locationId = $user->field_user_location->target_id;
    } else if(in_array(ROLE_SILAI_NGO_ADMIN,$roles)){
      $masterDataService = \Drupal::service('silai.master_data');
      $locationId = $masterDataService->getNgoLocationIds($current_user->id());
      $ngoIdArr = $masterDataService->getLinkedNgoForUser($current_user->id());
      $ngoId = $ngoIdArr[$current_user->id()];
    }  

    if($type == 1) {
      $results = QUARTERLY_TYPE_DATA;
      $item['type'] = 3;
    } else {
      $results = MONTHLY_TYPE_DATA;
      $item['type'] = 2;
    }
    if(!empty($fiscalYr)) {
      $result = $misDataService->getMQFiscalYear($type, $schoolCode);
      if(!in_array($fiscalYr, $result)) {
          $error['error'][] = $fiscalYr.' - Fiscal year is not valid in row number . '.$row.'.';
      }
    }
    $monthValid = array_search($fiscalMonth, $results);
    if(empty($monthValid)) {
      $error['error'][] = $fiscalYr.'-'.$fiscalMonth.' - Monthly/Quaterly is not valid in row number . '.$row.'.';
    }
    $query = \Drupal::entityQuery(NODE)
        ->condition(TYPE, 'silai_school')
        ->condition('field_school_code', $schoolCode);
    $schoolCodeArr = $query->execute();
    if(empty($schoolCodeArr)) {
        $error['error'][] = $item[2].' - School Code is not valid in row number '.$row.'.';
    } else {
        $nid = array_values($schoolCodeArr);
        $data = node::load($nid[0]);
        if(in_array(ROLE_SILAI_PC, $roles) && $locationId != $data->field_silai_location->target_id){
            $error['error'][] = $item[2].' - School Code is not belong to current user location in row number '.$row.'.';
        }else if(in_array(ROLE_SILAI_NGO_ADMIN,$roles) && $data->field_name_of_ngo->target_id != $ngoId){
            $ngoIdArr = $masterDataService->getLinkedNgoForUser($current_user->id());
            $error['error'][] = $item[2].' - School Code is not belong to current user location in row number '.$row.'.';
        }
        $result1 = $misDataService->monthlyQuarterlyList($fiscalYr, $type, $nid[0]);
        if(!in_array($fiscalMonth, $result1)) {
          $error['error'][] = $fiscalMonth.' - Month is already uploaded or not valid in row number '.$row.'.';
        }
        $query = $conn->select('usha_monthly_mis', 'm');
        $query->condition('monthly_quarterly_type', $type);
        $query->condition('fiscal_year', $fiscalYr);
        $query->condition('school_code', $nid , 'IN');
        $query->condition('monthly_quarterly_value', $monthValid);
        $query->condition('is_deleted', 0);
        $query->fields('m');
        $record = $query->countQuery()->execute()->fetchField();
        if($record > 0) {
          $error['error'][] = $fiscalYr.'-'.$schoolCode.'-'.$fiscalMonth.' - Data already exists on same fiscal year in row number . '.$row.'.';
        }
       /*  if(!empty($item[3]) && !$this->isRealDate($item[3]) && !is_numeric($item[3])) {
            $error['error'][] = $item[3].' - Date of Training is not valid in row number . '.$row.'.';
        } */
		if(!empty($item[4])){
			if(in_array($item[4], BULK_UPLOAD_NO_YES_OPTIONS)){
			  $item[4] = array_search($item[4], BULK_UPLOAD_NO_YES_OPTIONS);
			}else{
			  $error['error'][] = $item[4].' - Sign board receive is not valid in row number . '.$row.'.';
			}
		}
		if(!empty($item[5])){
			if(in_array($item[5], BULK_UPLOAD_NO_YES_OPTIONS)){
			  $item[5] = array_search($item[5], BULK_UPLOAD_NO_YES_OPTIONS);
			}else{
			  $error['error'][] = $item[5].' -  SIGN BOARD DISPLAYED AT PROMINENT PLACE is not valid in row number . '.$row.'.';
			}
		}
		if(!empty($item[6])){
			if(in_array($item[6], CONDITION_OF_SIGN_BOARD)){
			  $item[6] = array_search($item[6], CONDITION_OF_SIGN_BOARD);
			}else{
			  $error['error'][] = $item[6].' - CONDITION OF SIGN BOARD is not valid in row number . '.$row.'.';
			}
		}
		if(!empty($item[7])){
			if(in_array($item[7], BULK_UPLOAD_NO_YES_OPTIONS)){
			  $item[7] = array_search($item[7], BULK_UPLOAD_NO_YES_OPTIONS);
			}else{
			  $error['error'][] = $item[7].' - SEWING MACHINE WORKING PROPERLY is not valid in row number . '.$row.'.';
			}
		}
		if(!empty($item[8])){
			if(in_array($item[8], MONTHLY_QUARTERLY_IF_NO_THEN_WHAT_IS_THE_PROBLEM)){
			  $item[8] = array_search($item[8], MONTHLY_QUARTERLY_IF_NO_THEN_WHAT_IS_THE_PROBLEM);
			}else{
			  $error['error'][] = $item[8].' - IF NO THEN WHAT IS THE PROBLEM is not valid in row number . '.$row.'.';
			}
		}
		if(!empty($item[10])){
			if(in_array($item[10], MONTHLY_QUARTERLY_SCHOOL_WORKING_STATUS)){
			  $item[10] = array_search($item[10], MONTHLY_QUARTERLY_SCHOOL_WORKING_STATUS);
			}else{
			  $error['error'][] = $item[10].' - SCHOOL WORKING STATUS is not valid in row number . '.$row.'.';
			}
		}
		if(!empty($item[11])){
			if(in_array($item[11], MONTHLY_QUARTERLY_REASON_NON_WORKING_OF_SCHOOL)){
			  $item[11] = array_search($item[11], MONTHLY_QUARTERLY_REASON_NON_WORKING_OF_SCHOOL);
			}else{
			  $error['error'][] = $item[11].' - REASON FOR NON WORKING OF SCHOOL is not valid in row number . '.$row.'.';
			}
		}
		if(!empty($item[12])){
			if(in_array($item[12], MONTHLY_QUARTERLY_ACTIVITIES_DONE_FOR_INCREASING_LEARNERS)){
			  $item[12] = array_search($item[12], MONTHLY_QUARTERLY_ACTIVITIES_DONE_FOR_INCREASING_LEARNERS);
			}else{
			  $error['error'][] = $item[12].' - ACTIVITIES DONE FOR INCREASING LEARNERS is not valid in row number . '.$row.'.';
			}
		}
		if(!empty($item[24])){
			if(in_array($item[24], MONTHLY_QUARTERLY_WHERE_DO_STUDENTS_PRACTICE)){
			  $item[24] = array_search($item[24], MONTHLY_QUARTERLY_WHERE_DO_STUDENTS_PRACTICE);
			}else{
			  $error['error'][] = $item[24].' - WHERE DO STUDENTS PRACTICE is not valid in row number . '.$row.'.';
			}
		}
    }
    if(empty($error)) {
      $item[] = $data->field_silai_location->target_id;
       $item[2] = $nid[0];
	   /* if(!empty($item[3])){
		  if(!empty($item[3]) && !$this->isRealDate($item[3])) {
			  $item[3] = strtotime($this->getDate($item[3]));
		  } else {
			  $item[3] = str_replace('/', '-', $item[3]);
			  $item[3] = strtotime($item[3]);
		   }  
	   }else{
		   $item[3] = strtotime("now");
	   } */
      $item[] = '';
	  $item[28] = $data->field_silai_location->target_id;
      $item['ngo_id'] = $data->field_name_of_ngo->target_id;
      $item['school_type'] = $data->field_school_type->target_id;
      $item['state'] = $data->field_silai_business_state->target_id;
      $item['district'] = $data->field_silai_district->target_id;
      $item['block'] = $data->field_silai_block->target_id;
      $item['village'] = $data->field_silai_village->target_id;
      $result = $item;
    } else {
      $result = $error;
    }
    return $result;
  }

  public function isRealDate($date) {
    $date = str_replace('/', '-', $date); 
    if (false === strtotime($date)) { 
        return false;
    }
    list($day, $month, $year) = explode('-',$date);
    return checkdate($month, $day, $year);
  }

  public function getDate($excelDate){
    $unixDate = ($excelDate - 25569) * 86400;
    $excelDate = 25569 + ($unixDate / 86400);
    $unixDate = ($excelDate - 25569) * 86400;
    return gmdate("Y-m-d", $unixDate);  
  }
   
}