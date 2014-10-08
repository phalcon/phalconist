<?php

ini_set('memory_limit', '1024M');
set_time_limit(0);
error_reporting(E_ALL);

require_once __DIR__ . '/../../vendor/autoload.php';

use Phalcon\CLI\Dispatcher;
use Phalcon\CLI\Task;
use Phalcon\DI\FactoryDefault\CLI;
use Phalcon\CLI\Console;

use Elastica\Client;
use Elastica\ScanAndScroll;
use Elastica\Search;
use Elastica\Document;


/**
 * Class backupTask
 *
 * @property \Phalcon\Logger\Adapter log
 * @property \Phalcon\CLI\Dispatcher dispatcher
 */
class backupTask extends Task
{

    /**
     * @var Client
     */
    private $client;

    /**
     * @var \Elastica\Index
     */
    private $elasticaIndex;

    /**
     * @var \Elastica\Type
     */
    private $elasticaType;

    private $elasticsearchHost = 'localhost';
    private $elasticsearchPort = 9200;

    private $fileName;

    private $sizePerShard = 100;

    private $expiryTime = '1m';

    private $folderName;

    private $restoreParams
        = [
            'steps' => 1000
        ];

    protected $indexName;

    protected $typeName;

    /**
     * До любых других действий
     */
    public function initialize()
    {

        $currentActionName = $this->dispatcher->getActiveMethod();

        $annotations = $this->annotations->getMethod(self::class, $currentActionName);

        if ($annotations->has('actionInfo')) {

            $annotation  = $annotations->get('actionInfo');
            $actionTitle = $annotation->getNamedArgument('name');

            $this->log->info('Запустили: {actionTitle}', ['actionTitle' => $actionTitle]);
        } else {
            $currentTaskName = $this->dispatcher->getTaskName();
            $this->log->info(
                'Запустили: {currentTaskName}::{currentActionName}',
                ['currentTaskName' => $currentTaskName, 'currentActionName' => $currentActionName]
            );

        }

        $this->indexName = $this->dispatcher->getParam('index', 'string', false);
        $this->typeName  = $this->dispatcher->getParam('type', 'string', false);

        if (!$this->indexName) {

            $this->log->error('Указание индекса является обязательным параметром');
            die;
        }

        $this->sizePerShard = $this->dispatcher->getParam('sizePerShard', 'int', false) ?: $this->sizePerShard;

        $this->elasticsearchHost = $this->dispatcher->getParam('host', 'string', false) ?: $this->elasticsearchHost;
        $this->elasticsearchPort = $this->dispatcher->getParam('port', 'int', false) ?: $this->elasticsearchPort;

        $connectParams = [
            'host' => $this->elasticsearchHost,
            'port' => $this->elasticsearchPort
        ];
        $this->client  = new Client($connectParams);


        try {

            $this->client->getStatus();
        } catch (\Elastica\Exception\Connection\HttpException $e) {

            $context = ['host' => $this->elasticsearchHost, 'port' => $this->elasticsearchPort];
            $this->log->error('Подключение к серверу elasticsearch отсутствует: http://{host}:{port}', $context);
            die;
        }

        $this->elasticaIndex = $this->client->getIndex($this->indexName);
        $this->elasticaType  = $this->elasticaIndex->getType($this->typeName);

    }

    /**
     * @actionInfo(name='Бэкап данных')
     */
    public function backupAction()
    {

        $this->folderName = __DIR__ . '/../../backup/';

        if (!$this->elasticaIndex->exists()) {

            $this->log->error('Индекс для бэкапа отсутствует: {indexName}', ['indexName' => $this->indexName]);
            return;
        }

        $this->checkFileName();
        $this->checkBackupFolder();

        $this->log->info(
            'Всё ок, бекапим {indexName} в {fileName}',
            ['indexName' => $this->indexName, 'fileName' => $this->fileName]
        );

        $this->log->info('Параметры бэкапа: sizePerShard={sizePerShard}', ['sizePerShard' => $this->sizePerShard]);

        $scanAndScroll = $this->getScanAndScroll();

        foreach ($scanAndScroll as $resultSet) {

            $buffer = [];

            /* @var \Elastica\ResultSet $resultSet */
            $results = $resultSet->getResults();

            foreach ($results as $result) {

                $item            = [];
                $item['_id']     = $result->getId();
                $item['_source'] = $result->getSource();
                $buffer[]        = json_encode($item, JSON_UNESCAPED_UNICODE);
            }

            $fileBody = implode(PHP_EOL, $buffer);

            if (file_put_contents($this->fileName, $fileBody, FILE_APPEND)) {

                $countDocuments = count($results);
                $this->log->info('Сохранили {countDocuments} записей', ['countDocuments' => $countDocuments]);

            } else {

                $this->log->error('Ошибка записи данных');
                die;
            }
        }
    }

