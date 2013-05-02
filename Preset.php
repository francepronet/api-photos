<?php

namespace Fpn\ApiClient\Pictures;

use Fpn\ApiClient\Core\ApiObject\ApiObject;
use Fpn\ApiClient\Core\Utility\Caster;
use Fpn\ApiClient\Pictures\Filter;

class Preset extends ApiObject
{
    protected $fetchUrl    = '/presets/%d';
    protected $fetchAllUrl = '/presets';
    protected $createUrl   = '/presets';
    protected $updateUrl   = '/presets/%d';

    private $applyUrl = '/presets/%d/apply';

    private $name;
    private $posId;
    private $filters = array();

    public function save()
    {
        $datas = array(
            'preset' => array(
                'name'  => $this->name,
                'posId' => $this->posId
            )
        );

        $this->saveItem($datas);
    }

    public function fetch($id) {
        parent::fetch($id);

        foreach ($this->filters as &$filter) {
            $castedFilter = new Filter();
            Caster::cast($filter, $castedFilter);
            $filter = $castedFilter;
        }

        return $this;
    }

    public function fetchAll($page = 1, $limit = 20, $query = null)
    {
        $presets = parent::fetchAll($page, $limit, $query);

        foreach ($presets as $preset) {
            foreach ($preset->filters as &$filter) {
                $casterFilter = new Filter();
                Caster::cast($filter, $casterFilter);
                $casterFilter->setPresetId($this->id);

                $filter = $casterFilter;
            }
        }

        return $presets;
    }

    public function apply($source, $dest)
    {
        if (empty($this->id)) {
            throw new \InvalidArgumentException('Cannot apply a preset if no preset is loaded');
        }

        $datas = array(
            'image' => "@{$source}"
        );

        $response = $this->apiClient->request('POST', sprintf($this->applyUrl, $this->id), $datas);

        file_put_contents($dest, base64_decode($response->image));
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getPosId()
    {
        return $this->posId;
    }

    public function setPosId($posId)
    {
        $this->posId = $posId;
        return $this;
    }
}
