<?php namespace ComBank\OverdraftStrategy;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/28/24
 * Time: 12:27 PM
 */

use ComBank\OverdraftStrategy\Contracts\OverdraftInterface;
use Override;

class GoldOverdraft implements OverdraftInterface
{

    public function isGrantOverdraftFunds(float $newamount): bool
    {
        return ($this->getOverdraftFundsAmount() + $newamount) >= 0;
    }

   
    public function getOverdraftFundsAmount(): float
    {
        return 500.0;
    }

    #[Override]
    public function getInterestRate(): int
    {
        return 10;    
    }

}
