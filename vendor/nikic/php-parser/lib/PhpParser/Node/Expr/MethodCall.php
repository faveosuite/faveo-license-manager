<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Identifier;
<<<<<<< HEAD
<<<<<<< HEAD
use PhpParser\Node\VariadicPlaceholder;

class MethodCall extends CallLike
=======

class MethodCall extends Expr
>>>>>>> 22c0e54 (table changes)
=======
use PhpParser\Node\VariadicPlaceholder;

class MethodCall extends CallLike
>>>>>>> f330c64 (optimization in progress)
{
    /** @var Expr Variable holding object */
    public $var;
    /** @var Identifier|Expr Method name */
    public $name;
<<<<<<< HEAD
<<<<<<< HEAD
    /** @var array<Arg|VariadicPlaceholder> Arguments */
=======
    /** @var Arg[] Arguments */
>>>>>>> 22c0e54 (table changes)
=======
    /** @var array<Arg|VariadicPlaceholder> Arguments */
>>>>>>> f330c64 (optimization in progress)
    public $args;

    /**
     * Constructs a function call node.
     *
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
     * @param Expr                           $var        Variable holding object
     * @param string|Identifier|Expr         $name       Method name
     * @param array<Arg|VariadicPlaceholder> $args       Arguments
     * @param array                          $attributes Additional attributes
<<<<<<< HEAD
=======
     * @param Expr                   $var        Variable holding object
     * @param string|Identifier|Expr $name       Method name
     * @param Arg[]                  $args       Arguments
     * @param array                  $attributes Additional attributes
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)
     */
    public function __construct(Expr $var, $name, array $args = [], array $attributes = []) {
        $this->attributes = $attributes;
        $this->var = $var;
        $this->name = \is_string($name) ? new Identifier($name) : $name;
        $this->args = $args;
    }

    public function getSubNodeNames() : array {
        return ['var', 'name', 'args'];
    }
    
    public function getType() : string {
        return 'Expr_MethodCall';
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
