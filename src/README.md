# Amos Notify
Notify management.

### Installation
You need to require this package and enable the module in your configuration.

add to composer requirements in composer.json
```
"open20/amos-news": "dev-master",
```

or run command bash:
```bash
composer require "open20/amos-news:dev-master"
```

Enable the Notify modules in common modules-amos.php, add :
```php
 'notify' => [
	'class' => 'open20\amos\news\AmosNews',
 ],

```

add news migrations to console modules (console/config/migrations-amos.php):
```
'@vendor/open20/amos-notify/src/migrations'
```


The content is suitable to be used with cwh content management.
To do so:
- Activate cwh plugin
- Open cwh configuration wizard (admin privilege is required) url: <yourPlatformurl>/cwh/configuration/wizard
- search for news in content configuration section
- edit configuration of news and save

If tags are needed enable this module in "modules-amos.php" (backend/config folder in main project) in tag section.
After that, enable the trees in tag manager.

If platform uses report and/or comments and you want to enable News to be commented/to report a content, 
add the model to the configuration in modules-amos.php:

for reports: 

```
 'report' => [
     'class' => 'open20\amos\report\AmosReport',
     'modelsEnabled' => [
        .
        .
        'open20\amos\news\models\News', //line to add
        .
        .
     ]
     ],

```

### Configure contents for notification summary
- You have to configure the model in cwh/configuration/wizard
- If you want to personalize the views of the contents in the notification email summary you have to put the module in /common/config/modules-amos
```
 'news' => [
     'class' => 'open20\amos\news\AmosNews',
        'viewPathEmailSummary' => '@vendor/open20/amos-news/src/views/email/notify_summary'
        'viewPathEmailSummaryNetwork' => '@vendor/open20/amos-news/src/views/email/notify_summary_network'
     ]
     ],
```
* **viewPathEmailSummary** - view path for content in summary email
* **viewPathEmailSummaryNetwork** - view path for content the  inside a network (COmmunity) in summary email




### Configurable fields
* **$orderEmailSummary** - array
-Is used to define their order in the email summury
```php 
 public $orderEmailSummary = [
        'open20\amos\events\models\Event',
        'open20\amos\news\models\News',
        'open20\amos\partnershipprofiles\models',
        'open20\amos\discussioni\models\DiscussioniTopic',
        'open20\amos\sondaggi\models\Sondaggi',
    ];

```

