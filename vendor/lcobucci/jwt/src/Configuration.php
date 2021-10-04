<?php
<<<<<<< HEAD
<<<<<<< HEAD
=======
declare(strict_types=1);
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)

namespace Lcobucci\JWT;

use Closure;
<<<<<<< HEAD
<<<<<<< HEAD
use Lcobucci\JWT\Parsing\Decoder;
use Lcobucci\JWT\Parsing\Encoder;
=======
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
>>>>>>> 22c0e54 (table changes)
=======
use Lcobucci\JWT\Parsing\Decoder;
use Lcobucci\JWT\Parsing\Encoder;
>>>>>>> f330c64 (optimization in progress)
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\None;
use Lcobucci\JWT\Validation\Constraint;

/**
 * Configuration container for the JWT Builder and Parser
 *
 * Serves like a small DI container to simplify the creation and usage
 * of the objects.
 */
final class Configuration
{
<<<<<<< HEAD
<<<<<<< HEAD
    /** @var Parser */
    private $parser;

    /** @var Signer */
    private $signer;

    /** @var Key */
    private $signingKey;

    /** @var Key */
    private $verificationKey;

    /** @var Validator */
    private $validator;

    /** @var Closure(): Builder */
    private $builderFactory;

    /** @var Constraint[] */
    private $validationConstraints = [];
=======
    private Parser $parser;
    private Signer $signer;
    private Key $signingKey;
    private Key $verificationKey;
    private Validator $validator;
=======
    /** @var Parser */
    private $parser;
>>>>>>> f330c64 (optimization in progress)

    /** @var Signer */
    private $signer;

    /** @var Key */
    private $signingKey;

    /** @var Key */
    private $verificationKey;

    /** @var Validator */
    private $validator;

    /** @var Closure(): Builder */
    private $builderFactory;

    /** @var Constraint[] */
<<<<<<< HEAD
    private array $validationConstraints = [];
>>>>>>> 22c0e54 (table changes)
=======
    private $validationConstraints = [];
>>>>>>> f330c64 (optimization in progress)

    private function __construct(
        Signer $signer,
        Key $signingKey,
        Key $verificationKey,
<<<<<<< HEAD
<<<<<<< HEAD
        Encoder $encoder = null,
        Decoder $decoder = null
=======
        ?Encoder $encoder = null,
        ?Decoder $decoder = null
>>>>>>> 22c0e54 (table changes)
=======
        Encoder $encoder = null,
        Decoder $decoder = null
>>>>>>> f330c64 (optimization in progress)
    ) {
        $this->signer          = $signer;
        $this->signingKey      = $signingKey;
        $this->verificationKey = $verificationKey;
<<<<<<< HEAD
<<<<<<< HEAD
        $this->parser          = new Parser($decoder ?: new Decoder());
        $this->validator       = new Validation\Validator();

        $this->builderFactory = static function () use ($encoder) {
            return new Builder($encoder ?: new Encoder());
        };
    }

    /** @return self */
=======
        $this->parser          = new Token\Parser($decoder ?? new JoseEncoder());
=======
        $this->parser          = new Parser($decoder ?: new Decoder());
>>>>>>> f330c64 (optimization in progress)
        $this->validator       = new Validation\Validator();

        $this->builderFactory = static function () use ($encoder) {
            return new Builder($encoder ?: new Encoder());
        };
    }

<<<<<<< HEAD
>>>>>>> 22c0e54 (table changes)
=======
    /** @return self */
>>>>>>> f330c64 (optimization in progress)
    public static function forAsymmetricSigner(
        Signer $signer,
        Key $signingKey,
        Key $verificationKey,
<<<<<<< HEAD
<<<<<<< HEAD
        Encoder $encoder = null,
        Decoder $decoder = null
    ) {
=======
        ?Encoder $encoder = null,
        ?Decoder $decoder = null
    ): self {
>>>>>>> 22c0e54 (table changes)
=======
        Encoder $encoder = null,
        Decoder $decoder = null
    ) {
>>>>>>> f330c64 (optimization in progress)
        return new self(
            $signer,
            $signingKey,
            $verificationKey,
            $encoder,
            $decoder
        );
    }

