# UrlLoader
Multithreaded downloader links for Yii2.

## Instalation
The preferred way to install this extension is through composer.

Add
```"paulzi/yii2-urlloader": "*"```
to the require section and
```
{
    "type":"git",
    "url":"http://github.com/Paul-Zi/yii2-urlloader"
}
```
to the repository section of your composer.json file. Then run the command
```composer update```

## Usage
To use this extension,  simply add the following code in your application configuration:
```php
return [
    //....
    'components' => [
        'urlLoader' => [
            'class' => 'paulzi\urlloader\UrlLoader',
            // 'on success'
        ],
    ],
];
```

Or you can create separate object:
```php
$urlLoader = Yii::createObject([
    'class' => 'paulzi\urlloader\UrlLoader',
    'timeout' => 90,
]);
```

You can then usage:
```php
Yii::$app->urlloader->on('success', function($event){ var_dump($event->content); });
Yii::$app->urlloader->run([
    'https://example.com',
    'https://example.com/test' => ['param1' => 'test'],
]);
```