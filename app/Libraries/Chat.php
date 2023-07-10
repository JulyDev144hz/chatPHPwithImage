<?php

namespace App\Libraries;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface
{
    protected $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);

        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $numRecv = count($this->clients) - 1;
        echo sprintf(
            'Connection %d sending message "%s" to %d other connection%s' . "\n",
            $from->resourceId,
            $msg,
            $numRecv,
            $numRecv == 1 ? '' : 's'
        );


        $msgDecode = json_decode($msg);
        if ($msgDecode[0] == 'newMessage') {

            foreach ($this->clients as $client) {
                if ($from !== $client) {
                    // The sender is not the receiver, send to each client connected
                    $client->send($msg);
                }
            }
        } else if ($msgDecode[0] == 'newMessageWithImage') {
            $data = $msgDecode[1];
            $username = $data[0];
            $imageText = $data[1];
            $typeFile = $data[2];
            $mensaje = $data[3];

            // passamos el texto de la  imagen a base64
            $base64Data = substr($imageText, strpos($imageText, ',') + 1);
            $imageBinaryData = base64_decode($base64Data);
            // creamos nombre random para la imagen
            $filename = "public/uploads/" . uniqid('image_') . '.' . $typeFile;

            file_put_contents($filename, $imageBinaryData);
            foreach ($this->clients as $client) {
                if ($from !== $client) {
                    // The sender is not the receiver, send to each client connected
                    $client->send(json_encode(['newMessageWithImage', [$username, base_url($filename), $mensaje]]));
                }
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}
