0.3.1
=====
-UrlBehavior. Параметр url теперь принимает closure function ($model, $params). Параметры в функции необходимо мержить вручную
[
	'view',
    'url' => function ($model, $params) {
		return $model->is_foo ? ['/foo/view'] + $params : ['/bar/view'] + $params;
    },
    'params' => ['id']
]