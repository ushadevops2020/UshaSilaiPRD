<?php

namespace Drupal\custom_api\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Psr\Log\LoggerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Access;
/**
 * Provides a Custom Resource
 *
 * @RestResource(
 *   id = "custom_resource",
 *   label = @Translation("Custom Resource"),
 *   uri_paths = {
 *     "canonical" = "/custom_api/custom_resource"
 *   }
 * )
 */

class CustomResource extends ResourceBase {
	/**
	 *
	 * @var \Drupal\Core\Session\AccountProxyInterface
	 */
	protected $currentUser;
	/**
	 *
	 * @var \Symfony\Component\HttpFoundation\Request
	 */
	protected $currentRequest;

	/**
	 *
	 * @var Psr\Log\LoggerInterface
	 */
	protected $logger;

	/**
	   * Constructs a Drupal\rest\Plugin\ResourceBase object.
	   *
	   * @param array $configuration
	   *   A configuration array containing information about the plugin instance.
	   * @param string $plugin_id
	   *   The plugin_id for the plugin instance.
	   * @param mixed $plugin_definition
	   *   The plugin implementation definition.
	   * @param array $serializer_formats
	   *   The available serialization formats.
	   * @param \Psr\Log\LoggerInterface $logger
	   *   A logger instance.
	   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
	   *   The current user instance.
	   * @param Symfony\Component\HttpFoundation\Request $current_request
	   *   The current request
	   */
	  public function __construct(array $configuration, $plugin_id, $plugin_definition, array $serializer_formats, LoggerInterface $logger, AccountProxyInterface $current_user, Request $current_request) {
	    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
	    $this->currentUser = $current_user;
	    $this->currentRequest = $current_request;
	  }

  	/**
	   * {@inheritdoc}
	   */
  	public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
	    return new static(
	      $configuration,
	      $plugin_id,
	      $plugin_definition,
	      $container->getParameter('serializer.formats'),
	      $container->get('logger.factory')->get('rest'),
	      $container->get('current_user'),
	      $container->get('request_stack')->getCurrentRequest()
	    );
	  }
	/**
     * Responds to entity GET requests.
     * @return \Drupal\rest\ResourceResponse
     */
	public function get() {
		// $current_uri = \Drupal::request()->getRequestUri();
		// $current_path = \Drupal::service('path.current')->getPath()
		// You must to implement the logic of your REST Resource here.
	    // Use current user after pass authentication to validate access.
	    if (!$this->currentUser->hasPermission('access content')) {
	      throw new AccessDeniedHttpException();
	    }
	    $token = \Drupal::csrfToken()->get('http://usha-sewing.tk');
	    print_r($token);die;
	    // loading the user. now I can see all the user's roles       
	    $user = \Drupal\user\Entity\User::load($this->currentUser->id());
	    $csrf_token = $this->currentRequest->headers->get('X-CSRF-Token'); 
	    if(empty($csrf_token)) {
	    	$errorMessage['isSuccess'] = 0;
			$errorMessage['responseCode'] = 0;
	    	$errorMessage['message'] = 'X-CSRF-Token request header is missing';
	    	return new ResourceResponse($errorMessage);
	    }
	    // this shows the correct user account name       
	    //$this->currentUser->getAccountName());
	    $queryUId = $this->currentRequest->query->get('uid');
	    // the only role that appears is authenticated, although user has another role
	    // $user_roles = $this->currentUser->getRoles();
	    if(empty($queryUId)) {
	    	$errorMessage['isSuccess'] = 0;
			$errorMessage['responseCode'] = 0;
	    	$errorMessage['message'] = 'UseId request header is missing.';
	    	return new ResourceResponse($errorMessage);
	    } else {
	    	$account = \Drupal\user\Entity\User::load($queryUId);
			if ( empty($account)) {
			 	$errorMessage['isSuccess'] = 0;
				$errorMessage['responseCode'] = 0;
		    	$errorMessage['message'] = 'User is not exists.';
		    	return new ResourceResponse($errorMessage);
			}
	    }
		if (in_array('sewing_school_teacher',$user->getRoles())) {
			$masterDataService = \Drupal::service('silai.master_data');
			$inventoryArrayList =  $inventoryArray = array();
			$userId = $this->currentRequest->query->get('uid');
			$schoolId = $masterDataService->getSchoolFromUserId($userId);
			$inventoryArrayList['isSuccess'] = true;
			$inventoryArrayList['responseCode'] = 1;
			if(!empty($schoolId)) {
				$inventoryArrayList['message'] = 'Inventory Recived Succesfully';
				$inventoryList = $masterDataService->getInventoryListBySchoolId($schoolId);
				foreach ($inventoryList as $key => $inventory) {
				    $inventoryId = $inventory->nid;
				    $nodes = \Drupal\node\Entity\Node::load($inventoryId);
				    $itemId = $nodes->field_silai_item_name->target_id;
				    $itemLoad = \Drupal\node\Entity\Node::load($itemId);
				    $Itemname = $itemLoad->getTitle();
				    $inventoryArray['inventoryId'] = $inventory->nid;
				    $inventoryArray['inventoryName'] = $Itemname;
				    $inventoryArray['sendQty'] = $inventory->qty_send;
				    $inventoryArray['receivedQty'] = $inventory->qty_received;
				    $inventoryArray['sendDate'] = date("Y-m-d", $inventory->sent_date);
				    $inventoryArray['receivedDate'] = '';
				    if($inventory->received_date) {
				      $inventoryArray['receivedDate'] = date("Y-m-d", $inventory->received_date);
				    }
				    $inventoryArrayList['result'][] = $inventoryArray;
				}        
			} else {
				$inventoryArrayList['message'] = 'No Records Found';
			}
			return new ResourceResponse($inventoryArrayList);
		} else {
			print_r($this->currentUser);die;
		}	
	}

	/**
   * Responds to POST requests.
   *
   * Returns a list of bundles for specified entity.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Throws exception expected.
   */
	public function post() {
		// You must to implement the logic of your REST Resource here.
		// Use current user after pass authentication to validate access.
		/*
		if(!$this->currentUser->hasPermission($permission)) {
		    throw new AccessDeniedHttpException();
		}
		*/
		// Throw an exception if it is required.
		// throw new HttpException(t('Throw an exception if it is required.'));
		return new ResourceResponse("Implement REST State POST!");
	}
}