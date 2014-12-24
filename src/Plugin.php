<?php
/**
 * Phergie plugin for providing the ability to send and receive IRC messages
 * via middleware (https://github.com/phergie/phergie-irc-plugin-react-bridge)
 *
 * @link https://github.com/phergie/phergie-irc-plugin-react-bridge for the canonical source repository
 * @copyright Copyright (c) 2008-2014 Phergie Development Team (http://phergie.org)
 * @license http://phergie.org/license New BSD License
 * @package Phergie\Irc\Plugin\React\Bridge
 */

namespace Phergie\Irc\Plugin\React\Bridge;

use Evenement\EventEmitterInterface;
use Phergie\Irc\Bot\React\AbstractPlugin;
use Phergie\Irc\Bot\React\EventQueueInterface as Queue;
use Phergie\Irc\Bot\React\PluginInterface;
use Phergie\Irc\Client\React\LoopAwareInterface;
use Phergie\Irc\ConnectionInterface;
use Phergie\Irc\Event\EventInterface as Event;
use Phergie\Irc\Event\CtcpEventInterface;
use React\EventLoop\LoopInterface;

/**
 * Plugin class.
 *
 * @category Phergie
 * @package Phergie\Irc\Plugin\React\Bridge
 */
class Plugin extends AbstractPlugin implements LoopAwareInterface
{
    /**
     * Adapter used to exchange events with middleware
     *
     * @var \Phergie\Irc\Plugin\React\Bridge\AdapterInterface
     */
    protected $adapter;

    /**
     * Mapping of connection masks to connection objects, used to associate
     * events received from middleware with connections
     *
     * @var array
     */
    protected $connections;

    /**
     * Mapping of connection masks to corresponding event queues, used to
     * syndicate new items to channels or users
     *
     * @var array
     */
    protected $queues;

    /**
     * List of events to send to the adapter
     *
     * @var array
     */
    protected $events;

    /**
     * Accepts plugin configuration.
     *
     * Supported keys:
     *
     * adapter - object implementing AdapterInterface used to send and receive
     * IRC events
     *
     * events - array of strings containing names of events received by the
     * server to be sent to the middleware
     *
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        $this->adapter = $this->getAdapter($config);
        $this->events = $this->getEvents($config);
        $this->connections = array();
        $this->queues = array();
    }

    /**
     * Indicates that the plugin monitors events received from the adapter and
     * IRC servers as well as those needed to obtain connection instances to
     * associate with events and corresponding event queues to syndicate events
     * obtained from middleware to IRC servers.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        $events = array();
        foreach ($this->events as $event) {
            $events[$event] = 'sendToMiddleware';
        }
        $events[$this->adapter->getEventName()] = 'sendToServer';
        $events['irc.sent.user'] = 'getEventQueue';
        return $events;
    }

    /**
     * Injects the event loop into the adapter.
     *
     * @param \React\EventLoop\LoopInterface $loop
     */
    public function setLoop(LoopInterface $loop)
    {
        if ($this->adapter instanceof LoopAwareInterface) {
            $this->adapter->setLoop($loop);
        }
    }

    /**
     * Injects the event emitter into the adapter.
     *
     * @param \Evenement\EventEmitterInterface $emitter
     */
    public function setEventEmitter(EventEmitterInterface $emitter)
    {
        parent::setEventEmitter($emitter);
        $this->adapter->setEventEmitter($emitter);
    }

    /**
     * Stores references to each connection and their corresponding event
     * queues when a USER event is received.
     *
     * @param \Phergie\Irc\EventInterface $event
     * @param \Phergie\Irc\Bot\React\EventQueueInterface $queue
     */
    public function getEventQueue(Event $event, Queue $queue)
    {
        $connection = $event->getConnection();
        $mask = $this->getConnectionMask($connection);
        $this->connections[$mask] = $connection;
        $this->queues[$mask] = $queue;
    }

    /**
     * Returns the connection mask for a given connection.
     *
     * @param \Phergie\Irc\ConnectionInterface $connection
     * @return string
     */
    protected function getConnectionMask(ConnectionInterface $connection)
    {
        return sprintf('%s!%s@%s',
            $connection->getNickname(),
            $connection->getUsername(),
            $connection->getServerHostname()
        );
    }

    /**
     * Sends an event received from the middleware to the IRC server.
     *
     * @param \Phergie\Irc\Plugin\React\Bridge\BridgeEvent $bridgeEvent
     */
    public function sendToServer(BridgeEvent $bridgeEvent)
    {
        foreach ($this->getEventConnections($bridgeEvent) as $connection) {
            $this->sendEvent($bridgeEvent, $connection);
        }
    }

    /**
     * Sends an event received from an IRC server to the middleware.
     *
     * @param \Phergie\Irc\Event\EventInterface $event
     * @param \Phergie\Irc\Bot\React\EventQueueInterface $event
     */
    public function sendToMiddleware(Event $event, Queue $queue)
    {
        $this->adapter->sendEvent($event);
    }

    /**
     * Returns the IRC connections to which an event received from middleware
     * should be transmitted.
     *
     * @param \Phergie\Irc\Plugin\React\Bridge\BridgeEvent $bridgeEvent
     * @return \Phergie\Irc\ConnectionInterface[]
     */
    protected function getEventConnections(BridgeEvent $bridgeEvent)
    {
        $connectionMask = $bridgeEvent->getConnectionMask();
        $pattern = '/^' . str_replace('*', '.*', $connectionMask) . '$/';
        $connections = array();
        foreach ($this->connections as $mask => $connection) {
            if (preg_match($pattern, $mask)) {
                $connections[] = $connection;
            }
        }
        if (!$connections) {
            $this->getLogger()->warning(
                'Bridge event connection mask did not match any configured connections',
                array(
                    'event' => $bridgeEvent,
                    'pattern' => $pattern,
                    'connections' => array_keys($this->connections),
                )
            );
        }
        return $connections;
    }

    /**
     * Sends an event received from middleware to an IRC server.
     *
     * @param \Phergie\Irc\Plugin\React\Bridge\BridgeEvent $bridgeEvent
     * @param \Phergie\Irc\ConnectionInterface $connection
     */
    protected function sendEvent(BridgeEvent $bridgeEvent, ConnectionInterface $connection)
    {
        $event = $bridge->getEvent();
        $prefix = $event instanceof CtcpEventInterface ? 'ctcp' : 'irc';
        $method = $prefix . $event->getCommand();
        $mask = $this->getConnectionMask($connection);
        $queue = $this->queues[$mask];
        call_user_func_array(array($queue, $method), $event->getParams());
    }

    /**
     * Extracts the middleware adapter from configuration.
     *
     * @param array $config
     * @throws \DomainException adapter is not set or is not an object
     * implementing AdapterInterface
     * @return \Phergie\Irc\Plugin\React\Bridge\AdapterInterface
     */
    protected function getAdapter(array $config)
    {
        if (!isset($config['adapter'])
            || !$config['adapter'] instanceof AdapterInterface) {
            throw new \DomainException(
                '"adapter" must be an object implementing AdapterInterface'
            );
        }
        return $config['adapter'];
    }

    /**
     * Extracts a list of events to send to the middleware adapter from
     * configuration.
     *
     * @param array $config
     * @throws \DomainException events is set and is not an array of strings
     * @return array
     */
    protected function getEvents(array $config)
    {
        if (isset($config['events'])) {
            if (!(is_array($config['events'])
                && array_filter($config['events'], 'is_string') === $config['events'])) {
                throw new \DomainException(
                    '"events" must be an array of strings'
                );
            }
            return $config['events'];
        }
        return array('irc.received.each');
    }
}
