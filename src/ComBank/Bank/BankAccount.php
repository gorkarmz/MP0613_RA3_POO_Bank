<?php

namespace ComBank\Bank;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/27/24
 * Time: 7:25 PM
 */

use ComBank\Exceptions\BankAccountException;
use ComBank\Exceptions\InvalidArgsException;
use ComBank\Exceptions\ZeroAmountException;
use ComBank\OverdraftStrategy\NoOverdraft;
use ComBank\Bank\Contracts\BankAccountInterface;
use ComBank\Exceptions\FailedTransactionException;
use ComBank\Exceptions\InvalidOverdraftFundsException;
use ComBank\OverdraftStrategy\Contracts\OverdraftInterface;
use ComBank\Support\Traits\AmountValidationTrait;
use ComBank\Transactions\Contracts\BankTransactionInterface;

class BankAccount implements BankAccountInterface
{
    private $balance;
    private $status;
    private OverdraftInterface $overdraft;

    public function __construct($balance)
    {
        $this->balance = $balance;
        $this->status = true;
        $this->overdraft = new NoOverdraft();
    }

  
    public function getBalance(): float
    {
        return $this->balance;
    }

    public function setBalance(float $balance): void
    {
        $this->balance = $balance;
    }

    public function getOverdraft(): OverdraftInterface
    {
        return $this->overdraft;
    }

    public function applyOverdraft(OverdraftInterface $overdraft): void
    {
        $this->overdraft = $overdraft;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus(bool $status): void
    {
        $this->status = $status;
    }

    public function closeAccount(): void
    {
        if (!$this->isOpen()) {
            throw new BankAccountException('Bank account is already closed');
        }
        $this->status = false;
    }

    public function reopenAccount(): void
    {
        if ($this->isOpen()) {
            throw new BankAccountException('Bank account is already open');
        }
        $this->status = true;
    }

    public function isOpen(): bool
    {
        return $this->status;
    }

    public function transaction(BankTransactionInterface $transaction): void
    {
        if (!$this->isOpen()) {
            throw new BankAccountException('Bank account is already closed');
        }

        try {
            $newBalance = $transaction->applyTransaction($this);
            $this->setBalance($newBalance);
        } catch (InvalidOverdraftFundsException $e) {
            throw new FailedTransactionException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
