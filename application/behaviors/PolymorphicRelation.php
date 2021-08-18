<?php

namespace app\behaviors;

use Yii;
use yii\base\Behavior;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\behaviors
 * @property yii\db\ActiveRecord $owner
 */
class PolymorphicRelation extends Behavior
{
    /**
     * @var string
     */
    public $classAttribute = 'object_model';
    /**
     * @var string|integer
     */
    public $pkAttribute = 'object_id';
    /**
     * @var array
     */
    public $mustBeInstanceOf = [];
    /**
     * @var mixed the cached object
     */
    private $_cached = null;

    /**
     * @return mixed|null
     */
    public function getPolymorphicRelation()
    {
        if ($this->_cached !== null) {
            return $this->_cached;
        }

        $className = $this->owner->getAttribute($this->classAttribute);

        if (empty($className)) {
            return null;
        }

        if (!class_exists($className)) {
            Yii::error("Underlying object class " . $className . " not found!");
            return null;
        }

        if (!method_exists($className, 'tableName')) {
            return null;
        }

        try {
            $tableName = $className::tableName();
            $object = $className::find()->where([$tableName . '.id' => $this->owner->getAttribute($this->pkAttribute)])->one();

            if ($object !== null && $this->validateUnderlyingObjectType($object)) {
                $this->_cached = $object;
                return $object;
            }
        } catch (\Exception $e) {
            Yii::error($e);
        }

        return null;
    }

    /**
     * @param $object
     */
    public function setPolymorphicRelation($object)
    {
        if ($this->validateUnderlyingObjectType($object)) {
            $this->_cached = $object;
            if ($object instanceof \yii\db\ActiveRecord) {
                $this->owner->setAttribute($this->classAttribute, get_class($object));
                $this->owner->setAttribute($this->pkAttribute, $object->getPrimaryKey());
            }
        }
    }

    public function resetPolymorphicRelation()
    {
        $this->_cached = null;
    }

    /**
     * @param $object
     * @return bool
     */
    private function validateUnderlyingObjectType($object)
    {
        if (count($this->mustBeInstanceOf) == 0) {
            return true;
        }

        foreach ($this->mustBeInstanceOf as $instance) {
            if ($object instanceof $instance) {
                return true;
            }
        }

        return false;
    }
}
