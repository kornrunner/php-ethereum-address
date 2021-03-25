<?php declare(strict_types=1);

namespace kornrunner\Ethereum;

use InvalidArgumentException;
use kornrunner\Keccak;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Crypto\Key\PrivateKeyInterface;
use Mdanter\Ecc\Serializer\PublicKey\DerPublicKeySerializer;

class Address {

    public function __construct(string $privateKey = '') {
        $generator = EccFactory::getSecgCurves()->generator256k1();
        if (empty ($privateKey)) {
            $this->privateKey = $generator->createPrivateKey();
        } else {
            if (!ctype_xdigit($privateKey)) {
                throw new InvalidArgumentException('Private key must be a hexadecimal number');
            }
            if (strlen($privateKey) != 64) {
                throw new InvalidArgumentException('Private key should be exactly 64 chars long');
            }

            $key = gmp_init($privateKey, 16);
            $this->privateKey = $generator->getPrivateKeyFrom($key);
        }
    }

    public function getPrivateKey(): string {
        return str_pad(gmp_strval($this->privateKey->getSecret(), 16), 64, '0', STR_PAD_LEFT);
    }

    public function getPublicKey(): string {
        $publicKey = $this->privateKey->getPublicKey();
        $publicKeySerializer = new DerPublicKeySerializer(EccFactory::getAdapter());
        return substr($publicKeySerializer->getUncompressedKey($publicKey), 2);
    }

    public function get(): string {
        $hash = Keccak::hash(hex2bin($this->getPublicKey()), 256);
        return substr($hash, -40);
    }

    /**
     * @var PrivateKeyInterface
     */
    private $privateKey;
}
