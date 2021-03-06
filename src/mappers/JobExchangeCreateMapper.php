<?php
declare(strict_types=1);
namespace mcorten87\rabbitmq_api\mappers;

use mcorten87\rabbitmq_api\exceptions\WrongArgumentException;
use mcorten87\rabbitmq_api\jobs\JobBase;
use mcorten87\rabbitmq_api\jobs\JobExchangeCreate;
use mcorten87\rabbitmq_api\objects\Method;
use mcorten87\rabbitmq_api\objects\Url;

class JobExchangeCreateMapper extends BaseMapper
{
    protected function mapMethod() : Method
    {
        return new Method(Method::PUT);
    }

    /**
     * @param JobBase $job
     * @return Url
     * @throws WrongArgumentException
     */
    protected function mapUrl(JobBase $job) : Url
    {
        if (!$job instanceof JobExchangeCreate) {
            throw new WrongArgumentException($job, JobExchangeCreate::class);
        }

        return new Url(
            'exchanges/'
            .urlencode((string)$job->getVirtualHost())
            .'/'.urlencode((string)$job->getExchangeName())
        );
    }

    /**
     * @param JobBase $job
     * @return array
     * @throws WrongArgumentException
     */
    protected function mapConfig(JobBase $job) : array
    {
        if (!$job instanceof JobExchangeCreate) {
            throw new WrongArgumentException($job, JobExchangeCreate::class);
        }

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
