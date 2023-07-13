<?php

namespace Drupal\public_trouble_tickets\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;

/**
 * Submit a trouble ticket.
 */
class NewTicketForm extends FormBase {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'new_trouble_ticket';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $query = $this->getRequest()->query;
    $fogbugz = \Drupal::service('fogbugz_api.manager');
    $alerts = $fogbugz->getActiveAlerts();

    $blockManager = \Drupal::service('plugin.manager.block');
    $askUsBlock = $blockManager->createInstance('askus_popup', []);
    $access = $askUsBlock->access(\Drupal::currentUser());
    if (is_object($access) && $access->isForbidden() || is_bool($access) && !$access) {
      $askUs = '';
    }
    else {
      $renderArray = $askUsBlock->build();
      $askUs = \Drupal::service('renderer')->render($renderArray);
    }

    $form['#prefix'] = '<div class="row"><div class="col-8">';
    $form['#suffix'] = '</div><div class="col-4">
  <h2>Systems and Services Status</h2>
  <p><a href="/help/status" class="btn btn-danger"><i class="fas fa-list-ul"></i> View Services Status</a></p>
  <p><a href="/help/tickets" class="btn btn-info"><i class="fas fa-list-ul"></i> View Current Trouble Tickets</a></p>
  <h2>Further Assistance</h2>
  <p>If you require further assistance, need alternative or immediate access to the information or there has been considerable delay in correcting the problem, please <b>Ask Us</b> for assistance.</p>'
    . $askUs . '</div></div>';

    $form['alerts'] = [
      '#theme' => 'alerts',
      '#alerts' => $alerts,
    ];

    $form['info'] = [
      '#markup' => '<h2>Important:</h2>
        <ul>
          <li>This service is designed for technical support issues <strong>ONLY</strong>. For all other matters, please <a href="/help/ask-us">Ask Us</a>.</li>
          <li>Check out our <a href="/help/off-campus-access">Off-campus Proxy Access</a> page, if you are connecting from off-campus.</li>
          <li>View <a href="/help/tickets">current e-Resources Tickets</a> and <a href="/help/status">Systems and Services Status</a>.</li>
        </ul>',
    ];

