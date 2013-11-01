<?php
/**
 * @package   ImpressPages
 */

namespace Tests\Sauce;

class MinkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group Sauce
     */
    public function testInstall()
    {
        TestEnvironment::cleanupFiles();

        // install fresh copy of ImpressPages:
        $installation = new \PhpUnit\Helper\Installation(); //development version
        $installation->install();
        $installationUrl = $installation->getInstallationUrl();

        // init Mink:
        $driver = new \Behat\Mink\Driver\Selenium2Driver(
            'firefox',
            array('tunnel-identifier' => getenv('TRAVIS_JOB_NUMBER')),
            'http://username:access_key@ondemand.saucelabs.com/wd/hub'
        );
        //$driver = new \Behat\Mink\Driver\GoutteDriver();
        $session = new \Behat\Mink\Session($driver);
        $session->start();

        $session->visit($installationUrl);

        // get the current page URL:
        $this->assertEquals($installationUrl, $session->getCurrentUrl());

        $page = $session->getPage();

        $this->assertEquals('DEBUG', $page->getContent());

        $homepageTitle = $page->find('css', 'title');
        $this->assertNotEmpty($homepageTitle, 'Homepage rendering is broken!');
        $this->assertEquals('Home', $homepageTitle->getText());

        $headlineElement = $page->find('css', 'p.homeHeadline');
        $this->assertNotEmpty($headlineElement, 'Headline is not visible!');
        $this->assertEquals('ImpressPages theme Blank', $headlineElement->getText());
    }

}