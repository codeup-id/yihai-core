<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\models;


use Yihai;
use yihai\core\assets\AppAsset;
use yihai\core\base\ModelOptions;
use yihai\core\behaviors\UploadBehavior;
use yihai\core\db\DataTrait;
use yihai\core\grid\ActionColumn;
use yihai\core\rbac\RbacHelper;
use yihai\core\theming\Html;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;

/**
 * This is the model class for table "{{%sys_users}}".
 *
 * @property int $id
 * @property string $username
 * @property string $email [varchar(100)]
 * @property string $password
 * @property string $group
 * @property int $data
 * @property int $status
 * @property string $access_token
 * @property string $auth_key
 * @property string $reset_token
 * @property int $last_time
 * @property string $created_by
 * @property int $created_at timestamp
 * @property string $updated_by
 * @property int $updated_at timestamp
 *
 * @property AbstractUserModel $datauser Data user pada group class
 * @property array $groupList
 * @property string $groupClass
 *
 * @property string $memberSince
 * @property string $statustext
 * @property int $avatar [int(11)]
 * @property SysUploadedFiles $avatarFile
 */
class UserModel extends \yihai\core\db\ActiveRecord
{
    public static $crud_url = '/system/users/';
    const STATUS_ACTIVE = 1;
    const STATUS_NON_ACTIVE = 0;
    const STATUS_DELETED = -1;

