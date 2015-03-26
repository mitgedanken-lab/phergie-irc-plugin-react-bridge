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

namespace Phergie\Irc\Plugin\React\Bridge;

use Phergie\Irc\Bot\React\EventEmitterAwareInterface;
use Phergie\Irc\Bot\React\PluginInterface;
use Phergie\Irc\Event\EventInterface;

/**
 * AdapterInterface interface.
 *
 * Classes that implement this interface interact with external middleware to
 * receive messages from the middleware for the bot to send and to send
 * messages that the bot receives to the middleware.
 *
 * Events received for the bot to send can hypothetically be acquired
 * via pull or push mechanisms. As such, adapter classes must be able to
 * asynchronously receive and pass events to the Plugin class. They do this
 * by implementing EventEmitterAwareInterface to receive an event emitter and
 * using it to emit an event (the name of which is returned by the adapter's
 * getEventName() method) with a single parameter, an instance of
 * \Phergie\Irc\Plugin\React\Bridge\BridgeEvent. This class is a container for
 * two values: an object implementing \Phergie\Irc\Event\EventInterface that
 * represents the event to be sent and a connection mask representing one or
 * more connections to which the event will be sent. The Plugin class listens
 * for this event and executes an equivalent IRC command for the event object.
 *
 * Adapter classes must also implement the sendEvent() method included in this
 * interface in order to enable the Plugin class to send events it receives to
 * the middleware.
 *
 * Optionally, adapter classes may also implement
 * \Phergie\Irc\Client\React\LoopAwareInterface to gain access to the event
 * loop used by the bot (e.g. for setting up synchronous polling).
 *
 * @category Phergie
 * @package Phergie\Irc\Plugin\React\Bridge
 */
interface AdapterInterface extends EventEmitterAwareInterface
{
    /**
     * Sends an event received by the bot to the middleware associated with the
     * adapter.
     *
     * @param \Phergie\Irc\Event\EventInterface
     */
    public function sendEvent(EventInterface $event);

    /**
     * Returns the name of the event that the adapter emits when it receives an
     * event from the middleware.
     *
     * @return string
     */
    public function getEventName();
}
