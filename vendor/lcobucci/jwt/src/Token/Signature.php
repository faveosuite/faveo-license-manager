<?php
<<<<<<< HEAD
<<<<<<< HEAD

namespace Lcobucci\JWT\Token;

use Lcobucci\JWT\Signature as SignatureImpl;
use function class_alias;

class_exists(Signature::class, false) || class_alias(SignatureImpl::class, Signature::class);
=======
declare(strict_types=1);
=======
>>>>>>> f330c64 (optimization in progress)

namespace Lcobucci\JWT\Token;

use Lcobucci\JWT\Signature as SignatureImpl;
use function class_alias;

<<<<<<< HEAD
    public function __construct(string $hash, string $encoded)
    {
        $this->hash    = $hash;
        $this->encoded = $encoded;
    }

    public static function fromEmptyData(): self
    {
        return new self('', '');
    }

    public function hash(): string
    {
        return $this->hash;
    }

    /**
     * Returns the encoded version of the signature
     */
    public function toString(): string
    {
        return $this->encoded;
    }
}
>>>>>>> 22c0e54 (table changes)
=======
class_alias(SignatureImpl::class, Signature::class);
>>>>>>> f330c64 (optimization in progress)
