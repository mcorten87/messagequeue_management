<?php
declare(strict_types=1);
namespace mcorten87\rabbitmq_api\mappers;

use mcorten87\rabbitmq_api\jobs\JobBase;
use mcorten87\rabbitmq_api\jobs\JobQueueCreate;
use mcorten87\rabbitmq_api\objects\Method;
use mcorten87\rabbitmq_api\objects\Url;

class JobQueueCreateMapper extends BaseMapper
{
    protected function mapMethod() : Method
    {
        return new Method(Method::PUT);
    }

    /**
     * @param JobQueueCreate $job
     * @return Url
     */
    protected function mapUrl(JobBase $job) : Url
    {
        return new Url(
            'queues/'
            .urlencode((string)$job->getVirtualHost())
            .'/'.urlencode((string)$job->getQueueName())
        );
    }

    /**
     * @param JobQueueCreate $job
     * @return array
     */
    protected function mapConfig(JobBase $job) : array
    {
        $body = [
            'auto_delete'   => $job->isAutoDelete(),
            'durable'       => $job->isDurable(),
            'arguments'     => []
        ];

        foreach ($job->getArguments() as $argument) {
            $body['arguments'][$argument->getArgumentName()] = $argument->getValue();
        };

        return array_merge(parent::mapConfig($job), [
            'json'      =>  $body,
        ]);
    }
}
