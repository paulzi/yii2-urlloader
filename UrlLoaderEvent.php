<?php
/**
 * @link https://github.com/Paul-Zi/yii2-urlloader
 * @copyright Copyright (c) 2015 PaulZi (pavel.zimakoff@gmail.com)
 * @license MIT (https://github.com/Paul-Zi/yii2-urlloader/blob/master/LICENSE)
 */

namespace paulzi\urlloader;

use yii\base\Event;

/**
 * UrlLoaderEvent
 * @author PaulZi (pavel.zimakoff@gmail.com)
 */
class UrlLoaderEvent extends Event
{
    /**
     * @var \paulzi\multicurl\MultiCurlRequest request
     */
    public $request;

    /**
     * @var array response parameters @see: curl_getinfo
     */
    public $response;

    /**
     * @var string content body of response
     */
    public $content;
}