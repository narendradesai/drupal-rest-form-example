<?php

namespace Drupal\axe_site_api\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class SiteInfoRouteSubscriber
 */
class SiteInfoRouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    // Extending site information form.
    $site_info_route = $collection->get('system.site_information_settings');
    $site_info_route->setDefault('_form', 'Drupal\axe_site_api\Form\SiteInfoForm');
  }
}
