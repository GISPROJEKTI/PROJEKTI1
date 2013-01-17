<?php

class LatLngBehavior extends ModelBehavior
{
    public function setup(&$model, $config = array())
    {
        $defConfig = array(
            'field' => 'latlng',
            'lat' => 'lat',
            'lng' => 'lng'
        );

        $config = array_merge($defConfig, (array)$config);

        $this->settings[$model->alias] = $config;
    }

    public function beforeSave(&$model)
    {
        $settings = $this->settings[$model->alias];
        if (!empty($model->data[$model->alias][$settings['field']])) {
            $coords = $this->_split(
                $model->data[$model->alias][$settings['field']]
            );
            $model->data[$model->alias][$settings['lat']] = $coords['lat'];
            $model->data[$model->alias][$settings['lng']] = $coords['lng'];
            unset($model->data[$model->alias][$settings['field']]);
        } else {
            $model->data[$model->alias][$settings['lat']] = null;
            $model->data[$model->alias][$settings['lng']] = null;
        }
        return true;
    }

    public function afterFind($model, $results, $primary)
    {
        $newResults = array();
        $settings = $this->settings[$model->alias];
        foreach ($results as $result) {
            if (!empty($result[$model->alias][$settings['lat']])) {
                $latlng = $result[$model->alias][$settings['lat']] . ',' .
                    $result[$model->alias][$settings['lng']];
                $result[$model->alias][$settings['field']] = $latlng;
            }
            $newResults[] = $result;
        }
        return $newResults;
    }

    protected function _split($latLng)
    {
        if (!empty($latLng)) {
            $parts = split(',', $latLng);
        } else {
            $parts = array();
        }

        $coords = array();


        if (isset($parts[0]) && is_numeric($parts[0])) {
            $coords['lat'] = $parts[0];
        } else {
            $coords['lat'] = 0.0;
        }

        if (isset($parts[1]) && is_numeric($parts[1])) {
            $coords['lng'] = $parts[1];
        } else {
            $coords['lng'] = 0.0;
        }

        return $coords;
    }
}