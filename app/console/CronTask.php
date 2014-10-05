<?php


class CronTask extends \Phalcon\CLI\Task
{

    private static $daily_commands = [
    ];

    private static $hourly_commands = [
    ];

    private static $minutely_commands = [
        ['task' => 'ext', 'action' => 'update']
    ];


    public function mainAction()
    {
        echo 'usage: ./app/console.sh cron actionName param1=value1 param2=value2' . PHP_EOL;
    }

    public function minutelyAction()
    {
        $this->_execute(self::$minutely_commands);
    }

    public function hourlyAction()
    {
        $this->_execute(self::$hourly_commands);
    }

    public function dailyAction()
    {
        $this->_execute(self::$daily_commands);
    }

    private function _execute($commands)
    {
        foreach ($commands as $command) {
            $this->dispatcher->forward($command);
        }
    }
}
