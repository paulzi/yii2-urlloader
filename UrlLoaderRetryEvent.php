<?php
/**
 * @link https://github.com/Paul-Zi/yii2-urlloader
 * @copyright Copyright (c) 2015 PaulZi (pavel.zimakoff@gmail.com)
 * @license MIT (https://github.com/Paul-Zi/yii2-urlloader/blob/master/LICENSE)
 */

namespace paulzi\urlloader;

/**
 * UrlLoaderRetryEvent
 * @author PaulZi (pavel.zimakoff@gmail.com)
 */
class UrlLoaderRetryEvent extends UrlLoaderErrorEvent
{
    /**
     * @var int current retry index
     */
    public $retryIndex;

    /**
     * @var int total retry count
     */
    public $retryTotal;
}