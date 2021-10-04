<?php

namespace Prophecy\Doubler\Generator\Node;

use Prophecy\Exception\Doubler\DoubleException;

final class ReturnTypeNode extends TypeNodeAbstract
{
    protected function getRealType(string $type): string
    {
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
        switch ($type) {
            case 'void':
            case 'never':
                return $type;
            default:
                return parent::getRealType($type);
<<<<<<< HEAD
        }
=======
        if ($type == 'void') {
            return $type;
        }

        return parent::getRealType($type);
>>>>>>> 22c0e54 (table changes)
=======
        }
>>>>>>> f330c64 (optimization in progress)
    }

    protected function guardIsValidType()
    {
        if (isset($this->types['void']) && count($this->types) !== 1) {
            throw new DoubleException('void cannot be part of a union');
        }
<<<<<<< HEAD
<<<<<<< HEAD
        if (isset($this->types['never']) && count($this->types) !== 1) {
            throw new DoubleException('never cannot be part of a union');
        }
=======
>>>>>>> 22c0e54 (table changes)
=======
        if (isset($this->types['never']) && count($this->types) !== 1) {
            throw new DoubleException('never cannot be part of a union');
        }
>>>>>>> f330c64 (optimization in progress)

        parent::guardIsValidType();
    }

<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
    /**
     * @deprecated use hasReturnStatement
     */
    public function isVoid()
<<<<<<< HEAD
    {
        return $this->types == ['void' => 'void'];
    }

    public function hasReturnStatement(): bool
    {
        return $this->types !== ['void' => 'void']
            && $this->types !== ['never' => 'never'];
    }
=======
    public function isVoid(): bool
    {
        return $this->types == ['void' => 'void'];
    }
>>>>>>> 22c0e54 (table changes)
=======
    {
        return $this->types == ['void' => 'void'];
    }

    public function hasReturnStatement(): bool
    {
        return $this->types !== ['void' => 'void']
            && $this->types !== ['never' => 'never'];
    }
>>>>>>> f330c64 (optimization in progress)
}
