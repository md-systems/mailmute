<?php
/**
 * @file
 * Contains \Drupal\Tests\mailmute\Unit\SendStateManagerTest.
 */

namespace Drupal\Tests\mailmute\Unit;

use Drupal\Tests\UnitTestCase;

/**
 * Tests methods of the send state manager.
 *
 * @group mailmute
 * @coversDefaultClass \Drupal\mailmute\SendStateManager
 */
class SendStateManagerTest extends UnitTestCase {

  /**
   * Tests that the hierarchy is generated correctly.
   *
   * @covers ::getPluginIdHierarchy
   */
  function testGetPluginIdHierarchy() {
    // A few definition-like structures for input. Mostly follows
    // Mailmute/Inmail design but partly fake. Critical aspects are the third
    // level (child of a child) and the arbitrary order.
    $definitions = array(
      'admin_test' => ['id' => 'admin_test', 'parent_id' => 'send'],
      'really_persistent_send' => ['id' => 'really_persistent_send', 'parent_id' => 'persistent_send'],
      'temporarily_unreachable' => ['id' => 'temporarily_unreachable', 'parent_id' => 'onhold'],
      'invalid_address' => ['id' => 'invalid_address', 'parent_id' => 'onhold'],
      'send' => ['id' => 'send'],
      'persistent_sent' => ['id' => 'persistent_send', 'parent_id' => 'send'],
      'onhold' => ['id' => 'onhold'],
    );

    // Expected result.
    $hierarchy = array(
      'send' => array(
        'persistent_send' => array(
          'really_persistent_send' => array(),
        ),
        'admin_test' => array(),
      ),
      'onhold' => array(
        'invalid_address' => array(),
        'temporarily_unreachable' => array(),
      ),
    );

    /** @var \Drupal\mailmute\SendStateManager|\PHPUnit_Framework_MockObject_MockObject $manager */
    $manager = $this->getMockBuilder('Drupal\mailmute\SendStateManager')
      ->disableOriginalConstructor()
      ->setMethods(array('getDefinitions'))
      ->getMock();
    $manager->expects($this->once())
      ->method('getDefinitions')
      ->willReturn($definitions);

    $this->assertEquals($hierarchy, $manager->getPluginIdHierarchy());
  }

}
