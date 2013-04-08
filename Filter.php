<?php

namespace Fpn\ApiClient\Pictures;

use Fpn\ApiClient\Core\ApiObject\ApiObject;
use Fpn\ApiClient\Core\Utility\Caster;

class Filter extends ApiObject
{
    protected $fetchUrl    = '/presets/%d/filters/%d';
    protected $fetchAllUrl = '/presets/%d/filters';
    protected $createUrl   = '/presets/%d/filters';
    protected $updateUrl   = '/presets/%d/filters/%d';

    private $presetId;
    private $type;
    private $params = array();
    private $image;

    public function fetch($id)
    {
        $this->checkPresetId();

        $filter = $this->apiClient->request('GET', sprintf($this->fetchUrl, $this->presetId, $id));

        Caster::cast($filter, $this);

        return $this;
    }

    public function fetchAll($page = 1, $limit = 20)
    {
        $this->checkPresetId();

        $filters  = array();
        $_filters = $this->apiClient->request('GET', sprintf("{$this->fetchAllUrl}?page={$page}&limit={$limit}", $this->presetId))->items;

        foreach ($_filters as $filter) {
            $casterFilter = new Filter();
            Caster::cast($filter, $casterFilter);
            $filters[] = $casterFilter;
        }

        return $filters;
    }

    public function save()
    {
        $this->checkPresetId();

        $datas = array(
            'filter' => array(
                'type'   => $this->type,
                'params' => $this->params,
                'image'  => $this->image
            )
        );

        if (empty($this->id)) {
            $method = 'POST';
            $url    = sprintf($this->createUrl, $this->presetId);
        } else {
            $method = 'PUT';
            $url    = sprintf($this->updateUrl, $this->presetId, $this->id);
        }

        $filter = $this->apiClient->request($method, $url);

        Caster::cast($filter, $this);

        return $this;
    }

    private function checkPresetId()
    {
        if (empty($this->presetId)) {
            throw new \InvalidArgumentException('A preset ID must be defined before calling any API call.');
        }
    }

    public function getPresetId()
    {
        return $this->presetId;
    }

    public function setPresetId($presetId)
    {
        $this->presetId = $presetId;
        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }
}
