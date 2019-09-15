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
use yihai\core\behaviors\BlameableBehavior;
use yihai\core\behaviors\TimestampBehavior;
use yihai\core\behaviors\UploadBehavior;
use yihai\core\db\ActiveRecord;
use yihai\core\db\DataTrait;
use yihai\core\helpers\ArrayHelper;
use yihai\core\helpers\Url;
use yihai\core\log\LoggableBehavior;
use yihai\core\modules\system\ModuleSetting;
use yihai\core\report\BaseReport;
use yihai\core\theming\Html;
use yihai\core\web\Application;
use yii\base\UnknownClassException;

/**
 * Class SysSettings
 * @package yihai\core\models
 * @property int $id [int(11)]
 * @property string $key [varchar(50)]
 * @property string $module [varchar(50)]
 * @property string $template [blob]
 * @property string $desc
 * @property int $is_sys
 * @property string $created_by [varchar(64)]
 * @property int $created_at [int(11)]
 * @property string $updated_by [varchar(64)]
 * @property int $updated_at [int(11)]
 * @property string $class
 *
 *
 * @property string $templateFormat
 * @property int $set_use_watermark [tinyint(1)]
 * @property int $set_watermark_image [int(11)]
 * @property int $set_use_watermark_image_system [tinyint(1)]
 * @property bool $set_header_use_system [tinyint(1)]
 * @property string $set_page_format
 * @property string $set_page_orientation P|L
 * @property bool $isPagePotrait
 * @property bool $isPageLanscape
 *
 * @property BaseReport $reportClass
 * @property SysUploadedFiles $watermark_image
 *
 */
class SysReports extends ActiveRecord
{
    use DataTrait;

    public static function tableName()
    {
        return '{{%sys_reports}}';
    }

    private $_behaviors = [];

    public function behaviors()
    {
        $behaviors = array_merge($this->_behaviors, [
            TimestampBehavior::class,
        ]);
        if (Yihai::$app instanceof Application) {
            $behaviors[] = LoggableBehavior::class;
            $behaviors[] = BlameableBehavior::class;
            $behaviors[] = [
                'class' => UploadBehavior::class,

                'deleteOldFile' => true,
                'group' => 'report_watermark',
                'attribute' => 'set_watermark_image_upload',
                'savedAttribute' => 'set_watermark_image',
                'uploadPath' => '@yihai/storages/report-watermark',
                'autoSave' => true,
                'autoDelete' => true,

            ];
        }

        return $behaviors;
    }

    public function addBehavior($behavior)
    {
        $this->_behaviors[] = $behavior;
    }

