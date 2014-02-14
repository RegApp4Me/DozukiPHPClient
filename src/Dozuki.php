<?php
/**
 * Dozuki PHP Client
 *
 * Copyright (c) 2014 WhyteSpyder Inc.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @category Library
 * @package  DozukiPHPClient
 * @author   Daniel Lawson <corexian@gmail.com>
 * @license  https://github.com/WhyteSpyder/DozukiPHPClient/blob/master/license.txt MIT
 * @link     https://github.com/WhyteSpyder/DozukiPHPClient/
 */
namespace WhyteSpyder\DozukiPHPClient;

/**
 * Dozuki
 * 
 * @category Library
 * @package  DozukiPHPClient
 * @author   Daniel Lawson <corexian@gmail.com>
 * @license  https://github.com/WhyteSpyder/DozukiPHPClient/blob/master/license.txt MIT
 * @link     https://github.com/WhyteSpyder/DozukiPHPClient/
 */
class Dozuki
{
    private $_apiEndpoint;
    private $_appId;
    private $_authToken;

    /**
     * Constructor
     * 
     * @param string $apiEndpoint The client site for the Dozuki account
     * @param string $appId       The appId associated with Dozuki account
     *
     * @return null
     */
    public function __construct( $apiEndpoint, $appId = null )
    {
        if (!in_array('curl', get_loaded_extensions())) {
            throw new Exception('You need to install cURL, see: http://curl.haxx.se/docs/install.html');
        }

        if (!filter_var($apiEndpoint, FILTER_VALIDATE_URL)) {
            throw new Exception('API Endpoint must be a valid URL');
        } else {
            $this->_apiEndpoint = "{$apiEndpoint}/api/2.0/";
        }

        if (is_null($appId)) {
            throw new Exception('App ID is required');
        } else {
            $this->_appId = $appId;
        }
    }

    /**
     * Create an authorization token for an existing user account using the provided credentials.
     *
     * @param string $email    Account's email address.
     * @param string $password Account's password.
     *
     * @return null
     */
    public function getAuthToken($email, $password)
    {
        $requestUrl = $this->_apiEndpoint . "user/token";

        $postBody = [
            'email' => $email,
            'password' => $password
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $requestUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-App-Id: {$this->_appId}"]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postBody));
        $content = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);

            throw new \Exception("Failed retrieving  '" . $requestUrl . "' because of ' " . $error . "'.");
        }

        $response = json_decode(curl_exec($ch), true);

        if (!isset($response['authToken'])) {
            throw new \Exception("Failed retrieving  '" . $requestUrl . "' because of ' " . $error . "'.");
        }

