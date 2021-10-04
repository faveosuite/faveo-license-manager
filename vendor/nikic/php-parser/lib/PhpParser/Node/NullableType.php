<?php declare(strict_types=1);

namespace PhpParser\Node;

<<<<<<< HEAD
<<<<<<< HEAD
class NullableType extends ComplexType
=======
use PhpParser\NodeAbstract;

class NullableType extends NodeAbstract
>>>>>>> 22c0e54 (table changes)
=======
class NullableType extends ComplexType
>>>>>>> f330c64 (optimization in progress)
{
    /** @var Identifier|Name Type */
    public $type;

    /**
     * Constructs a nullable type (wrapping another type).
     *
     * @param string|Identifier|Name $type       Type
     * @param array                  $attributes Additional attributes
     */
    public function __construct($type, array $attributes = []) {
        $this->attributes = $attributes;
        $this->type = \is_string($type) ? new Identifier($type) : $type;
    }

    public function getSubNodeNames() : array {
        return ['type'];
    }
    
    public function getType() : string {
        return 'NullableType';
    }
}
