<?php

/**
* @file providing the service that for master Data.
*
*/

namespace  Drupal\location_master\Services;

class MasterDataServices {
	public function __construct() {
		
	}

	/**
	 * Get Location By Country Id
	 * @params: $country_id as Integer
	 * @return: $locationList as an array
	 */
	public function getLocationByCountryId($countryId = NULL) {
		$query = \Drupal::entityQuery('node')
		    ->condition('type', 'manage_locations')
		    ->condition(STATUS, 1);
		if($countryId) {
			$query->condition('field_country', $countryId);
		} 
		$ids = $query->execute();
		$nodes = \Drupal\node\Entity\Node::loadMultiple($ids);
		foreach($nodes as $node) {
			$locationList[$node->id()] = $node->getTitle();
		}
		return $locationList;		
	}

   /**
    * Get State By Location
    * @params: $locationId as integer
    * @return: $stateList as an array
   */
	public function getStatesByLocationId($locationId = NULL) {
		$query =\Drupal::entityQuery('node')
			->condition('type', 'manage_business_states')
			->condition(STATUS, 1);
		if($locationId) {
			$query->condition('field_location', $locationId);
		}
		$ids = $query->execute();
		$nodes = \Drupal\node\Entity\Node::loadMultiple($ids);
		foreach($nodes as $node) {
			$stateList[$node->id()] = $node->getTitle();
		}
		return $stateList;
	}
			
	/**
	 * Get District By State
	 * @params: $stateId as integer
	 * @return: $districtList as an array
	*/
	public function getDistrictsByStateId($stateId) {
		$ids = \Drupal::entityQuery('node')
		->condition('type', 'manage_districts')
		->condition(STATUS, 1)
		->condition('field_business_state', $stateId)
		->execute();
		$nodes = \Drupal\node\Entity\Node::loadMultiple($ids);
		foreach($nodes as $node) {
			$districtList[$node->id()] = $node->getTitle();
		}
		return $districtList;
	}

	/**
	 * Get Town By District Id
	 * @params: $districtId as integer
	 * @return: $districtList as an array
	*/
	public function getTownsByDistrictId($districtId) {
		$ids = \Drupal::entityQuery('node')
		->condition('type', 'manage_towns')
		->condition(STATUS, 1)
		->condition('field_district', $districtId)
		->execute();

		$nodes = \Drupal\node\Entity\Node::loadMultiple($ids);
		foreach($nodes as $node) {
			$townList[$node->id()] = $node->getTitle();
		}
		return $townList;
	}


	/**
	 * Get District By Location
	 * @params: $stateId as integer
	 * @return: $districtList as an array
	*/
	public function getDistrictsByLocationId($locationIds) {
		if(!is_array($locationIds)){
			$locationIds = [$locationIds];	
		}
		$ids = \Drupal::entityQuery('node')
		->condition('type', 'manage_districts')
		->condition(STATUS, 1)
		->condition('field_location', $locationIds, 'IN')
		->execute();
		$nodes = \Drupal\node\Entity\Node::loadMultiple($ids);
		foreach($nodes as $node) {
			$districtList[$node->id()] = $node->getTitle();
		}
		return $districtList;
	}
}