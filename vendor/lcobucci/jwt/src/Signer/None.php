<?php
<<<<<<< HEAD
<<<<<<< HEAD

namespace Lcobucci\JWT\Signer;

final class None extends BaseSigner
{
    public function getAlgorithmId()
=======
declare(strict_types=1);
=======
>>>>>>> f330c64 (optimization in progress)

namespace Lcobucci\JWT\Signer;

final class None extends BaseSigner
{
<<<<<<< HEAD
    public function algorithmId(): string
>>>>>>> 22c0e54 (table changes)
=======
    public function getAlgorithmId()
>>>>>>> f330c64 (optimization in progress)
    {
        return 'none';
    }

<<<<<<< HEAD
<<<<<<< HEAD
    public function createHash($payload, Key $key)
=======
    // @phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
    public function sign(string $payload, Key $key): string
>>>>>>> 22c0e54 (table changes)
=======
    public function createHash($payload, Key $key)
>>>>>>> f330c64 (optimization in progress)
    {
        return '';
    }

<<<<<<< HEAD
<<<<<<< HEAD
    public function doVerify($expected, $payload, Key $key)
=======
    // @phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
    public function verify(string $expected, string $payload, Key $key): bool
>>>>>>> 22c0e54 (table changes)
=======
    public function doVerify($expected, $payload, Key $key)
>>>>>>> f330c64 (optimization in progress)
    {
        return $expected === '';
    }
}
