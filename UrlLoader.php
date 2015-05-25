<?php
/**
 * @link https://github.com/Paul-Zi/yii2-urlloader
 * @copyright Copyright (c) 2015 PaulZi (pavel.zimakoff@gmail.com)
 * @license MIT (https://github.com/Paul-Zi/yii2-urlloader/blob/master/LICENSE)
 */

namespace paulzi\urlloader;

use yii\base\Component;
use paulzi\multicurl\MultiCurlRequest;
use paulzi\multicurl\MultiCurlQueue;

/**
 * UrlLoader component. Multithreaded downloader links.
 * @author PaulZi (pavel.zimakoff@gmail.com)
 */
class UrlLoader extends Component
{
    /**
     * @var int threads count
     */
    public $threads      = 2;

    /**
     * @var int the number of retries
     */
    public $retry        = 0;

    /**
     * @var int max follow redirects
     */
    public $maxRedirects = 2;

    /**
     * @var int connect timeout, sec
     */
    public $timeout      = 30;


    /**
     * Return array of headers for requests
     * @return array
     */
    protected function getBaseHeaders()
    {
        return [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Encoding: gzip, deflate',
            'Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3',
            'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:37.0) Gecko/20100101 Firefox/37.0',
        ];
    }

    /**
     * Execute url loader process
     * @param string|string[]|MultiCurlRequest[] $urls list of urls
     */
    public function run($urls)
    {
        if (is_string($urls)) {
            $urls = [$urls];
        }

        $requests = [];
        foreach ($urls as $i => $url) {
            if (is_object($url) && $url instanceof MultiCurlRequest) {
                $requests[] = $url;
                continue;
            }

            $params = [];
            if (is_array($url)) {
                $params = $url;
                $url    = $i;
            }
            $params['url'] = $url;

            $request = new MultiCurlRequest();
            $request->curl      = $this->getRequest($url);
            $request->params    = $params;
            $request->onBefore  = [$this, 'onBefore'];
            $request->onSuccess = [$this, 'onSuccess'];
            $request->onError   = [$this, 'onError'];
            $request->onAlways  = [$this, 'onAlways'];
            $request->onRetry   = [$this, 'onRetry'];
            $requests[] = $request;
        }

        $mr = new MultiCurlQueue();
        $mr->threads = $this->threads;
        $mr->retry   = $this->retry;
        $mr->run($requests);
    }

    /**
     * Make curl_init request
     * @param string $url
     * @return resource
     */
    protected function getRequest($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL,				$url);
        curl_setopt($curl, CURLOPT_HEADER,			false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,	true);
        curl_setopt($curl, CURLOPT_ENCODING,		'');
        curl_setopt($curl, CURLOPT_HTTPHEADER,		$this->getBaseHeaders());
        curl_setopt($curl, CURLOPT_TIMEOUT,			$this->timeout);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION,	$this->maxRedirects>0);
        curl_setopt($curl, CURLOPT_MAXREDIRS,		$this->maxRedirects);
        return $curl;
    }

    /**
     * On before send request event
     * @param MultiCurlRequest $request
     */
    public function onBefore($request)
    {
        $event = new UrlLoaderEvent();
        $event->request = $request;
        $this->trigger('before', $event);
    }

    /**
     * On success response event
     * @param MultiCurlRequest $request
     * @param array $response response parameters @see: curl_getinfo
     * @param string $content content body of response
     */
    public function onSuccess($request, $response, $content)
    {
        $event = new UrlLoaderEvent();
        $event->request  = $request;
        $event->response = $response;
        $event->content  = $content;
        $this->trigger('success', $event);
    }

    /**
     * On error response event
     * @param MultiCurlRequest $request
     * @param array $response response parameters @see: curl_getinfo
     * @param string $content content body of response
     * @param int $errCode CURLE_* error code
     * @param string $errMsg error message
     */
    public function onError($request, $response, $content, $errCode, $errMsg)
    {
        $event = new UrlLoaderErrorEvent();
        $event->request  = $request;
        $event->response = $response;
        $event->content  = $content;
        $event->errCode  = $errCode;
        $event->errMsg   = $errMsg;
        $this->trigger('error', $event);
    }

    /**
     * On always response event
     * @param MultiCurlRequest $request
     * @param array $response response parameters @see: curl_getinfo
     * @param string $content content body of response
     */
    public function onAlways($request, $response, $content)
    {
        $event = new UrlLoaderEvent();
        $event->request  = $request;
        $event->response = $response;
        $event->content  = $content;
        $this->trigger('always', $event);
    }

    /**
     * On retry request event
     * @param MultiCurlRequest $request
     * @param array $response response parameters @see: curl_getinfo
     * @param string $content content body of response
     * @param int $errCode CURLE_* error code
     * @param string $errMsg error message
     * @param int $retryIndex current retry index
     * @param int $retryTotal total retry count
     */
    public function onRetry($request, $response, $content, $errCode, $errMsg, $retryIndex, $retryTotal)
    {
        $event = new UrlLoaderRetryEvent();
        $event->request    = $request;
        $event->response   = $response;
        $event->content    = $content;
        $event->errCode    = $errCode;
        $event->errMsg     = $errMsg;
        $event->retryIndex = $retryIndex;
        $event->retryTotal = $retryTotal;
        $this->trigger('retry', $event);
    }
}