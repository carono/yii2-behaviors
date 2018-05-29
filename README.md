```php   
 public function behaviors()
    {
        return [
            'urls' => [
                'class' => UrlBehavior::class,
                'rules' => [
                    [
                        'view-content',
                        'url' => function ($model, $params) {
                            return $model->is_main ? ['/'] : ['/menu/view'] + $params;
                        },
                        'params' => ['id']
                    ],
                    ['view', 'url' => ['/admin/menu/view'], 'params' => ['id'], 'role' => ['root']],
                ]
            ]
        ];
    }
```

function getUrl($action, $asString = false)

$url = $model->getUrl('view'); // array ['/admin/menu/view','id'=>1]