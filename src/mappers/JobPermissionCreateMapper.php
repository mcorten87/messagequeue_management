<?php
declare(strict_types=1);
namespace mcorten87\rabbitmq_api\mappers;


use mcorten87\rabbitmq_api\jobs\JobBase;
use mcorten87\rabbitmq_api\jobs\JobPermissionCreate;
use mcorten87\rabbitmq_api\objects\Method;
use mcorten87\rabbitmq_api\objects\Url;

class JobPermissionCreateMapper extends BaseMapper
{

    protected function mapMethod() : Method {
        return new Method(Method::PUT);
    }

    /**
     * @param JobPermissionCreate $job
     * @return Url
     */
    protected function mapUrl(JobBase $job) : Url {
        return new Url('permissions/'
            .urlencode((string)$job->getVirtualHost())
            .'/'.urlencode((string)$job->getUser())
        );
    }

    /**
     * @param JobPermissionCreate $job
     * @return array
     */
    protected function mapConfig(JobBase $job) : array {
        $body = [
            'configure'     => '.*',
            'write'         => '.*',
            'read'          => '.*',
        ];

        return array_merge(parent::mapConfig($job), [
            'json'      =>  $body,
        ]);
    }
}
