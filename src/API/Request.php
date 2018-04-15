<?php
namespace Pinpro\API;

#use Pinpro\helper\KeepaTime;

/**
 * Common Request
 */
class Request
{
    //@var HashMap<String, String>
    public $parameter = [];
    //@var string
    public $postData;
    //@var string
    public $path;

    public function __construct(array $parameter = null)
    {
        $this->parameter = $parameter;
    }

    /**
     * By accessing our deals you can find products that recently changed and match your search criteria. A single request will return a maximum of 150 deals.
     *
     * @param dealRequest DealRequest dealRequest contains all request parameters.
     * @return Request
     */
    public static function getDealsRequest(DealRequest $dealRequest)
    {
        $r = new Request();
        $r->path = "deal";
        $r->postData = json_encode($dealRequest);
        return $r;
    }

    /**
     * Retrieve category objects using their node ids and (optional) their parent tree.
     *
     * @param int $domainID Locale of the product <a href='psi_element://AmazonLocale'>AmazonLocale</a>
     * @param true $parents or not to include the category tree for each category.
     * @param string $category category node id of the category you want to request. For batch requests a comma separated list of ids (up to 10, the token cost stays the same).
     * @return Request
     */
    public static function getAuthToken()
    {
        $r = new Request();
        $r->path = "oauth";
        $r->parameter["response_type"] = 'code';
        $r->parameter["client_id"] = 'code';
        $r->parameter["state"] = '12345';
        $r->parameter["scope"] = 'read_public,write_public';
        $r->parameter["redirect_uri"] = 'https://mywebsite.com/connect/pinterest/';

        return $r;
    }

    /**
     * Retrieve user boards objects.
     *
     * @param str $fields ex: id%2Cname%2Curl
     * 
     * 
     * @return Request
     */
    public static function getBoards($fields = null)
    {
        $r = new Request();
        $r->path = "v1/me/boards";
        if($fields) {
            $r->parameter["fields"] = $fields;
        }
        return $r;
    }

    /**
     * Search for Amazon category names. Retrieves the category objects12 and optional their parent tree.
     *
     * @param int $domainID locale of the product <a href='psi_element://AmazonLocale'>AmazonLocale</a>
     * @param string $term The $term term you want to search for. Multiple space separated keywords are possible and if provided must all match. The minimum length of a keyword is 3 characters.
     * @param bool $parents Whether $parents or not to include the category tree for each category.
     * @return Request
     */
    public static function addPin($board, $note, $image_url, $link = null)
    {
        $r = new Request();
        $r->path = "v1/pins";
        $r->postData["board"] = $board;
        $r->postData["note"] = $note;
        if($link){
            $r->postData["link"] = $link;
        }
        $r->postData["image_url"] = $image_url;

        return $r;
    }

    public function query()
    {
        if ($this->parameter == null || count($this->parameter) == 0)
            return "";
        else
            $query = http_build_query($this->parameter, "", "&");
        return $query;
    }
}