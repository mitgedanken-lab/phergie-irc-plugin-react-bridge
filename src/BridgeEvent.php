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

use Phergie\Irc\Event\EventInterface;

/**
 * BridgeEvent class.
 *
 * @category Phergie
 * @package Phergie\Irc\Plugin\React\Bridge
 */
class BridgeEvent
{
    /**
     * Mask used by Plugin to determine which connections should receive the
     * contained event
     *
     * @var string
     */
    protected $connectionMask;

    /**
     * Object representing the event to be transmitted
     *
     * @var \Phergie\Irc\Event\EventInterface
     */
    protected $event;

    /**
     * Sets the connection mask for this event.
     *
     * * wildcards are supported for all segments of the connection mask up to
     * and including the entire connection mask.
     *
     * @param string $connectionMask
     */
    public function setConnectionMask($connectionMask)
    {
        $this->connectionMask = $connectionMask;
    }

    /**
     * Returns the connection mask for this event.
     *
     * @return string
     */
    public function getConnectionMask()
    {
        return $this->connectionMask;
    }

    /**
     * Sets the event to be transmitted.
     *
     * @param \Phergie\Irc\Event\EventInterface $event
     */
    public function setEvent(EventInterface $event)
    {
        $this->event = $event;
    }

    /**
     * Returns the event to be transmitted.
     *
     * @return \Phergie\Irc\Event\EventInterface
     */
    public function getEvent()
    {
        return $this->event;
    }
}
