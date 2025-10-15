<?php

// Inspired by https://github.com/KuroNeko-san/ponvif

namespace Controllers\Onvif;

use Exception;
use SimpleXMLElement;
use DOMDocument;

class Onvif
{
    protected $username;
    protected $password;
    protected $ptzUri;
    protected $mediaUri;
    protected $deviceUri;
    protected $curlHandle;

    public function __construct(string $mediaUri, string $username, string $password)
    {
        $this->mediaUri = $mediaUri;
        $this->username = $username;
        $this->password = $password;

        try {
            /**
             *  Initialize curl
             */
            $this->curlHandle = curl_init();

            /**
             *  Get system date and time
             */
            $datetime = $this->getSystemDateAndTime();

            $timestamp = mktime($datetime['Time']['Hour'], $datetime['Time']['Minute'], $datetime['Time']['Second'], $datetime['Date']['Month'], $datetime['Date']['Day'], $datetime['Date']['Year']);
            $this->deltatime = time() - $timestamp - 5;

            /**
             *  Get capabilities
             */
            $capabilities = $this->getCapabilities();

            /**
             *  Get ONVIF version
             */
            $onvifVersion = $this->getOnvifVersion($capabilities);

            /**
             *  Set media uri, device uri and ptz uri from ONVIF version
             */
            $this->mediaUri  = $onvifVersion['media'];
            $this->deviceUri = $onvifVersion['device'];
            $this->ptzUri    = $onvifVersion['ptz'];

            /**
             *  Get video sources
             */
            $this->videosources = $this->getVideoSources();

            /**
             *  Get profiles
             */
            $this->profiles = $this->getProfiles();

            /**
             *  Get active sources
             */
            $this->sources = $this->getActiveSources($this->videosources, $this->profiles);
        } catch (Exception $e) {
            throw new Exception('Error while initializing the Onvif camera: ' . $e->getMessage());
        }
    }

    public function getSources() : array
    {
        return $this->sources;
    }

    public function setMediaUri(string $mediauri) : void
    {
        $this->mediaUri = $mediauri;
    }

    public function setUsername(string $username) : void
    {
        $this->username = $username;
    }

    public function setPassword(string $password) : void
    {
        $this->password = $password;
    }

    /**
     *  Get system date and time
     */
    public function getSystemDateAndTime() : array
    {
        try {
            $xml = '<s:Envelope xmlns:s="http://www.w3.org/2003/05/soap-envelope">
                <s:Body xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
                    <GetSystemDateAndTime xmlns="http://www.onvif.org/ver10/device/wsdl"/>
                </s:Body>
            </s:Envelope>';

            /**
             *  Send request
             */
            $response = $this->request($this->mediaUri, $xml);

            /**
             *  Check response
             */
            $this->isFault($response);

            return $response['Envelope']['Body']['GetSystemDateAndTimeResponse']['SystemDateAndTime']['UTCDateTime'];
        } catch (Exception $e) {
            throw new Exception('could not get system date and time: ' . $e->getMessage());
        }
    }

    /**
     *  Get capabilities
     */
    public function getCapabilities() : array
    {
        try {
            /**
             *  First, get the token
             */
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
                        <s:Body xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"><GetCapabilities xmlns="http://www.onvif.org/ver10/device/wsdl">
                        <Category>All</Category>
                    </GetCapabilities>
                </s:Body>
            </s:Envelope>';

            /**
             *  Send request
             */
            $response = $this->request($this->mediaUri, $xml);

            /**
             *  Check response
             */
            $this->isFault($response);

            return $response['Envelope']['Body']['GetCapabilitiesResponse']['Capabilities'];
        } catch (Exception $e) {
            throw new Exception('could not get capabilities: ' . $e->getMessage());
        }
    }

    /**
     *  Return the ONVIF version
     */
    protected function getOnvifVersion(array $capabilities) : array
    {
        $version['media']  = $capabilities['Media']['XAddr'];
        $version['device'] = $capabilities['Device']['XAddr'];
        $version['event']  = $capabilities['Events']['XAddr'];

        if (isset($capabilities['Device']['System']['SupportedVersions']['Major'])) {
            // NVT supports a specific onvif version
            $version['major'] = $capabilities['Device']['System']['SupportedVersions']['Major'];
            $version['minor'] = $capabilities['Device']['System']['SupportedVersions']['Minor'];
        } else {
            // NVT supports more onvif versions
            $currentma = 0;
            $currentmi = 0;

            foreach ($capabilities['Device']['System']['SupportedVersions'] as $cver) {
                if ($cver['Major'] > $currentma) {
                    $currentma = $cver['Major'];
                    $currentmi = $cver['Minor'];
                }
            }

            $version['major'] = $currentma;
            $version['minor'] = $currentmi;
        }

        if (isset($capabilities['PTZ']['XAddr'])) {
            $version['ptz'] = $capabilities['PTZ']['XAddr'];
        }

        return $version;
    }

