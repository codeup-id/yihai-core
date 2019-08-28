<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\modules\system\controllers;


use Mpdf\Output\Destination;
use Yihai;
use yihai\core\actions\CrudFormAction;
use yihai\core\models\SysReports;
use yihai\core\rbac\RbacHelper;
use yihai\core\report\BaseReport;
use yihai\core\web\BackendController;
use yihai\core\web\Response;
use yii\web\NotFoundHttpException;

class ReportsController extends BackendController
{
    /** @var SysReports */
    private $sysReportBuild;
    /** @var BaseReport */
    private $reportClass;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        if (($this->action->id === 'build' || $this->action->id === 'export-report') && ($key = Yihai::$app->request->getQueryParam('key'))) {
            if (!$sysReport = SysReports::findOne(['key' => $key])) {
                throw new NotFoundHttpException();
            }
            $this->sysReportBuild = $sysReport;
            $this->reportClass = $sysReport->reportClass;
            $behaviors['access_action_build'] = [
                'class' => 'yihai\core\filters\AccessControl',
                'only' => ['build', 'export-report'],
                'rules' => [
                    [
                        'controllers' => [$this->getUniqueId()],
                        'allow' => true,
                        'roles' => [RbacHelper::menuRoleName($this->getUniqueId() . '/build/' . $key)],
                    ]
                ]
            ];
        }
        if (($this->action->id === 'template' || $this->action->id === 'update' || $this->action->id === 'delete') && ($id = Yihai::$app->request->getQueryParam('id'))) {
            if (!$sysReport = SysReports::findOne(['id' => $id, 'is_sys' => 0])) {
                throw new NotFoundHttpException();
            }
        }
        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        $actions['duplicate'] = [
            'class' => CrudFormAction::class,
            'modelClass' => $this->_modelClass(),
            'formType' => CrudFormAction::FORM_CREATE,
            'formView' => $this->getViewPath() . '/_form_duplicate.php'
        ];
        $actions['template'] = [
            'class' => CrudFormAction::class,
            'modelClass' => $this->_modelClass(),
            'formType' => CrudFormAction::FORM_UPDATE,
            'formView' => $this->getViewPath() . '/_form_template.php',
            'formConfig' => [
                'layout' => 'default'
            ]
        ];
        return $actions;
    }

    /**
     * class model
     * @return string|\yihai\core\db\ActiveRecord
     */
    public function _modelClass()
    {
        return SysReports::class;
    }

    /**
     * update model options
     * @param \yihai\core\base\ModelOptions $options
     * @return void
     */
    public function _modelOptions(&$options)
    {
        $options->formConfig = [
            'layout' => 'default'
        ];
    }

    /**
     * @param $key
     * @return string
     */
    public function actionBuild($key)
    {
        return $this->render('build', [
            'key' => $key,
            'model' => $this->sysReportBuild,
        ]);

    }

    /**
     * @param $key
     * @param string $type
     * @return string
     * @throws \Mpdf\MpdfException
     */
    public function actionExportReport($key, $__type = 'print')
    {
        $reportClass = $this->reportClass;
        /** @var \yihai\core\modules\system\ModuleSetting $systemSetting */
        $systemSetting = \yihai\core\modules\system\Module::loadSettings();

        $reportClass->build();
        $this->layout = '@yihai/views/_layouts/blank-content';
        $template = $this->render('export-report', [
            'model' => $this->sysReportBuild,
            'reportClass' => $this->reportClass,
            'key' => $key,
            'type' => $__type,
            'systemSetting' => $systemSetting,
        ]);
        Yihai::$app->response->format = Response::FORMAT_PDF;
        Yihai::$app->response->formatters['pdf'] = [
            'class' => 'yihai\core\web\response\MpdfFormatter',
            'orientation' => $this->sysReportBuild->set_page_orientation,
            'dest' => $__type === 'print' ? Destination::INLINE : Destination::DOWNLOAD,
            'fileName' => Yihai::t('yihai', 'Laporan') . ' ' . $this->sysReportBuild->key . ' (' . date('Y-m-d H-i-s', time()) . ').pdf',
            'mpdfConfig' => array_merge([
                'showWatermarkImage' => true,
                'shrink_tables_to_fit' => 1
            ],$reportClass->mpdf()),
            'format' => $this->sysReportBuild->set_page_format,
            'mpdf' => function(\Mpdf\Mpdf $mpdf) use($reportClass, $systemSetting){
                if ($this->sysReportBuild->useWatermark($systemSetting) && ($watermark_image = $this->sysReportBuild->watermark_image($systemSetting))) {
                    $mpdf->showWatermarkImage = true;
                    $mpdf->SetWatermarkImage($watermark_image->fullpath, 0.1, 40, 'F');
                }

                $mpdf->SetTitle($this->sysReportBuild->key . ' Report');
                $mpdf->SetAuthor(Yihai::$app->user->identity->model->username . ' (Yihai App)');
                $reportClass->mpdfOptions($mpdf);
            }
        ];
        return $template;
    }
}