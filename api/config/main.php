<?php

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'), require(__DIR__ . '/../../common/config/params-local.php'), require(__DIR__ . '/params.php'), require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'api\controllers',
    'components' => [
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                //'host' => 'mail.tollr.world',
                //'host' =>'*.prod.sin2.secureserver.net',
                'host' => 'smtp.elasticemail.com',
                'username' => 'donotreply@tollr.world',
                //'password' => 'donotreply@t0llrw0rld',
                'password' => '12a06ae3-70fd-4232-841b-8ca6bd6b8e9c',
                'port' => '2525',
                'encryption' => 'tls',
            ],
        ],
        'request' => [
            'class' => 'common\components\Request',
            'web' => '/api/web',
            'adminUrl' => '/api'
        ],
        'user' => [
            'identityClass' => 'api\models\User',
            #           'enableAutoLogin' => false,
            'enableSession' => false,
            'loginUrl' => null,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'user',
//                    'pluralize' => false,
//                    'extraPatterns' => [
//                        'POST login' => 'login',
//                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['2119/u' => 'user'],
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST u/<id>' => 'update', 'GET d/<id>' => 'delete', 'POST l' => 'login', 'POST ch' => 'changepassword', 'POST re' => 'resetpassword', 'POST sre' => 'sendresetpassword', 'POST ud' => 'userdetails', 'POST udde' => 'userdetailsdelete', 'POST cin' => 'cashin', 'POST otpv' => 'verifyotp', 'POST uep' => 'extrapayments', 'POST exceptionapi' => 'exceptionapi', 'GET pingcheck' => 'pingcheck'
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['2119/t' => 'toll'],
                    'pluralize' => false,
                    //'pattern' => '2001',
                    'extraPatterns' => [
                        'POST cmp' => 'createmonthlypass', 'GET mpl/<id>' => 'montlypasslist', 'POST mpc' => 'monthlypasscost', 'POST c1' => 'create1', 'POST sw' => 'sampleweb', 'POST calternative' => 'create01122015', 'GET tl/<id>' => 'tollslist', 'POST rt' => 'regiontolls'
                    ]
                    //'route' => 'post/index',
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['2119/pt' => 'pytm'],
                    'pluralize' => false,
                    //'pattern' => '2001',
                    'extraPatterns' => [
                        "POST v" => 'vali', "GET v" => 'vali', "GET cit" => "citrus", "POST creturnurl" => "creturnurl", "POST cit" => "citrus", "GET creturnurl" => "creturnurl", "GET careturnurl" => "careturnurl", "GET cits" => "citruss", "POST creturnurls" => "creturnurls", "POST cits" => "citruss", "GET creturnurls" => "creturnurls",
                    ]
                    //'route' => 'post/index',
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['2119/uv' => 'uservehicle'],
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST u/<id>' => 'update', 'POST uvde' => 'delete',
                    ]
                    //'route' => 'post/index',
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['2119/tr' => 'trip'],
                    'pluralize' => false,
                    //'pattern' => '2001',
                    'extraPatterns' => [
                        'POST geo' => 'geofence', 'POST ulp' => 'userlogpath', 'GET ctr/<id>' => 'canceltrip', 'POST c1' => 'create1', 'POST rt' => 'repeattrip', 'GET th/<id>' => 'history', 'GET tf/<id>' => 'favourite', 'POST tfu' => 'favupdate', 'POST cro' => 'commonroutes', 'POST pendingtrip' => 'pendingtrip', 'POST tripdetails' => 'triphistorydetails', 'POST tredit' => 'edit', 'GET rhis' => 'tripreport',
                    ]
                    //'route' => 'post/index',
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['2019/t' => 'toller'],
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST s' => 'status', 'POST l' => 'signin', 'GET BS/<id>' => 'boothsides', 'GET B/<id>' => 'booths', 'POST bsi' => 'boothsign', 'POST se/<id>' => 'search', 'POST vioadd' => 'violationadd', 'POST searchvech' => 'searchvehical', 'GET vehicaltypes' => 'vechicaltypes', 'POST list/<id>' => 'list', 'GET toll/<id>' => 'tolldetails', 'POST auth' => 'authentic'
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['2019/ps' => 'poss'],
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST s' => 'status', 'POST l' => 'signin', 'GET BS/<id>' => 'boothsides', 'GET B/<id>' => 'booths', 'POST bsi' => 'boothsign', 'POST se/<id>' => 'search', 'POST vioadd' => 'violationadd', 'POST searchvech' => 'searchvehical', 'GET vehicaltypes' => 'vehicaltypes', 'POST list/<id>' => 'list', 'POST vs' => 'vserach', 'POST updtd' => 'updtripdetails'
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['2119/cc' => 'cards'],
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST clist' => 'cards',
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['2319/wua' => 'webuserapi'],
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST th/<id>' => 'history', 'POST tripdetails' => 'triphistorydetails', 'POST l' => 'login', 'POST vl/<id>' => 'vehiclelist', 'POST fav/<id>' => 'favourite', 'POST mp/<id>' => 'monthly-pass',
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['2419/hra' => 'historyapi'],
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST r' => 'reports'
                    ]
                ],
                /*[
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['2019/t' => 'toller'],
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST s' => 'status','POST l' => 'signin','GET BS/<id>' => 'boothsides', 'GET B/<id>' => 'booths', 'POST bsi' => 'boothsign','POST se/<id>' => 'search',
                    ]
                ],*/
            ],
        ],
        'response' => [
            'class' => 'yii\web\Response',
            'on beforeSend' => function ($event) {
                $request_get = Yii::$app->request->get('suppress_response_code');
                $response = $event->sender;
                if ($response->data !== null && !empty($request_get)) {
                    $response->data = [
                        'success' => $response->isSuccessful,
                        'data' => $response->data,
                    ];
                    $response->statusCode = 200;
                }
            },
        ],
    ],
    'params' => $params,
];
