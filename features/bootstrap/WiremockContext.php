<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;
use Behat\Testwork\Hook\Scope\AfterSuiteScope;
use Behat\Testwork\Hook\Scope\BeforeSuiteScope;
use WireMock\Client\WireMock;

class WiremockContext implements Context
{
    protected $wiremock;
    protected $baseUrl;

    public function __construct($host='localhost', $port=9090)
    {
        $this->wiremock = WireMock::create($host, $port);

        $this->baseUrl = 'http://' . $host;
        if ($port) {
            $this->baseUrl .= ':' . $port;
        }

        // Wait for wiremock to be running and ready
        if (!$this->wiremock->isAlive()) {
            throw new \RuntimeException("Wiremock was not ready");
        }
    }

    /**
     * @BeforeSuite
     */
    public static function prepare(BeforeSuiteScope $scope)
    {
        echo "### Starting wiremock ###\n";
        exec('nohup java -jar bin/wiremock-standalone-2.1.6.jar --port 9090 >/dev/null 2>&1 &');
    }

    /**
     * @AfterSuite
     */
    public static function shutdown(AfterSuiteScope $scope)
    {
        echo "\n### Stopping wiremock ###\n";
        exec('pkill -f "wiremock-2.1.6-standalone.jar"');
    }

    /**
     * @BeforeScenario
     */
    public function resetMappings(BeforeScenarioScope $scope)
    {
        // Reset wiremock
        $this->wiremock->reset();
    }

    private function setGETStubFor($uri, $data)
    {
        $this->wiremock->stubFor(
            WireMock::get(WireMock::urlEqualTo($uri))
                ->willReturn(
                    WireMock::aResponse()
                        ->withHeader('Content-Type', 'application/json; charset=utf-8')
                        ->withBody(json_encode($data, JSON_UNESCAPED_SLASHES))
                )
        );
    }

    /**
     * @Given the github project :project has these contributors:
     */
    public function theGithubProjectHasTheseContributors($project, TableNode $table)
    {
        $contributorsUri = '/repos/' . $project . '/contributors';

        $contributors = [];

        $index = 4321; // just a random number

        foreach ($table->getHash() as $row) {
            $username = $row['username'];
            $location = $row['location'];

            $userUri = '/users/' . $username;
            $contributor = (object)[
                'login' => $username,
                'id' => ++$index,
                'url' => $this->baseUrl . $userUri,
                'html_url' => 'a value we do not use',
                'type' => 'User'
            ];
            $contributors[] = $contributor;

            $userData = clone $contributor;
            $userData->location = $location;
            $this->setGETStubFor($userUri, $userData);
        }

        $this->setGETStubFor($contributorsUri, $contributors);
    }
}
