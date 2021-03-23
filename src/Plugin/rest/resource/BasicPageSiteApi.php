<?php

namespace Drupal\axe_site_api\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Psr\Log\LoggerInterface;
use Drupal\rest\ResourceResponse;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Cache\CacheableResponseInterface;
use Drupal\node\NodeInterface;

/**
 * Class BasicPageSiteApi.
 *
 * @RestResource(
 *   id = "page_site_api",
 *   label = @Translation("Page Site Api"),
 *   uri_paths = {
 *     "canonical" = "/page_json/{api_key}/{nid}"
 *   }
 * )
 */
 class BasicPageSiteApi extends ResourceBase {
  /**
   * ConfigFactoryInterface Manager.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * EntityTypeInterface Manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, array $serializer_formats, LoggerInterface $logger, ConfigFactoryInterface $config_factory, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
    $this->configFactory = $config_factory;
    $this->entityTypeManager = $entity_type_manager;
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
      $container->get('logger.factory')->get('axe_site_api'),
      $container->get('config.factory'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * GET requests.
   *
   * @return node object.
   */
  public function get($api_key, $nid) {
    if (!empty($nid)) {
      $config = $this->configFactory->get('system.site');
      $site_api_key = $config->get('siteapikey');
      // Access denied if api key is empty or not matching.
      if (empty($api_key) || $site_api_key != $api_key) {
        return new ResourceResponse('access denied.', 403);
      }
      // Loading node.
      $node = $this->entityTypeManager->getStorage('node')->load($nid);
      // Check if node is of type page bundle.a2
      if ($node instanceof NodeInterface && $node->getType() == 'page') {
        // Returns node.
        return new ResourceResponse($node);
      }
      return new ResourceResponse('Invalid node.', 400);
    }
    return new ResourceResponse('Node id required.', 400);
  }
}

