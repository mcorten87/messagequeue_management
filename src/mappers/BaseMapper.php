<?php

namespace mcorten87\rabbitmq_api\mappers;

use GuzzleHttp\Client;
use mcorten87\rabbitmq_api\jobs\JobBase;
use mcorten87\rabbitmq_api\MqManagementConfig;
use mcorten87\rabbitmq_api\objects\MapResult;
use mcorten87\rabbitmq_api\objects\Method;
use mcorten87\rabbitmq_api\objects\Url;

abstract class BaseMapper
{
    /** @var MqManagementConfig */
    protected $config;

    public function __construct(MqManagementConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @param JobBase $job
     * @return MapResult
     */
    public function map(JobBase $job) : MapResult {
        return new MapResult(
            $this->mapMethod($job),
            $this->mapUrl($job),
            $this->mapConfig($job)
        );
    }

    abstract protected function mapMethod(JobBase $job) : Method;

    abstract protected function mapUrl(JobBase $job) : Url;

    protected function mapConfig(JobBase $job) : array {
        return [
            'auth'      =>  array($this->config->getUser(), $this->config->getPassword()),
            'headers'   =>  ['content-type' => 'application/json'],
        ];
    }
}
