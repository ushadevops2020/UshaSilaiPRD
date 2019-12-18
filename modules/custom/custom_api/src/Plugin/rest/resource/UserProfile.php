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
 * Provides a User Profile Resource
 *
 * @RestResource(
 *   id = "user_profile",
 *   label = @Translation("User Profile Resource"),
 *   uri_paths = {
 *     "canonical" = "/api/user_profile"
 *   }
 * )
 */

class UserProfile extends ResourceBase {
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
		$user = \Drupal::currentUser();
	    $id = $user->id();
	    $decoded = array();
        $decoded['isSuccess'] = true;
        $decoded['message'] = 'User Profile Details';
        $decoded['responseCode'] = 1;
	    $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
	    $masterDataService = \Drupal::service('silai.master_data');
		$schoolId = $masterDataService->getSchoolFromUserId(\Drupal::currentUser()->id());
		$nodes = \Drupal\node\Entity\Node::load($schoolId);
        $decoded['result'] = [
          "uid" => \Drupal::currentUser()->id(),
          "name" => $user->getUsername(),
          "mail" => $user->getEmail(),
          "firstName" => $user->field_first_name->value,
          "lastName" => $user->field_last_name->value,
          "mobile" =>  $user->field_user_contact_no->value,
          "location" =>  $user->field_user_location->target_id,
          "school_code" => $nodes->field_school_code->value,
          "date_of_joining" => $nodes->field_date_open_of_silai_school->value,
          "total_learners" => 24,
          "currently_active" => 20,
          "aadhar_card" => "xxxxxxxxxxxxxxxxxxxxxxx"
        ];
        
		return new ResourceResponse($decoded);
	}

	/**
   * Responds to POST requests.
   *
   * Returns a list of bundles for specified entity.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Throws exception expected.
   */
	public function post(Request $request) {
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