<?php

use Behat\Behat\Context\Context;
use Behat\Testwork\Hook\Scope\AfterSuiteScope;
use Behat\Testwork\Hook\Scope\BeforeSuiteScope;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, SnippetAcceptingContext
{
    /**
     * @BeforeSuite
     */
    public static function prepare(BeforeSuiteScope $scope)
    {
        echo "### Starting php web server ###\n";
        exec('nohup php bin/console -v server:run --env=test localhost:8080 >/tmp/web_error.log 2>&1 &');
        exec('php bin/console cache:clear --env=test --no-debug');
    }

    /**
     * @AfterSuite
     */
    public static function shutdown(AfterSuiteScope $scope)
    {
        echo "### Stopping php web server ###\n";
        exec('pkill -f "router_test.php"');
    }
}
