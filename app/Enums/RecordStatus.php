<?php

namespace App\Enums;

enum RecordStatus: string {

  case ACTIVE = "ACT";
  case DELETE = "DEL";
  case DISABLE = "DBL";
  case BLOCK = "BLK";
  case LOCKED = "LCK";
  case PENDING = "PND";
}
