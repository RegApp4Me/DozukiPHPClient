<?php
/**
 * Dozuki PHP Client
 *
 * Copyright (c) 2014 WhyteSpyder Inc.
 *
 * @category Library
 * @package  DozukiPHPClient
 * @author   Daniel Lawson <corexian@gmail.com>
 * @license  https://github.com/WhyteSpyder/DozukiPHPClient/blob/master/license.txt MIT
 * @link     https://github.com/WhyteSpyder/DozukiPHPClient/
 * */
namespace WhyteSpyder\DozukiPHPClient\Tests;
use WhyteSpyder\DozukiPHPClient\DozukiClient;

/**
 * DozukiClient Test
 * 
 * @category Library
 * @package  DozukiPHPClient
 * @author   Daniel Lawson <corexian@gmail.com>
 * @license  https://github.com/WhyteSpyder/DozukiPHPClient/blob/master/license.txt MIT
 * @link     https://github.com/WhyteSpyder/DozukiPHPClient/
 */
class DozukiClientTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test data provider
     *
     * @return  null
     */
    public function provideConfigValues()
    {
        return array(
            array('example.dozuki.com', 'YOUR_APP_ID', 'test@example.com', 'YOUR_PASSWORD')
        );
    }

    /**
     * Test that the factory actually returns a client.
     * 
     * @param string $domain Account Dozuki domain
     *
     * @dataProvider provideConfigValues
     */
    public function testFactoryReturnsClient($domain)
    {
        $config = array(
            'dozuki_domain' => $domain
        );

        $client = DozukiClient::factory($config);

        $this->assertInstanceOf('\WhyteSpyder\DozukiPHPClient\DozukiClient', $client);
        $this->assertEquals($config['dozuki_domain'], $client->getConfig('dozuki_domain'));
    }

    /**
     * Get an exception for null configuration values.
     * 
     * @expectedException \Guzzle\Common\Exception\InvalidArgumentException
     */
    public function testFactoryReturnsExceptionOnNullArguments()
    {
        $config = array();

        $client = DozukiClient::factory($config);
    }

    /**
     * Get an exception for blank configuration values.
     * 
     * @expectedException \Guzzle\Common\Exception\InvalidArgumentException
     */
    public function testFactoryReturnsExceptionOnBlankArguments()
    {
        $config = array(
            'dozuki_domain' => ''
        );

        $client = DozukiClient::factory($config);
    }

    /**
     * Test authentication
     * 
     * @param string $domain   Account dozuki domain
     * @param string $appId    Account app id
     * @param string $email    Account email
     * @param string $password Account password
     *
     * @dataProvider provideConfigValues
     */
    public function testAddToken($domain, $appId, $email, $password)
    {
        $config = array(
            'dozuki_domain' => $domain
        );

        $client = DozukiClient::factory($config);

        $authRequest = $client->getCommand(
            'user/token',
            array(
                'X-App-Id' => $appId,
                'email' => $email,
                'password' => $password
            )
        );
        $response = $authRequest->execute();

        $this->assertNotNull($response['authToken'], 'No authToken found.');

        $token = "api " . $response['authToken'];

        $client->addAuthToken($response['authToken']);

        $this->assertEquals($token, $client->getDefaultOption('headers')['Authorization']);
    }
}