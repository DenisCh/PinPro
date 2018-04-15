<?php
namespace Pinpro;

#use JsonMapper;
use Pinpro\API\Request;
use Pinpro\API\Response;
use Pinpro\API\ResponseStatus;

class PinproAPI
{
    const MAX_DELAY = 60000;

    private $accessKey = null;
    private $userAgent = null;
    private $serializer = null;

    public function __construct($accessKey)
    {
        $this->accessKey = $accessKey;
        $this->userAgent = "PinPro-PHP Framework-" . "1.00";
        //$this->serializer = new JsonMapper();
    }

    /**
     * Issue a request to the Pinterest API.
     *
     * @param r Request the API Request {@link Request}
     * @return Response for {@link Response}
     * @throws \Exception
     */
    public function sendRequest(Request $r)
    {
        $url = "https://api.pinterest.com/" . $r->path . "/?access_token=" . $this->accessKey . "&" . $r->query();

        $response = new Response($r);

        // create curl resource
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");

        if ($r->postData != null) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $r->postData);
        }

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);


        $responseTime = microtime(true);

        // $output contains the output string
        $output = curl_exec($ch);

        if ($output === false)
            throw new \Exception(curl_error($ch));

        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if ($responseCode == 200 || $responseCode == 201) {
            try {
                $jo = json_decode($output);
                if ($jo === false)
                    throw new \Exception("Failed to parse JSON");

                $response = $jo; //$this->serializer->map($jo, new Response($r));
                //$response = $this->serializer->deserialize($output, 'Pinpro\API\Response', 'json');
                $response->status = ResponseStatus::OK;
            } catch (\Exception $e) {
                $response->status = ResponseStatus::REQUEST_FAILED;
                throw $e;
            }
        } else {
            try {
                $jo = json_decode($output);
                if ($jo === false)
                    throw new \Exception("Failed to parse JSON");
                $response = $jo; //$this->serializer->map($jo, new Response($r));

                switch ($responseCode) {
                    case 400:
                        $response->status = ResponseStatus::REQUEST_INVALID;
                        break;
                    case 401:
                        $response->status = ResponseStatus::AUTHENTICATION_FAILED;
                        break;
                    case 404:
                        $response->status = ResponseStatus::NOT_FOUND;
                        break;
                    case 408:
                        $response->status = ResponseStatus::TIME_OUT;
                        break;
                    case 429:
                        $response->status = ResponseStatus::TOO_MANY_REQUESTS;
                        break;
                    default:
                        $response->error = $output;
                        $response->status = ResponseStatus::REQUEST_FAILED;
                        break;
                }

                return $response;
            } catch (\Exception $e) {
                $response->status = ResponseStatus::REQUEST_FAILED;
                throw $e;
            }
        }


        // close curl resource to free up system resources
        curl_close($ch);
        $response->url = $url;
        $response->requestTime = intval((microtime(true) - $responseTime) * 1000);

        return $response;
    }
}