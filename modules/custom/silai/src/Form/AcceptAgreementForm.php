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
 
class AcceptAgreementForm extends FormBase {


	public function getFormId() {
		return 'silai_custom_accept_agreement_form';
	}

	public function buildForm(array $form, FormStateInterface $form_state) {
		$nid = $form_state->getBuildInfo()['args'][0];
		$destinationData = drupal_get_destination();
		$node_storage = \Drupal::entityTypeManager()->getStorage('node');
		$node = $node_storage->load($nid);
		$Cararr_id = $node->get('field_cararr_id')->value;
		$agreement_amount = $node->get('field_agreement_amount')->value;
		$ngo_nid =  $node->get('field_agreement_ngo_name')->target_id;
		$ngo_node = $node_storage->load($ngo_nid);
		$ngo_name =  $ngo_node->get('title')->value;
		$balance_amount = $node->get('field_silai_agree_due_balance')->value;
		$agreement_id = $node->get('field_agreement_id')->value;
		$form[HASH_PREFIX] = '<div id="wrapper_modal_accept_agreement_form">';
	    $form[HASH_SUFFIX] = '</div>';
	    $form['status_messages'] = [
	      HASH_TYPE => 'status_messages',
	      '#weight' => -10,
	    ];
	    # Agreement Details section table.
	    $form['agreement_detail'] = array(
	        HASH_TYPE => 'details',
	        HASH_TITLE => t('Agreement Detail'),
	        '#open' => TRUE,
	    );
		$form['agreement_detail'][AGREEMENT] = [
		    HASH_TYPE => 'table', 
		    '#header' => [
			    $this->t('Agreement Id'),
			    $this->t('NGO Name'),
			    $this->t('Agreement Amount'),
			    $this->t('Balance Amount'),
		  	],
		];
		$form['agreement_detail'][AGREEMENT][1]['agreement_id'] = [
		    HASH_PLAIN_TEXT => $agreement_id,
		];
		$form['agreement_detail'][AGREEMENT][1]['ngo_name'] = [
		    HASH_PLAIN_TEXT => $ngo_name,
		];
		$form['agreement_detail'][AGREEMENT][1]['agreement_amount'] = [
		    HASH_PLAIN_TEXT => $agreement_amount,
		];
		$form['agreement_detail'][AGREEMENT][1]['balance_amount'] = [
		    HASH_PLAIN_TEXT => $balance_amount,
		];
		# Section for Recived Payment List
		$conn = Database::getConnection();
		$rec_query = $conn->select('silai_ngo_payment_detail', 's')
	            ->condition('nid', $nid)
	            ->condition('payment_status', 1)
	            ->fields('s');
	    $rec_pay_datas = $rec_query->execute()->fetchAll(\PDO::FETCH_OBJ);
		$header_table = array(
	       	'bank_drawn' => t('PO Number'),
	       	'cheque_no' => t('cheque_no'),
		    'amount' => t('Amount'),
		    'payment_mode' => t('Payment Mode'),
		    'invoice_no' => t('Invoice No'),
	    );
	    $rows=array();
	    foreach($rec_pay_datas as $rec_pay_data){
	    	if($rec_pay_data->payment_mode = 1){
		    	$payment_mode = 'Cheque / DD';
		    }else{
		    	$payment_mode = 'NEFT/RTGS';
		    }
            $rows[] = array(
             	'bank_drawn' => $rec_pay_data->bank_drawn,
             	'cheque_no' => $rec_pay_data->cheque_no,
			    'amount' => $rec_pay_data->amount,
			    'payment_mode' => $payment_mode,
			    'invoice_no' => $rec_pay_data->invoice_no,
            );
	    }
	   	$form['rec_table'] = array(
	        HASH_TYPE => 'details',
	        HASH_TITLE => t('Recieved Payment List'),
	        '#open' => TRUE,
	    );
	    $form['rec_table']['payment_data'] = [
            '#type' => 'table',
            '#header' => $header_table,
            '#rows' => $rows,
            '#empty' => t('No Data found'),
        ];
        # Section for Non - Recived Payment List
	    $query = $conn->select('silai_ngo_payment_detail', 's')
	            ->condition('nid', $nid)
	            ->condition('payment_status', 0)
	            ->fields('s');
	    $payDatas = $query->execute()->fetchAll(\PDO::FETCH_OBJ);
		$header = [
		    'bank_drawn' => t('PO Number'),
		    'cheque_no' => t('Cheque No'),
		    'amount' => t('Amount'),
		    'payment_mode' => t('Payment Mode'),
		    'invoice_no' => t('Invoice No'),
		];
		$output = array();
		foreach ($payDatas as $payData) {
			if($payData->payment_mode = 1){
		    	$payment_mode = 'Cheque / DD';
		    }else{
		    	$payment_mode = 'NEFT/RTGS';
		    }
		    $output[$payData->id] = [
		        'bank_drawn' => $payData->bank_drawn,
		        'cheque_no' => $payData->cheque_no,
		        'amount' => $payData->amount,
		        'payment_mode' => $payment_mode,
		        'invoice_no' => $payData->invoice_no,
		    ];
		}
		$form['non_rec_table'] = array(
	        HASH_TYPE => 'details',
	        HASH_TITLE => t('Non-Recieved Payment List'),
	        '#open' => TRUE,
	    );
		$form['non_rec_table']['payment_recive_data'] = [
			'#type' => 'tableselect',
			'#header' => $header,
			'#options' => $output,
			'#empty' => t('No Data found'),
		];
		# Save button   - 
		$form[ACTIONS] = array(HASH_TYPE => ACTIONS);
		$recCount = count($payDatas);
		if($recCount == 0){
			$form[ACTIONS]['cancel'] = array(
		    HASH_TYPE => 'button',
		    HASH_VALUE => t('Cancel'),
		    '#weight' => -1,
		    HASH_ATTRIBUTES => array('onClick' => 'window.location.href = "'.$destinationData['destination'].'"; event.preventDefault();'),
		    );
		}else{
			$form[ACTIONS]['submit'] = array(
				HASH_TYPE => 'submit',
				HASH_VALUE => $this->t('Recieve'),
				'#attributes' => [
			        'class' => [
			          'use-ajax',
			        ],
			    ],
			    '#ajax' => [
			        'callback' => [$this, 'acceptAgreementAjax'],
			        'event' => 'click',
			    ],
			);
		}
		$form['#attached']['library'][] = 'core/drupal.dialog.ajax';
		return $form;
	}
	 /**
	   * AJAX callback handler that displays any errors or a success message.
	   */
	public function acceptAgreementAjax(array $form, FormStateInterface $form_state) {
	    $doamin = _get_current_domain();
	    $response = new AjaxResponse(); 
	    if ($form_state->hasAnyErrors()) {
	        $response->addCommand(new ReplaceCommand('#wrapper_modal_accept_agreement_form', $form));
	        return $response;
	    }
	      else {
	        $command = new CloseModalDialogCommand();
	        $response->addCommand($command);
	        drupal_set_message(t('Payment has been successfully Received.'), STATUS);
	        $response->addCommand(new RedirectCommand('/ngo-agreements-listing'));
	        return $response;
	      } 
	}
	public function validateForm(array &$form, FormStateInterface $form_state) {

    }
    public function submitForm(array &$form, FormStateInterface $form_state) {	
		$nid = $form_state->getBuildInfo()['args'][0];	
		$field = $form_state->getValues();
		$payment_recive_datas = $field['payment_recive_data'];
		$keyArr = array();
		$dataArray = array(
            'payment_status'    => 1,
        );		
		foreach ($payment_recive_datas as $key => $value) {
			if($value != 0){
				$keyArr[] = $key;
			}
		}
	    if(!empty($keyArr)) {
	    	$database = \Drupal::database();
			$query_update = $database->update('silai_ngo_payment_detail')->fields($dataArray)->condition('id', $keyArr, 'IN')->execute();	
	    }

	    $connection = Database::getConnection();
	    $query = $connection->select('silai_ngo_payment_detail', 'v');
	    $query->addExpression('SUM(v.amount)', 'totalAmount');
	    $query->condition('v.payment_mode', 1);
	    $query->condition('v.nid', $nid);
	    $totalPaidAmountArr = $query->execute()->fetchAll(\PDO::FETCH_OBJ);
	    $totalPaidAmount = 0;
	    if($totalPaidAmountArr[0]) {
	    	$totalPaidAmount = $totalPaidAmountArr[0]->totalAmount;
	    }
	    
		#For Received Payment Status 
		$conn = Database::getConnection();
		$rec_query = $conn->select('silai_ngo_payment_detail', 's')
	            ->condition('nid', $nid)
	            ->condition('payment_status', 0)
	            ->fields('s');
	    $rec_pay_datas = $rec_query->execute()->fetchAll(\PDO::FETCH_OBJ);
	    $recStatus = count($rec_pay_datas);
    	$node = Node::load($nid);
    	$totalAgrAmount = $node->get('field_agreement_amount')->value;
    	$balanceAmount = $totalAgrAmount - $totalPaidAmount;	    
	    if($recStatus == '0'){
	  		$node->set('field_received_payment_status', '0');
	  		$node->save();
	    }else{
	  		$node->set('field_received_payment_status', '1');
	    }

  		if($updateDueBalance) {
  			$node->set('field_silai_agree_due_balance', $balanceAmount);
  		}
  		$node->save();	    

	} 
}



















