<?php
namespace mcorten87\rabbitmq_api\tests\jobs;

use mcorten87\rabbitmq_api\jobs\JobUserList;
use mcorten87\rabbitmq_api\objects\User;
use PHPUnit\Framework\TestCase;

class JobUseListTest extends TestCase
{
    /**
     * Tests if the dependency injection in the constructor works
     */
    public function test_dependencyInjection() {
        $user = new User('test');

        $job = new JobUserList();
        $job->setUser($user);

        $this->assertEquals($user, $job->getUser());
    }
}
