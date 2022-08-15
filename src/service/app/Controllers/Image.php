<?php

namespace App\Controllers;
use App\Models\CommonModel;

class Image extends BaseController
{

    /**
     * 이미지 그리기
     */
    public function getImage($uuid = null)
    { // {{{
        $model = new CommonModel;
        $data = $model->getImage($uuid);

        header("Content-type: ".$data["mime"]);
        echo $data["data"];

        die();

    } // }}}
}
