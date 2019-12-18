<?php

namespace Drupal\silai\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Database\Database;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Entity\EntityInterface;

use Drupal\node\Entity\Node;
use Drupal\Core\Url;

class AgreementPaymentAmountEdit extends FormBase {


	public function getFormId() {
		return 'silai_custom_agreement_payment_amount_edit_form';
	}

	public function buildForm(array $form, FormStateInterface $form_state) {
		$formData = $form_state->getBuildInfo()['args'];
		//print_r($formData);
		$agreementId = $form_state->getBuildInfo()['args'][0];
		$nid = $form_state->getBuildInfo()['args'][1];
		$destinationData = drupal_get_destination();

		$form[HASH_PREFIX] = '<div id="wrapper_modal_agreement_payment_amount_edit">';
	    $form[HASH_SUFFIX] = '</div>';
	    $form['status_messages'] = [
	      HASH_TYPE => 'status_messages',
	      '#weight' => -10,
	    ];
	    $conn = Database::getConnection();
    	$query = $conn->select(TABLE_SILAI_NGO_PAYMENT_DETAIL, 's')
	            ->condition('id', $agreementId)
	            ->condition('nid', $nid)
	            ->fields('s');
	    $payDatas = $query->execute()->fetchAll(\PDO::FETCH_OBJ);
	    $payData = current($payDatas);
	    #load agreement data  
	    $node = Node::load($nid);
		$agreement_amount = $node->get('field_agreement_amount')->value;
	    # Section for new payment custom form
		$form[INSTALLMENT] = [
	     	HASH_TYPE => TEXTFIELD,
	      	HASH_TITLE => $this->t('Installment'),
	      	HASH_DEFAULT_VALUE => ($payData->installment) ? $payData->installment : '',
	      	HASH_MAXLENGTH => 10,
	      	HASH_REQUIRED => FALSE,
	      	//HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]]
	    ];
		$form[PAYMENT_MODE] = [
				HASH_TYPE => RADIOSFIELD,
				HASH_TITLE => t('Payment Mode'),
				HASH_OPTIONS => array( 
							1 => t('CHEQUE/DD'),
							2 => t('NEFT/RTGS'),
						),
				HASH_REQUIRED => TRUE,
				HASH_DEFAULT_VALUE => ($payData->payment_mode) ? $payData->payment_mode : '',
		];
		$form[AMOUNT] = [
	     	HASH_TYPE => TEXTFIELD,
	      	HASH_TITLE => $this->t('Amount'),
	      	HASH_DEFAULT_VALUE => ($payData->amount) ? $payData->amount : '',
	      	HASH_MAXLENGTH => 20,
	      	HASH_REQUIRED => TRUE,
	      	HASH_ATTRIBUTES => [CLASS_CONST => [ONLY_NUMERIC_VALUE]]
	    ];
	    $form['old_amount'] = [
	     	HASH_TYPE => 'hidden',
	      	HASH_TITLE => $this->t('Amount'),
	      	HASH_DEFAULT_VALUE => ($payData->amount) ? $payData->amount : '',
	      	HASH_MAXLENGTH => 20,
	      	HASH_REQUIRED => TRUE,
	      	HASH_ATTRIBUTES => [CLASS_CONST => [ONLY_NUMERIC_VALUE]]
	    ];
	     $form[AGREEMENT_AMOUNT] = [
	      	HASH_TYPE => 'hidden',
	      	HASH_TITLE => $this->t('Agreement_amount'),
	       	HASH_DEFAULT_VALUE => ($agreement_amount) ? $agreement_amount : '',
	      	HASH_MAXLENGTH => 20,
	       	//HASH_REQUIRED => TRUE,
	       	HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]]
	     ];
	    $form['cheque_dd_no'] = [
	     	HASH_TYPE => TEXTFIELD,
	      	HASH_TITLE => $this->t('Cheque/DD No.'),
	      	HASH_DEFAULT_VALUE => ($payData->cheque_no) ? $payData->cheque_no : '',
	      	HASH_MAXLENGTH => 20,
	      	//HASH_REQUIRED => TRUE,
	      	HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]]
	    ];
	    $form['bank_drawn'] = [
	     	HASH_TYPE => TEXTFIELD,
	      	HASH_TITLE => $this->t('PO Number'),
	      	HASH_DEFAULT_VALUE => ($payData->bank_drawn) ? $payData->bank_drawn : '',
	      	HASH_MAXLENGTH => 20,
	      	HASH_REQUIRED => TRUE,
	      	HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]]
	    ];
	    $form['transaction_date'] = [
	     	HASH_TYPE => DATEFIELD,
	      	HASH_TITLE => $this->t('Transaction Date'),
	      	HASH_DEFAULT_VALUE => ($payData->transaction_date) ? date('Y-m-d', $payData->transaction_date) : '',
	      	HASH_MAXLENGTH => 20,
	      	HASH_REQUIRED => TRUE,
	    ];
	    $form['invoice_no'] = [
	     	HASH_TYPE => TEXTFIELD,
	      	HASH_TITLE => $this->t('Invoice Number'),
	      	HASH_DEFAULT_VALUE => ($payData->invoice_no) ? $payData->invoice_no : '',
	      	HASH_MAXLENGTH => 20,
	      	HASH_REQUIRED => TRUE,
	      	HASH_ATTRIBUTES => [CLASS_CONST => [ALPHANUMERIC]]
	    ];
	    $form['invoice_date'] = [
	     	HASH_TYPE => DATEFIELD,
	      	HASH_TITLE => $this->t('Invoice Date'),
	      	HASH_DEFAULT_VALUE => ($payData->invoice_date) ? date('Y-m-d', $payData->invoice_date) : '',
	      	HASH_MAXLENGTH => 30,
	      	HASH_REQUIRED => TRUE,
	    ];
		# Cancle and save button   - 
		$form[ACTIONS] = array(HASH_TYPE => ACTIONS);
	    $form[ACTIONS]['cancel'] = array(
	    HASH_TYPE => 'button',
	    HASH_VALUE => t('Cancel'),
	    '#weight' => -1,
	    HASH_ATTRIBUTES => array('onClick' => 'window.location.href = "'.$destinationData['destination'].'"; event.preventDefault();'),
	    );
		$form[ACTIONS]['submit'] = array(
			HASH_TYPE => 'submit',
			HASH_VALUE => 'Update',
			'#attributes' => [
		        'class' => [
		          'use-ajax',
		        ],
		    ],
		    '#ajax' => [
		        'callback' => [$this, 'agreementPaymentAmountEditAjax'],
		        'event' => 'click',
		    ],
		);
		$form['#attached']['library'][] = 'core/drupal.dialog.ajax';
	   // die();
		return $form;
	}
	 /**
	   * AJAX callback handler that displays any errors or a success message.
	   */
	public function agreementPaymentAmountEditAjax(array $form, FormStateInterface $form_state) {
	  $doamin = _get_current_domain();
	  $response = new AjaxResponse();
	  
	  if ($form_state->hasAnyErrors()) {
	    $response->addCommand(new ReplaceCommand('#wrapper_modal_agreement_payment_amount_edit', $form));
	    return $response;
	  }
	  else {
	    $command = new CloseModalDialogCommand();
	    $response->addCommand($command);
	    drupal_set_message(t('Payment has been sucessfully done.'), STATUS);
	    $response->addCommand(new RedirectCommand('/agreements-listing'));
	    return $response;
	  } 
	}
	public function validateForm(array &$form, FormStateInterface $form_state) {
		$field = $form_state->getValues();
		$nid = $form_state->getBuildInfo()['args'][1];
		$conn = Database::getConnection();
	    $query = $conn->select(TABLE_SILAI_NGO_PAYMENT_DETAIL, 's')
	            ->condition('nid', $nid)
	            ->fields('s');
	    $payDatas = $query->execute()->fetchAll(\PDO::FETCH_OBJ);
	    $output = 0;
	    $total_pay_am = 0;
	    //print_r($payDatas);
	    foreach ($payDatas as $payData) {
	    	$output = $payData->amount + $total_pay_am;
	    	$total_pay_am = $output;
	    }
	    $amount = $field['amount'];
	    $oldAmount = $field['old_amount'];
	    $full_amount = ($amount + $total_pay_am) - $oldAmount;
		$agreement_amount = $field[AGREEMENT_AMOUNT];
		$old_due_amount = $agreement_amount - $total_pay_am;
		$new_due_amount = $old_due_amount + $oldAmount;
	    if($agreement_amount < $full_amount ){
	    	$message = 'Due Amount is : '.($new_due_amount);
	    	$form_state->setErrorByName(AMOUNT, $message);
	    }
	    $payment_mode = $field['payment_mode'];
	    if($payment_mode == 1){
	    	if(empty($field['cheque_dd_no'])){
	    		$message = 'CHEQUE/DD NO. field is required.';
	    		$form_state->setErrorByName('cheque_dd_no', $message);
	    	}
	    }
    }
    public function submitForm(array &$form, FormStateInterface $form_state) {	
    	$agreementId = $form_state->getBuildInfo()['args'][0];
		$nid = $form_state->getBuildInfo()['args'][1];	
		$field = $form_state->getValues();

		$dataArray = array(
            INSTALLMENT       => $field[INSTALLMENT],
            PAYMENT_MODE      => $field[PAYMENT_MODE],
            AMOUNT            => $field[AMOUNT],
            'cheque_no'         => $field['cheque_dd_no'],
            'bank_drawn'        => $field['bank_drawn'],
            'transaction_date'  => strtotime($field['transaction_date']),
            'invoice_no'        => $field['invoice_no'],
            'invoice_date'      => strtotime($field['invoice_date']),
            'payment_status'    => 0,
        );
        $database = \Drupal::database();
        $query = $database->update(TABLE_SILAI_NGO_PAYMENT_DETAIL)->fields($dataArray)->condition('id', $agreementId)->execute();
		$conn = Database::getConnection();
	    $query = $conn->select(TABLE_SILAI_NGO_PAYMENT_DETAIL, 's')
	            ->condition('nid', $nid)
	            ->fields('s');
	    $payDatas = $query->execute()->fetchAll(\PDO::FETCH_OBJ);
	    $output = 0;
	    $total_pay_am = 0;
	    foreach ($payDatas as $payData) {
	    	$output = $payData->amount + $total_pay_am;
	    	$total_pay_am = $output;
	    }

	    $amount = $field['amount'];
	    $full_amount = $total_pay_am;
	    $agreement_ammount = $field[AGREEMENT_AMOUNT];
		$due_amount = $agreement_ammount - $total_pay_am; 
		
		$node = Node::load($nid);
		$node->set('field_silai_agre_received_amount', $total_pay_am); 
		$node->set('field_silai_agree_due_balance', $due_amount);
		$node->set('field_received_payment_status', '1'); 
		$node->save();
	} 
}



















