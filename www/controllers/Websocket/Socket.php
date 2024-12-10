<?php

namespace Controllers\Websocket;

/**
 *  Composer autoload
 */
require ROOT . '/libs/vendor/autoload.php';

use Exception;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

/**
 *  Class Socker extends WebsocketServer to gain access to its methods
 */
class Socket extends WebsocketServer implements MessageComponentInterface
{
    protected $clients;

    /**
     *  Initialize socket
     *  Basically like a constructor but to avoid conflicts with the parent constructor
     */
    public function initialize()
    {
        /**
         *  Initialize clients storage
         */
        $this->clients = new \SplObjectStorage;

        /**
         *  Clean database from old connections
         *  (e.g. connections that were not removed from database because of a crash or a bug)
         */
        try {
            $this->cleanWsConnections();
        } catch (Exception $e) {
            $this->log('Error while cleaning database from old connections: ' . $e->getMessage());
        }
    }

    /**
     *  Return all websocket clients
     */
    public function getClients()
    {
        return $this->clients;
    }

    /**
     *  On websocket connection open
     */
    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        $this->log('[connection #' . $conn->resourceId . '] New connection!');

        /**
         *  Adding connection Id to database
         */
        try {
            $this->newWsConnection($conn->resourceId);
        } catch (Exception $e) {
            $this->log('[connection #' . $conn->resourceId . '] Error while adding connection to database: ' . $e->getMessage());

            /**
             *  Send a message to the client to inform that the connection is not allowed, and close it
             *  TODO: Add an error Id to the message
             */
            $conn->send(json_encode(array('error' => "You've been connected but an error occured on the server side. Please try again later.")));
            $conn->close();
        }
    }

    /**
     *  On websocket message received
     */
    public function onMessage(ConnectionInterface $conn, $message)
    {
        /**
         *  Decode JSON message
         */
        try {
            $message = json_decode($message, true);
        } catch (Exception $e) {
            $this->log('[connection #' . $conn->resourceId . '] Error while decoding message: ' . $e->getMessage());
            return;
        }

        /**
         *  If the client is sending its connection type
         */
        if (!empty($message['connection-type'])) {
            // Connection type must be either 'browser-client' or 'android-client'
            if (!in_array($message['connection-type'], array('browser-client', 'android-client'))) {
                $this->log('[connection #' . $conn->resourceId . '] Invalid connection type: ' . $message['connection-type']);

                // Close connection
                $conn->close();
            }

            // Set connection type in database
            $this->setWsConnectionType($conn->resourceId, $message['connection-type']);
        }
    }

    /**
     *  On websocket connection close
     */
    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        $this->log('[connection #' . $conn->resourceId . '] Connection closed');

        /**
         *  Removing connection Id from database
         */
        try {
            $this->deleteWsConnection($conn->resourceId);
        } catch (Exception $e) {
            $this->log('[connection #' . $conn->resourceId . '] Error while removing connection from database: ' . $e->getMessage());
        }
    }

    /**
     *  On websocket connection error
     */
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $this->log('[connection #' . $conn->resourceId . '] An error occured with connection: ' . $e->getMessage());
        $conn->close();
    }
}
