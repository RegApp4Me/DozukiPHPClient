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
    private $_api_endpoint;
    private $_appId;
    private $_authToken;

    /**
     * Constructor
     * 
     * @param string $api_endpoint The client site for the Dozuki account
     * @param string $appId        The appId associated with Dozuki account
     *
     * @return null
     */
    public function __construct( $api_endpoint, $appId = null )
    {
        if (!in_array('curl', get_loaded_extensions())) {
            throw new Exception('You need to install cURL, see: http://curl.haxx.se/docs/install.html');
        }

        if (!filter_var($api_endpoint, FILTER_VALIDATE_URL)) {
            throw new Exception('$api_endpoint must be a valid URL');
        }

        $this->_api_endpoint = "{$api_endpoint}/api/2.0/";

        if (!is_null($appId)) {
            $this->_appId = $appId;
        }
    }

    /**
     * Get list of categories
     *
     * @return null
     */
    public function getCategories()
    {
        $request_url = $this->_api_endpoint . "categories";

        return $this->get($request_url);
    }

    /**
     * Get a specific category
     *
     * @param string $categoryname Identifier for category
     *
     * @return null
     */
    public function getCategory($categoryname)
    {
        $request_url = $this->_api_endpoint . "categories/{$categoryname}";

        return $this->get($request_url);
    }

    /**
     * Get list of guides
     *
     * @return null
     */
    public function getGuides()
    {
        $request_url = $this->_api_endpoint . "guides";

        return $this->get($request_url);
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
        $request_url = $this->_api_endpoint . "guides/{$guideid}";

        return $this->get($request_url);
    }

    /**
     * Get something
     *
     * @param string $request_url Modified request URL
     *
     * @return $response
     */
    public function get( $request_url )
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept-Charset: utf-8"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $content = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);

            throw new \Exception("Failed retrieving  '" . $request_url . "' because of ' " . $error . "'.");
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

            throw new \Exception("Failed retrieving  '" . $request_url . "' because of ' " . $error . "'.");
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
    
            throw new \Exception("Cannot read response by  '" . $request_url . "' because of: '" . $error . "'.");
        }

        return $response;
    }

    // TODO Authentication
}