<?php
/**
 * Phergie plugin for providing the ability to send and receive IRC messages
 * via middleware (https://github.com/phergie/phergie-irc-plugin-react-bridge)
 *
 * @link https://github.com/phergie/phergie-irc-plugin-react-bridge for the canonical source repository
 * @copyright Copyright (c) 2008-2014 Phergie Development Team (http://phergie.org)
 * @license http://phergie.org/license Simplified BSD License
 * @package Phergie\Irc\Plugin\React\Bridge
 */

namespace Phergie\Irc\Tests\Plugin\React\Bridge;

use Phake;
use Phergie\Irc\Bot\React\EventQueueInterface as Queue;
use Phergie\Irc\Event\EventInterface as Event;
use Phergie\Irc\Plugin\React\Bridge\Plugin;

/**
 * Tests for the Plugin class.
 *
 * @category Phergie
 * @package Phergie\Irc\Plugin\React\Bridge
 */
class PluginTest extends \PHPUnit_Framework_TestCase
{


    /**
     * Tests that getSubscribedEvents() returns an array.
     */
    public function testGetSubscribedEvents()
    {
        $plugin = new Plugin;
        $this->assertInternalType('array', $plugin->getSubscribedEvents());
    }
}
