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
namespace WhyteSpyder\DozukiPHPClient;

/**
 * Dozuki Test
 * 
 * @category Library
 * @package  DozukiPHPClient
 * @author   Daniel Lawson <corexian@gmail.com>
 * @license  https://github.com/WhyteSpyder/DozukiPHPClient/blob/master/license.txt MIT
 * @link     https://github.com/WhyteSpyder/DozukiPHPClient/
 */
class DozukiTest extends \PHPUnit_Framework_TestCase
{
    protected $dozuki;
    private   $_apiEndpoint = "http://example.dozuki.com";
    private   $_appId = "<YOUR APP ID>";

    /**
     * setUp
     *
     * @return null
     */
    public function setUp ()
    {
        $this->dozuki = new Dozuki($this->_apiEndpoint, $this->_appId);
    }

    /**
     * tearDown
     *
     * @return null
     */
    protected function tearDown()
    {
        unset($this->dozuki);
    }

    /**
     * testGetGuides
     *
     * @return null
     */
    public function testGetGuides()
    {
        $arrayOfObjects = $this->dozuki->getGuides();
        $this->assertArrayHasKey(0, $arrayOfObjects, 'Array does not have key');

        $this->assertInternalType('object', $arrayOfObjects[0], 'Not an object');
    }

    /**
     * testGetGuide
     *
     * @return null
     */
    public function testGetGuide()
    {
        $arrayOfObjects = $this->dozuki->getGuides(5);
        $this->assertArrayHasKey(0, $arrayOfObjects, 'Array does not have key');

        $this->assertInternalType('object', $arrayOfObjects[0], 'Not an object');
    }

    /**
     * testGetCategories
     *
     * @return null
     */
    public function testGetCategories()
    {
        $categoryObject = $this->dozuki->getCategories();
        $this->assertInternalType('object', $categoryObject, 'Not an object');
    }

    /**
     * testGetCategory
     *
     * @return null
     */
    public function testGetCategory()
    {
        $categoryObject = $this->dozuki->getCategory("Examples");
        $this->assertInternalType('object', $categoryObject, 'Not an object');
    }

    /**
     * testGetCategoriesAll
     *
     * @return null
     */
    public function testGetCategoriesAll()
    {
        $categoryObject = $this->dozuki->getCategoriesAll();
        $this->assertInternalType('array', $categoryObject, 'Not an array');
    }
}
