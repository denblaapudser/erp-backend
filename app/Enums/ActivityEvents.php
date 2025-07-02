<?php

namespace App\Enums;

enum ActivityEvents
{
    case LoggedIn;
    case LoggedOut;
    case ProductUpdated;
    case ProductCreated;
    case ProductDeleted;
    case ProductTaken;
    case UserCreated;
    case UserUpdated;
    case UserDeleted;
}