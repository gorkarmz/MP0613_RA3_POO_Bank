<?php

namespace ComBank\Transactions;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/28/24
 * Time: 1:22 PM
 */

use ComBank\Bank\Contracts\BankAccountInterface;
use ComBank\Exceptions\InvalidOverdraftFundsException;
use ComBank\Exceptions\ZeroAmountException;
use ComBank\Transactions\Contracts\BankTransactionInterface;

class WithdrawTransaction extends BaseTransaction implements BankTransactionInterface
{
    public function __construct(float $amount)
    {
        if ($amount <= 0) {
            throw new ZeroAmountException('Amount must be greater than zero');
        }

        $this->amount = $amount;
    }

    public function applyTransaction(BankAccountInterface $bankAccount): float
    {
        $newBalance = $bankAccount->getBalance() - $this->amount;

        if (!$bankAccount->getOverdraft()->isGrantOverdraftFunds($newBalance)) {
            throw new InvalidOverdraftFundsException('Your withdraw has reach the max overdraft funds.');
        }

        return $newBalance;
    }

    public function getTransactionInfo(): string
    {
        return 'WITHDRAW_TRANSACTION';
    }

    public function getAmount(): float
    {
        return $this->amount;
    }
}