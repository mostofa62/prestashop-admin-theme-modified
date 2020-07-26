<?php

namespace Bannermanager\Core\Domain\Banner\QueryHandler;
use Bannermanager\Core\Domain\Banner\Query\GetBannerForEditing;
interface GetBannerForEditingHandlerInterface{

	public function handle(GetBannerForEditing $query);
}