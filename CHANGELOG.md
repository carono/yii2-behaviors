0.3.4
=====
* Добавлена настройка authManager для \carono\yii2behaviors\UrlBehavior

0.3.3
=====
* Исправлена ошибка в UrlBehavior, если использовать через консоль, сделана проверка на существование компонента user

0.3.2
=====
* AuthorBehavior помечен как устаревший, использовать \yii\behaviors\BlameableBehavior
* Исправлена ошибка, если в params передается аттрибут, значение которого равно null

0.3.1
=====
* UrlBehavior. Параметр url теперь принимает closure function ($model, $params). Параметры в функции необходимо мержить вручную
[
	'view',
    'url' => function ($model, $params) {
		return $model->is_foo ? ['/foo/view'] + $params : ['/bar/view'] + $params;
    },
    'params' => ['id']
]