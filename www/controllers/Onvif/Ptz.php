<?php

// Inspired by https://github.com/KuroNeko-san/ponvif

namespace Controllers\Onvif;

use Exception;

class Ptz extends Onvif
{
    public function __construct(string $uri, string $username, string $password)
    {
        parent::__construct($uri, $username, $password);

        if (empty($this->ptzUri)) {
            throw new Exception('PTZ URI is not set');
        }
    }

    /**
     *  PTZ move
     */
    public function ptzContinuousMove(string $profileToken, float $velocitPantiltX, float $velocitPantiltY) : void
    {
        try {
            $data = $this->getToken($this->username, $this->password);

            $xml = '<s:Envelope xmlns:s="http://www.w3.org/2003/05/soap-envelope" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
                <s:Header>
                    <wsse:Security s:mustUnderstand="1" xmlns="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
                        <UsernameToken>
                            <Username>' . $data['username'] . '</Username>
                            <Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordDigest">' . $data['PASSDIGEST'] . '</Password>
                            <Nonce EncodingType="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary">' . $data['NONCE'] . '</Nonce>
                            <Created xmlns="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">' . $data['timestamp'] . '</Created>
                        </UsernameToken>
                    </wsse:Security>
                </s:Header>
                <s:Body xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
                    <ContinuousMove xmlns="http://www.onvif.org/ver20/ptz/wsdl">
                        <ProfileToken>' . $profileToken . '</ProfileToken>
                        <Velocity>
                            <PanTilt x="' . $velocitPantiltX . '" y="' . $velocitPantiltY . '" space="http://www.onvif.org/ver10/tptz/PanTiltSpaces/VelocityGenericSpace" xmlns="http://www.onvif.org/ver10/schema"/>
                        </Velocity>
                    </ContinuousMove>
                </s:Body>
            </s:Envelope>';

            /**
             *  Send request
             */
            $response = $this->request($this->ptzUri, $xml);

            /**
             *  Check response
             */
            $this->isFault($response);
        } catch (Exception $e) {
            throw new Exception('could not move PTZ: ' . $e->getMessage());
        }
    }

    /**
     *  PTZ move to relative position
     */
    public function ptzRelativeMove(string $profileToken, float $translationPantiltX, float $translationPantiltY, float $speedPantiltX, float $speedPantiltY) : void
    {
        try {
            $data = $this->getToken($this->username, $this->password);

            $xml = '<s:Envelope xmlns:s="http://www.w3.org/2003/05/soap-envelope" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
                <s:Header>
                    <wsse:Security s:mustUnderstand="1" xmlns="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
                        <UsernameToken>
                            <Username>' . $data['username'] . '</Username>
                            <Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordDigest">' . $data['PASSDIGEST'] . '</Password>
                            <Nonce EncodingType="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary">' . $data['NONCE'] . '</Nonce>
                            <Created xmlns="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">' . $data['timestamp'] . '</Created>
                        </UsernameToken>
                    </wsse:Security>
                </s:Header>
                <s:Body xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
                    <RelativeMove xmlns="http://www.onvif.org/ver20/ptz/wsdl">
                        <ProfileToken>' . $profileToken . '</ProfileToken>
                        <Translation>
                            <PanTilt x="' . $translationPantiltX . '" y="' . $translationPantiltY . '" space="http://www.onvif.org/ver10/tptz/PanTiltSpaces/TranslationGenericSpace" xmlns="http://www.onvif.org/ver10/schema"/>
                        </Translation>
                        <Speed>
                            <PanTilt x="' . $speedPantiltX . '" y="' . $speedPantiltY . '" space="http://www.onvif.org/ver10/tptz/PanTiltSpaces/GenericSpeedSpace" xmlns="http://www.onvif.org/ver10/schema"/>
                        </Speed>
                    </RelativeMove>
                </s:Body>
            </s:Envelope>';

            /**
             *  Send request
             */
            $response = $this->request($this->ptzUri, $xml);

            /**
             *  Check response
             */
            $this->isFault($response);
        } catch (Exception $e) {
            throw new Exception('could not move PTZ: ' . $e->getMessage());
        }
    }

    /**
     *  PTZ move to absolute position
     */
    public function ptzAbsoluteMove(string $profileToken, float $positionPantiltX, float $positionPantiltY, float $zoom) : void
    {
        try {
            $data = $this->getToken($this->username, $this->password);

            $xml = '<s:Envelope xmlns:s="http://www.w3.org/2003/05/soap-envelope" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
                <s:Header>
                    <wsse:Security s:mustUnderstand="1" xmlns="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
                        <UsernameToken>
                            <Username>' . $data['username'] . '</Username>
                            <Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordDigest">' . $data['PASSDIGEST'] . '</Password>
                            <Nonce EncodingType="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary">' . $data['NONCE'] . '</Nonce>
                            <Created xmlns="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">' . $data['timestamp'] . '</Created>
                        </UsernameToken>
                    </wsse:Security>
                </s:Header>
                <s:Body xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
                    <AbsoluteMove xmlns="http://www.onvif.org/ver20/ptz/wsdl">
                        <ProfileToken>' . $profileToken . '</ProfileToken>
                        <Position>
                            <PanTilt x="' . $positionPantiltX . '" y="' . $positionPantiltY . '" space="http://www.onvif.org/ver10/tptz/PanTiltSpaces/PositionGenericSpace" xmlns="http://www.onvif.org/ver10/schema"/>
                            <Zoom x="' . $zoom . '" space="http://www.onvif.org/ver10/tptz/ZoomSpaces/PositionGenericSpace" xmlns="http://www.onvif.org/ver10/schema"/>
                        </Position>
                    </AbsoluteMove>
                </s:Body>
            </s:Envelope>';

            /**
             *  Send request
             */
            $response = $this->request($this->ptzUri, $xml);

            /**
             *  Check response
             */
            $this->isFault($response);
        } catch (Exception $e) {
            throw new Exception('could not move PTZ: ' . $e->getMessage());
        }
    }

    /**
     *  PTZ stop
     */
    public function ptzStop(string $profileToken, bool $pantilt, bool $zoom) : void
    {
        try {
            $data = $this->getToken($this->username, $this->password);

            $xml = '<s:Envelope xmlns:s="http://www.w3.org/2003/05/soap-envelope" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
                <s:Header>
                    <wsse:Security s:mustUnderstand="1" xmlns="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
                        <UsernameToken>
                            <Username>' . $data['username'] . '</Username>
                            <Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordDigest">' . $data['PASSDIGEST'] . '</Password>
                            <Nonce EncodingType="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary">' . $data['NONCE'] . '</Nonce>
                            <Created xmlns="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">' . $data['timestamp'] . '</Created>
                        </UsernameToken>
                    </wsse:Security>
                </s:Header>
                <s:Body xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
                    <Stop xmlns="http://www.onvif.org/ver20/ptz/wsdl">
                        <ProfileToken>' . $profileToken . '</ProfileToken>
                        <PanTilt>' . $pantilt . '</PanTilt>
                        <Zoom>' . $zoom . '</Zoom>
                    </Stop>
                </s:Body>
            </s:Envelope>';

            /**
             *  Send request
             */
            $response = $this->request($this->ptzUri, $xml);

            /**
             *  Check response
             */
            $this->isFault($response);
        } catch (Exception $e) {
            throw new Exception('could not stop PTZ: ' . $e->getMessage());
        }
    }
}
