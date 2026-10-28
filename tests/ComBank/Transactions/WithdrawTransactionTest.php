<?php

use ComBank\Bank\BankAccount;
use ComBank\Exceptions\InvalidOverdraftFundsException;
use ComBank\Exceptions\ZeroAmountException;
use ComBank\OverdraftStrategy\SilverOverdraft;
use ComBank\Transactions\WithdrawTransaction;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/28/24
 * Time: 1:47 PM
 */

class WithdrawTransactionTest extends TestCase
{
    public function testWithdrawTransactionLessThanBalance()
    {
        $bankAccount = new BankAccount(150.0);
        $trans = new WithdrawTransaction(50);
        $newBalance = $trans->applyTransaction($bankAccount);
        $this->assertEquals(100, $newBalance);
    }

    public function testWithdrawTransactionMoreThanBalanceNoOverdraft()
    {
        $this->expectException(InvalidOverdraftFundsException::class);

        $bankAccount = new BankAccount(100.0);
        $trans = new WithdrawTransaction(150);
        $trans->applyTransaction($bankAccount);
    }

    public function testWithdrawTransactionMoreThanBalanceWithOverdraft()
    {
        $bankAccount = new BankAccount(100.0);
        $bankAccount->applyOverdraft(new SilverOverdraft());

        $trans = new WithdrawTransaction(150);
        $newBalance = $trans->applyTransaction($bankAccount);
        $this->assertEquals(-50, $newBalance);
    }

    #[DataProvider('invalidAmountProvider')]
    public function testInvalidAmount($amount)
    {
        $this->expectException(ZeroAmountException::class);
        new WithdrawTransaction($amount);
    }

    public function testTransactionInfo()
    {
        $trans = new WithdrawTransaction(22.0);
        $this->assertEquals('WITHDRAW_TRANSACTION', $trans->getTransactionInfo());
    }

    public function testGetAmount()
    {
        $trans = new WithdrawTransaction(100.25);
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