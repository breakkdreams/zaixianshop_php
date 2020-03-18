<?php
//create 20190915  by hong
namespace app\admin\annotation;

use mindplay\annotations\Annotation;

/**
 * Specifies validation of a string, requiring a minimum and/or maximum length.
 *
 * @usage('class'=>true, 'inherited'=>true, 'multiple'=>false)
 */
class ActionInfoAnnotation extends Annotation
{

    public $name = '';
    public $symbol = '';
    public $list = [];

    /**
     * Initialize the annotation.
     * @param array $properties
     */
    public function initAnnotation(array $properties)
    {
        parent::initAnnotation($properties);
    }
}
