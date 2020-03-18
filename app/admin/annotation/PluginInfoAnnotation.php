<?php
//create 20190915  by hong
namespace app\admin\annotation;

use mindplay\annotations\Annotation;

/**
 * Specifies validation of a string, requiring a minimum and/or maximum length.
 *
 * @usage('class'=>true, 'inherited'=>true, 'multiple'=>false)
 */
class PluginInfoAnnotation extends Annotation
{
    public $name = ''; //插件中文名称
    public $symbol = ''; //标志  插件英文名  驼峰命名的
    /**
     * Initialize the annotation.
     * @param array $properties
     */
    public function initAnnotation(array $properties)
    {
        parent::initAnnotation($properties);
    }
}
