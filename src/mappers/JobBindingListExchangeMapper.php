<?php
declare(strict_types=1);
namespace mcorten87\rabbitmq_api\mappers;

use mcorten87\rabbitmq_api\exceptions\WrongArgumentException;
use mcorten87\rabbitmq_api\jobs\JobBase;
use mcorten87\rabbitmq_api\jobs\JobBindingListExchange;
use mcorten87\rabbitmq_api\objects\Method;
use mcorten87\rabbitmq_api\objects\Url;

class JobBindingListExchangeMapper extends BaseMapper
{

    /**
     * @return Method
     */
    protected function mapMethod() : Method
    {
        return new Method(Method::GET);
    }

    /**
     * @param JobBase $job
     * @return Url
     * @throws WrongArgumentException
     */
    protected function mapUrl(JobBase $job) : Url
    {
        if (!$job instanceof JobBindingListExchange) {
            throw new WrongArgumentException($job, JobBindingListExchange::class);
        }

        $url = 'exchanges';
        $url .= sprintf('/%1$s', urlencode((string)$job->getVirtualHost()));
        $url .= sprintf('/%1$s', urlencode((string)$job->getExchangeName()));
        $url .= '/bindings/source';
        return new Url($url);
    }
}
