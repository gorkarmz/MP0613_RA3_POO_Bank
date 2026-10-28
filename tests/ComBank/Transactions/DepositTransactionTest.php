<?php

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/28/24
 * Time: 11:41 AM
 */

use ComBank\Bank\BankAccount;
use ComBank\Exceptions\ZeroAmountException;
use ComBank\Transactions\DepositTransaction;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DepositTransactionTest extends TestCase
{
    public function testApplyTransaction()
    {
        $bankAccount = new BankAccount(50.25);
        $amount = 25.25;
        $trans = new DepositTransaction($amount);
        $newBalance = $trans->applyTransaction($bankAccount);
        $this->assertEquals(75.50, $newBalance);
    }

    #[DataProvider('invalidAmountProvider')]
    public function testInvalidAmount($amount)
    {
        $this->expectException(ZeroAmountException::class);
        new DepositTransaction($amount);
    }

    public function testTransactionInfo()
    {
        $trans = new DepositTransaction(22.0);
        $this->assertEquals('DEPOSIT_TRANSACTION', $trans->getTransactionInfo());
    }

    public function testGetAmount()
    {
        $trans = new DepositTransaction(100.25);
        $this->assertEquals(100.25, $trans->getAmount());
    }

    public static function invalidAmountProvider(): array
    {
        return [
            'negative'       => [-100],
            'small negative' => [-0.01],
            'zero'           => [0],
        ];
    }
}