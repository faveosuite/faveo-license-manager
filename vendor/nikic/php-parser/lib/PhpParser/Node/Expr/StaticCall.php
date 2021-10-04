<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node;
<<<<<<< HEAD
<<<<<<< HEAD
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Identifier;
use PhpParser\Node\VariadicPlaceholder;

class StaticCall extends CallLike
=======
=======
use PhpParser\Node\Arg;
>>>>>>> f330c64 (optimization in progress)
use PhpParser\Node\Expr;
use PhpParser\Node\Identifier;
use PhpParser\Node\VariadicPlaceholder;

<<<<<<< HEAD
class StaticCall extends Expr
>>>>>>> 22c0e54 (table changes)
=======
class StaticCall extends CallLike
>>>>>>> f330c64 (optimization in progress)
{
    /** @var Node\Name|Expr Class name */
    public $class;
    /** @var Identifier|Expr Method name */
    public $name;
<<<<<<< HEAD
<<<<<<< HEAD
    /** @var array<Arg|VariadicPlaceholder> Arguments */
=======
    /** @var Node\Arg[] Arguments */
>>>>>>> 22c0e54 (table changes)
=======
    /** @var array<Arg|VariadicPlaceholder> Arguments */
>>>>>>> f330c64 (optimization in progress)
    public $args;

    /**
     * Constructs a static method call node.
     *
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
     * @param Node\Name|Expr                 $class      Class name
     * @param string|Identifier|Expr         $name       Method name
     * @param array<Arg|VariadicPlaceholder> $args       Arguments
     * @param array                          $attributes Additional attributes
<<<<<<< HEAD
=======
     * @param Node\Name|Expr         $class      Class name
     * @param string|Identifier|Expr $name       Method name
     * @param Node\Arg[]             $args       Arguments
     * @param array                  $attributes Additional attributes
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)
     */
    public function __construct($class, $name, array $args = [], array $attributes = []) {
        $this->attributes = $attributes;
        $this->class = $class;
        $this->name = \is_string($name) ? new Identifier($name) : $name;
        $this->args = $args;
    }

    public function getSubNodeNames() : array {
        return ['class', 'name', 'args'];
    }
    
    public function getType() : string {
        return 'Expr_StaticCall';
    }
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)

    public function getRawArgs(): array {
        return $this->args;
    }
<<<<<<< HEAD
=======
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)
}
