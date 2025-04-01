<?php

declare(strict_types=1);

namespace Duon\Wire;

enum Type
{
	case Literal;
	case Env;
	case Create;
	case Entry;
	case Callback;
}
