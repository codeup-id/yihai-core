<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\modules\system\controllers;


use Yihai;
use yihai\core\helpers\Url;
use yihai\core\models\SysUploadedFiles;
use yihai\core\web\Controller;
use yii\web\NotFoundHttpException;

class PublicController extends Controller
{

    const TYPE_SHOW = 1;
    const TYPE_DOWNLOAD = 2;
    public $weakEtag = false;
    public $cacheControlHeader = 'public, max-age=3600';
    public $sessionCacheLimiter = '';
    private $_behaviors = [];
    public function behaviors()
    {
        return [
            [
                'class' => 'yii\filters\HttpCache',
                'lastModified' => function ($action, $params) {
                    if($action->id === 'files' && ($path = Yihai::$app->request->get('path'))){
                        $filePath = Yihai::getAlias('@yihai/storages/'.$this->pathNormalize($path));
                        return filemtime($filePath);
                    }
                    return null;
                }

            ]
        ];
    }
    protected function pathNormalize($path){
        return Url::sanitize_filename(str_replace(':', '.', $path), true);
    }
    /**
     * @param $path
     * @return \yii\console\Response|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionFiles($path){
        if (!$path)
            throw new NotFoundHttpException();
        $path = Url::sanitize_filename(str_replace(':', '.', $path), true);

        $filePath = Yihai::getAlias('@yihai/storages/'.$path);
        $filename = basename($path);


        if(file_exists($filePath) && is_file($filePath)){
            $response = Yihai::$app->getResponse();
            return $response->sendFile($filePath, $filename, [
                'inline' => true
            ]);
        }
        throw new NotFoundHttpException();
    }

    /**
     * @param string $group
     * @param string $filename
     * @return \yii\console\Response|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUserAvatar($filename = '')
    {
        if (!$filename)
            throw new NotFoundHttpException();
        $filename = str_replace(':', '.', $filename);

        $file = SysUploadedFiles::findOne(['group' => 'user_avatar', 'filename' => $filename]);
        if (!$file)
            throw new NotFoundHttpException();
        if (!is_file($file->fullpath))
            throw new NotFoundHttpException();
        $response = Yihai::$app->getResponse();
        return $response->sendFile($file->fullpath, $file->name, [
            'mimeType' => $file->type,
            'fileSize' => $file->size,
            'inline' => true
        ]);
    }
}