    /**
     *  Make token for authentication, from username, password and timestamp
     */
    protected function getToken(string $username, string $password) : array
    {
        return $this->getPasswordDigest($username, $password, date('Y-m-d\TH:i:s.000\Z', time() - $this->deltatime));
    }

    /**
     *  Create password digest
     */
    protected function getPasswordDigest(string $username, string $password, string $timestamp = 'default', $nonce = 'default') : array
    {
        /**
         *  If timestamp is default, set it to current date
         */
        if ($timestamp == 'default') {
            $timestamp = date('Y-m-d\TH:i:s.000\Z');
        }

        /**
         *  If nonce is default, set it to random number
         */
        if ($nonce == 'default') {
            $nonce = mt_rand();
        }

        $data = [];

        /**
         *  Create password digest
         */
        $passdigest = base64_encode(pack('H*', sha1(pack('H*', $nonce) . pack('a*', $timestamp).pack('a*', $password))));
        //$passdigest=base64_encode(sha1($nonce.$timestamp.$password,true)); // alternative

        $data['username'] = $username;
        $data['PASSDIGEST'] = $passdigest;
        $data['NONCE'] = base64_encode(pack('H*', $nonce));
        //$REQ['NONCE']=base64_encode($nonce); // alternative
        $data['timestamp'] = $timestamp;

        return $data;
    }

    /**
     *  Check if response contains a fault
     */
    public function isFault($response) : void
    {
        if (array_key_exists('Fault', $response)) {
            throw new Exception('Fault: ' . $response['Fault']);
        }

        if (array_key_exists('Fault', $response['Envelope']['Body'])) {
            print_r($response);
            throw new Exception('Fault: ' . $response['Envelope']['Body']['Fault']['Reason']['Text']);
        }
    }

