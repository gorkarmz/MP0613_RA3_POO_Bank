<?php

namespace ComBank\Transactions;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/28/24
 * Time: 11:30 AM
 */

use ComBank\Bank\Contracts\BankAccountInterface;
use ComBank\Exceptions\InvalidOverdraftFundsException;
use ComBank\Exceptions\ZeroAmountException;
use ComBank\Transactions\Contracts\BankTransactionInterface;
use ComBank\Transactions\DepositTransaction;

class TransferTransaction extends BaseTransaction implements BankTransactionInterface
{

    private $destinationAccount;
    public function __construct(float $amount, BankAccountInterface $destinationAccount)
    {
        if ($amount <= 0) {
            throw new ZeroAmountException('Amount must be greater than zero');
        }
        $this->destinationAccount = $destinationAccount;
        $this->amount = $amount;
    }

    public function applyTransaction(BankAccountInterface $bankAccount): float
    {
        $newBalance = $bankAccount->getBalance() - $this->amount;

        if (!$bankAccount->getOverdraft()->isGrantOverdraftFunds($newBalance)) {
            throw new InvalidOverdraftFundsException('Transfer rejected when the source account has insufficient available funds.');
        }

        $this->destinationAccount->transaction(new DepositTransaction($this->amount));

        return $newBalance;
    }

    public function getTransactionInfo(): string
    {
        return 'TRANSFER_TRANSACTION';
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getDestinationAccount(): BankAccountInterface
    {
        return $this->destinationAccount;
    }

    public function setDestinationAccount(BankAccountInterface $destinationAccount): void

    {

        $this->destinationAccount = $destinationAccount;
    }
}