    public function init()
    {
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['key', 'class', 'desc'], 'required'],
            ['key', 'match',
                'pattern' => '/^(?![-])(?!.*[-]{2})[a-z0-9-]+(?<![-])$/',
                'message' => Yihai::t('yihai', "{attribute} tidak valid. tidak ada ({begin}) di awal, tidak ada ({inside}) di dalam, tidak ada ({end}) di akhir, karakter yang diizinkan ({allowed})", [
                    'attribute' => 'Key',
                    'begin' => '-',
                    'inside' => '--',
                    'end' => '-',
                    'allowed' => 'a-z,0-9,-'
                ])
            ],
            [['set_use_watermark', 'set_use_watermark_image_system', 'set_header_use_system'], 'required'],
            [['set_page_format', 'set_page_orientation'], 'required'],
            ['set_watermark_image_upload', 'file', 'skipOnEmpty' => true, 'extensions' => ['jpg', 'jpeg', 'png']],
            [['template', 'desc', 'class', 'created_by', 'updated_by'], 'string'],
            [['is_sys'], 'integer'],
            [['class'], 'in', 'range' => Yihai::$app->reports->listReportClass],
            [['is_sys'], 'in', 'range' => ['0', '1']],
            [['created_at', 'updated_at'], 'integer'],
            [['module'], 'string', 'max' => 50],
            [['key'], 'unique', 'targetAttribute' => ['key']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'key' => Yihai::t('yihai', 'ID/Key'),
            'module' => Yihai::t('yihai', 'Modul'),
            'desc' => Yihai::t('yihai', 'Keterangan'),
            'is_sys' => Yihai::t('yihai', 'Laporan Sistem'),
            'set_use_watermark' => Yihai::t('yihai', 'Gunakan watermark'),
            'set_use_watermark_image_system' => Yihai::t('yihai', 'Gunakan pengaturan sistem'),
            'set_watermark_image_upload' => Yihai::t('yihai', 'Kustom gambar watermark'),
            'set_header_use_system' => Yihai::t('yihai', 'Gunakan pengaturan sistem (Header)'),
            'set_page_format' => Yihai::t('yihai', 'Format Halaman'),
            'set_page_orientation' => Yihai::t('yihai', 'Orientasi halaman'),
            'created_by' => Yihai::t('yihai', 'Dibuat oleh'),
            'created_at' => Yihai::t('yihai', 'Dibuat pada'),
            'updated_by' => Yihai::t('yihai', 'Diperbarui oleh'),
            'updated_at' => Yihai::t('yihai', 'Diperbarui pada'),
        ];
    }

    public function beforeSave($insert)
    {

        $this->template = str_replace(array_values(Url::getKontenReplacing()), array_keys(Url::getKontenReplacing()), $this->template);
        if ($insert) {
            if ($this->is_sys == 0 && !$this->module) {
                if ($cek = static::find()->select('module')->where(['class' => $this->class, 'is_sys' => 1])->one()) {
                    $this->module = $cek->module;
                } else {
                    $this->addError('class', Yihai::t('yihai', '"class" Tidak dikenal.'));
                    return false;
                }
            }
        }

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            Yihai::$app->reports->reportBuildRole($this->key);
        }
        parent::afterSave($insert, $changedAttributes);
    }

    public function afterDelete()
    {
        Yihai::$app->reports->reportBuildRoleDelete($this->key);
        parent::afterDelete();
    }

    /**
     * @param ModuleSetting $sysSetting
     * @return int|bool
     */
    public function useWatermark($sysSetting)
    {
        if ($this->set_use_watermark === 0)
            return $sysSetting->reportWatermark;
        elseif ($this->set_use_watermark === 1)
            return true;
        return false;
    }

    /**
     * @param ModuleSetting $sysSetting
     * @return SysUploadedFiles|bool
     */
    public function watermark_image($sysSetting)
    {
        if ($this->set_use_watermark_image_system) {
            /** @var SysUploadedFiles $image */
            $image = $sysSetting->reportWatermarkImage;
            return $image;
        } elseif ($this->watermark_image) {
            return $this->watermark_image;
        }
        return false;
    }

    /**
     * @return BaseReport
     * @throws UnknownClassException
     */
    public function getReportClass()
    {
        $i = new $this->class(['model' => $this]);
        if (!$i instanceof BaseReport)
            throw new UnknownClassException('Class ' . $this->class . ' bukan instance dari "' . BaseReport::class . '"');
//        $i->setModel($this);
        return $i;
    }

    /**
     * @return string
     */
    public function getTemplateFormat()
    {
        return strtr($this->template, Url::getKontenReplacing());
    }

    public function getWatermark_image()
    {
        return $this->hasOne(SysUploadedFiles::class, ['id' => 'set_watermark_image']);
    }

    public function getIsPagePotrait()
    {
        return $this->set_page_orientation === 'P';
    }

    public function getIsPageLanscape()
    {
        return $this->set_page_orientation === 'L';
    }

    public function filterRules()
    {
        return [
            ['key', 'safe'],
            ['module', 'safe'],
            ['desc', 'safe'],
            ['is_sys', 'integer'],
        ];
    }

    /**
     * @param \yii\db\ActiveQuery|\yii\db\QueryInterface $query
     * @param \yihai\core\base\FilterModel|static $filterModel
     */
    public function onSearch(&$query, $filterModel)
    {
        if ($filterModel->key !== '') {
            $query->andWhere(['like', 'key', $filterModel->key]);
        }
        if ($filterModel->module !== '') {
            $query->andWhere(['module' => $filterModel->module]);
        }
        if ($filterModel->desc !== '') {
            $query->andWhere(['like', 'desc', $filterModel->desc]);
        }
        if ($filterModel->is_sys !== '') {
            $query->andWhere(['is_sys' => $filterModel->is_sys]);
        }
    }

    protected function onDataProvider(&$dataProvider)
    {
        $dataProvider->query = static::find()->orderBy(['class' => SORT_ASC]);
    }

    protected function _options()
    {

        return new ModelOptions([
            'baseTitle' => Yihai::t('yihai', 'Laporan/Dokumen'),
            'actionCreate' => false,
            'mergeDeleteParams' => [
                'is_sys' => '0'
            ],
            'hint' => [
                Yihai::t('yihai', 'Tidak Dapat Memperbarui Laporan Sistem, Menghapus dan mengedit Template.')
            ],
            'gridViewActionColumn' => [
                'class' => 'yihai\core\grid\ActionColumn',
                'visibleButtons' => [
                    'update' => function ($model) {
                        return $model->is_sys == '0';
                    },
                    'delete' => function ($model) {
                        return $model->is_sys == '0';
                    },
                    'template' => function ($model) {
                        return $model->is_sys == '0';
                    },
                ],
                'templateAppend' => ' - {template} {duplicate}',
                'buttonsCustom' => [
                    'duplicate' => [
                        'modal' => true,
                        'label' => Yihai::t('yihai', 'Duplikat'),
                        'icon' => 'copy'
                    ],
                    'template' => [
                        'modal' => false,
                        'icon' => 'code',
                    ]
                ]
            ],
            'gridColumnData' => [
                [
                    'headerOptions' => ['style' => 'text-align:center'],
                    'contentOptions' => ['style' => 'text-align:center'],
                    'label' => 'Build',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return Html::a(Html::icon('file'), ['build', 'key' => $model->key], ['data-pjax' => 0, 'title' => Yihai::t('yihai', 'Hasilkan laporan/dokumen')]);
                    }
                ],
                'key',
                [
                    'attribute' => 'module',
                    'filter' => function () {
                        return ArrayHelper::map(static::find()->select('module')->distinct('module')->all(), 'module', 'module');
                    }
                ],
                [
                    'attribute' => 'desc',
                ],
                [
                    'attribute' => 'is_sys',
                    'value' => function ($model) {
                        return $model->is_sys == '1' ? Yihai::t('yihai', 'Ya') : Yihai::t('yihai', 'Tidak');
                    },
                    'filter' => [
                        '1' => Yihai::t('yihai', 'Ya'),
                        '0' => Yihai::t('yihai', 'Tidak')
                    ]
                ],
                static::gridCreatedBy(),
                static::gridCreatedAtSimple(),
                static::gridUpdatedBy(),
                static::gridUpdatedAtSimple(),

            ],
            'detailViewCreatedUpdated' => true,
            'detailViewData' => [
                'id',
                'key',
                'module',
                'class',
                'desc',
                'is_sys:yesNo',
                'set_use_watermark:yesNo',
                'set_use_watermark_image_system:yesNo',
                'set_header_use_system:yesNo',
                'set_page_format',
                'set_page_orientation'
            ]
        ]);
    }
}