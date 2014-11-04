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
  public static $modules = array('mailmute', 'field_ui');

  /**
   * A test user with admin privileges.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $adminUser;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->adminUser = $this->drupalCreateUser(array(
      'administer user fields',
      'administer user display',
      'administer user form display',
    ));
  }

  /**
   * Test the Mailmute UI.
   */
  public function testField() {
    // Log in admin.
    $this->drupalLogin($this->adminUser);

    // Enable the form and view display components.
    $edit = array(
      'fields[field_sendstate][type]' => 'sendstate',
    );
    $this->drupalPostForm('admin/config/people/accounts/form-display', $edit, 'Save');
    $this->drupalPostForm('admin/config/people/accounts/display', $edit, 'Save');

    // Check the edit form.
    $this->drupalGet('user/' . $this->adminUser->id() . '/edit');
    $this->assertField('field_sendstate', 'Send state field found on user form');
    $this->assertOption('field_sendstate', 'onhold', NULL, '"On hold" option found on user form');
    $this->assertOption('field_sendstate', 'send', NULL, '"Send" option found on user form');
    $this->assertOption('field_sendstate', 'send', TRUE, '"Send" option selected by default');

    $edit = array(
      'field_sendstate' => 'onhold',
    );
    $this->drupalPostForm(NULL, $edit, 'Save');
    $this->assertOption('field_sendstate', 'onhold', TRUE, '"On hold" option selection saved');

    // Check the user view.
    $this->drupalGet('user');
    $this->assertText('Send emails');
    $this->assertText('On hold');
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