    /**
     *  Send request
     */
    protected function request($url, $postString)
    {
        try {
            curl_setopt($this->curlHandle, CURLOPT_URL, $url);
            curl_setopt($this->curlHandle, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($this->curlHandle, CURLOPT_TIMEOUT, 10);
            curl_setopt($this->curlHandle, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->curlHandle, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($this->curlHandle, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($this->curlHandle, CURLOPT_POST, true);
            curl_setopt($this->curlHandle, CURLOPT_POSTFIELDS, $postString);
            curl_setopt($this->curlHandle, CURLOPT_HTTPHEADER, array('Content-Type: text/xml; charset=utf-8', 'Content-Length: ' . strlen($postString)));
            //curl_setopt($this->curlHandle, CURLOPT_USERPWD, $user . ":" . $password); // HTTP authentication

            /**
             *  Send request
             */
            $result = curl_exec($this->curlHandle);

            /**
             *  If curl fails with an error, try to retrieve the error message and error number and throw an exception
             */
            if ($result === false) {
                $exception = 'curl error';
                $errorNumber = curl_errno($this->curlHandle);
                $error = curl_error($this->curlHandle);

                // Add curl error number
                if (!empty($errorNumber)) {
                    $exception .= ' (' . $errorNumber . ')';
                }

                // Add curl error message
                if (!empty($error)) {
                    $exception .= ': ' . $error;
                }

                throw new Exception($exception);
            }

            /**
             *  If curl execution succeeded, retrieve the HTTP response code
             */
            $responseCode = curl_getinfo($this->curlHandle, CURLINFO_RESPONSE_CODE);

            if (empty($responseCode)) {
                throw new Exception('could not retrieve HTTP response code');
            }

            /**
             *  If the response code is different from 200, then return the response code
             */
            if ($responseCode != 200) {
                throw new Exception('HTTP response code: ' . $responseCode);
            }

            return $this->xmlToArray($result);
        } catch (Exception $e) {
            throw new Exception('request error: ' . $e->getMessage());
        } finally {
            curl_close($this->curlHandle);
        }
    }

    /**
     *  TODO : à améliorer
     */
    protected function xmlToArray($response)
    {
        $sxe = new SimpleXMLElement($response);
        $dom_sxe = dom_import_simplexml($sxe);
        $dom = new DOMDocument('1.0');
        $dom_sxe = $dom->importNode($dom_sxe, true);
        $dom_sxe = $dom->appendChild($dom_sxe);
        $element = $dom->childNodes->item(0);
        foreach ($sxe->getDocNamespaces() as $name => $uri) {
                $element->removeAttributeNS($uri, $name);
        }
        $xmldata=$dom->saveXML();
        $xmldata=substr($xmldata, strpos($xmldata, "<Envelope>"));
        $xmldata=substr($xmldata, 0, strpos($xmldata, "</Envelope>")+strlen("</Envelope>"));
        $xml=simplexml_load_string($xmldata);
        $data=json_decode(json_encode((array)$xml), 1);
        $data=array($xml->getName()=>$data);

        return $data;
    }

    /**
     *  Get video sources
     */
    public function getVideoSources() : array
    {
        try {
            /**
             *  First, get the token
             */
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
                    <GetVideoSources xmlns="http://www.onvif.org/ver10/media/wsdl"/>
                </s:Body>
            </s:Envelope>';

            /**
             *  Send request
             */
            $response = $this->request($this->mediaUri, $xml);

            /**
             *  Check response
             */
            $this->isFault($response);

            return $response['Envelope']['Body']['GetVideoSourcesResponse']['VideoSources'];
        } catch (Exception $e) {
            throw new Exception('could not get video sources: ' . $e->getMessage());
        }
    }

    /**
     *  Get profiles
     */
    public function getProfiles() : array
    {
        try {
            /**
             *  First, get the token
             */
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
                    <GetProfiles xmlns="http://www.onvif.org/ver10/media/wsdl"/>
                </s:Body>
            </s:Envelope>';

            /**
             *  Send request
             */
            $response = $this->request($this->mediaUri, $xml);

            /**
             *  Check response
             */
            $this->isFault($response);

            return $response['Envelope']['Body']['GetProfilesResponse']['Profiles'];
        } catch (Exception $e) {
            throw new Exception('could not get profiles: ' . $e->getMessage());
        }
    }

    /**
     *  Get active sources
     */
    protected function getActiveSources(array $videoSources, array $profiles) : array
    {
        $sources = [];

        // NVT is a camera
        if (isset($videoSources['@attributes'])) {
            $sources[0]['sourcetoken'] = $videoSources['@attributes']['token'];
            $sources = $this->getProfileData($sources, 0, $profiles);

        // NVT is an encoder
        } else {
            for ($i=0; $i < count($videoSources); $i++) {
                if (strtolower($videoSources[$i]['@attributes']['SignalActive']) == 'true') {
                    $sources[$i]['sourcetoken'] = $videoSources[$i]['@attributes']['token'];
                    $sources = $this->getProfileData($sources, $i, $profiles);
                }
            }
        }

        return $sources;
    }

    /**
     *  Get profile data
     */
    protected function getProfileData(array $sources, int $i, array $profiles) : array
    {
        $inprofile = 0;

        for ($y=0; $y < count($profiles); $y++) {
            if ($profiles[$y]['VideoSourceConfiguration']['SourceToken'] == $sources[$i]['sourcetoken']) {
                $sources[$i][$inprofile]['profilename']  = $profiles[$y]['Name'];
                $sources[$i][$inprofile]['profiletoken'] = $profiles[$y]['@attributes']['token'];

                if (isset($profiles[$y]['VideoEncoderConfiguration'])) {
                    $sources[$i][$inprofile]['encodername'] = $profiles[$y]['VideoEncoderConfiguration']['Name'];
                    $sources[$i][$inprofile]['encoding']    = $profiles[$y]['VideoEncoderConfiguration']['Encoding'];
                    $sources[$i][$inprofile]['width']       = $profiles[$y]['VideoEncoderConfiguration']['Resolution']['Width'];
                    $sources[$i][$inprofile]['height']      = $profiles[$y]['VideoEncoderConfiguration']['Resolution']['Height'];
                    $sources[$i][$inprofile]['fps']         = $profiles[$y]['VideoEncoderConfiguration']['RateControl']['FrameRateLimit'];
                    $sources[$i][$inprofile]['bitrate']     = $profiles[$y]['VideoEncoderConfiguration']['RateControl']['BitrateLimit'];
                }

                if (isset($profiles[$y]['PTZConfiguration'])) {
                    $sources[$i][$inprofile]['ptz']['name']      = $profiles[$y]['PTZConfiguration']['Name'];
                    $sources[$i][$inprofile]['ptz']['nodetoken'] = $profiles[$y]['PTZConfiguration']['NodeToken'];
                }

                $inprofile++;
            }
        }

        return $sources;
    }
}
