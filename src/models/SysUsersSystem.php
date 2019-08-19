<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\models;

use Yihai;
use yihai\core\base\ModelOptions;
use yihai\core\db\DataTrait;
use yihai\core\theming\Html;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%sys_users_system}}".
 *
 * @property int $id
 * @property string $fullname
 * @property string $created_by
 * @property int $created_at
 * @property string $updated_by
 * @property int $updated_at
 *
 */
class SysUsersSystem extends AbstractUserModel
{
    use DataTrait;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%sys_users_system}}';
    }

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            TimestampBehavior::class,
            BlameableBehavior::class
        ]);
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['fullname'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['fullname'], 'string', 'max' => 100],
            [['created_by', 'updated_by'], 'string', 'max' => 64],

            [['fullname'],'filter','filter'=>'\yii\helpers\Html::encode']
        ]);
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'id' => Yihai::t('yihai', 'ID'),
            'fullname' => Yihai::t('yihai', 'Fullname'),
            'created_by' => Yihai::t('yihai', 'Created By'),
            'created_at' => Yihai::t('yihai', 'Created At'),
            'updated_by' => Yihai::t('yihai', 'Updated By'),
            'updated_at' => Yihai::t('yihai', 'Updated At'),
        ]);
    }

    /**
     * full name of user
     * @return string
     */
    public function getFullname()
    {
        return $this->fullname;
    }

    /**
     * group name of user
     * @return string
     */
    public function getGroup()
    {
        return 'system';
    }

    /**
     * @inheritDoc
     */
    public function filterRules()
    {
        return [
            ['fullname', 'safe'],
            ['sys_user.username', 'safe'],
            ['sys_user.email', 'safe'],
        ];
    }

    /**
     * @param \yii\db\ActiveQuery|\yii\db\QueryInterface $query
     * @param \yihai\core\base\FilterModel|static $filterModel
     */
    public function onSearch(&$query, $filterModel)
    {
        if($filterModel->fullname){
            $query->andFilterWhere(['like', 'fullname', $filterModel->fullname]);
        }
        if($filterModel->{'sys_user.username'}){
            $query->andFilterWhere(['like', 'sys_user.username', $filterModel->{'sys_user.username'}]);
        }
        if($filterModel->{'sys_user.email'}){
            $query->andFilterWhere(['like', 'sys_user.email', $filterModel->{'sys_user.email'}]);
        }
    }

    /**
     * @inheritDoc
     */
    public function onDataProvider(&$dataProvider)
    {
        $query = static::find();
        $query->joinWith(['sys_user as sys_user']);
        $dataProvider->query = $query;
        $dataProvider->sort->attributes['sys_user.username'] = $this->addSortAttribute('sys_user.username');
        $dataProvider->sort->attributes['sys_user.email'] = $this->addSortAttribute('sys_user.email');
        parent::onDataProvider($dataProvider);
    }

    protected function _options()
    {

        return new ModelOptions([
            'baseTitle' => 'Users System',
            'gridColumnData' => [
                static::gridID(),
                'fullname',
                'sys_user.username',
                'sys_user.email',
                static::gridCreatedBy(),
                static::gridCreatedAtSimple(),
                static::gridUpdatedBy(),
                static::gridUpdatedAtSimple(),
            ],
            'importAttributes' => [
                'fullname',
                'user_username',
                'user_email',
                'user_password'
            ],
            'detailViewData' => [
                'id',
                'fullname',
                [
                    'attribute'=>'user_avatar',
                    'format'=>'raw',
                    'value' => function(SysUsersSystem $model){
                        if($model->sys_user->avatarFile)
                            return Html::img($model->sys_user->avatarUrl(), ['style'=>'width:100px']);
                        return NULL;
                    }
                ],
            ],
            'detailViewCreatedUpdated' => true,
            'detailViewCustom' => [
                static::viewUserRole()
            ]
        ]);
    }

    /**
     * custom attribute info
     * @return array
     */
    public function infoAttributes()
    {
        return [
        ];
    }
    public function updateFormFile()
    {
        return '@yihai-core/modules/system/views/_sys_users_update_form.php';
    }
}
