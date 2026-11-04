<?php

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/27/24
 * Time: 7:24 PM
 */

use ComBank\Bank\BankAccount;
use ComBank\OverdraftStrategy\GoldOverdraft;
use ComBank\OverdraftStrategy\NoOverdraft;
use ComBank\OverdraftStrategy\SilverOverdraft;
use ComBank\Transactions\DepositTransaction;
use ComBank\Transactions\InterestTransaction;
use ComBank\Transactions\WithdrawTransaction;
use ComBank\Exceptions\BankAccountException;
use ComBank\Exceptions\FailedTransactionException;
use ComBank\Exceptions\InvalidArgsException;
use ComBank\Exceptions\ZeroAmountException;

require_once 'bootstrap.php';


//---[Bank account 1]---/
// create a new account1 with balance 400
$bankAccount1 = new BankAccount(400, 'john.smith@example.com');


pl('--------- [Start testing bank account #1, No overdraft] --------');
try {
    // show balance account
    pl('My balance: ' . $bankAccount1->getBalance());
    pl('My email: ' . $bankAccount1->getEmail());
    // close account
    $bankAccount1->closeAccount();
    // reopen account
    $bankAccount1->reopenAccount();


    // deposit +150 
    pl('Doing transaction deposit (+150) with current balance ' . $bankAccount1->getBalance());
    $bankAccount1->transaction(new DepositTransaction(150));

    pl('My new balance after deposit (+150) : ' . $bankAccount1->getBalance());


    // withdrawal -25
    pl('Doing transaction withdrawal (-25) with current balance ' . $bankAccount1->getBalance());
    $bankAccount1->transaction(new WithdrawTransaction(25));

    pl('My new balance after withdrawal (-25) : ' . $bankAccount1->getBalance());

    // withdrawal -600
    pl('Doing transaction withdrawal (-600) with current balance ' . $bankAccount1->getBalance());
    $bankAccount1->transaction(new WithdrawTransaction(600));
} catch (ZeroAmountException $e) {
    pl($e->getMessage());
} catch (BankAccountException $e) {
    pl($e->getMessage());
} catch (FailedTransactionException $e) {
    pl('Error transaction: ' . $e->getMessage());
}
pl('My balance after failed last transaction : ' . $bankAccount1->getBalance());

$bankAccount1->closeAccount();





//---[Bank account 2]---/
pl('--------- [Start testing bank account #2, Silver overdraft (100.0 funds)] --------');
try {
    $bankAccount2 = new BankAccount(200, 'johnexample.com');

    pl('My balance: ' . $bankAccount2->getBalance());
    pl('My email: ' . $bankAccount2->getEmail());

    $bankAccount2->closeAccount();
    $bankAccount2->reopenAccount();

    $bankAccount2->applyOverdraft(new SilverOverdraft());

    pl('Doing transaction deposit (+100) with current balance ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new DepositTransaction(100));

    pl('My new balance after deposit (+100) : ' . $bankAccount2->getBalance());

    pl('Doing transaction deposit (-300) with current balance ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new WithdrawTransaction(300));

    pl('My new balance after withdrawal (-300) : ' . $bankAccount2->getBalance());

    pl('Doing transaction deposit (-50) with current balance ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new WithdrawTransaction(50));

    pl('My new balance after withdrawal (-50) with funds : ' . $bankAccount2->getBalance());

    pl('Doing transaction withdrawal (-120) with current balance ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new WithdrawTransaction(120));
} catch (InvalidArgsException $e) {
    pl($e->getMessage());
} catch (FailedTransactionException $e) {
    pl('Error transaction: ' . $e->getMessage());
}

if (isset($bankAccount2)) {
    pl('My balance after failed last transaction : ' . $bankAccount2->getBalance());

    try {
        pl('Doing transaction withdrawal (-20) with current balance : ' . $bankAccount2->getBalance());
        $bankAccount2->transaction(new WithdrawTransaction(20));
    } catch (FailedTransactionException $e) {
        pl('Error transaction: ' . $e->getMessage());
    }
    pl('My new balance after withdrawal (-20) with funds : ' . $bankAccount2->getBalance());

    try {
        $bankAccount2->closeAccount();
        $bankAccount2->closeAccount();
    } catch (BankAccountException $e) {
        pl($e->getMessage());
    }
}


$bankAccount3 = new BankAccount(1000.0);
pl('Initial balance: ' . $bankAccount3->getBalance());
$bankAccount3->applyOverdraft(new NoOverdraft());
$bankAccount3->transaction(new InterestTransaction());
pl('Balance after interest transaction (1%): ' . $bankAccount3->getBalance());


$bankAccount4 = new BankAccount(1000.0);
pl('Initial balance: ' . $bankAccount4->getBalance());
$bankAccount4->applyOverdraft(new SilverOverdraft());
$bankAccount4->transaction(new InterestTransaction());
pl('Balance after interest transaction (5%): ' . $bankAccount4->getBalance());

$bankAccount5 = new BankAccount(1000.0);
pl('Initial balance: ' . $bankAccount5->getBalance());
$bankAccount5->applyOverdraft(new GoldOverdraft());
$bankAccount5->transaction(new InterestTransaction());
pl('Balance after interest transaction (10%): ' . $bankAccount5->getBalance());
