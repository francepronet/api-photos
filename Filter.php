<?php

namespace Fpn\ApiClient\Pictures;

use Fpn\ApiClient\Core\ApiObject\ApiObject;

class Filter extends ApiObject
{
    protected $fetchUrl    = '/presets/%d';
    protected $fetchAllUrl = '/presets';
    protected $createUrl   = '/presets';
    protected $updateUrl   = '/presets/%d';

    private $type;
    private $params = array();
    private $image;

    public function save()
    {
        $datas = array(
            'filter' => array(
                'type'   => $this->type,
                'params' => $this->params,
                'image'  => $this->image
            )
        );

        parent::saveItem($datas);
    }
}