<<<<<<< HEAD
<<<<<<< HEAD
    /** @return self */
    public static function forSymmetricSigner(
        Signer $signer,
        Key $key,
        Encoder $encoder = null,
        Decoder $decoder = null
    ) {
=======
    public static function forSymmetricSigner(
        Signer $signer,
        Key $key,
        ?Encoder $encoder = null,
        ?Decoder $decoder = null
    ): self {
>>>>>>> 22c0e54 (table changes)
=======
    /** @return self */
    public static function forSymmetricSigner(
        Signer $signer,
        Key $key,
        Encoder $encoder = null,
        Decoder $decoder = null
    ) {
>>>>>>> f330c64 (optimization in progress)
        return new self(
            $signer,
            $key,
            $key,
            $encoder,
            $decoder
        );
    }

<<<<<<< HEAD
<<<<<<< HEAD
    /** @return self */
    public static function forUnsecuredSigner(
        Encoder $encoder = null,
        Decoder $decoder = null
    ) {
        $key = InMemory::plainText('');
=======
    public static function forUnsecuredSigner(
        ?Encoder $encoder = null,
        ?Decoder $decoder = null
    ): self {
        $key = InMemory::empty();
>>>>>>> 22c0e54 (table changes)
=======
    /** @return self */
    public static function forUnsecuredSigner(
        Encoder $encoder = null,
        Decoder $decoder = null
    ) {
        $key = InMemory::plainText('');
>>>>>>> f330c64 (optimization in progress)

        return new self(
            new None(),
            $key,
            $key,
            $encoder,
            $decoder
        );
    }

<<<<<<< HEAD
<<<<<<< HEAD
    /** @param callable(): Builder $builderFactory */
    public function setBuilderFactory(callable $builderFactory)
    {
        if (! $builderFactory instanceof Closure) {
            $builderFactory = static function() use ($builderFactory) {
                return $builderFactory();
            };
        }
        $this->builderFactory = $builderFactory;
    }

    /** @return Builder */
    public function builder()
    {
        $factory = $this->builderFactory;

        return $factory();
    }

    /** @return Parser */
    public function parser()
=======
    /** @param callable(ClaimsFormatter): Builder $builderFactory */
    public function setBuilderFactory(callable $builderFactory): void
=======
    /** @param callable(): Builder $builderFactory */
    public function setBuilderFactory(callable $builderFactory)
>>>>>>> f330c64 (optimization in progress)
    {
        if (! $builderFactory instanceof Closure) {
            $builderFactory = static function() use ($builderFactory) {
                return $builderFactory();
            };
        }
        $this->builderFactory = $builderFactory;
    }

    /** @return Builder */
    public function builder()
    {
        $factory = $this->builderFactory;

        return $factory();
    }

<<<<<<< HEAD
    public function parser(): Parser
>>>>>>> 22c0e54 (table changes)
=======
    /** @return Parser */
    public function parser()
>>>>>>> f330c64 (optimization in progress)
    {
        return $this->parser;
    }

<<<<<<< HEAD
<<<<<<< HEAD
    public function setParser(Parser $parser)
=======
    public function setParser(Parser $parser): void
>>>>>>> 22c0e54 (table changes)
=======
    public function setParser(Parser $parser)
>>>>>>> f330c64 (optimization in progress)
    {
        $this->parser = $parser;
    }

<<<<<<< HEAD
<<<<<<< HEAD
    /** @return Signer */
    public function signer()
=======
    public function signer(): Signer
>>>>>>> 22c0e54 (table changes)
=======
    /** @return Signer */
    public function signer()
>>>>>>> f330c64 (optimization in progress)
    {
        return $this->signer;
    }

<<<<<<< HEAD
<<<<<<< HEAD
    /** @return Key */
    public function signingKey()
=======
    public function signingKey(): Key
>>>>>>> 22c0e54 (table changes)
=======
    /** @return Key */
    public function signingKey()
>>>>>>> f330c64 (optimization in progress)
    {
        return $this->signingKey;
    }

<<<<<<< HEAD
<<<<<<< HEAD
    /** @return Key */
    public function verificationKey()
=======
    public function verificationKey(): Key
>>>>>>> 22c0e54 (table changes)
=======
    /** @return Key */
    public function verificationKey()
>>>>>>> f330c64 (optimization in progress)
    {
        return $this->verificationKey;
    }

<<<<<<< HEAD
<<<<<<< HEAD
    /** @return Validator */
    public function validator()
=======
    public function validator(): Validator
>>>>>>> 22c0e54 (table changes)
=======
    /** @return Validator */
    public function validator()
>>>>>>> f330c64 (optimization in progress)
    {
        return $this->validator;
    }

<<<<<<< HEAD
<<<<<<< HEAD
    public function setValidator(Validator $validator)
=======
    public function setValidator(Validator $validator): void
>>>>>>> 22c0e54 (table changes)
=======
    public function setValidator(Validator $validator)
>>>>>>> f330c64 (optimization in progress)
    {
        $this->validator = $validator;
    }

    /** @return Constraint[] */
<<<<<<< HEAD
<<<<<<< HEAD
    public function validationConstraints()
=======
    public function validationConstraints(): array
>>>>>>> 22c0e54 (table changes)
=======
    public function validationConstraints()
>>>>>>> f330c64 (optimization in progress)
    {
        return $this->validationConstraints;
    }

<<<<<<< HEAD
<<<<<<< HEAD
    public function setValidationConstraints(Constraint ...$validationConstraints)
=======
    public function setValidationConstraints(Constraint ...$validationConstraints): void
>>>>>>> 22c0e54 (table changes)
=======
    public function setValidationConstraints(Constraint ...$validationConstraints)
>>>>>>> f330c64 (optimization in progress)
    {
        $this->validationConstraints = $validationConstraints;
    }
}
