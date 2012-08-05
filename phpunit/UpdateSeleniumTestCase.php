<?php

namespace IpUpdate\PhpUnit;

//class UpdateTestCase extends \PHPUnit_Extensions_Database_TestCase
class UpdateSeleniumTestCase extends \PHPUnit_Extensions_SeleniumTestCase
{
    protected function setup()
    {
        $fileSystemHelper = new \IpUpdate\PhpUnit\Helper\FileSystem();
        $fileSystemHelper->chmod(TEST_TMP_DIR, 0755);
        $fileSystemHelper->cleanDir(TEST_TMP_DIR);
        
        $this->setBrowser('*firefox');
        $this->setBrowserUrl(TEST_TMP_URL);
    }
    
    protected function tearDown()
    {
        $fileSystemHelper = new \IpUpdate\PhpUnit\Helper\FileSystem();
        $fileSystemHelper->chmod(TEST_TMP_DIR, 0755);
        $fileSystemHelper->cleanDir(TEST_TMP_DIR);
    }
    
    protected function assertNoErrors() 
    {
        $this->assertTextNotPresent('error');
        $this->assertTextNotPresent('warning');
        $this->assertTextNotPresent('note');
    }
}