<?php

namespace Controllers\Api\Event;

use Exception;
use Datetime;

class Event
{
    private $motionEventController;
    private $method;
    private $component;
    private $action;
    private $data;

    public function __construct(string $method, array $uri, object $data)
    {
        $this->motionEventController = new \Controllers\Motion\Event();
        $this->method = $method;

        /**
         *  Retrive component and action from URI
         */
        if (isset($uri[4])) {
            $this->component = $uri[4];
        }
        if (isset($uri[5])) {
            $this->action = $uri[5];
        }

        $this->data = $data;
    }

    public function execute()
    {
        /**
         *  If a component is specified
         *  http://127.0.0.1/api/v2/event/
         */
        if (!empty($this->component)) {
            /**
             *  Register a new event
             *  http://127.0.0.1/api/v2/event/new
             */
            if ($this->component == 'new' and $this->method == 'POST') {
                /**
                 *  Check if all required data are present
                 */
                if (empty($this->data->id) or empty($this->data->id_short) or empty($this->data->cameraId)) {
                    throw new Exception('Missing event Id or event short Id or camera Id.');
                }

                /**
                 *  Try registering the event
                 */
                $this->motionEventController->new($this->data->id, $this->data->id_short, $this->data->cameraId);

                /**
                 *  If register is successful
                 */
                return array('message' => array('Event successfully registered.'), 'results' => '');
            }

            /**
             *  End an event
             *  http://127.0.0.1/api/v2/event/end
             */
            if ($this->component == 'end' and $this->method == 'PUT') {
                /**
                 *  Check if all required data are present
                 */
                if (empty($this->data->id)) {
                    throw new Exception('Missing event Id.');
                }

                /**
                 *  Try registering the event
                 */
                $this->motionEventController->end($this->data->id);

                /**
                 *  If register is successful
                 */
                return array('message' => array('Event successfully ended.'), 'results' => '');
            }

            /**
             *  Attach a file to an event
             *  http://127.0.0.1/api/v2/event/file
             */
            if ($this->component == 'file' and $this->method == 'POST') {
                /**
                 *  Check if all required data are present
                 */
                if (empty($this->data->id) or empty($this->data->file)) {
                    throw new Exception('Missing event Id or file path.');
                }

                $width = 0;
                $height = 0;
                $fps = 0;
                $changed_pixels = 0;

                if (!empty($this->data->width)) {
                    $width = $this->data->width;
                }
                if (!empty($this->data->height)) {
                    $height = $this->data->height;
                }
                if (!empty($this->data->fps)) {
                    $fps = $this->data->fps;
                }
                if (!empty($this->data->changed_pixels)) {
                    $changed_pixels = $this->data->changed_pixels;
                }

                /**
                 *  Try registering the event
                 */
                $this->motionEventController->attachFile($this->data->id, $this->data->file, $width, $height, $fps, $changed_pixels);

                /**
                 *  If register is successful
                 */
                return array('message' => array('Event file successfully registered.'), 'results' => '');
            }
        }

        throw new Exception('Invalid request');
    }
}
