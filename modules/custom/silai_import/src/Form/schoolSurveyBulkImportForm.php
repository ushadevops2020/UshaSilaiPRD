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

class schoolSurveyBulkImportForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'silai_school_survey_bulk_import';
  }
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $cancelRedirectURI = drupal_get_destination();
   /*  $form['description'] = array(
      HASH_MARKUP => '<p>Use this form to upload a XLS file of Data</p>',
      HASH_PREFIX => '<div class="sample-file-download"><a target="_blank" href="/sites/default/files/import_file/silai_school_data.xls"><i class="fa fa-download"></i> Download Template</a></div>',
    ); */
    $form['import_file'] = array(
      '#type' => 'managed_file',
      '#title' => t('Upload file here'),
      '#upload_location' => 'public://importschoolSurvey/',
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
        $operations[] = ['\Drupal\silai_import\Services\schoolSurveyBulkImportContent::schoolSurveyAddImportContentItem', [$data]];
      }
    }
    if(!empty($operations)) {
      $batch = array(
        'title' => t('Importing Data...'),
        'operations' => $operations,
        'init_message' => t('Import is starting.'),
        'finished' => '\Drupal\silai_import\Services\schoolSurveyBulkImportContent::schoolSurveyAddImportContentItemCallback',
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
		$error['error'][$row] = $item[0].' - Teacher Id field is Blank. In Row Number : '.$row;
    }else{
		$query = \Drupal::entityQuery(NODE)
			->condition(TYPE, 'silai_school')
			->condition('field_school_code', $item[0]);
		$schoolCodeCheck = $query->execute();
		if(empty($schoolCodeCheck)) {
			$error['error'][$row] = $item[0].' - Teacher Id is not exist. In Row Number : '.$row;
		} else {
			foreach ($schoolCodeCheck as $schoolNid) {
				$item[0] = $schoolNid;
			}
		}
    }
	if(!empty($item[1])){
		if(in_array($item[1], RADIOSFIELD_NO_YES_OPTIONS)){
		  $item[1] = array_search($item[1], RADIOSFIELD_NO_YES_OPTIONS);
		}else{
		  $error['error'][] = $item[1].' - SIGNAGE IN THE SCHOOL is not valid in row number . '.$row.'.';
		}
	}
	if(!empty($item[2])){
		if(in_array($item[2], CONDITION_OF_SIGNBOARD_OPTIONS)){
		  $item[2] = array_search($item[2], CONDITION_OF_SIGNBOARD_OPTIONS);
		}else{
		  $error['error'][] = $item[2].' - CONDITION OF SIGNBOARD is not valid in row number . '.$row.'.';
		}
	}
	if(!empty($item[12])){
		if(in_array($item[12], RELIGION_OPTIONS)){
		  $item[12] = array_search($item[12], RELIGION_OPTIONS);
		}else{
		  $error['error'][] = $item[12].' - RELIGION is not valid in row number . '.$row.'.';
		}
	}
	/* print_r($item);
	die('hello'); */
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
    $logfile = IMPORT_LOG_PATH."/import-school-survey-log_".date("j_M_Y_h_i_s_a").".txt";
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