    /**
     * @actionInfo(name='Восстановление данных')
     */
    public function restoreAction()
    {


        $backupFile = $this->dispatcher->getParam('fileName', 'string', false);

        if (!is_file($backupFile)) {

            $this->log->error('Файл {fileName} не найден', ['fileName' => $backupFile]);
            return false;
        }

        $documentsForSave = [];

        $i = 0;

        $handle = fopen($backupFile, 'r') or die('Не удалось получить хэндл');

        $context = ['indexName' => $this->indexName, 'typeName' => $this->typeName, 'fileName' => $backupFile];
        $this->log->info('Восстановление в {indexName} / {typeName} файла {fileName}', $context);

        if ($handle) {

            while (!feof($handle)) {

                $row        = fgets($handle);
                $decodedRow = json_decode($row, true);

                if (is_array($decodedRow)) {

                    $documentsForSave[] = new Document($decodedRow['_id'], $decodedRow['_source']);
                    $i++;
                    if ($i == $this->restoreParams['steps']) {

                        $this->saveInfoInElastic($documentsForSave);
                        $this->log->info('Сохранено документов: {count}', ['count' => $i]);
                        $i = 0;
                    }
                }
            }

            $this->saveInfoInElastic($documentsForSave);

            fclose($handle);
        }

    }

    /**
     * @param array $documentsForSave
     */
    private function saveInfoInElastic(array $documentsForSave)
    {
        if (count($documentsForSave) == 0) {

            return;
        }

        $resultUpdate = $this->elasticaType->addDocuments($documentsForSave);

        if (!$resultUpdate->isOk()) {

            $this->log->error('Ошибка записи документов');
            exit;
        }
    }

    /**
     * @return ScanAndScroll
     */
    protected function getScanAndScroll()
    {
        $search = new Search($this->client);
        $search->addIndex($this->elasticaIndex);

        // type не обязателен, бэкапить можно весь индекс со всеми типами
        if ($this->typeName) {

            $search->addType($this->elasticaType);
        }

        $scanAndScroll               = new ScanAndScroll($search);
        $scanAndScroll->sizePerShard = $this->sizePerShard;
        $scanAndScroll->expiryTime   = $this->expiryTime;

        return $scanAndScroll;
    }

    protected function checkFileName()
    {
        $this->fileName = sprintf(
            '%s%s-%s-%s-dump.json',
            $this->folderName,
            $this->indexName,
            ($this->typeName ? $this->typeName : 'all-types'),
            date('Y-m-d-H-i-s', time())
        );
    }

    protected function checkBackupFolder()
    {
        if (!is_dir($this->folderName) && mkdir($this->folderName, 0777, true)) {

            $this->log->info('Каталог бэкапа {folder} отсутствовал, создали его.', ['folder' => $this->folderName]);
        } elseif (is_dir($this->folderName) && is_writable($this->folderName)) {

            $this->log->info(
                'Каталог бэкапа {folder} уже существует и доступен для записи',
                ['folder' => $this->folderName]
            );
        } else {

            $this->log->error(
                'Каталог бэкапа {folder} отсутствует или недоступен для создания',
                ['folder' => $this->folderName]
            );

        }
    }
}

$di = new CLI();

$di['dispatcher'] = function () {

    $dispatcher = new Dispatcher;

    $dispatcher->setDefaultTask('Backup');
    $dispatcher->setDefaultAction('backup');

    return $dispatcher;
};

$di['log'] = function () {

    return new Phalcon\Logger\Adapter\Stream('php://stdout');
};

try {

    $console = new Console($di);

    $handleParams = [];
    array_shift($argv);

    foreach ($argv as $param) {
        list($name, $value) = explode('=', $param);
        $handleParams[$name] = $value;
    }

    $console->handle($handleParams);

} catch (\Phalcon\Exception $e) {

    echo $e->getMessage();
    exit(255);
}