    use DataTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%sys_users}}';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class,
            [
                'class' => UploadBehavior::class,

                'deleteOldFile' => true,
                'group' => 'user_avatar',
                'attribute' => 'avatar_upload',
                'savedAttribute' => 'avatar',
                'uploadPath' => '@yihai/storages/avatars',
                'autoSave' => true,
                'autoDelete' => true,

            ],
            'yihai\core\log\LoggableBehavior'

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'email'], 'required'],
            ['password', 'required', 'on' => 'create'],
            [['data', 'status', 'last_time', 'created_at', 'updated_at'], 'integer'],
            [['username', 'password', 'reset_token', 'created_by', 'updated_by'], 'string', 'max' => 64],
            ['username','match',
                'pattern' => '/^(?![_.-])(?!.*[_.-]{2})[a-z0-9._.-]+(?<![_.-])$/',
                'message' => Yihai::t('yihai', "{attribute} tidak valid. tidak ada ({begin}) di awal, tidak ada ({inside}) di dalam, tidak ada ({end}) di akhir, karakter yang diizinkan ({allowed})",[
                    'attribute' => 'Username',
                    'begin' => '_, ., -',
                    'inside' => '__, _, ._, .., --',
                    'end' => '_, ., -',
                    'allowed'=>'a-z,0-9,.,_,-'
                ])
            ],
            [['email'], 'string', 'max' => 100],
            ['password', 'string', 'min'=>6],
            [['group', 'access_token', 'auth_key'], 'string', 'max' => 32],
            [['group', 'email'], 'unique', 'targetAttribute' => ['group', 'email']],
            [['group', 'username'], 'unique', 'targetAttribute' => ['group', 'username']],
            [['group', 'data'], 'unique', 'targetAttribute' => ['group', 'data']],
            ['avatar_upload', 'file', 'skipOnEmpty' => true, 'extensions' => ['jpg', 'jpeg', 'png']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yihai::t('yihai', 'ID'),
            'username' => Yihai::t('yihai', 'Nama pengguna'),
            'email' => Yihai::t('yihai', 'Email'),
            'password' => Yihai::t('yihai', 'Kata sandi'),
            'group' => Yihai::t('yihai', 'Grup'),
            'data' => Yihai::t('yihai', 'Data'),
            'status' => Yihai::t('yihai', 'Status'),
            'statustext' => Yihai::t('yihai', 'Status'),
            'access_token' => Yihai::t('yihai', 'Access Token'),
            'auth_key' => Yihai::t('yihai', 'Auth Key'),
            'reset_token' => Yihai::t('yihai', 'Reset Token'),
            'last_time' => Yihai::t('yihai', 'Terakhir masuk'),
            'created_by' => Yihai::t('yihai', 'Dibuat oleh'),
            'created_at' => Yihai::t('yihai', 'Dibuat pada'),
            'updated_by' => Yihai::t('yihai', 'Diperbarui oleh'),
            'updated_at' => Yihai::t('yihai', 'Diperbarui pada'),
            'memberSince' => Yihai::t('yihai', 'Anggota sejak'),
        ];
    }
    //--EVENTS-----------------------------------------------------------------------

    /**
     * {@inheritDoc}
     */
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->password = Yihai::$app->security->generatePasswordHash($this->password);
        } else {
            if (!empty($this->password)) {
                $this->password = Yihai::$app->security->generatePasswordHash($this->password);
            } else {
                $this->password = (string)$this->getOldAttribute('password');
            }
        }

        return parent::beforeSave($insert);
    }

    /**
     * {@inheritDoc}
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            RbacHelper::forceAssignRole(RbacHelper::userGroupRoleName($this->group), $this->id);
        }
        parent::afterSave($insert, $changedAttributes);
    }

    //-------------------------------------------------------------------------

    public function fields()
    {
        $fields = parent::fields();
        if(isset($fields['password']))
            unset($fields['password']);
        return $fields;
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDatauser()
    {
        if ($this->groupClass) {
            return $this->hasOne($this->groupClass, ['id' => 'data']);
        }
        return $this->hasOne('yihai\core\models\SysUsersSystem', ['id' => 'data']);;
    }

    /**
     * mengambil group list pada user component
     * @return array
     */
    public function getGroupList()
    {
        return Yihai::$app->user->groupClass;
    }

    /**
     * mengambil nama class group
     * @return string nama class group
     */
    public function getGroupClass()
    {
        if (isset($this->groupList[$this->group]))
            return $this->groupList[$this->group];
        return NULL;
    }

    /**
     * @param $password
     * @return bool
     */
    public function updatePassword($password)
    {
        $this->password = $password;
        return $this->save(false, ['password']);
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function getMemberSince()
    {
        if ($this->created_at)
            return Yihai::$app->formatter->asDateTime($this->created_at);
        return null;
    }

    public function getStatustext()
    {
        if ($this->status === self::STATUS_ACTIVE)
            return Yihai::t('yihai', 'Aktif');
        elseif ($this->status === self::STATUS_DELETED)
            return Yihai::t('yihai', 'Dihapus');
        return Yihai::t('yihai', 'Tidak aktif');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAvatarFile()
    {
        if ($this->avatar)
            return $this->hasOne(SysUploadedFiles::class, ['id' => 'avatar']);
        return null;
    }

    public function avatarUrl($default = '')
    {
        if ($default === '') {
            /** @var AppAsset $appAsset */
            $appAsset = new Yihai::$app->params['AppAssetClass']();
            $default = $appAsset->getDefaultAvatar();
        }
        $avatar = $this->avatarFile;
        if (!$avatar) return $default;
        return $avatar->url('user-avatar');
    }
    public function extraFields()
    {
        return ['datauser'];
    }

    public function filterRules()
    {
        return [
            ['id','number'],
            ['username','string'],
            ['email','string'],
            ['group','string'],
        ];
    }

    /**
     * @param \yii\db\ActiveQuery|\yii\db\QueryInterface $query
     * @param \yihai\core\base\FilterModel|static $filterModel
     */
    public function onSearch(&$query, $filterModel)
    {
        if($filterModel->username){
            $query->andWhere(['like', 'username', $filterModel->username]);
        }
        if($filterModel->email){
            $query->andWhere(['like', 'email', $filterModel->email]);
        }
        if($filterModel->group){
            $query->andWhere(['group'=>$filterModel->group]);
        }
    }

    /**
     * @return ModelOptions
     */
    protected function _options()
    {
        return new ModelOptions([
            'model' => $this,
            'baseTitle' => Yihai::t('yihai','Semua pengguna'),
            'actionCreate' => false,
            'actionDelete' => false,
            'actionUpdate' => false,
            'actionImport' => false,
            'gridViewCheckboxColumn' => false,
            'gridViewActionColumn' => [
                'class' => 'yihai\core\grid\ActionColumn',
                'templateAppend' => '{password} {roles}',
                'buttonsCustom' => [
                    'password' => [
                        'modal' => true,
                        'icon' => 'password',
                        'title' => Yihai::t('yihai', 'Ganti kata sandi'),
                    ],
                ]
            ],
            'gridColumnData' => [
                static::gridID(),
                'username',
                'email',
                [
                    'attribute'=>'group',
                    'filter' => static::toArrayDropdown('group','group')
                ],
                'datauser.fullname',
                static::gridCreatedBy(),
                static::gridCreatedAtSimple(),
                static::gridUpdatedBy(),
                static::gridUpdatedAtSimple(),
            ],
            'detailViewData' => [
                'id',
                'username',
                'email',
            ],
            'detailViewCreatedUpdated' => true,
            'detailViewCustom' => [
                static::viewUserRole()
            ],
            'gridPdfOrientation' => 'L'

        ]);
    }
}
