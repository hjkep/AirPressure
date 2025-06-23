<?php

namespace AirPressure\Model;
enum ExecuteMode: string {
    case Gather = 'gather';
    case Interpret = 'interpret';
    case List = 'list';
    case Current = 'current';
}