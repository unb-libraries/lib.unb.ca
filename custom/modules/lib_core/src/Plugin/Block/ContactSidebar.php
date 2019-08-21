<?php

namespace Drupal\lib_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Additional Options' block for the UNB Libraries Contact sidebar.
 *
 * @Block(
 *  id = "contact_webform_sidebar",
 *  admin_label = @Translation("Contact Webform Additional Options"),
 *   category = @Translation("UNB Libraries Misc"),
 * )
 */
class ContactSidebar extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $html = '
        <dl>
            <dt class="mb-2"><i class="fas fa-comments">&nbsp;</i>Ask Us</dt>
            <dd class="ml-3"><a href="/help/ask-us">Contact our Ask Us service</a> with any research questions or
              service requests.</dd>

            <dt class="mt-4 mb-2"><i class="fas fa-bug">&nbsp;</i>Submit a Trouble Ticket</dt>
            <dd class="ml-3">Please <a href="https://web.lib.unb.ca/help/troubleticket.php">report inaccessible
              e-Resources and eJournals</a> with a <strong>TROUBLE TICKET</strong>.</dd>
        </dl>';

    $render_array = [
      '#type' => 'markup',
      '#markup' => $html,
    ];

    return $render_array;
  }

}
