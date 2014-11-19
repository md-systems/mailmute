<?php
/**
 * @file
 * Contains \Drupal\mailmute\Tests\MailmuteWebTest.
 */

namespace Drupal\mailmute\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Test the Mailmute UI.
 *
 * @group mailmute
 */
class MailmuteWebTest extends WebTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('mailmute', 'mailmute_test', 'field_ui');

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

  }

  /**
   * Test the Mailmute UI.
   */
  public function testField() {
    // Log in admin.
    $admin = $this->drupalCreateUser(array('administer send state'));
    $this->drupalLogin($admin);

    // Check the edit form.
    $this->drupalGet('user/' . $admin->id() . '/edit');
    $this->assertField('field_sendstate[plugin_id]', 'Send state field found on user form');
    $this->assertOption('field_sendstate[plugin_id]', 'onhold', NULL, '"On hold" option found on user form');
    $this->assertOption('field_sendstate[plugin_id]', 'send', NULL, '"Send" option found on user form');
    $this->assertOption('field_sendstate[plugin_id]', 'send', TRUE, '"Send" option selected by default');

    $edit = array(
      'field_sendstate[plugin_id]' => 'onhold',
    );
    $this->drupalPostForm(NULL, $edit, 'Save');
    $this->assertOption('field_sendstate[plugin_id]', 'onhold', TRUE, '"On hold" option selection saved');

    // Check the user view.
    $this->drupalGet('user');
    $this->assertText('Send emails');
    $this->assertText('On hold');
  }

  /**
   * Test that some states require admin permission.
   */
  public function testAdminStates() {
    // Log in admin user.
    $admin = $this->drupalCreateUser(array('administer send state'));
    $this->drupalLogin($admin);

    // Check that the admin state is selectable.
    $this->drupalGet('user/' . $admin->id() . '/edit');
    $xpath = "//select[@name='field_sendstate[plugin_id]']//option[@value='admin_state']";
    $this->assertFieldByXPath($xpath);

    // Log in non-admin user.
    $this->drupalLogout();
    $user = $this->drupalCreateUser(array('change own send state'));
    $this->drupalLogin($user);

    // Check that the admin state is not selectable.
    $this->drupalGet('user/' . $user->id() . '/edit');
    $this->assertNoFieldByXPath($xpath);
  }

  /**
   * Test the send state overview page.
   */
  public function testStatesOverview() {
    $admin = $this->drupalCreateUser(array('administer send state'));
    $this->drupalLogin($admin);
    $this->drupalGet('admin/config/people/mailmute/sendstates');

    $this->assertText('On hold');
    $this->assertText('The address owner requested muting until further notice.');

    $this->assertText('Send');
    $this->assertText('Messages are not suppressed. This is the default state.');

    // The test state has Send as parent, and should be indented.
    $this->assertRaw('<div class="indentation">&nbsp;</div>Admin state');
  }

  /**
   * Asserts that an option element exists within a select element.
   *
   * @param string $select_field
   *   The name of the expected select field.
   * @param string $option_key
   *   The value of the expected option element.
   * @param bool|null $selected
   *   (optional) To assert that the option is selected, set to TRUE. To assert
   *   not selected, set to FALSE. Omit to not assert on the selected state.
   * @param string $message
   *   (optional) A message to display with the assertion.
   * @param string $group
   *   (optional) The group this message is in.
   *
   * @return bool
   *   TRUE if the assertion passed, FALSE if it failed.
   */
  protected function assertOption($select_field, $option_key, $selected = NULL, $message = '', $group = 'Other') {
    $match_selected = isset($selected) ? ($selected ? ' and @selected' : ' and not(@selected)') : '';
    $xpath = "//select[@name='$select_field']//option[@value='$option_key'$match_selected]";
    $this->assertFieldByXPath($xpath, NULL, $message, $group);
  }

}
