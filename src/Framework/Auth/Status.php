<?php

namespace Framework\Auth;

enum Status {

    case EXISTS;
    case FAILED;
    case INVALID_INPUT;
    case SUCCESS;
}
