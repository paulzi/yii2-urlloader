<?php
/**
 * @link https://github.com/Paul-Zi/yii2-urlloader
 * @copyright Copyright (c) 2015 PaulZi (pavel.zimakoff@gmail.com)
 * @license MIT (https://github.com/Paul-Zi/yii2-urlloader/blob/master/LICENSE)
 */

namespace paulzi\urlloader;

/**
 * UrlLoaderErrorEvent
 * @author PaulZi (pavel.zimakoff@gmail.com)
 */
class UrlLoaderErrorEvent extends UrlLoaderEvent
{
    /**
     * @var int CURLE_* error code
     */
    public $errCode;

    /**
     * @var string error message
     */
    public $errMsg;
}