<?php

namespace App\Models\CustomerManagement\Travel;

use App\Models\CustomerManagement\Customer;

class TravelCustomer extends Customer
{
  public function travelInfo()
  {
    return collect(DB::select('select * from ins_get_customer_info(?)', [$this->customer_no]))->first();
  }
}