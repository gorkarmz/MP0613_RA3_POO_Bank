<?php namespace ComBank\OverdraftStrategy;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/28/24
 * Time: 12:27 PM
 */

use ComBank\OverdraftStrategy\Contracts\OverdraftInterface;

class NoOverdraft implements OverdraftInterface
{

    public function isGrantOverdraftFunds(float $newamount): bool
    {
        return ($this->getOverdraftFundsAmount() + $newamount) >= 0;
    }

   
    public function getOverdraftFundsAmount(): float
    {
        return 0.0;
    }

}