    $form['wrapper'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'mt-4',
          'theme-dark',
        ],
      ],
    ];

    $form['wrapper']['personal'] = [
      '#type' => 'fieldgroup',
      '#title' => $this->t('Personal Information'),
    ];
    $form['wrapper']['personal']['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#required' => TRUE,
      '#weight' => 0,
      '#default_value' => $query->get('name') ?? NULL,
    ];
    $form['wrapper']['personal']['email'] = [
      '#type' => 'webform_email_multiple',
      '#title' => $this->t('UNB/STU Email'),
      '#required' => TRUE,
      '#cardinality' => 3,
      '#weight' => 0,
      '#default_value' => $query->get('email') ?? NULL,
    ];

    $form['wrapper']['eresource'] = [
      '#type' => 'fieldgroup',
      '#title' => $this->t('e-Resource Information'),
    ];
    $form['wrapper']['eresource']['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title of e-Resource'),
      '#required' => TRUE,
      '#weight' => 0,
      '#default_value' => $query->get('title') ?? NULL,
    ];
    $form['wrapper']['eresource']['nature'] = [
      '#type' => 'select',
      '#title' => $this->t('Nature of the Problem'),
      '#empty_option' => $this->t('Please select one...'),
      '#options' => [
        'Broken link' => $this->t('Broken link (eResources, Resolver, UNBWorldCat, etc.)'),
        'Access denied' => $this->t('Access denied (Fulltext inaccessible, ID prompt, Payment requested)'),
        'Holding problem' => $this->t('Holdings problem (incorrect Coverage, no access, Access but NOT listed)'),
        'Reserves problem' => $this->t('Reserves problem'),
        'Worldcat' => $this->t('UNB WorldCat (library catalogue)'),
        'Proxy' => $this->t('Proxy/Authentication Problems'),
        'Other' => $this->t('Other (describe below)'),
      ],
      '#required' => TRUE,
      '#weight' => 0,
      '#default_value' => $query->get('nature') ?? NULL,
    ];

    $form['wrapper']['eresource']['details'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Details'),
      '#resizeable' => TRUE,
      '#rows' => 7,
      '#required' => TRUE,
      '#attributes' => [
        'class' => [
          'form-text',
        ],
        'placeholder' => 'A maximum of two URLs may be specified here.

Provide as much information as possible. Please DO NOT include any personal information in this field.',
      ],
    ];

    $form['wrapper']['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit Ticket'),
    ];

    $form['#cache']['contexts'] = ['url.query_args'];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $fogbugz = \Drupal::service('fogbugz_api.manager');

    $ip = empty($_SERVER["HTTP_X_REAL_IP"]) ? \Drupal::request()->getClientIp() : $_SERVER["HTTP_X_REAL_IP"];
    $ip = substr($ip, 0, -2) . 'xx';

    $params = [
      'customerEmail' => $form_state->getValue('email'),
      'title' => $form_state->getValue('title'),
      'event' => $form_state->getValue('details'),
      'plugin_customfields_at_fogcreek_com_natureg119' => $form_state->getValue('nature'),
      'plugin_customfields_at_fogcreek_com_patronxipm81a' => $ip,
      'tags' => 'trouble_ticket_eresources',
      'mailbox' => 1,
    ];

    switch ($form_state->getValue('nature')) {
      case 'Reserves problem':
        $params['project'] = 'Reserves';
        break;

      case 'Worldcat':
        $params['project'] = 'UNB WorldCat / WMS';
        break;

      case 'Proxy':
        $params['project'] = 'Proxy / Authentication Issues';
        break;

      default:
        $params['project'] = 'eResources Trouble Tickets';
        break;

    }

    if (!empty($params['project']) && $params['project'] == 'UNB WorldCat / WMS') {
      $params['personAssignedTo'] = 'Scott Shannon';
    }

    $case = $fogbugz->createCase($params);
    if ($case === FALSE) {
      $this->messenger()->addError($this->t('There was an error creating your trouble ticket, please try again later.'));
      $form_state->setRebuild();
      return;
    }

    $id = $case->getCaseId();
    $ticketUrl = Url::fromRoute('public_trouble_tickets.ticket_view', ['id' => $id], ['absolute' => TRUE]);

    // Send confirmation email as ticket forward.
    $message = $form_state->getValue('name') . ",\n\n";
    $message .= "Your UNB Libraries trouble ticket for " . $form_state->getValue('title') . " has been successfully submitted to our eResources staff.\n\n";
    $message .= "Depending on the nature of your reported trouble, please note:\n
      -You may be contacted by staff with questions or suggestions to help solve your issue.\n
      -Problems are triaged which may result in a speedy resolution to your trouble ticket, or a significant delay.\n
      -If needed, please ASK US about research assistance or possible alternatives to existing problems: https://lib.unb.ca/help/ask-us\n\n";
    $message .= "This email confirms UNB eResources staff are addressing your concern as readily as we can. Further updates will be sent when available.\n\n";
    $message .= "Your trouble ticket status can be tracked here:\n";
    $message .= $ticketUrl->toString() . "\n";

    $params = [
      'bug' => $id,
      'from' => $fogbugz->getConfig()->get('fogbugz_email'),
      'to' => $form_state->getValue('email'),
      'subject' => 'Your trouble ticket has been submitted (Case ' . $id . ')',
      'event' => $message,
    ];
    $fogbugz->addForwardEvent($params);

    $this->messenger()->addStatus(
      $this->t("Successfully submitted your trouble ticket (reference #: @caseId).", [
        '@caseId' => $id,
      ])
    );
    $form_state->setRedirectUrl($ticketUrl);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $emails = preg_split('/\\s*,\\s*/', $form_state->getValue('email'));
    foreach ($emails as $email) {
      if (!preg_match('/\@(unb|stu)\.ca$/', $email)) {
        $form_state->setErrorByName('email', $this->t('Email must be a UNB or STU address.'));
        break;
      }
    }

    if (substr_count($form_state->getValue('details'), '://') > 2) {
      $form_state->setErrorByName('details', $this->t('Maximum two links per message.'));
    }

    return parent::validateForm($form, $form_state);
  }

}
