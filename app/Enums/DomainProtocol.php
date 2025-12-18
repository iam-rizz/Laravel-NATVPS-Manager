<?php

namespace App\Enums;

enum DomainProtocol: string
{
    case Http = 'http';
    case Https = 'https';
}
