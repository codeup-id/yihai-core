<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\base;

use yihai\core\models\SysUploadedFiles;

/**
 * Interface IdentGroupInterface
 * @package yihai\core\interfaces
 * @property string $fullname
 * @property string $group
 * @property SysUploadedFiles $avatarFile
 */
interface IdentGroupInterface
{
    /**
     * full name of user
     * @return string
     */
    public function fieldDataId();

    /**
     * full name of user
     * @return string
     */
    public function getFullname();

    /**
     * group name of user
     * @return string
     */
    public function getGroup();

    /**
     * return avatar File Model
     * @return \yii\db\ActiveQuery
     */
    public function getAvatarFile();

    /**
     * custom attribute info
     * @return array
     */
    public function infoAttributes();

    /**
     * path form view untuk update profile user
     * @return string
     */
    public function updateFormFile();
}