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

use Guzzle\Common\Collection;
use Guzzle\Service\Client;
use Guzzle\Service\Description\ServiceDescription;
use Guzzle\Service\Builder\ServiceBuilder;
use Guzzle\Common\Exception\InvalidArgumentException;

/**
 * DozukiClient
 * 
 * @category Library
 * @package  DozukiPHPClient
 * @author   Daniel Lawson <corexian@gmail.com>
 * @license  https://github.com/WhyteSpyder/DozukiPHPClient/blob/master/license.txt MIT
 * @link     https://github.com/WhyteSpyder/DozukiPHPClient/
 */
class DozukiClient extends Client
{
    /**
     * Factory
     * 
     * @param array $config The client site for the Dozuki account
     *
     * @return null
     */
    public static function factory( $config = array() )
    {
        // The following values are required when creating the client
        $required = array(
            'dozuki_domain'
        );

        foreach ($required as $value) {
            if (empty($config[$value])) {
                throw new InvalidArgumentException("Argument '{$value}' must not be blank.");
            }
        }

        $domain = $config['dozuki_domain'];

        // Provide a hash of default client configuration options
        $default = array('base_url' => "https://{$domain}/api/2.0/");

        $config = Collection::fromConfig($config, $default, $required);

        $client = new self($config->get('base_url'), $config);

        $client->setDescription(ServiceDescription::factory('dozuki_config.json'));

        return $client;
    }

    /**
     * Create an authorization token for an existing user account using the provided credentials.
     *
     * @param array $token Authentication token.
     *
     * @return null
     */

    public function addAuthToken( $token )
    {
        $this->setDefaultOption('headers/Authorization', "api $token");
    }
}