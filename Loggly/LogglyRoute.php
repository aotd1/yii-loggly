<?php

/**
 * Log router for Loggly - Cloud Based Log Management Service
 * @author Alexey Ashurok <work@aotd.ru>
 * @link http://github.com/aotd1/yii-loggly
 * @link http://loggly.com/
 */
class LogglyRoute extends CLogRoute
{

    /* @var string */
    public $inputKey;

    /* @var bool */
    public $finishRequest = true;

    /* @var string */
    public $url = 'http://logs-01.loggly.com/inputs/';

    /* @var string */
    public $cert;

    /* @var resource */
    private $curl;

    public function init()
    {
        if (!is_string($this->inputKey) || strlen($this->inputKey) !== 36) {
            throw new CException("Loggly key '$this->inputKey' must be a valid 36 character string");
        }
    }

    /**
     * @param string $message
     * @param string $level
     * @param string $category
     * @param int $time
     * @return array
     */
    protected function formatLogMessage($message, $level, $category, $time)
    {
        return array('level' => $level, 'category' => $category, 'message' => $message);
    }

    /**
     * @return resource
     */
    private function initCurl()
    {
        if ($this->curl !== null) {
            return $this->curl;
        }

        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_URL, $this->url . $this->inputKey . "/tag/http/");
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('application/x-www-form-urlencoded'));
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 5);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_POST, 1);

        return $this->curl;
    }

    /**
     * @param array $logs
     */
    protected function processLogs($logs)
    {
        if ($this->finishRequest && function_exists('fastcgi_finish_request')) {
            session_write_close();
            fastcgi_finish_request();
        }

        $ch = $this->initCurl();
        foreach ($logs as $log) {
            $data = json_encode($this->formatLogMessage($log[0], $log[1], $log[2], $log[3]), JSON_FORCE_OBJECT);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $ret = curl_exec($ch);
        }
    }

    public function __destruct()
    {
        if ($this->curl !== null) {
            curl_close($this->curl);
        }
    }

}
