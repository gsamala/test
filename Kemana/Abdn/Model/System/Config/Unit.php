<?php

namespace Kemana\Abdn\Model\System\Config;

class Unit
{
    public function toOptionArray()
    {
        $options = array(
            array('value' => \Kemana\Abdn\Model\Config::IN_DAYS, 'label' => __('Days')),
            array('value' => \Kemana\Abdn\Model\Config::IN_HOURS, 'label' =>__('Hours'))
        );
        return $options;
    }
}