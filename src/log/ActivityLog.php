<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\log;


use Yihai;
use yihai\core\base\ModelOptions;
use yihai\core\db\ActiveRecord;
use yihai\core\web\Application;
use yii\grid\DataColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Class ActivityLog
 * @package yihai\core\log
 * @property int $id [int(11)]
 * @property string $action [varchar(255)]
 * @property string $model [varchar(255)]
 * @property string $type [varchar(20)]
 * @property string $user [varchar(255)]
 * @property int $time [int(11)]
 * @property string $ip [varchar(45)]
 * @property string $msg [blob]
 */
class ActivityLog extends ActiveRecord
{
    const TYPE_LOGIN = 'login';
    const TYPE_LOGOUT = 'logout';
    const TYPE_INSERT = 'insert';
    const TYPE_UPDATE = 'update';
    const TYPE_DELETE = 'delete';

    public static function tableName()
    {
        return '{{%sys_activity_logs}}';
    }

    public function rules()
    {
        return [
            [['action', 'user', 'time', 'type'], 'required'],
            [['action', 'model'], 'string', 'max' => 255],
            ['type', 'string', 'max' => 20],
            ['user', 'string', 'max' => 255],
            [['msg', 'ip'], 'safe'],
            ['time', 'integer'],
        ];
    }

    public function setMsg($msg)
    {
        $this->msg = Json::encode($msg, JSON_PRETTY_PRINT);
    }

    public function getMsg()
    {
        return Json::decode($this->msg);
    }

    /**
     * @param $type
     * @param null|\yii\base\Component|string|ActiveRecord $owner
     * @param array|string $msg
     * @param bool $save
     * @return ActivityLog|bool
     */
    public static function newLog($type, $owner = null, $msg = null, $save = true)
    {

        $log = new static();
        $log->type = $type;
        if(Yihai::$app instanceof Application) {
            if (Yihai::$app->user->isGuest)
                $log->user = '0|guest|guest';
            else {
                $user = Yihai::$app->user->identity->model->id . '|' . Yihai::$app->user->identity->model->group . '|' . Yihai::$app->user->identity->model->username;
                $log->user = $user;
            }
            $log->ip = Yihai::$app->request->getRemoteIP();
        }else{
            $log->user = '-1|console|console';
            $log->ip = '127.0.0.1';
        }
        if (Yihai::$app->controller && Yihai::$app->controller->action)
            $log->action = Yihai::$app->controller->action->getUniqueId();
        else{
            $log->action = '__start_session';
        }
        $log->time = time();

        if ($owner) {
            if ($owner instanceof ActiveRecord || $owner instanceof \yii\base\Component) {
                $log->model = $owner->className();
                if (strlen($log->model) > 255 && $owner instanceof ActiveRecord)
                    $log->model = $owner->classNameSort();
            } elseif (is_string($owner))
                $log->model = $owner;
        }
        if ($msg != null) {
            $log->setMsg($msg);
        }
        if ($save) {
            return $log->save();
        } else {
            return $log;
        }
    }

    public function filterRules()
    {
        return [
            ['id', 'integer'],
            ['action', 'safe'],
            ['model', 'safe'],
            ['type', 'safe'],
            ['user', 'safe'],
            ['time', 'safe'],
            ['msg', 'safe'],
            ['ip', 'safe'],
        ];
    }

    /**
     * @param \yii\db\ActiveQuery|\yii\db\QueryInterface $query
     * @param \yihai\core\base\FilterModel|static $filterModel
     */
    public function onSearch(&$query, $filterModel)
    {
        if ($filterModel->id) {
            $query->andFilterWhere(['like', 'id', $filterModel->id]);
        }
        if ($filterModel->action) {
            $query->andFilterWhere(['like', 'action', $filterModel->action]);
        }
        if ($filterModel->model) {
            if ($filterModel->model == 'NULL')
                $query->andWhere(['model' => null]);
            else
                $query->andWhere(['model' => $filterModel->model]);
        }
        if ($filterModel->type) {
            $query->andFilterWhere(['type' => $filterModel->type]);
        }
        if ($filterModel->user) {
            $query->andFilterWhere(['like', 'user', $filterModel->user]);
        }
        if ($filterModel->time) {
            $query->andFilterWhere(['date(FROM_UNIXTIME(`time`))' => $filterModel->time]);
        }
        if ($filterModel->msg) {
            $query->andFilterWhere(['like', 'msg', $filterModel->msg]);
        }
        if ($filterModel->ip) {
            $query->andFilterWhere(['like', 'ip', $filterModel->ip]);
        }
    }

    public static function getTypeDropdown()
    {
        return ArrayHelper::map(static::find()->select('type')->distinct()->all(), 'type', 'type');
    }

    public static function getModelDropdown()
    {
        return ArrayHelper::map(static::find()->select('model')->distinct()->all(), function ($model) {
            if ($model->model) return $model->model;
            return 'NULL';
        }, function ($model) {
            if ($model->model) return $model->model;
            return 'NULL';
        });
    }

    protected function _options()
    {
        return new ModelOptions([
            'baseTitle' => 'Activity Log',
            'actionCreate' => false,
            'actionUpdate' => false,
            'actionDelete' => false,
            'gridViewCheckboxColumn' => false,
            'gridColumnData' => [
                'id',
                [
                    'attribute' => 'type',
                    'filter' => static::getTypeDropdown()
                ],
                'action',
                [
                    'attribute' => 'model',
                    'filter' => static::getModelDropdown()
                ],
                'user',
                [
                    'attribute' => 'time',
                    'format' => 'datetime',
                    'filterInputOptions' => [
                        'placeholder' => 'Ex: YYYY-MM-DD'
                    ]
                ],
                'ip'
            ],
            'detailViewData' => [
                'id',
                'type',
                'action',
                'model',
                [
                    'attribute' => 'user',
                    'format' => 'raw',
                    'value' => function ($model) {
                        $user = explode('|', $model->user);
                        $user_html = [];
                        if (isset($user[0]))
                            $user_html[] = 'ID: ' . $user[0];
                        if (isset($user[1]))
                            $user_html[] = 'group: ' . $user[1];
                        if (isset($user[2]))
                            $user_html[] = 'username: ' . $user[2];
                        return nl2br(implode("\n", $user_html));
                    }
                ],
                'time:datetime',
                'ip'
            ],
            'gridDataProvider' => [
                'pagination' => [
                    'pageSize' => 50
                ]
            ],
            'detailViewCustom' => [
                'Data' => [
                    [
                        'attribute' => 'msg',
                        'value' => function ($model) {
                            return '<pre><code>' . $model->msg . '</code></pre>';
                        },
                        'format' => 'raw',
                        'label' => ''
                    ]
                ]
            ]
        ]);
    }
}