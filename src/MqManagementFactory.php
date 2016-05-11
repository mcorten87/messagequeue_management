<?php

namespace mcorten87\messagequeue_management;


use mcorten87\messagequeue_management\exceptions\NoMapperForJob;
use mcorten87\messagequeue_management\jobs\JobBase;
use mcorten87\messagequeue_management\jobs\JobQueueCreate;
use mcorten87\messagequeue_management\jobs\JobQueueDelete;
use mcorten87\messagequeue_management\jobs\JobQueueList;
use mcorten87\messagequeue_management\jobs\JobQueuesList;
use mcorten87\messagequeue_management\jobs\JobUserList;
use mcorten87\messagequeue_management\jobs\JobVirtualHostCreate;
use mcorten87\messagequeue_management\jobs\JobVirtualHostDelete;
use mcorten87\messagequeue_management\jobs\JobVirtualHostList;
use mcorten87\messagequeue_management\jobs\JobVirtualHostsList;
use mcorten87\messagequeue_management\mappers\BaseMapper;
use mcorten87\messagequeue_management\mappers\JobVirtualHostCreateMapper;
use mcorten87\messagequeue_management\objects\JobResult;
use mcorten87\messagequeue_management\objects\Password;
use mcorten87\messagequeue_management\objects\QueueName;
use mcorten87\messagequeue_management\objects\User;
use mcorten87\messagequeue_management\objects\VirtualHost;
use mcorten87\messagequeue_management\services\JobService;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class MqManagementFactory
{
    const HTTPCLIENT = 'HttpClient';

    const SERVICE_JOB = 'JobService';

    const JOB_RESULT = 'JobResult';

    const JOB_CREATEVHOST = 'JobCreateVhost';
    const JOB_CREATEVHOSTMAPPER = 'JobCreateVhostMapper';

    const JOB_LISTVHOSTS = 'JobListVhosts';
    const JOB_LISTVHOSTSMAPPER = 'JobListVhostsMapper';

    const JOB_LISTVHOST = 'JobListVhost';
    const JOB_LISTVHOSTMAPPER = 'JobListVhostMapper';

    const JOB_DELETEVHOSTS = 'JobDeleteVhosts';
    const JOB_DELETEVHOSTSMAPPER = 'JobDeleteVhostsMapper';

    const JOB_LISTQUEUES = 'JobListQueues';
    const JOB_LISTQUEUESMAPPER = 'JobListQueuesMapper';

    const JOB_LISTQUEUE = 'JobListQueue';
    const JOB_LISTQUEUEMAPPER = 'JobListQueueMapper';

    const JOB_CREATEQUEUE = 'JobCreateQueue';
    const JOB_CREATEQUEUEMAPPER = 'JobCreateQueueMapper';

    const JOB_DELETEQUEUEMAPPER = 'JobDeleteQueueMapper';

    const JOB_LISTUSERMAPPER = 'JobDeleteQueueMapper';


    /** @var MqManagementConfig */
    private $config;

    public function getConfig() : MqManagementConfig {
        return $this->config;
    }

    /**
     * @var ContainerBuilder
     */
    private $container;


    public function __construct()
    {
        $container = new ContainerBuilder();
        $this->container = $container;
    }

    /**
     * @param MqManagementConfig $config
     */
    public function register(MqManagementConfig $config) {
        $this->config = $config;

        $this->container->register(self::HTTPCLIENT,'GuzzleHttp\Client');

        $this->container->register(self::SERVICE_JOB,'mcorten87\messagequeue_management\services\JobService')
            ->addArgument($this)
            ->addArgument($this->container->get(self::HTTPCLIENT))
        ;

        $this->registerJobs();
    }

    protected function registerJobs() {
        // results
        $definition = new Definition('mcorten87\messagequeue_management\objects\JobResult');
        $definition->setShared(false);
        $this->container->setDefinition(self::JOB_RESULT, $definition);

        // virtual hosts
        $this->container->register(self::JOB_LISTVHOSTSMAPPER, 'mcorten87\messagequeue_management\mappers\JobVirtualHostsListMapper')
            ->addArgument($this->config)
        ;

        $this->container->register(self::JOB_LISTVHOSTMAPPER, 'mcorten87\messagequeue_management\mappers\JobVirtualHostListMapper')
            ->addArgument($this->config)
        ;

        $this->container->register(self::JOB_CREATEVHOSTMAPPER, 'mcorten87\messagequeue_management\mappers\JobVirtualHostCreateMapper')
            ->addArgument($this->config)
        ;

        $this->container->register(self::JOB_DELETEVHOSTSMAPPER, 'mcorten87\messagequeue_management\mappers\JobVirtualHostDeleteMapper')
            ->addArgument($this->config)
        ;

        // queues
        $this->container->register(self::JOB_LISTQUEUESMAPPER, 'mcorten87\messagequeue_management\mappers\JobQueuesListMapper')
            ->addArgument($this->config)
        ;

        $this->container->register(self::JOB_LISTQUEUEMAPPER, 'mcorten87\messagequeue_management\mappers\JobQueueListMapper')
            ->addArgument($this->config)
        ;

        $this->container->register(self::JOB_CREATEQUEUEMAPPER, 'mcorten87\messagequeue_management\mappers\JobQueueCreateMapper')
            ->addArgument($this->config)
        ;

        $this->container->register(self::JOB_DELETEQUEUEMAPPER, 'mcorten87\messagequeue_management\mappers\JobQueueDeleteMapper')
            ->addArgument($this->config)
        ;

        // users
        $this->container->register(self::JOB_LISTUSERMAPPER, 'mcorten87\messagequeue_management\mappers\JobUserListMapper')
            ->addArgument($this->config)
        ;


    }

    public function getJobResult($response) : JobResult {
        /** @var JobResult */
        $result = $this->container->get(self::JOB_RESULT);
        $result->setResponse($response);
        return $result;
    }

    /**
     * @return JobVirtualHostsList
     */
    public function getJobListVirtualHosts() : JobVirtualHostsList {
        return new JobVirtualHostsList();
    }

    /**
     * @param VirtualHost $vhost
     * @return JobVirtualHostList
     */
    public function getJobListVirtualHost(VirtualHost $virtualHost) : JobVirtualHostList {
        $job = new JobVirtualHostList($virtualHost);
        return $job;
    }

    /**
     * @param VirtualHost $vhost
     * @return JobVirtualHostCreate
     */
    public function getJobCreateVirtualHost(VirtualHost $virtualHost) : JobVirtualHostCreate {
        /** @var JobVirtualHostCreate $job */
        $job = new JobVirtualHostCreate($virtualHost);
        return $job;
    }

    public function getJobDeleteVirtualHost(VirtualHost $virtualHost) : JobVirtualHostDelete {
        /** @var JobVirtualHostDelete $job */
        $job = new JobVirtualHostDelete($virtualHost);
        return $job;
    }

    public function getJobListQueues(VirtualHost $virtualHost = null) : JobQueuesList {
        /** @var JobQueuesList $job */
        $job = new JobQueuesList();
        if ($virtualHost !== null) { $job->setVirtualhost($virtualHost); }
        return $job;
    }

    public function getJobListQueue(VirtualHost $virtualHost, QueueName $queueName) : JobQueueList {
        $job = new JobQueueList($virtualHost, $queueName);
        return $job;
    }

    public function getJobCreateQueue(VirtualHost $virtualHost, QueueName $queueName) : JobQueueCreate {
        $job = new JobQueueCreate($virtualHost, $queueName);
        return $job;
    }

    public function getJobDeleteQueue(VirtualHost $virtualHost, QueueName $queueName) : JobQueueDelete {
        $job = new JobQueueDelete($virtualHost, $queueName);
        return $job;
    }

    public function getJobListUser(User $user = null) : JobUserList {
        $job = new JobUserList();
        if ($user !== null) { $job->setUser($user); }
        return $job;
    }

    /**
     * Gets a mapper for the job, if non found it throws an NoMapperForJob exception
     *
     * @param JobBase $job
     * @return BaseMapper
     * @throws NoMapperForJob
     */
    public function getJobMapper(JobBase $job) : BaseMapper {
        switch ($job) {
            // virtual host
            case $job instanceof JobVirtualHostsList:
                return $this->container->get(self::JOB_LISTVHOSTSMAPPER);
                break;
            case $job instanceof JobVirtualHostList:
                return $this->container->get(self::JOB_LISTVHOSTMAPPER);
                break;
            case $job instanceof JobVirtualHostCreate:
                return $this->container->get(self::JOB_CREATEVHOSTMAPPER);
                break;
            case $job instanceof JobVirtualHostDelete:
                return $this->container->get(self::JOB_DELETEVHOSTSMAPPER);
                break;

            // queue
            case $job instanceof JobQueuesList:
                return $this->container->get(self::JOB_LISTQUEUESMAPPER);
                break;
            case $job instanceof JobQueueList:
                return $this->container->get(self::JOB_LISTQUEUEMAPPER);
                break;
            case $job instanceof JobQueueCreate:
                return $this->container->get(self::JOB_CREATEQUEUEMAPPER);
                break;
            case $job instanceof JobQueueDelete:
                return $this->container->get(self::JOB_DELETEQUEUEMAPPER);
                break;

            // user
            case $job instanceof JobUserList:
                return $this->container->get(self::JOB_LISTUSERMAPPER);
                break;
            default:
                throw new NoMapperForJob($job);
        }
    }

    /**
     * @return JobService
     */
    public function getJobService() : JobService {
        return $this->container->get(self::SERVICE_JOB);
    }
}
