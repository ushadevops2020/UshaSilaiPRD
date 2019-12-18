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

class WeeklyBulkImportForm extends FormBase {
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
      HASH_PREFIX      => '<div class="sample-file-download"><a target="_blank" href="/sites/default/files/import_file/weekly_mis_sample_file_v.1.xls"><i class="fa fa-download"></i> Download Template</a></div>',
    );

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
    $duplicateData = array();
    for ($row = 2; $row <= $highestRow; $row++) {
      //  Read a row of data into an array
      $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
      $data = $this->validateImportData($rowData[0], $row);
      if(!empty($data['error'])) {
        $error[] = $data['error'];
      } else {
        if(!in_array($data[0], $duplicateData)) {
          $finalData = ['\Drupal\mis\Services\addImportContent::addImportContentItem', [$data]];
          $operations[] = $finalData;
        } else {
          $date = date("m/d/Y", $data[0]);
          $error['error'][] = $date.' - Week Start Date is not valid/already used in row number '.$row.'.';
        }
        $duplicateData[] = $data[0];
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
      $error = array_map('current', $error);
      foreach($error as $key=>$value) {
        drupal_set_message(t('Error '.$value), 'error');
      }
    } 
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    /* Fetch the array of the file stored temporarily in database */
    $import_file = $form_state->getValue('import_file');
    if (empty($import_file)) {
        $form_state->setErrorByName('error', $this->t('Please upload file.'));
    }


  }

  public function csvtoarray($filename='', $delimiter){

    if(!file_exists($filename) || !is_readable($filename)) return FALSE;
    $header = NULL;
    $data = array();

    if (($handle = fopen($filename, 'r')) !== FALSE ) {
      while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
      {
        if(!$header){
          $header = $row;
        }else{
          $data[] = array_combine($header, $row);
        }
      }
      fclose($handle);
    }
    return $data;
  }

  public function validateImportData($item, $row) {
    $error = array();
    $database = Database::getConnection();
    $current_user = \Drupal::currentUser();
    $roles = $current_user->getRoles();
    $user = User::load($current_user->id());
    $misDataService = \Drupal::service('silai_mis.monthly_quarterly_list');

    if(in_array(ROLE_SILAI_PC, $roles)){
      $locationId = $user->field_user_location->target_id;
    } elseif(in_array(ROLE_SILAI_NGO_ADMIN,$roles)){
      $masterDataService = \Drupal::service('silai.master_data');
      $locationId = $masterDataService->getNgoLocationIds($current_user->id());
    }

    if(!empty($item[0]) && !$this->isRealDate($item[0])) {
        $error['error'][] = $item[0].' - Week Start Date is not correct format in row number '.$row.'.';
    } else {
      $date = $this->isRealDate($item[0]);
      $fromDate = strtotime($date);
      $weeksData = $misDataService->getWeeks();
      foreach(array_keys($weeksData) as $value) {
        $startDate = explode('@@',$value);
        $startDateArr[] = $startDate[0];
      }
      if(!in_array($fromDate, $startDateArr)) {
        $error['error'][] = $date.' - Week Start Date is not valid/already used in row number '.$row.'.';
      } else {
        $toDate = strtotime("+6 day", $fromDate);
        $item[0] = $fromDate;
        $item['endDate'] = $toDate;
      }
    }
    $query = \Drupal::entityQuery(NODE)
        ->condition(TYPE, 'manage_silai_locations')
        ->condition('field_silai_location_code', $item[1]);
    $locationCode = $query->execute();
    if(empty($locationCode)) {
        $error['error'][] = $item[1].' - Location Code is not exist in row number '.$row.'.';
    } else {
      if(in_array(ROLE_SILAI_PC, $roles) && $locationId != current($locationCode)){
          $error['error'][] = $item[1].' - Location Code is not belong to current user location in row number '.$row.'.';
      }else if(in_array(ROLE_SILAI_NGO_ADMIN,$roles) && !in_array(current($locationCode), $locationId)){
          $error['error'][] = $item[1].' - Location Code is not belong to current user location in row number '.$row.'.';
      }else{
          $item[1] = current($locationCode);
      }      
    }
    
    if(empty($error)) {
      $item['type'] = 1;
      $result = $item;
    } else {
      $result = $error;
    }
    // print_r($result);die;
    return $result;
  }

  public function isRealDate($date) {
    if(is_numeric($date)) {
      $date = $this->getDate($date);
    }
    $date = str_replace('/', '-', $date);
    if (false === strtotime($date)) { 
        return false;
    }

    list($year, $month, $day) = explode('-',$date);
    if(checkdate($month, $day, $year)) {
      return $date;
    } else {
      return false;
    }
  }

  public function getDate($excelDate){
    $unixDate = ($excelDate - 25569) * 86400;
    $excelDate = 25569 + ($unixDate / 86400);
    $unixDate = ($excelDate - 25569) * 86400;
    return gmdate("Y-m-d", $unixDate);  
  }

  /**
 * Bulk Import log write
 * @param
 * $node   Object
 * @return
 */

  public function importLog($error) {
    $logfile = IMPORT_LOG_PATH."/import-mis-weekly-log.txt";
    $filesize = filesize($logfile); 
    $filesize = round($filesize / 1024 / 1024, 1);
    $fileContents = file_get_contents($logfile);
    $log = '--------------------------'.PHP_EOL;
    file_put_contents($logfile, $log . $fileContents);
    $message = array();
    foreach($error as $key=>$value) {
      if($filesize > 1) {
        $filename2 = IMPORT_LOG_PATH."/import-mis-weekly-log_" .date("j_M_Y").'.txt';
        rename($logfile, $filename2);
        $log  = date("j M Y h:i A").' - Row No.- '.$key.' - Error- '.$error[$key][0].PHP_EOL;
        $message .= $log;
        $fileContents = file_get_contents($logfile);
        file_put_contents($logfile, $log . $fileContents);
      } else {
        $log  = date("j M Y h:i A").' - Row No.- '.$key.' - Error- '.$error[$key][0].PHP_EOL;
        $message .= $log;
        $fileContents = file_get_contents($logfile);
        file_put_contents($logfile, $log . $fileContents);
      }
    }
    $_SESSION['weekly_mis_error'] = $message;
  } 

}