        $this->_authToken = $response['authToken'];
    }

    /**
     * List all category titles in the hierarchy structure except for stubs.
     *
     * @return null
     */
    public function getCategories()
    {
        $requestUrl = $this->_apiEndpoint . "categories";

        return $this->get($requestUrl);
    }

    /**
     * Returns a comprehensive list of attributes about a category, including 
     * the full text of the main category page and a list of all guides. The 
     * category name must be URL encoded.
     *
     * @param string $categoryname Identifier for category
     *
     * @return null
     */
    public function getCategory($categoryname)
    {
        $requestUrl = $this->_apiEndpoint . "categories/{$categoryname}";

        return $this->get($requestUrl);
    }

    /**
     * Returns a flat list of the category names on the site.
     *
     * @return null
     */
    public function getCategoriesAll()
    {
        $requestUrl = $this->_apiEndpoint . "categories/all";

        return $this->get($requestUrl);
    }

    /**
     * Get list of guides
     *
     * @return null
     */
    public function getGuides()
    {
        $requestUrl = $this->_apiEndpoint . "guides";

        return $this->get($requestUrl);
    }

    /**
     * Get a specific guide
     *
     * @param int $guideid Unique identifier.
     *
     * @return null
     */
    public function getGuide($guideid)
    {
        $requestUrl = $this->_apiEndpoint . "guides/{$guideid}";

        return $this->get($requestUrl);
    }

    /**
     * Create a new guide
     *
     * @param string $category     The category of the guide. A list of categories can be retrieved from the categories endpoints. Case sensitive.
     * @param string $type         The type of procedure the guide explains. Used to organize content.
     * @param string $subject      The subject of the guide.
     * @param string $title        Custom guide title. If not set, a default title is generated using the guide type, category, and subject.
     * @param string $summary      Short description of the guide. Must be less than 255 characters.
     * @param string $introduction Introduction text for the guide. Must be less than 50 lines.
     * @param string $conclusion   Conclusion text of the guide.
     * @param bool   $public       Set the guide to public or private.
     *
     * @return null
     */
    public function postGuide($category, $type, $subject = '', $title = '', $summary ='', $introduction = '', $conclusion = '', $public = true)
    {
        if (!is_string($category) || !is_string($type)) {
            throw new \Exception("Failed");
        }
        $requestUrl = $this->_apiEndpoint . "guides";

        $postBody = [
            'category' => $category,
            'type' => $type
        ];

        return $this->post($requestUrl, json_encode($postBody));
    }

    /**
     * List of work log entries.
     *
     * @return null
     */
    public function getWorkLogs()
    {
        $requestUrl = $this->_apiEndpoint . "work_log";

        return $this->get($requestUrl);
    }

    /**
     * Get all information about an entry with a known entryid.
     *
     * @param int $entryid Unique ID for work log entry
     *
     * @return null
     */
    public function getWorkLog($entryid)
    {
        $requestUrl = $this->_apiEndpoint . "work_log/{$entryid}";

        return $this->get($requestUrl);
    }

    /**
     * Search for content matching the provided query
     *
     * @param int    $query  Search terms
     * @param int    $offset The number of results to skip from the beginning. 
     *        Range: [0, infinity). Defaults to 0.
     * @param int    $limit  The maximum number of results to include in the response. 
     *        Range: [1, 200]. Defaults to 20.
     * @param string $filter Filters search results by their type. Filters can be 
     *        combined into a comma-separated string to apply multiple filters.
     *        Possible Values: guide, teardown, wiki, category, item, info, question, product
     *
     * @return null
     */
    public function getSearch($query, $offset = 0, $limit = 20, $filter = '')
    {
        $requestUrl = $this->_apiEndpoint . "search/{$query}";

        return $this->get($requestUrl);
    }

    /**
     * GET something
     *
     * @param string $requestUrl Modified request URL
     *
     * @return $response
     */
    public function get( $requestUrl )
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $requestUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Accept-Charset: utf-8"]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $content = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);

            throw new \Exception("Failed retrieving  '" . $requestUrl . "' because of ' " . $error . "'.");
        }

        $response = json_decode($content);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($status != 200) {

            if (isset($response->errors[0]->message)) {
                $error = $response->errors[0]->message;
            } else {
                $error = 'Status ' . $status;
            }

            throw new \Exception("Failed retrieving  '" . $requestUrl . "' because of ' " . $error . "'.");
        }

        if (isset($response) == false) {

            switch (json_last_error()) {
            case JSON_ERROR_NONE:
                $error = 'No errors';
                break;
            case JSON_ERROR_DEPTH:
                $error = 'Maximum stack depth exceeded';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $error = ' Underflow or the modes mismatch';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $error = 'Unexpected control character found';
                break;
            case JSON_ERROR_SYNTAX:
                $error = 'Syntax error, malformed JSON';
                break;
            case JSON_ERROR_UTF8:
                $error = 'Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
            default:
                $error = 'Unknown error';
                break;
            }
    
            throw new \Exception("Cannot read response by  '" . $requestUrl . "' because of: '" . $error . "'.");
        }

        return $response;
    }

    /**
     * POST something
     *
     * @param string $requestUrl Modified request URL
     * @param array  $postBody   Post body to be encoded and sent with request
     *
     * @return $response
     */
    public function post( $requestUrl, $postBody )
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $requestUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-App-Id: {$this->_appId}"]);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: api {$this->_authToken}"]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postBody));
        $content = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);

            throw new \Exception("Failed retrieving  '" . $requestUrl . "' because of ' " . $error . "'.");
        }

        $response = json_decode($content);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($status != 200) {

            if (isset($response->errors[0]->message)) {
                $error = $response->errors[0]->message;
            } else {
                $error = 'Status ' . $status;
            }

            throw new \Exception("Failed retrieving  '" . $requestUrl . "' because of ' " . $error . "'.");
        }

        if (isset($response) == false) {

            switch (json_last_error()) {
            case JSON_ERROR_NONE:
                $error = 'No errors';
                break;
            case JSON_ERROR_DEPTH:
                $error = 'Maximum stack depth exceeded';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $error = ' Underflow or the modes mismatch';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $error = 'Unexpected control character found';
                break;
            case JSON_ERROR_SYNTAX:
                $error = 'Syntax error, malformed JSON';
                break;
            case JSON_ERROR_UTF8:
                $error = 'Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
            default:
                $error = 'Unknown error';
                break;
            }
    
            throw new \Exception("Cannot read response by  '" . $requestUrl . "' because of: '" . $error . "'.");
        }

        return $response;
    }
}