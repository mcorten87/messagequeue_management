<?php
declare(strict_types=1);
namespace mcorten87\rabbitmq_api\mappers;

use mcorten87\rabbitmq_api\jobs\JobBase;
use mcorten87\rabbitmq_api\jobs\JobBindingListAll;
use mcorten87\rabbitmq_api\jobs\JobConnectionListAll;
use mcorten87\rabbitmq_api\objects\Method;
use mcorten87\rabbitmq_api\objects\Url;
use mcorten87\rabbitmq_api\services\MqManagementConfig;

class JobConnectionListAllMapper extends BaseMapper
{

    /**
     * @return Method
     */
    protected function mapMethod() : Method
    {
        return new Method(Method::GET);
    }

    /**
     * @param JobConnectionListAll $job
     * @return Url
     */
    protected function mapUrl(JobBase $job) : Url
    {
        $url = 'connections';
        return new Url($url);
    }
}
