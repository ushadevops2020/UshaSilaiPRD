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

class BulkImportForm extends FormBase {
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

    $form[HASH_PREFIX] = '<div id="wrapper_mis_import_form">';
    $form[HASH_SUFFIX] = '</div>';
    $form['status_messages'] = [
      HASH_TYPE => 'status_messages',
      '#weight' => -10,
    ];
    $form['description'] = array(
      HASH_MARKUP => '<p>Use this form to upload a CSV/XLS file of Data</p>',
      HASH_PREFIX      => '<div class="sample-file-download"><a target="_blank" href="'.$base_url.'/sites/default/files/import_file/Weekly MIS Data Sample.xlsx"><i class="fa fa-download"></i> Download Template</a></div>',
    );

    $form['import_file'] = array(
      HASH_TYPE => 'managed_file',
      HASH_TITLE => t('Upload file here'),
      HASH_UPLOAD_LOCATION => 'public://importmis/',
      HASH_DEFAULT_VALUE => '',
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
    // $form['#attached']['library'][] = 'core/drupal.dialog.ajax';

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    /* Fetch the array of the file stored temporarily in database */
    $import_file = $form_state->getValue('import_file');
    /* Load the object of the file by it's fid */
    $file = File::load( $import_file[0] );

    /* Set the status flag permanent of the file object */
    $file->setPermanent();
    /* Save the file in database */
    $file->save();
    // You can use any sort of function to process your data. The goal is to get each 'row' of data into an array
    // If you need to work on how data is extracted, process it here.
    // $data = $this->csvtoarray($file->getFileUri(), ',');
    // $inputFileType = IOFactory::identify($file->getFileUri());
    $objReader   = IOFactory::createReader('Xls');
    $objPHPExcel = $objReader->load($file->getFileUri());
    $sheet = $objPHPExcel->getSheet(0); 
    $highestRow = $sheet->getHighestRow(); 
    $highestColumn = $sheet->getHighestColumn();
    for ($row = 1; $row <= $highestRow; $row++) {
      //  Read a row of data into an array
      $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
      $operations[] = ['\Drupal\mis\Services\addImportContent::addImportContentItem', [$rowData[0]]];
    }
    // print_r($container);die;
    $batch = array(
      'title' => t('Importing Data...'),
      'operations' => $operations,
      'init_message' => t('Import is starting.'),
      'finished' => '\Drupal\mis\Services\addImportContent::addImportContentItemCallback',
    );
    batch_set($batch);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    /* Fetch the array of the file stored temporarily in database */
    $import_file = $form_state->getValue('import_file');
    if (empty($import_file)) {
        $form_state->setErrorByName($key, $this->t('Please upload file.'));
    }

  }

  /**
   * AJAX callback handler that displays any errors or a success message.
   */
  public function ajaxValidation(array $form, FormStateInterface $form_state) {
      $doamin = _get_current_domain();
      $response = new AjaxResponse();
      
      if ($form_state->hasAnyErrors()) {
        $response->addCommand(new ReplaceCommand('#wrapper_mis_import_form', $form));
        return $response;
      }
      else {
        $command = new CloseModalDialogCommand();
        $response->addCommand($command);
        drupal_set_message(t('File uploaded succesfully.'), STATUS);
        return $response;
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

}