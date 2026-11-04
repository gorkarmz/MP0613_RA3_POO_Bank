<?php

namespace ComBank\Transactions;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/28/24
 * Time: 1:22 PM
 */

use ComBank\Bank\Contracts\BankAccountInterface;
use ComBank\Transactions\Contracts\BankTransactionInterface;

class InterestTransaction extends BaseTransaction implements BankTransactionInterface
{
    public function __construct(float $amount = 0.0)
    {
        $this->amount = $amount;
    }

    public function applyTransaction(BankAccountInterface $bankAccount): float
    {
        $interest = ($bankAccount->getBalance() * $bankAccount->getOverdraft()->getInterestRate()) / 100;
        $this->amount = $interest;

        return $bankAccount->getBalance() + $interest;
    }

    public function getTransactionInfo(): string
    {
        return 'INTEREST_TRANSACTION';
    }

    public function getAmount(): float
    {
        return $this->amount;
    }
}
