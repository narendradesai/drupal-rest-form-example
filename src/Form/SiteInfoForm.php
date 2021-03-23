<?php

namespace Drupal\axe_site_api\Form;

use Drupal\system\Form\SiteInformationForm;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\path_alias\AliasManagerInterface;
use Drupal\Core\Path\PathValidatorInterface;
use Drupal\Core\Routing\RequestContext;
use Drupal\Core\Messenger\MessengerInterface;

/**
 * Class SiteInfoForm.
 */
class SiteInfoForm extends SiteInformationForm {
  /**
   * Messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * {@inheritdoc}
   */
  public function __construct(ConfigFactoryInterface $config_factory, AliasManagerInterface $alias_manager, PathValidatorInterface $path_validator, RequestContext $request_context, MessengerInterface $messenger) {
    parent::__construct($config_factory, $alias_manager, $path_validator, $request_context);
    $this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('path_alias.manager'),
      $container->get('path.validator'),
      $container->get('router.request_context'),
      $container->get('messenger')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    $config = $this->config('system.site');

    // Updating submit label.
    $form['actions']['submit']['#value'] = $this->t('Update Configuration');
    // Adding siteapikey field to form.
    $form['site_information']['siteapikey'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Site API Key'),
      '#default_value' => $config->get('siteapikey') ? $config->get('siteapikey') : "No API Key yet",
      '#description' => $this->t("Enter Site API Key."),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('system.site');
    // Saving value of siteapikey and showing message.
    $config->set('siteapikey', $form_state->getValue('siteapikey'))->save();
    if (!empty($form_state->getValue('siteapikey')) && $form_state->getValue('siteapikey') != "No API Key yet") {
      $this->messenger->addMessage($this->t('Site API Key saved with key: ') . $config->get('siteapikey') , $this->messenger::TYPE_STATUS);
    }
    parent::submitForm($form, $form_state);
  }
}
