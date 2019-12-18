<?php
namespace Drupal\test_resource\Routing;


use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;


/**
* Route subscriber class.
*/

class RouteSubscriber extends RouteSubscriberBase {


	/**
	 * {@inheritdoc}
	 *
	 * Add a CSRF-Token  as requirement.
	 */
	public function alterRoutes(RouteCollection $collection) {

	  if ($route = $collection->get('tal_webservice.ingredient')) {
	    $route->setRequirement('_access_rest_csrf', 'TRUE');
	  }

	}


}
?>