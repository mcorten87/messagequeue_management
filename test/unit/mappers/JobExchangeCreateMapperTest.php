<?php
namespace mcorten87\rabbitmq_api\test\unit\mappers;

use mcorten87\rabbitmq_api\exceptions\WrongArgumentException;
use mcorten87\rabbitmq_api\jobs\JobExchangeCreate;
use mcorten87\rabbitmq_api\jobs\JobQueueListVirtualHost;
use mcorten87\rabbitmq_api\mappers\JobExchangeCreateMapper;
use mcorten87\rabbitmq_api\MqManagementConfig;
use mcorten87\rabbitmq_api\objects\ExchangeName;
use mcorten87\rabbitmq_api\objects\Method;
use mcorten87\rabbitmq_api\objects\Password;
use mcorten87\rabbitmq_api\objects\Url;
use mcorten87\rabbitmq_api\objects\User;
use mcorten87\rabbitmq_api\objects\VirtualHost;
use PHPUnit\Framework\TestCase;

class JobExchangeCreateMapperTest extends TestCase
{

    /** @var  MqManagementConfig */
    private $config;

    /**
     * MqManagementFactoryTest constructor.
     * setUp gets called after the datapProvicers, in this case it is not good enough
     * @param null $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        $url = new Url('http://localhost:15672/api/');
        $user = new User('user');
        $password = new Password('password');

        $this->config = new MqManagementConfig($user, $password, $url);

        parent::__construct($name, $data, $dataName);
    }

    /**
     * @expectedException \mcorten87\rabbitmq_api\exceptions\WrongArgumentException
     */
    public function testIfExceptionIsThrownWhenWrongArgumentIsGiven()
    {
        $job = new JobQueueListVirtualHost(new VirtualHost('/'));

        $mapper = new JobExchangeCreateMapper($this->config);
        $mapper->map($job);
    }


    public function testBasicJob()
    {
        $virtualHost = new VirtualHost('/t!@#$%^&*()-=[]\'\;/.,mest/');
        $exchangeName = new ExchangeName('t!@#$%^&*()-=[]\'\;/.,mest');
        $job = new JobExchangeCreate($virtualHost, $exchangeName);

        $mapper = new JobExchangeCreateMapper($this->config);
        $mapResult = $mapper->map($job);

        $this->assertEquals(Method::PUT, $mapResult->getMethod()->getValue());
        $this->assertEquals('exchanges/'.urlencode($virtualHost).'/'.urlencode($exchangeName), $mapResult->getUrl());
    }
}
