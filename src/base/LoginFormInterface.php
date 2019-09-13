<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\base;


interface LoginFormInterface
{
    /**
     * handle login
     * @return bool
     */
    public function login();
}