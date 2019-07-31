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