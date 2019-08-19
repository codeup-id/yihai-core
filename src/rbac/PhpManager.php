<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\rbac;


use yii\helpers\FileHelper;

class PhpManager extends \yii\rbac\PhpManager
{

    public $itemFile = '@yihai/storages/yihai/rbac/items.php';

    public $assignmentFile = '@yihai/storages/yihai/rbac/assignments.php';

    public $ruleFile = '@yihai/storages/yihai/rbac/rules.php';

}