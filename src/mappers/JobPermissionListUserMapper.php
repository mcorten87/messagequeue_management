<?php
declare(strict_types=1);
namespace mcorten87\rabbitmq_api\mappers;


use mcorten87\rabbitmq_api\jobs\JobBase;
use mcorten87\rabbitmq_api\jobs\JobPermissionListAll;
use mcorten87\rabbitmq_api\jobs\JobPermissionListUser;
use mcorten87\rabbitmq_api\jobs\JobPermissionListVirtualHost;
use mcorten87\rabbitmq_api\objects\Method;
use mcorten87\rabbitmq_api\objects\Url;
use mcorten87\rabbitmq_api\services\MqManagementConfig;

class JobPermissionListUserMapper extends BaseMapper
{

    protected function mapMethod() : Method {
        return new Method(Method::GET);
    }

    /**
     * @param JobPermissionListAll|JobPermissionListVirtualHost $job
     * @return Url
     */
    protected function mapUrl(JobBase $job) : Url {
        if (!$job instanceof JobPermissionListUser) {
            throw new \RuntimeException('Invalid parameter type $job');
        }

        $url = 'users';
        $url .= '/'.urlencode((string)$job->getUser());
        $url .= '/permissions';
        return new Url($url);
    }
}
