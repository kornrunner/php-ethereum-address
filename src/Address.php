<?php declare(strict_types=1);

namespace kornrunner\Ethereum;

use InvalidArgumentException;
use kornrunner\Keccak;
use Mdanter\Ecc\Crypto\Key\PrivateKeyInterface;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Serializer\PublicKey\DerPublicKeySerializer;

class Address
{

    private const SIZE = 64;
    protected $prefix = '';
    /**
     * @var PrivateKeyInterface
     */
    private $privateKey;

    public function __construct(string $privateKey = '', string $prefix = '')
    {
        $this->setPrefix($prefix);
        $privateKey = $this->removePrefix($privateKey);
        $generator = EccFactory::getSecgCurves()->generator256k1();
        if (empty ($privateKey)) {
            $this->privateKey = $generator->createPrivateKey();
        } else {
            if (!ctype_xdigit($privateKey)) {
                throw new InvalidArgumentException('Private key must be a hexadecimal number');
            }
            if (strlen($privateKey) != self::SIZE) {
                throw new InvalidArgumentException(sprintf('Private key should be exactly %d chars long', self::SIZE));
            }

            $key = gmp_init($privateKey, 16);
            $this->privateKey = $generator->getPrivateKeyFrom($key);
        }
    }

    public function setPrefix(string $prefix = '')
    {
        $this->prefix = $prefix;
    }

    public function removePrefix(string $any)
    {
        if (substr($any, 0, strlen($this->prefix)) === $this->prefix) {
            return substr($any, strlen($this->prefix));
        }
        return $any;
    }

    public function getPrivateKey(): string
    {
        return $this->prefix . str_pad(gmp_strval($this->privateKey->getSecret(), 16), self::SIZE, '0', STR_PAD_LEFT);
    }

    public function get(): string
    {
        $hash = Keccak::hash(hex2bin($this->removePrefix($this->getPublicKey())), 256);
        return $this->prefix . substr($hash, -40);
    }

    public function getPublicKey(): string
    {
        $publicKey = $this->privateKey->getPublicKey();
        $publicKeySerializer = new DerPublicKeySerializer(EccFactory::getAdapter());
        return $this->prefix . substr($publicKeySerializer->getUncompressedKey($publicKey), 2);
    }
}
