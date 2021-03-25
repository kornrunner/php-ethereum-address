<?php declare(strict_types=1);

namespace kornrunner\Ethereum;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class AddressTest extends TestCase {

    public function testCreateNew(): void {
        $address = new Address;
        $this->assertNotEmpty($address->getPrivateKey());
        $this->assertIsString($address->getPrivateKey());
        $this->assertSame(64, strlen($address->getPrivateKey()));
        $this->assertSame(40, strlen($address->get()));
    }

    public function testCreateFromPrivateKey(): void {
        $key = '996b7de9c371b0ca9f916d6c264c04a57e350e84addc286ac3f91e8937113f63';
        $address = new Address($key);
        $this->assertSame($key, $address->getPrivateKey());
        $this->assertSame('677a637ec8f0bb2c8d33c6ace08054e521bff4b5', $address->get());
        $this->assertSame('5f65c9c32a4e38393b79ccf94913c1e5dbe7071d4264aad290d936c4bb2a7c0e3a71ebc855aaadd38f477320d54cd88e5133bfcf97bbf037252db4cd824ab902', $address->getPublicKey());
    }
    /**
     * @dataProvider privateKeyPading
     */
    public function testPrivateKeyPadding($key, $public): void {
        $address = new Address($key);
        $this->assertSame($public, $address->get());
    }

    public static function privateKeyPading(): array {
        return [
            ['93262d84237f92dc8e4409062dcc9dfc8cdc211ec32b18aa073af15841cd8440', '669d9098736e33b8a0ee0470c10357b66caac548'],
            ['093262d84237f92dc8e4409062dcc9dfc8cdc211ec32b18aa073af15841cd844', '2c10383ae14f59415979d7c232ca2c85b62c18a9'],
            ['07a51d7d4445c567c12639ca38e4c9fc4b12f6ec9f0aab82f98c28acaae446a3', 'f81153ba99e401149c6d028eb39fd657e474e7c0'],
            ['7a51d7d4445c567c12639ca38e4c9fc4b12f6ec9f0aab82f98c28acaae446a30', 'f783c3bccfcc24a3731eb25b9587bf5071aab592'],
        ];
    }

    public function testThrowsNotHex(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Private key must be a hexadecimal number');
        new Address('xxxx');
    }

    public function testThrowsWrongSize(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Private key should be exactly 64 chars long');
        new Address(dechex(1));
    }

}