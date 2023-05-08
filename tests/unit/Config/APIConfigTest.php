<?php
declare(strict_types=1);

namespace unit\Config;

use PHPUnit\Framework\TestCase;
use Sellvation\OneWelcome\Config\APIConfig;
use Sellvation\OneWelcome\Config\APIConfigInterface;

class APIConfigTest extends TestCase
{
    /**
     * @var APIConfigInterface
     */
    private $config;

    public function setup(): void
    {
        $this->config = new APIConfig('url', '123', 'secret', 'scope', 'username', 'password');
    }

    public function testShouldReturnCorrectValuesWhenCreatingNewConfigObject(): void
    {
        $this->assertEquals('url', $this->config->getAuthenticationURL());
        $this->assertEquals('123', $this->config->getClientId());
        $this->assertEquals('secret', $this->config->getClientSecret());
        $this->assertEquals('scope', $this->config->getScope());
        $this->assertEquals('username', $this->config->getUsername());
        $this->assertEquals('password', $this->config->getPassword());
    }

    public function testShouldReturnDefaultGrandTypeWhenNoneIsSet(): void
    {
        $this->assertEquals('password', $this->config->getGrantType());
    }

    public function testShouldReturnCorrectGrandTypeWhenSettingNewGrandType(): void
    {
        $this->config->setGrantType('foobar');
        $this->assertEquals('foobar', $this->config->getGrantType());
    }
}
